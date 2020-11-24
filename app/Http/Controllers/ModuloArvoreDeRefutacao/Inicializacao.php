<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;

class Inicializacao  
{

    protected $lista_opcoes=[]; //Lista de nó disponiveis para insercao na arvore
    protected $lista_inseridos=[]; //Lista de nós inseridos na arvore
    protected $finalizado=false; //Diz sê o processo de inicialização já foi finalizado
    function __construct($lista_argumentos) {
        $this->arg = new Argumento;
        $this->constr = new Construcao;

        $this->lista_opcoes = $this->constr->geraListaPremissasConclsao($lista_argumentos,[]);
    }


    public function getListaOpcoes(){
        return $this->lista_opcoes;
    }


    public function updateListaOpcoes( $lista){
       return  $this->lista_opcoes = $lista;
    }

    public function getListaInseridos(){
        return $this->lista_inseridos;
    }

    public function setListaInseridos($lista){
         $this->lista_inseridos=$lista;
    }

    public function getFinalizado(){
        return $this->finalizado;
    }

    public function setFinalizado($finalizado){
       $this->finalizado=$finalizado;
    }
}