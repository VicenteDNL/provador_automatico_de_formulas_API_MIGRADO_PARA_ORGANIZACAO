<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class PassoTicagem extends Serializa
{
    protected int $idNo;
    protected bool $idNoContraditorio;

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
}
