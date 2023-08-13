<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class PassoDerivacao extends Serializa
{
    /** @var Array<int>  */
    protected array $idNoInsercoes;
    protected int $idNoDerivacao;
    protected RegrasEnum $regra;

    /**
     * @return Array<int>
     */
    public function getIdNoInsercoes(): array
    {
        return $this->idNoInsercoes;
    }

    /**
     * @param  Array<int> $idNoInsercoes
     * @return void
     */
    public function setIdNoInsercoes(array $idNoInsercoes): void
    {
        $this->idNoInsercoes = $idNoInsercoes;
    }

    /**
     * @param  int   $idNoInsercoes
     * @param  array $idNoinsercao
     * @return void
     */
    public function addIdNoInsercoes(array $idNoinsercao): void
    {
        array_push($this->idNoInsercoes, $idNoinsercao);
    }

    /**
     * @return int
     */
    public function getIdNoDerivacao(): int
    {
        return $this->idNoDerivacao;
    }

    /**
     * @param  int  $idNoDerivacao
     * @return void
     */
    public function setIdNoDerivacao(int $idNoDerivacao): void
    {
        $this->idNoDerivacao = $idNoDerivacao;
    }

    /**
     * @return RegrasEnum
     */
    public function getRegra(): RegrasEnum
    {
        return $this->regra;
    }

    /**
     * @param  RegrasEnum $regra
     * @return void
     */
    public function setRegra(RegrasEnum $regra): void
    {
        $this->regra = $regra;
    }
}
