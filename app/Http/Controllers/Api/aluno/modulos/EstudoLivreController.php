<?php

namespace App\Http\Controllers\Api\Aluno\Modulos;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Exceptions\ArvoreDeRefutacaoExecption;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoTicagem;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreAdicionaRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreArvoreRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreIniciarRequest;
use Exception;
use Illuminate\Http\Request;

class EstudoLivreController extends Controller
{
    public function arvore(EstudoLivreArvoreRequest $request)
    {
        try {
            $arvore = new Base($request->xml);

            if (!$arvore->arvoreOtimizada()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }

            return ResponseController::json(Type::success, Action::store, $arvore->imprimirArvore());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function iniciar(EstudoLivreIniciarRequest $request)
    {
        try {
            $arvore = new Base($request->xml);
            return ResponseController::json(Type::success, Action::index, $arvore->imprimir());
        } catch(ArvoreDeRefutacaoExecption $e) {
            return ResponseController::json(Type::error, Action::index, null, $e->getMessage());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    public function adiciona(EstudoLivreAdicionaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoInicializacao($request->passo);

            if (!$arvore->tentativaInicializacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function deriva(Request $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoDerivacao($request->passo);

            if (!$arvore->tentativaDerivacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function tica(Request $request)
    {
        try {
            $arvore = new Base($request->xml);
            $arvore->carregarCamposEssenciais($request->all());

            $passo = new PassoTicagem($request->passo);

            if (!$arvore->tentativaTicagem($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    public function fecha(Request $request)
    {
        try {
            $arvore = new Base($request->xml);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFechamento($request->passo);

            if (!$arvore->tentativaFechamento($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }
}
