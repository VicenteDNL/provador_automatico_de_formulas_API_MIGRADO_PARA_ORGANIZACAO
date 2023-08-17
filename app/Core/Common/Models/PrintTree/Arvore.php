<?php

namespace App\Core\Common\Models\PrintTree;

use App\Core\Common\Serialization\Serializa;

class Arvore extends Serializa
{
    /** @var No[] */
    protected array $nos = [];

    /** @var Aresta[] */
    protected array $arestas = [];

    /** @var Linha[] */
    protected array $linhas = [];
    protected float $width = 0;
    protected float $height = 0;

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

        /**
         * @return Linha[]
         */
    public function getLinhas(): array
    {
        return $this->linhas;
    }

    /**
     * @param  Linha[] $linhas
     * @return void
     */
    public function setLinhas(array $linhas): void
    {
        $this->linhas = $linhas;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @param  float $width
     * @return void
     */
    public function setWidth(float $width): void
    {
        $this->width = $width;
    }

        /**
         * @return float
         */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @param  float $width
     * @param  float $height
     * @return void
     */
    public function setHeight(float $height): void
    {
        $this->height = $height;
    }
}
