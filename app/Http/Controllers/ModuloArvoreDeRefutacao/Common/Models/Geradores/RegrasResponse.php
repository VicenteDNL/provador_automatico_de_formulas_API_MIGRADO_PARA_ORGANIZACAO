<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class RegrasResponse extends Serializa
{
    /** @var Predicado[] */
    protected ?array $esquerda;

    /** @var Predicado[] */
    protected ?array $centro;

    /** @var Predicado[] */
    protected ?array $direita;

    /**
     * @return Predicado[]|null
     */
    public function getEsquerda(): ?array
    {
        return $this->esquerda;
    }

    /**
     * @param Predicado[]
     * @param  Predicado[]|null $esquerda
     * @return void
     */
    public function setEsquerda(?array $esquerda): void
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
     * @param  Predicado[]|null $centro
     * @return void
     */
    public function setCentro(?array $centro): void
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
     * @param  Predicado[]|null $direita
     * @return void
     */
    public function setDireita(?array $direita): void
    {
        $this->direita = $direita;
    }
}
