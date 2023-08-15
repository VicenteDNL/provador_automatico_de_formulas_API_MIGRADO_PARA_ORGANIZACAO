<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

class Conclusao
{
    /** String do caracter que representa o simbolo de conclusão */
    protected string $simbolo;
    protected string $valor_str;
    protected Predicado $valor_obj;
    protected string $id;

    public function __construct(string $valor_str, Predicado $valor_obj, string $id)
    {
        $this->valor_str = $valor_str;
        $this->simbolo = '|- ';
        $this->valor_obj = $valor_obj;
        $this->id = $id;
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
     * @return Predicado
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

        /**
         * @return string
         */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param  string $id
     * @return void
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
