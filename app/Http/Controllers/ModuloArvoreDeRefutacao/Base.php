<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;

class Base  
{  
    protected $xml_formula;
    protected $string_formula; //String da Formula
    protected $lista_no;       //Lista de posicionamento dos nós da arvore
    protected $lista_aresta;    //Lista de posicionamento das arestas da arvore
    protected $lista_passos;    //Lista de passos para reconstruir a arvore
    protected $lista_argumentos;    //Lista de argumentos da formula (Premissas e conclusao)
    protected $lista_ticagem;    //Lista dos nos já ticados
    protected $lista_fechamento; //Lista dos nos já derivador
    public $inicializacao;      //Objeto com as informaçoes da inicialização
    public $derivacao;          //Objeto com as informaçoes da derivacao
    public $arvore;             //Objeto com as informaçoes da arvore já montada

    function __construct($xml) {
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
        $this->constr = new Construcao;

        $this->prepararArvore($xml);



    }

    /*
    * Inicializa o objeto para armazenar as informaçoes da derivação da arvore
    *
    */
    private function prepararArvore($xml){
        try{$this->xml_formula = simplexml_load_string($xml);}
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>'']);}

        $this->lista_argumentos=$this->arg->CriaListaArgumentos($this->xml_formula);
        $this->string_formula = $this->arg->stringFormula($this->xml_formula);
        $this->lista_no = [];
        $this->lista_aresta = [];
        $this->derivacao = new Derivacao;
        $this->inicializacao = new Inicializacao( $this->lista_argumentos);
        
    }


    public function montarArvore(){

        /**
         * 
         * Neste momento a arvore é contruida com seus primeiros nós já posicionados na arvore
         * 
         */
        $arvore = $this->gerador->inicializarPassoPasso($this->lista_argumentos ,null,$this->lista_passos, null);
        if(!$arvore['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$arvore['messagem'], 'data'=>'']); 
        }

        /**
         * 
         * Neste momento a arvore é reconstruida por completo,sua reconstrução segue a lista de Derivações
         * 
         */
 
        $arvore = $this->gerador->gerarArvorePassoPasso($arvore['arv'], $this->derivacao->getListaDerivacoes());


        #tica os nos já derivados
        $arvore = $this->gerador->ticarTodosNos($arvore, $this->lista_ticagem);
        if(!$arvore['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$arvore['messagem'], 'data'=>'']); 
        }

        #fechar informados os ramos
        $arvore = $this->gerador->fecharTodosNos($arvore['arv'], $this->lista_fechamento);
        if(!$arvore['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$arvore['messagem'], 'data'=>'']); 
        }

        /**
         * 
         * Cria a lista de posiçoes dos nós e arestas para serem exibidas no navegador
         * 
         */
        $impresaoAvr = $this->constr->geraListaArvore($arvore['arv'],700,350,0);

        
        $this->lista_aresta = $impresaoAvr['arestas'];
        $this->lista_no = $impresaoAvr['nos'];
        $this->arvore=$arvore['arv'];
      
        return true;


    }

    public function derivar($derivacao,$insercao,$regra){

        $arvore = $this->montarArvore();
        $arvore =$this->gerador->derivar($this->arvore,$derivacao,$insercao,$regra);

        if(!$arvore['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$arvore['messagem'], 'data'=>'']);
        }

        #Adiciona a nova dericação a lista
        $this->derivacao->setDerivacao($insercao,$derivacao,$regra);
        #-----

        $this->arvore=$arvore['arv'];

        return true;
    }



    public function getListaNo(){
        return $this->lista_no;
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



}
