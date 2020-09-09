<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Formula;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Regras extends Controller
{
    /*Aplica a regra da Dupla Negação em uma nova instancia do objeto e
      retorna um array com o novo valor*/
      public function DuplaNeg($predicado){
        $centro =clone $predicado;
        $centro->removeNegacaoPredicado();
        $centro->removeNegacaoPredicado();
        return ['esquerda'=>null, 'centro'=>[$centro],'direita'=>null,];
    }

    /*Aplica a regra da Conjuncao em uma nova instancia do objeto e
     retorna um array com o novo valor*/
    public function conjuncao($predicado){
        $esquerda =clone $predicado->getEsquerdaPredicado();
        $direita = clone $predicado->getDireitaPredicado();
        return ['esquerda'=>null, 'centro'=>[ $esquerda, $direita],'direita'=>null,];
    }

    /*Aplica a regra da Conjuncao Negada em uma nova instancia do
    objeto e retorna um array com o novo valor*/
    public function conjuncaoNeg($predicado){
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        $segundo->addNegacaoPredicado();
        return ['esquerda'=>[$primeiro],'centro'=>null,'direita'=>[$segundo]];
    }

    /*Aplica a regra da Disjuncao em uma nova instancia do
     objeto e retorna um array com o novo valor*/
    public function disjuncao ($predicado){
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        return ['esquerda'=>[$primeiro], 'centro'=>null, 'direita'=>[$segundo]];

    }

    /*Aplica a regra da Disjuncao Negada em uma nova instancia do
    objeto e retorna um array com o novo valor*/
    public function disjuncaoNeg($predicado){
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        $segundo->addNegacaoPredicado();
        return ['esquerda'=>null,'centro'=>[$primeiro,$segundo], 'direita'=>null,];
    }

    /*Aplica a regra da Condicional em uma nova instancia do
    objeto e retorna um array com o novo valor*/
    public function condicional($predicado){
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo =  clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        return ['esquerda'=>[$primeiro],'centro'=>null,'direita'=>[$segundo],];
    }

    /*Aplica a regra da Condicional Negado em uma nova instancia do
    objeto e retorna um array com o novo valor*/
    public function condicionalNeg($predicado){
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $segundo->addNegacaoPredicado();
        return ['esquerda'=>null, 'centro'=>[$primeiro,$segundo], 'direita'=>null,];

    }

    /*Aplica a regra da Bicondicional Negado em uma nova instancia do
    objeto e retorna um array com o novo valor*/
    public function bicondicional($predicado){
        $primeiro1 = clone $predicado->getEsquerdaPredicado();
        $primeiro2 = clone $primeiro1;
        $segundo1 = clone $predicado->getDireitaPredicado();
        $segundo2 = clone $segundo1;
        $primeiro2->addNegacaoPredicado();
        $segundo2->addNegacaoPredicado();
        return ['esquerda'=>[$primeiro1,$segundo1],'centro'=>null,'direita'=>[$primeiro2,$segundo2]];
    }

    /*Aplica a regra da Condicional Negado em uma nova instancia do
        objeto e retorna um array com o novo valor*/
    public function bicondicionalNeg($predicado){
        $primeiro1 = clone $predicado->getEsquerdaPredicado();
        $primeiro2 = clone $primeiro1;
        $segundo1 = clone $predicado->getDireitaPredicado();
        $segundo2 = clone $segundo1;
        $primeiro2->addNegacaoPredicado();
        $segundo1->addNegacaoPredicado();
        return ['esquerda'=>[$primeiro1,$segundo1], 'centro'=>null, 'direita'=>[$primeiro2,$segundo2],
        ];
    }

}