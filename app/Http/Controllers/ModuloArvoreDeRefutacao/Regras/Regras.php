<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Regras;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula\Predicado;

class Regras
{
    /**
     * Aplica a regra da Dupla Negação em uma instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function duplaNeg(Predicado $predicado): RegrasResponse
    {
        $centro = clone $predicado;
        $centro->removeNegacaoPredicado();
        $centro->removeNegacaoPredicado();
        return new RegrasResponse(['esquerda' => null, 'centro' => [$centro], 'direita' => null]);
    }

    /** Aplica a regra da Conjuncao em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function conjuncao(Predicado $predicado): RegrasResponse
    {
        $esquerda = clone $predicado->getEsquerdaPredicado();
        $direita = clone $predicado->getDireitaPredicado();
        return new RegrasResponse(['esquerda' => null, 'centro' => [ $esquerda, $direita], 'direita' => null]);
    }

    /** Aplica a regra da Conjuncao Negada em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function conjuncaoNeg(Predicado $predicado): RegrasResponse
    {
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        $segundo->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => [$primeiro], 'centro' => null, 'direita' => [$segundo]]);
    }

    /** Aplica a regra da Disjuncao em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function disjuncao(Predicado $predicado): RegrasResponse
    {
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        return new RegrasResponse(['esquerda' => [$primeiro], 'centro' => null, 'direita' => [$segundo]]);
    }

    /** Aplica a regra da Disjuncao Negada em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function disjuncaoNeg(Predicado $predicado): RegrasResponse
    {
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        $segundo->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => null, 'centro' => [$primeiro, $segundo], 'direita' => null]);
    }

    /** Aplica a regra da Condicional em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function condicional(Predicado $predicado): RegrasResponse
    {
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $primeiro->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => [$primeiro], 'centro' => null, 'direita' => [$segundo]]);
    }

    /** Aplica a regra da Condicional Negado em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function condicionalNeg(Predicado $predicado): RegrasResponse
    {
        $primeiro = clone $predicado->getEsquerdaPredicado();
        $segundo = clone $predicado->getDireitaPredicado();
        $segundo->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => null, 'centro' => [$primeiro, $segundo], 'direita' => null]);
    }

    /** Aplica a regra da Bicondicional Negado em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function bicondicional(Predicado $predicado): RegrasResponse
    {
        $primeiro1 = clone $predicado->getEsquerdaPredicado();
        $primeiro2 = clone $primeiro1;
        $segundo1 = clone $predicado->getDireitaPredicado();
        $segundo2 = clone $segundo1;
        $primeiro2->addNegacaoPredicado();
        $segundo2->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => [$primeiro1, $segundo1], 'centro' => null, 'direita' => [$primeiro2, $segundo2]]);
    }

    /** Aplica a regra da Condicional Negado em uma nova instancia do Predicado
     * @param  Predicado      $predicado
     * @return RegrasResponse
     */
    public function bicondicionalNeg(Predicado $predicado): RegrasResponse
    {
        $primeiro1 = clone $predicado->getEsquerdaPredicado();
        $primeiro2 = clone $primeiro1;
        $segundo1 = clone $predicado->getDireitaPredicado();
        $segundo2 = clone $segundo1;
        $primeiro2->addNegacaoPredicado();
        $segundo1->addNegacaoPredicado();
        return new RegrasResponse(['esquerda' => [$primeiro1, $segundo1], 'centro' => null, 'direita' => [$primeiro2, $segundo2]]);
    }
}
