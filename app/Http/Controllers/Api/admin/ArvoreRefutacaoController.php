<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\Base;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\ArvoreRefutacao\ArvoreRefutacaoOtimizadaRequest;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class ArvoreRefutacaoController extends Controller
{
    public function arvoreOtimizada(ArvoreRefutacaoOtimizadaRequest $request)
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

    public function criarPiorArvore(Request $request)
    {
        // try {
        //     $xml = simplexml_load_string($request->xml);
        // } catch(Exception $e) {
        //     return response()->json(['success' => false, 'msg' => 'XML INVALIDO!', 'data' => ''], 500);
        // }

        // #Cria a arvore passando o XML
        // $listaArgumentos = $this->arg->criarFormula($xml);
        // $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'], $listaArgumentos['conclusao']);
        // $arv = $this->gerador->piorArvore($arvore);

        // #Gera lista das possicoes de cada no da tabela
        // $impressaoAvr = $this->constr->geraListaArvore($arv, $xml, 0, true, true);

        // #Gera uma string da Formula XML
        // $formulaGerada = $this->arg->stringFormula($xml);
        // #--------

        // return response()->json(['success' => true, 'msg' => '', 'data' => ['impressao' => $impressaoAvr, 'str' => $formulaGerada]]);
    }

    public function premissasConclusao(Request $request)
    {
        // $arvore = new Base($request->xml);
        // $arvore->setListaPassos([]);
        // $arvore->setListaTicagem([]);
        // $arvore->setListaFechamento([]);
        // $arvore->derivacao->setListaDerivacoes([]);
        // $arvore->fecharAutomatido(false);
        // $arvore->ticarAutomatico(false);

        // if (!$arvore->montarArvore()) {
        //     return  response()->json(['success' => false, 'msg' => 'Error ar criar arvore', 'data' => ''], 500);
        // }

        // return  response()->json([
        //     'success' => true,
        //     'msg'     => '',
        //     'data'    => $arvore->retorno(null, $request->usu_hash, $request->exe_hash, true),
        // ]);
    }

    public function adicionaNoIncializacao(Request $request)
    {
        // // try{

        // $arvore = new Base($request->xml);
        // $arvore->setListaPassos($request->inicio['lista']);

        // if (!$arvore->montarArvore($request->inicio['no']['id'], $request->inicio['negacao'])) {
        //     return  response()->json([
        //         'success' => false,
        //         'msg'     => $arvore->getError(),
        //     ]);
        // }

        // return  response()->json([
        //     'success' => true,
        //     'msg'     => '',
        //     'data'    => $arvore->retorno(null, $request->usu_hash, $request->exe_hash, true),
        // ]);

        // }catch(\Exception $e){
        //     return response()->json(['success' => false, 'msg'=>'erro interno', 'data'=>''],500);
        // }
    }

    public function derivar(Request $request)
    {
        // $arvore = new Base($request->xml);
        // $arvore->setListaPassos($request->inicio['lista']);
        // $arvore->setListaTicagem($request->ticar['lista']);
        // $arvore->setListaFechamento($request->fechar['lista']);
        // $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        // $arvore->fecharAutomatido(false);
        // $arvore->ticarAutomatico(false);
        // $arvore->inicializacao->setFinalizado(true);

        // if (!$arvore->derivar($request->derivacao['no']['idNo'], $request->derivacao['folhas'], $request->derivacao['regra'])) {
        //     return  response()->json([
        //         'success' => false,
        //         'msg'     => $arvore->getError(),
        //     ]);
        // }

        // return  response()->json([
        //     'success' => true,
        //     'msg'     => '',
        //     'data'    => $arvore->retorno(null, $request->usu_hash, $request->exe_hash, true),
        // ]);
    }

    public function ticarNo(Request $request)
    {
        // $arvore = new Base($request->xml);
        // $arvore->setListaPassos($request->inicio['lista']);
        // $arvore->setListaTicagem($request->ticar['lista']);
        // $arvore->setListaFechamento($request->fechar['lista']);
        // $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        // $arvore->fecharAutomatido(false);
        // $arvore->ticarAutomatico(false);
        // $arvore->inicializacao->setFinalizado(true);

        // if (!$arvore->montarArvore()) {
        //     return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
        // }

        // if (!$arvore->ticarNo($request->ticar['no'])) {
        //     return  response()->json([
        //         'success' => false,
        //         'msg'     => $arvore->getError(),
        //     ]);
        // }

        // return  response()->json([
        //     'success' => true,
        //     'msg'     => '',
        //     'data'    => $arvore->retorno(null, $request->usu_hash, $request->exe_hash, true),
        // ]);
    }

    public function fecharNo(Request $request)
    {
        // $arvore = new Base($request->xml);
        // $arvore->setListaPassos($request->inicio['lista']);
        // $arvore->setListaTicagem($request->ticar['lista']);
        // $arvore->setListaFechamento($request->fechar['lista']);
        // $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        // $arvore->fecharAutomatido(false);
        // $arvore->ticarAutomatico(false);
        // $arvore->inicializacao->setFinalizado(true);

        // if (!$arvore->montarArvore()) {
        //     return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
        // }

        // if (!$arvore->fecharNo($request->fechar['folha'], $request->fechar['no'])) {
        //     return  response()->json([
        //         'success' => false,
        //         'msg'     => $arvore->getError(),
        //     ]);
        // }

        // return  response()->json([
        //     'success' => true,
        //     'msg'     => '',
        //     'data'    => $arvore->retorno(null, $request->usu_hash, $request->exe_hash, true),
        // ]);
    }
}
