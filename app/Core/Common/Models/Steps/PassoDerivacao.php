<?php

namespace App\Core\Common\Models\Steps;

use App\Core\Common\Models\Enums\RegrasEnum;
use App\Core\Common\Serialization\Serializa;

class PassoDerivacao extends Serializa
{
    /** @var Array<int>  */
    protected array $idsNoInsercoes;
    protected int $idNoDerivacao;
    protected RegrasEnum $regra;

    /**
     * @return Array<int>
     */
    public function getIdsNoInsercoes(): array
    {
        return $this->idsNoInsercoes;
    }

    /**
     * @param  Array<int> $idsNoInsercoes
     * @return void
     */
    public function setIdsNoInsercoes(array $idsNoInsercoes): void
    {
        $this->idsNoInsercoes = $idsNoInsercoes;
    }

    /**
     * @param  int   $idsNoInsercoes
     * @param  array $idNoinsercao
     * @return void
     */
    public function addIdNoInsercoes(array $idNoinsercao): void
    {
        array_push($this->idsNoInsercoes, $idNoinsercao);
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
