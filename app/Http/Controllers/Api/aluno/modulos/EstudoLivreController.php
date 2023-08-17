<?php

namespace App\Http\Controllers\Api\Aluno\Modulos;

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
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreAdicionaRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreArvoreRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreDerivaRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreFechaRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreIniciarRequest;
use App\Http\Requests\API\Aluno\Modulos\EstudoLivre\EstudoLivreTicaRequest;
use Exception;

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
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function inicia(EstudoLivreIniciarRequest $request)
    {
        try {
            $arvore = new Base($request->xml);
            return ResponseController::json(Type::success, Action::index, $arvore->imprimir());
        } catch(ArvoreDeRefutacaoExecption $e) {
            return ResponseController::json(Type::error, Action::index, null, $e->getMessage());
        } catch(Exception $e) {
            return ResponseController::json(Type::exception, Action::index);
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
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function deriva(EstudoLivreDerivaRequest $request)
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
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function tica(EstudoLivreTicaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            $passo = new PassoTicagem($request->passo);

            if (!$arvore->tentativaTicagem($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    public function fecha(EstudoLivreFechaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFechamento($request->passo);

            if (!$arvore->tentativaFechamento($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Exception $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }
}
