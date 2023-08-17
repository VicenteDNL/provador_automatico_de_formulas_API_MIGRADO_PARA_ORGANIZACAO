<?php

namespace App\Core\Common\Models\Steps;

use App\Core\Common\Serialization\Serializa;

class PassoFechamento extends Serializa
{
    protected int $idNoFolha;
    protected int $idNoContraditorio;

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
