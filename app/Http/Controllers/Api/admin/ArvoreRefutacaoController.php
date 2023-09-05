<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\Base;
use App\Core\Common\Exceptions\ArvoreDeRefutacaoExecption;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoAdicionaRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoDerivaRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoFechaRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoIniciaRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoOtimizadaRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoRecriarRequest;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoTicaRequest;
use Throwable;

class ArvoreRefutacaoController extends Controller
{
    public function arvore(ArvoreRefutacaoOtimizadaRequest $request)
    {
        try {
            $arvore = new Base($request->xml);

            if (!$arvore->arvoreOtimizada()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }

            return ResponseController::json(Type::success, Action::store, $arvore->imprimirArvore($request['exibirLinhas'] ?? true));
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function recria(ArvoreRefutacaoRecriarRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            if (!$arvore->reconstruirPassos()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function inicia(ArvoreRefutacaoIniciaRequest $request)
    {
        try {
            $arvore = new Base($request->xml);
            return ResponseController::json(Type::success, Action::index, $arvore->imprimir());
        } catch(ArvoreDeRefutacaoExecption $e) {
            return ResponseController::json(Type::error, Action::index, null, $e->getMessage());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::index);
        }
    }

    public function adiciona(ArvoreRefutacaoAdicionaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoInicializacao($request->passo);

            if (!$arvore->tentativaInicializacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function deriva(ArvoreRefutacaoDerivaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoDerivacao($request->passo);

            if (!$arvore->tentativaDerivacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function tica(ArvoreRefutacaoTicaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            $passo = new PassoTicagem($request->passo);

            if (!$arvore->tentativaTicagem($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    public function fecha(ArvoreRefutacaoFechaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFechamento($request->passo);

            if (!$arvore->tentativaFechamento($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }
}
