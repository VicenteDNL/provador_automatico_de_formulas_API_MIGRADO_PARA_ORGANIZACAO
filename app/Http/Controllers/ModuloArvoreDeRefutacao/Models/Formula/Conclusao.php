<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Conclusao
{
    protected $valor_str; //String conteudo
    protected $simbolo;  //String do caracter que representa o simbolo de conclusão
    protected $valor_obj; //Objeto (Predicado)


    function __construct($valor_str,$simbolo,$valor_obj) {
       $this->valor_str=$valor_str;
       $this->simbolo=$simbolo;
       $this->valor_obj=$valor_obj;
    }

    public function getValorStrConclusao(){
        return $this->valor_str;
    }

    public function setValorStrConclusao($valor_str){
        $this->valor_str=$valor_str;
    }

    public function getValorObjConclusao(){
        return $this->valor_obj;
    }

    public function setValorObjConclusao($valor_obj){
        $this->valor_obj=$valor_obj;
    }

    public function getSimboloConclusao(){
       return $this->simbolo;
    }
}