<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;

class Inicializacao  
{

    protected $lista_opcoes; //Lista de nó disponiveis para insercao na arvore
    protected $lista_inseridos; //Lista de nós inseridos na arvore
    function __construct($lista_argumentos) {
        $this->arg = new Argumento;
        $this->constr = new Construcao;


        $this->lista_opcoes = $this->constr->geraListaPremissasConclsao($lista_argumentos,[]);
    }


    public function getListaOpcoes(){
        return $this->lista_opcoes;
    }

    public function getListaInseridos(){
        return $this->lista_opcoes;
    }
}