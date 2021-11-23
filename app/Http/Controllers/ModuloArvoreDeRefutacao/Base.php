<?php

/**
 *
 *  Essa Classe é responsavel por se comunicar com o módulo de árvore de refutação
 *  para realizar as operações de derivações
 *
 *  Toda Operação para criar a arvore de refutação deve passar por essa classe
 *
 */

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;

class Base
{
    protected $canvas_width =0;           //
    protected $ticar_automatico  = false;  //
    protected $fechar_automatico =false;   //

    protected $resposta;           //Resposta final da arvore
    protected $error;              //insere uma mensagem de error em caso da execução não ter sucesso
    protected $xml;                //String do Xml da formula
    protected $xml_formula;        //Objeto do Xml da formula
    protected $string_formula;     //String da Formula
    protected $lista_no;           //Lista de posicionamento dos nós da arvore
    protected $lista_aresta;       //Lista de posicionamento das arestas da arvore
    protected $lista_passos;       //Lista de passos para reconstruir a arvore
    protected $lista_argumentos;   //Lista de argumentos da formula (Premissas e conclusao)
    protected $lista_ticagem;      //Lista dos nos já ticados
    protected $lista_fechamento;   //Lista dos nos já derivador
    public $inicializacao;         //Objeto com as informaçoes da inicialização
    public $derivacao;             //Objeto com as informaçoes da derivacao
    public $arvore;                //Objeto com as informaçoes da arvore já montada



    function __construct($xml) {
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
        $this->constr = new Construcao;
        $this->prepararArvore($xml);
    }



    /*
    *
    * Inicializa o objeto para armazenar as informaçoes da derivação da arvore
    *
    */
    private function prepararArvore($xml){
        try{$this->xml_formula = simplexml_load_string($xml);
            $this->xml = $xml;
        }
        catch(\Exception $e){
            $this->error= 'XML INVALIDO!';}

        $this->lista_argumentos=$this->arg->CriaListaArgumentos($this->xml_formula);
        $this->string_formula = $this->arg->stringFormula($this->xml_formula);
        $this->lista_no = [];
        $this->lista_aresta = [];
        $this->lista_passos= [];
        $this->lista_ticagem = [];
        $this->lista_fechamento= [];
        $this->derivacao = new Derivacao;
        $this->inicializacao = new Inicializacao( $this->lista_argumentos);

    }


