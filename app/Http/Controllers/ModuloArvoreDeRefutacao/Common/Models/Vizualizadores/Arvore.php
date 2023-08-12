<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class Arvore extends Serializa
{
    /** @var No[] */
    protected array $nos = [];

    /** @var Aresta[] */
    protected array $arestas = [];

    /**
     * @return No[]
     */
    public function getNos(): array
    {
        return $this->nos;
    }

    /**
     * @param  No[] $nos
     * @return void
     */
    public function setNos(array $nos): void
    {
        $this->nos = $nos;
    }

    /**
     * @return Aresta[]
     */
    public function getArestas(): array
    {
        return $this->arestas;
    }

    /**
     * @param  Aresta[] $arestas
     * @return void
     */
    public function setArestas(array $arestas): void
    {
        $this->arestas = $arestas;
    }
}
