<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class ModuloModel extends Serializa
{
    protected int $mod_codigo;
    protected int $gam_codigo;
    protected string $mod_hash;
    protected ?int $per_codigo;
    protected ?int $rec_codigo;
    protected string $mod_nome;
    protected string $mod_descricao;
    protected string $mod_situacao;
    protected bool $mod_ativo;
    protected string $created_at;
    protected string $updated_at;

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
     * @return int
     */
    public function getGamCodigo(): int
    {
        return $this->gam_codigo;
    }

    /**
     * @param  int  $gam_codigo
     * @return void
     */
    public function setGamCodigo(int $gam_codigo): void
    {
        $this->gam_codigo = $gam_codigo;
    }

    /**
     * @return string
     */
    public function getModHash(): string
    {
        return $this->mod_hash;
    }

    /**
     * @param  string $mod_hash
     * @return void
     */
    public function setModHash(string $mod_hash): void
    {
        $this->mod_hash = $mod_hash;
    }

    /**
     * @return ?int
     */
    public function getPerCodigo(): ?int
    {
        return $this->per_codigo;
    }

    /**
     * @param  ?int $per_codigo
     * @return void
     */
    public function setPerCodigo(?int $per_codigo): void
    {
        $this->per_codigo = $per_codigo;
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
     * @return string
     */
    public function getModNome(): string
    {
        return $this->mod_nome;
    }

    /**
     * @param  string $mod_nome
     * @return void
     */
    public function setModNome(string $mod_nome): void
    {
        $this->mod_nome = $mod_nome;
    }

    /**
     * @return string
     */
    public function getModDescricao(): string
    {
        return $this->mod_descricao;
    }

    /**
     * @param  string $mod_descricao
     * @return void
     */
    public function setModDescricao(string $mod_descricao): void
    {
        $this->mod_descricao = $mod_descricao;
    }

    /**
     * @return string
     */
    public function getModSituacao(): string
    {
        return $this->mod_situacao;
    }

    /**
     * @param  string $mod_situacao
     * @return void
     */
    public function setModSituacao(string $mod_situacao): void
    {
        $this->mod_situacao = $mod_situacao;
    }

    /**
     * @return bool
     */
    public function getModAtivo(): bool
    {
        return $this->mod_ativo;
    }

    /**
     * @param  bool $mod_ativo
     * @return void
     */
    public function setModAtivo(bool $mod_ativo): void
    {
        $this->mod_ativo = $mod_ativo;
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
