<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula;

class Premissa
{
    protected string $valor_str;
    protected Predicado $valor_obj;

    public function __construct(string $valor_str, Predicado $valor_obj)
    {
        $this->valor_str = $valor_str;
        $this->valor_obj = $valor_obj;
    }

    /**
     * @return string
     */
    public function getValorStrPremissa(): ?string
    {
        return $this->valor_str;
    }

    /**
     * @param  string $valor_str
     * @return void
     */
    public function setValorStrPremissa($valor_str): void
    {
        $this->valor_str = $valor_str;
    }

    /**
     * @return Predicado
     */
    public function getValorObjPremissa(): Predicado
    {
        return $this->valor_obj;
    }

    /**
     * @param  Predicado $valor_obj
     * @return void
     */
    public function setValorObjPremissa($valor_obj): void
    {
        $this->valor_obj = $valor_obj;
    }
}
