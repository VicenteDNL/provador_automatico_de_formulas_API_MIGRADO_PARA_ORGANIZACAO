<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\Usuario\UsuarioStoreRequest;
use App\Http\Requests\API\Admin\Usuario\UsuarioUpdateRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UsuarioController extends Controller
{
    private $user;

    public function __construct(Usuario $user)
    {
        $this->user = $user;
    }

    /**
     * @return Response
     */
    public function index()
    {
        try {
            $data = $this->user->orderBy('created_at', 'desc')->paginate(10);
            return ResponseController::json(Type::success, Action::index, $data);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    /**
     * @param  UserStoreRequest $request
     * @return Response
     */
    public function store(UsuarioStoreRequest $request)
    {
        try {
            $params = $request->all();
            $user = new Usuario();
            $user->nome = $params['nome'];
            $user->email = $params['email'];
            $user->password = Hash::make($params['password']);
            $user->ativo = $params['ativo'];

            if ($user->save()) {
                return ResponseController::json(Type::success, Action::store);
            }
            return ResponseController::json(Type::error, Action::store);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    /**
     * @param  int      $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $data = $this->user->findOrFail($id);
            return ResponseController::json(Type::success, Action::show, $data);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    /**
     * @param  Request  $request
     * @param  int      $id
     * @return Response
     */
    public function update(UsuarioUpdateRequest $request, $id)
    {
        try {
            $params = $request->all();
            $user = $this->user->findOrFail($id);
            $user->nome = $params['nome'] ?? $user->nome ;
            $user->email = $params['email'] ?? $user->email;
            $user->password = empty($params['password']) ? $user->password : Hash::make($params['password']);
            $user->ativo = $params['ativo'] ?? $user->ativo ;

            if ($user->save()) {
                if ($request->user()->id == $id) {
                    $request->user()->currentAccessToken()->delete();
                }
                return ResponseController::json(Type::success, Action::update);
            }
            return ResponseController::json(Type::error, Action::update);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    /**
     *
     * @param  int      $id
     * @param  Request  $request
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->id == $id) {
                return ResponseController::json(Type::error, Action::destroy, null, 'Não é permitido deletar um usuário com sessão ativa');
            }
            $user = Usuario::findOrFail($id);

            if ($user->delete()) {
                return ResponseController::json(Type::success, Action::destroy);
            }
            return ResponseController::json(Type::error, Action::destroy);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::destroy);
        }
    }
}
