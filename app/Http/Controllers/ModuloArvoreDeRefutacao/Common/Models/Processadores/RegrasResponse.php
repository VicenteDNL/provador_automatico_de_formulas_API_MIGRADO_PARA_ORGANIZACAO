<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores;

class RegrasResponse
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
