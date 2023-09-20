<?php

namespace App\Core\Common\Models\PrintTree;

use App\Core\Common\Serialization\Serializa;

class Aresta extends Serializa
{
    protected float $linhaX1;
    protected float $linhaY1;
    protected float $linhaX2;
    protected float $linhaY2;

    /**
     * @return float
     */
    public function getLinhaX1(): float
    {
        return $this->linhaX1;
    }

    /**
     * @param  float $linhaX1
     * @return void
     */
    public function setLinhaX1(float $linhaX1): void
    {
        $this->linhaX1 = $linhaX1;
    }

    /**
     * @return float
     */
    public function getLinhaY1(): float
    {
        return $this->linhaY1;
    }

    /**
     * @param  float $linhaY1
     * @return void
     */
    public function setLinhaY1(float $linhaY1): void
    {
        $this->linhaY1 = $linhaY1;
    }

    /**
     * @return float
     */
    public function getLinhaX2(): float
    {
        return $this->linhaX2;
    }

    /**
     * @param  float $linhaX2
     * @return void
     */
    public function setLinhaX2(float $linhaX2): void
    {
        $this->linhaX2 = $linhaX2;
    }

    /**
     * @return float
     */
    public function getLinhaY2(): float
    {
        return $this->linhaY2;
    }

    /**
     * @param  float $linhaY2
     * @return void
     */
    public function setLinhaY2(float $linhaY2): void
    {
        $this->linhaY2 = $linhaY2;
    }
}
