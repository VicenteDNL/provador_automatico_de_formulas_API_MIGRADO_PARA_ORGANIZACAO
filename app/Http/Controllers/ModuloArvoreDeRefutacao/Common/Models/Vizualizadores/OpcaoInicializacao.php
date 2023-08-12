<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class OpcaoInicializacao extends Serializa
{
    protected int $posicao;
    protected string $id ;
    protected string $tipo;
    protected string $texto;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * @param  string $tipo
     * @return void
     */
    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

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
}
