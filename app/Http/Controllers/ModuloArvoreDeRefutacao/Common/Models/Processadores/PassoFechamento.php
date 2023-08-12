<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class PassoFechamento extends Serializa
{
    protected int $idNoFolha;
    protected bool $idNoContraditorio;

    /**
     * @return int
     */
    public function getIdNoFolha(): int
    {
        return $this->idNoFolha;
    }

    /**
     * @param  int  $idNoFolha
     * @return void
     */
    public function setIdNoFolha(int $idNoFolha): void
    {
        $this->idNoFolha = $idNoFolha;
    }

    /**
     * @return int
     */
    public function getIdNoContraditorio(): int
    {
        return $this->idNoContraditorio;
    }

    /**
     * @param  int  $idNoContraditorio
     * @return void
     */
    public function setIdNoContraditorio(int $idNoContraditorio): void
    {
        $this->idNoContraditorio = $idNoContraditorio;
    }
}
