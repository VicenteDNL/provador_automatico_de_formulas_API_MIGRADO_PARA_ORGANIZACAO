<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\RegrasEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Criadores\CriarNoBifurcado;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Criadores\CriarNoBifurcadoDuplo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Criadores\CriarNoCentro;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Criadores\CriarNoCentroDuplo;

class GeradorArvore
{
    protected ?No $arvore;
    protected AplicadorRegras $regras;
    private int $idNo;

    public function __construct()
    {
        $this->regras = new AplicadorRegras();
    }

    /**
     * @return No|null
     */
    public function getArvore(): ?No
    {
        return $this->arvore;
    }

    /**
     * @param No         $noDerivacao
     * @param No[]       $listaNoInsercao
     * @param RegrasEnum $regra
     */
    protected function derivarByRegra(No $noDerivacao, array $listaNoInsercao, RegrasEnum $regra)
    {
        switch($regra) {
            case RegrasEnum::DUPLANEGACAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->duplaNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentro::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), $this->genereteIdNo());
                }
                break;
            case RegrasEnum::CONJUNCAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->conjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::DISJUNCAONEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->disjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONDICIONALNEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->condicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::DISJUNCAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->disjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONDICIONAL:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->condicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::BICONDICIONAL:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->bicondicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcadoDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONJUNCAONEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->conjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::BICONDICIONALNEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->bicondicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcadoDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
        }
    }

    /**
     * Gera um novo id para o No
     * @return int
     */
    protected function genereteIdNo(): int
    {
        $this->idNo += 1;
        return $this->idNo;
    }
}
