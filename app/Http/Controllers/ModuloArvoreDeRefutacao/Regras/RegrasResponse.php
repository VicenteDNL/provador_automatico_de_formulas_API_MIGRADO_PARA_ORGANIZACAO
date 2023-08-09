<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Regras;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula\Predicado;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class RegrasResponse extends Serializa
{
    /** @var Predicado[] */
    private ?array $esquerda;

    /** @var Predicado[] */
    private ?array $centro;

    /** @var Predicado[] */
    private ?array $direita;

    /**
     * @return Predicado[]|null
     */
    public function getEsquerda(): ?array
    {
        return $this->esquerda;
    }

    /**
     * @param Predicado[]
     * @param  array $esquerda
     * @return void
     */
    public function setEsquerda(array $esquerda): void
    {
        $this->esquerda = $esquerda;
    }

    /**
     * @return Predicado[]|null
     */
    public function getCentro(): ?array
    {
        return $this->centro;
    }

    /**
     * @param Predicado[]
     * @param  mixed $centro
     * @return void
     */
    public function setCentro($centro): void
    {
        $this->centro = $centro;
    }

    /**
     * @return Predicado[]|null
     */
    public function getDireita(): ?array
    {
        return $this->direita;
    }

    /**
     * @param Predicado[]
     * @param  mixed $direita
     * @return void
     */
    public function setDireita($direita): void
    {
        $this->direita = $direita;
    }
}
