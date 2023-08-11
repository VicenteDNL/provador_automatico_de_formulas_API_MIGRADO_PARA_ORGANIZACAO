<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class PassoInicializacao extends Serializa
{
    protected int $idNo;
    protected bool $negacao;

    /**
     * @return int
     */
    public function getIdNo(): int
    {
        return $this->idNo;
    }

    /**
     * @param  int  $idNo
     * @return void
     */
    public function setIdNo(int $idNo): void
    {
        $this->idNo = $idNo;
    }

    /**
     * @return bool
     */
    public function getNegacao(): bool
    {
        return $this->negacao;
    }

    /**
     * @param  bool $negacao
     * @return void
     */
    public function setNegacao(bool $negacao): void
    {
        $this->negacao = $negacao;
    }
}
