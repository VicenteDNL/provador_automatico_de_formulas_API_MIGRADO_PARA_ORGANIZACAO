<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class RecompensaModel extends Serializa
{
    protected int $rec_codigo;
    protected string $rec_nome;
    protected string $rec_imagem;
    protected float $rec_pontuacao;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return int
     */
    public function getRecCodigo(): int
    {
        return $this->rec_codigo;
    }

    /**
     * @param  int  $rec_codigo
     * @return void
     */
    public function setRecCodigo(int $rec_codigo): void
    {
        $this->rec_codigo = $rec_codigo;
    }

    /**
     * @return string
     */
    public function getRecNome(): string
    {
        return $this->rec_nome;
    }

    /**
     * @param  string $rec_nome
     * @return void
     */
    public function setRecNome(string $rec_nome): void
    {
        $this->rec_nome = $rec_nome;
    }

    /**
     * @return string
     */
    public function getRecImagem(): string
    {
        return $this->rec_imagem;
    }

    /**
     * @param  string $rec_imagem
     * @return void
     */
    public function setRecImagem(string $rec_imagem): void
    {
        $this->rec_imagem = $rec_imagem;
    }

    /**
     * @return float
     */
    public function getRecPontuacao(): float
    {
        return $this->rec_pontuacao;
    }

    /**
     * @param  float $rec_pontuacao
     * @return void
     */
    public function setRecPontuacao(float $rec_pontuacao): void
    {
        $this->rec_pontuacao = $rec_pontuacao;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param  string $created_at
     * @return void
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    /**
     * @param  string $updated_at
     * @return void
     */
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
