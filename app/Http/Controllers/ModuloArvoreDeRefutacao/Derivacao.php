<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;


class Derivacao  
{
    protected $lista_derivacoes =[];
    
    function __construct() {
        
    }


    public function setListaDerivacoes($lista){
        $this->lista_derivacoes = $lista;
    }

    public function getListaDerivacoes (){
        return $this->lista_derivacoes;
    }

    public function setDerivacao($insercao,$derivacao,$regra){
        array_push( 
            $this->lista_derivacoes, 
            ['insercao'=>$insercao,'derivacao'=>$derivacao,'regra'=>$regra]
        );
       
    }
}