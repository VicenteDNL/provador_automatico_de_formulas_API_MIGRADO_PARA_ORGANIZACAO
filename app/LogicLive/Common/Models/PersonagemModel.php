<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class PersonagemModel extends Serializa
{
    protected int $per_codigo;
    protected string $per_nome;
    protected string $per_imagem;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return int
     */
    public function getPerCodigo(): int
    {
        return $this->per_codigo;
    }

    /**
     * @param  int  $per_codigo
     * @return void
     */
    public function setPerCodigo(int $per_codigo): void
    {
        $this->per_codigo = $per_codigo;
    }

    /**
     * @return string
     */
    public function getPerNome(): string
    {
        return $this->per_nome;
    }

    /**
     * @param  string $per_nome
     * @return void
     */
    public function setPerNome(string $per_nome): void
    {
        $this->per_nome = $per_nome;
    }

    /**
     * @return string
     */
    public function getPerImagem(): string
    {
        return $this->per_imagem;
    }

    /**
     * @param  string $per_imagem
     * @return void
     */
    public function setPerImagem(string $per_imagem): void
    {
        $this->per_imagem = $per_imagem;
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
