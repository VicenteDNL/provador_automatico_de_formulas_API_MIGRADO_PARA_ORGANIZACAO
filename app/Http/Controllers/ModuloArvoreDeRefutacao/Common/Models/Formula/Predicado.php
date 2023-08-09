<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula;

class Predicado
{
    /**  Onde 0 = não negado, 1  = negado, 2 = dupla negação... */
    protected int $negado;
    protected string $valor;
    protected PredicadoTipoEnum $tipo;
    protected ?Predicado $esquerda;
    protected ?Predicado $direita;

    public function __construct(string $valor, int $negado, PredicadoTipoEnum $tipo, ?Predicado $esquerda = null, ?Predicado $direita = null)
    {
        $this->valor = $valor;
        $this->negado = $negado;
        $this->tipo = $tipo;
        $this->direita = $direita;
        $this->esquerda = $esquerda;
    }

    /**
     * @return string
     */
    public function getValorPredicado(): string
    {
        return $this->valor;
    }

    /**
     * @param  string $valor
     * @return void
     */
    public function setValorPredicado($valor): void
    {
        $this->valor = $valor;
    }

    /**
     * @return PredicadoTipoEnum
     */
    public function getTipoPredicado(): PredicadoTipoEnum
    {
        return $this->tipo;
    }

    /**
     * @param  PredicadoTipoEnum $tipo
     * @return void
     */
    public function setTipoPredicado($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return Predicado|null
     */
    public function getDireitaPredicado(): ?Predicado
    {
        return $this->direita;
    }

    /**
     * @param  Predicado $direita
     * @return void
     */
    public function setDireitaPredicado($direita)
    {
        $this->direita = $direita;
    }

    /**
     * @return Predicado|null
     */
    public function getEsquerdaPredicado(): ?Predicado
    {
        return $this->esquerda;
    }

    /**
     * @param  Predicado $esquerda
     * @return void
     */
    public function setEsquerdaPredicado($esquerda)
    {
        $this->esquerda = $esquerda;
    }

    /**
     * @return int
     */
    public function getNegadoPredicado(): int
    {
        return $this->negado;
    }

     /**
      * @return void
      */
    public function addNegacaoPredicado(): void
    {
        $this->negado = $this->negado + 1;
    }

    /**
     * @return void
     */
    public function removeNegacaoPredicado(): void
    {
        $this->negado = $this->negado - 1;
    }

    /**
     * @return bool
     */
    public function existeEsqDirPredicado(): bool
    {
        if ($this->esquerda == null && $this->direita == null) {
            return true;
        }
        return false;
    }
}
