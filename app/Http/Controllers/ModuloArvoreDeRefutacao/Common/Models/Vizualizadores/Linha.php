<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class Linha extends Serializa
{
    protected string $texto;
    protected string $numero;
    protected float $posY;
    protected float $posX;

    /**
     * @return string
     */
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param  string $texto
     * @return void
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @return float
     */
    public function getNumero(): float
    {
        return $this->numero;
    }

    /**
     * @param  float $numero
     * @return void
     */
    public function setNumero(float $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return float
     */
    public function getPosY(): float
    {
        return $this->posY;
    }

    /**
     * @param  float $posY
     * @return void
     */
    public function setPosY(float $posY): void
    {
        $this->posY = $posY;
    }

    /**
     * @return float
     */
    public function getPosX(): float
    {
        return $this->posX;
    }

    /**
     * @param  float $posX
     * @return void
     */
    public function setPosX(float $posX): void
    {
        $this->posX = $posX;
    }
}
