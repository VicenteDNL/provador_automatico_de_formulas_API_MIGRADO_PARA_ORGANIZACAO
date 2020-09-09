<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Premissa
{
    protected $valor_str; //String conteudo
    protected $valor_obj; //Objeto (Predicado)

    function __construct($valor_str,$valor_obj) {
       $this->valor_str=$valor_str;
       $this->valor_obj=$valor_obj;
   }

   public function getValorStrPremissa(){
       return $this->valor_str;
   }

   public function setValorStrPremissa($valor_str){
      $this->valor_str=$valor_str;
       
  }

  public function getValorObjPremissa(){
   return $this->valor_obj;
}

   public function setValorObjPremissa($valor_obj){
   $this->valor_obj=$valor_obj;
       
   }


}