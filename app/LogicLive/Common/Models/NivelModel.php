<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class NivelModel extends Serializa
{
    protected int $niv_codigo;
    protected ?int $rec_codigo;
    protected int $mod_codigo;
    protected string $niv_nome;
    protected string $niv_descricao;
    protected string $niv_situacao;
    protected bool $niv_ativo;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return int
     */
    public function getNivCodigo(): int
    {
        return $this->niv_codigo;
    }

    /**
     * @param  int  $niv_codigo
     * @return void
     */
    public function setNivCodigo(int $niv_codigo): void
    {
        $this->niv_codigo = $niv_codigo;
    }

    /**
     * @return ?int
     */
    public function getRecCodigo(): ?int
    {
        return $this->rec_codigo;
    }

    /**
     * @param  ?int $rec_codigo
     * @return void
     */
    public function setRecCodigo(?int $rec_codigo): void
    {
        $this->rec_codigo = $rec_codigo;
    }

    /**
     * @return int
     */
    public function getModCodigo(): int
    {
        return $this->mod_codigo;
    }

    /**
     * @param  int  $mod_codigo
     * @return void
     */
    public function setModCodigo(int $mod_codigo): void
    {
        $this->mod_codigo = $mod_codigo;
    }

    /**
     * @return string
     */
    public function getNivNome(): string
    {
        return $this->niv_nome;
    }

    /**
     * @param  string $niv_nome
     * @return void
     */
    public function setNivNome(string $niv_nome): void
    {
        $this->niv_nome = $niv_nome;
    }

    /**
     * @return string
     */
    public function getNivDescricao(): string
    {
        return $this->niv_descricao;
    }

    /**
     * @param  string $niv_descricao
     * @return void
     */
    public function setNivDescricao(string $niv_descricao): void
    {
        $this->niv_descricao = $niv_descricao;
    }

    /**
     * @return string
     */
    public function getNivSituacao(): string
    {
        return $this->niv_situacao;
    }

    /**
     * @param  string $niv_situacao
     * @return void
     */
    public function setNivSituacao(string $niv_situacao): void
    {
        $this->niv_situacao = $niv_situacao;
    }

    /**
     * @return bool
     */
    public function getNivAtivo(): bool
    {
        return $this->niv_ativo;
    }

    /**
     * @param  bool $niv_ativo
     * @return void
     */
    public function setNivAtivo(bool $niv_ativo): void
    {
        $this->niv_ativo = $niv_ativo;
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
