<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Predicado 
{
    protected $valor; //String conteudo
    protected $negado; //Int onde 0=não negado, 1=negado, 2=dupla negação...
    protected $tipo; //String do tipo do Predicado, se ele é um: CONDICIONAL, BICONDICIONAL, DISJUNÇÃO, CONJUNÇÃO, PREDICATIVO
    protected $esquerda; //OBJECT(Predicado)
    protected $direita; //OBJECT(Predicado)

    function __construct($valor,$negado,$tipo,$esquerda,$direita) {
       $this->valor=$valor;
       $this->negado=$negado;
       $this->tipo=$tipo;
       $this->direita=$direita;
       $this->esquerda=$esquerda;
   }

    public function getValorPredicado(){
        return $this->valor;
    }

    public function setValorPredicado($valor){
       $this->valor=$valor;
        
   }

   public function getTipoPredicado(){
       return $this->tipo;
   }

   public function setTipoPredicado($tipo){
      $this->tipo=$tipo;
  }

   public function getDireitaPredicado(){
       return $this->direita;
   }

   public function setDireitaPredicado($direita){
      $this->direita=$direita;
  }

  public function getEsquerdaPredicado(){
       return $this->esquerda;
   }

   public function setEsquerdaPredicado($esquerda){
      $this->esquerda=$esquerda;
       
  }

  public function getNegadoPredicado(){
        return $this->negado;
   }

   public function addNegacaoPredicado(){
        $this->negado= $this->negado+1;
   }

   public function removeNegacaoPredicado(){
       $this->negado= $this->negado-1;
   }
   public function existeEsqDirPredicado(){
       if ($this->esquerda==null && $this->direita==null){
           return true;
       }
       return false;
   }
}
