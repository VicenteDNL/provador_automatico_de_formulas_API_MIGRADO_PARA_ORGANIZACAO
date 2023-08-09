<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula;

class Conclusao
{
    /** String do caracter que representa o simbolo de conclusão */
    protected string $simbolo;
    protected string $valor_str;
    protected Predicado $valor_obj;

    public function __construct(string $valor_str, string $simbolo, Predicado $valor_obj)
    {
        $this->valor_str = $valor_str;
        $this->simbolo = $simbolo;
        $this->valor_obj = $valor_obj;
    }

    /**
     * @return string
     */
    public function getValorStrConclusao(): string
    {
        return $this->valor_str;
    }

    /**
     * @param  string $valor_str
     * @return void
     */
    public function setValorStrConclusao($valor_str): void
    {
        $this->valor_str = $valor_str;
    }

    /**
     * @return string
     */
    public function getValorObjConclusao(): Predicado
    {
        return $this->valor_obj;
    }

    /**
     * @param  string $valor_obj
     * @return void
     */
    public function setValorObjConclusao($valor_obj): void
    {
        $this->valor_obj = $valor_obj;
    }

    /**
     * @return string
     */
    public function getSimboloConclusao(): string
    {
        return $this->simbolo;
    }
}