    public function otimizada(){
        #Cria a arvore passando o XML
        $arvore = $this->gerador->inicializarDerivacao($this->lista_argumentos['premissas'],$this->lista_argumentos['conclusao']);
        $this->arvore =  $this->gerador->arvoreOtimizada($arvore);
        #--------

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,true, true);
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];

        return true;

    }

    public function piorArvore(){
        #Cria a arvore passando o XML
        $arvore = $this->gerador->inicializarDerivacao($this->lista_argumentos['premissas'],$this->lista_argumentos['conclusao']);
        $this->arvore =  $this->gerador->piorArvore($arvore);
        #--------

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,true, true);
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];

        return true;
    }


    public function validar(){

        $arvore = $this->gerador->validarArvore($this->arvore);
        if($arvore['sucesso']==false){
            $this->error = $arvore['messagem'];
            return false;
        }
        $this->resposta=$arvore['resposta'];
        return true;

    }


    /**
     *
     * Esse metodo é responsavel por reconstruir a árvore de retutação
     *
     * parametro:
     *    $idNo -> o identificador do nó que se deseja inserir na árvore
     *    $negacao -> informar se pretende inserir o nó negado ou nao (boleano)
     *    $impressao -> informa se vai ser gerado a impressao das nos e arestas da arvore
     *
    */
    public function montarArvore($idNo=null,$negacao=null,$impressao=true){



        // Neste momento é feito a construção inicial da arvore.
        $arvore = $this->gerador->inicializarPassoPasso($this->lista_argumentos ,$idNo,$this->lista_passos, $negacao);
        if(!$arvore['sucesso']){
            $this->error = $arvore['messagem'];
            return  false;
        }
        // return  $arvore;
        //Verifica se todos os elementos de premissa e conclução estão inseridos na arvore
        $listaStr = $this->constr->geraListaPremissasConclsao($this->lista_argumentos,$arvore['lista']);
        $this->inicializacao->setListaInseridos($arvore['lista']);
        $this->inicializacao->updateListaOpcoes($listaStr);

        //verifica se a etapa de inicialização já foi finalizada
        if(!$this->inicializacao->getFinalizado()){
            $this->inicializacao->setFinalizado(count($listaStr)==0? true:false);
            $this->arvore=$arvore['arv'];

            if($impressao){
                if($this->arvore==null){
                    $this->lista_aresta = [];
                    $this->lista_no = [];
                }
                else{
                    $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,$this->ticar_automatico, $this->fechar_automatico);
                    $this->lista_aresta = $impresaoAvr['arestas'];
                    $this->lista_no = $impresaoAvr['nos'];
                }

            }
            return  true;
        }

        //Neste momento a arvore é reconstruida por completo,sua reconstrução segue a lista de Derivações
        $arvore = $this->gerador->gerarArvorePassoPasso($arvore['arv'], $this->derivacao->getListaDerivacoes());

        //tica os nos já informados pelo usuario
        $arvore = $this->gerador->ticarTodosNos($arvore, $this->lista_ticagem);
        if(!$arvore['sucesso']){
            $this->error = $arvore['messagem'];
            return  false;
        }

        //fechar os nós já informados pelo usuario
        $arvore = $this->gerador->fecharTodosNos($arvore['arv'], $this->lista_fechamento);
        if(!$arvore['sucesso']){
            $this->error = $arvore['messagem'];
            return  false;
        }

        // Cria a lista de posiçoes dos nós e arestas para serem exibidas no navegador

        $this->arvore=$arvore['arv'];
        if($impressao){
            $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,$this->ticar_automatico, $this->fechar_automatico);
            $this->lista_aresta = $impresaoAvr['arestas'];
            $this->lista_no = $impresaoAvr['nos'];
        }

        return true;
    }

    public function setAll($request,$fechar_auto,$ticar_auto){
        $this->setListaPassos($request['inicio']['lista'] );
        $this->setListaTicagem($request['ticar']['lista']);
        $this->setListaFechamento($request['fechar']['lista']);
        $this->fecharAutomatido($fechar_auto);
        $this->ticarAutomatico($ticar_auto);
        $this->derivacao->setListaDerivacoes($request['derivacao']['lista']);
        $this->inicializacao->setFinalizado(true);
    }


    public function retorno($exercicio,$usu_has,$exe_has, $admin =false){
          $retorno=[
            'regras'=>$exercicio!=null?$this->buscarRegras($exercicio,$admin):null,
            'exe_hash'=>$exe_has!=null?$exe_has:null,
            'usu_hash'=>$usu_has!=null?$usu_has:null,
            'exercicio'=>$exercicio,
            'arestas'=>$this->lista_aresta,
            'nos'=>$this->lista_no,
            'derivacao'=> (object) [
                'lista'=> $this->derivacao->getListaDerivacoes(),
                'folhas'=>[],
                'no'=> null,
                'regra'=>null,
            ],
            'fechar'=> (object) [
                'lista'=>$this->lista_fechamento,
                'no'=>null,
                'folha'=>null,
                'auto'=> $this->fechar_automatico
            ],
            'inicio'=>(object)[
                'completa'=>$this->inicializacao->getFinalizado(),
                'lista'=> $this->inicializacao->getListaInseridos(),
                'negacao'=>null,
                'no'=>null,
                'opcoes'=>$this->inicializacao->getListaOpcoes()
            ],
            'ticar'=>(object)[
                'auto'=> $this->ticar_automatico,
                'lista'=> $this->lista_ticagem,
                'no'=>null,
            ],
            'finalizada'=>$this->isFinalizada(),
             'strformula'=>$this->getStrFormula(),
            'xml'=>$this->xml

        ];

        if($admin==true){
            array_splice($retorno, 0, 4);

        }
        return $retorno;

    }


    public function retornoOtimizada(){
        return [
          'arestas'=>$this->lista_aresta,
          'nos'=>$this->lista_no,
           'strformula'=>$this->getStrFormula()
      ] ;

   }


     /**
     *
     * Esse metodo é responsavel por reconstruir a árvore de retutação e derivar a arvore
     * conforme as informações do usuario
     *
     * parametro:
     *    $derivacao -> o identificador do nó que se deseja derivar
     *    $insercao  -> uma lista de identificadores dos nós que receberão a nova derivação
     *    $regra     -> o identificador da regra de vai ser aplicada
     *
    */
    public function derivar($derivacao,$insercao,$regra){

        $arvore = $this->montarArvore(null,null,false);
        $arvore =$this->gerador->derivar($this->arvore,$derivacao,$insercao,$regra);
        if(!$arvore['sucesso']){
            $this->error = $arvore['messagem'];
            return  false;
        }

        #Adiciona a nova dericação a lista
        $this->derivacao->setDerivacao($insercao,$derivacao,$regra);
        #-----

        $this->arvore=$arvore['arv'];
        $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,$this->ticar_automatico, $this->fechar_automatico);
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];
        return true;
    }


    public function fecharNo($noFolha, $noContradicao){

        $fechada = $this->gerador->fecharNo($this->arvore, $noFolha, $noContradicao);
        if(!$fechada['sucesso']){
            $this->error = $fechada['messagem'];
            return  false;
        }
        array_push($this->lista_fechamento,['nofechado'=>$noFolha,'noContradicao'=>$noContradicao]);

        $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,$this->ticar_automatico, $this->fechar_automatico);
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];
        return true;
    }

    public function ticarNo($no){

        $arvorefinal = $this->gerador->ticarNo($this->arvore, $no);
        if(!$arvorefinal['sucesso']){
            $this->error = $arvorefinal['messagem'];
            return  false;
        }

        array_push($this->lista_ticagem,$no);
        $impresaoAvr = $this->constr->geraListaArvore($this->arvore,$this->xml_formula,$this->canvas_width,$this->ticar_automatico, $this->fechar_automatico);
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];
        return true;
    }


    private function buscarRegras($exercicio,$admin){
        if(!$this->inicializacao->getFinalizado() || $admin){
            return [];
        }
        $exercicio  =  ExercicioMVFLP::findOrFail($exercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
       return $this->gerador->arrayPerguntas($this->arvore,$formula->quantidade_regras);

    }


    private function isFinalizada(){

        if(!$this->inicializacao->getFinalizado()){
            return false;
        }
        if($this->gerador->proximoNoParaInsercao($this->arvore)!=null){
            return false;
        }

        if($this->fechar_automatico==false){
            if($this->gerador->existeNoPossivelFechamento($this->arvore)!=null){
                return false;
            }
        }

        if($this->ticar_automatico==false){
            if($this->gerador->existeNoPossivelTicagem($this->arvore)!=null){
                return false;
            }

        }

        return true;
    }

    public function getListaNo(){
        return $this->lista_no;
    }
    public function getResposta(){
        return $this->resposta;
    }



    public function getListaAresta(){
        return $this->lista_aresta;
    }

    public function getStrFormula(){
        return $this->string_formula;
    }

    public function setListaPassos($lista){
        $this->lista_passos=$lista;
    }
    public function getListaPassos(){
        return $this->lista_passos;
    }

    public function setListaTicagem($lista){
        $this->lista_ticagem=$lista;
    }
    public function setListaFechamento($lista){
        $this->lista_fechamento=$lista;
    }

    public function getArvore(){
        return $this->arvore;
    }

    public function getListaArgumentos(){
        return $this->lista_argumentos;
    }

    public function getError(){
        return $this->error;
    }

    public function fecharAutomatido($dado){
        $this->fechar_automatico=$dado;
    }

    public function ticarAutomatico($dado){
        $this->ticar_automatico=$dado;
    }



}
