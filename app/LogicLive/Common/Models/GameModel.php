<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class GameModel extends Serializa
{
    protected ?int $gam_codigo;
    protected string $gam_nome;
    protected string $gam_descricao;
    protected string $gam_situacao;
    protected bool $gam_ativo;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return ?int
     */
    public function getGamCodigo(): ?int
    {
        return $this->gam_codigo;
    }

    /**
     * @param  ?int $gam_codigo
     * @return void
     */
    public function setGamCodigo(?int $gam_codigo): void
    {
        $this->gam_codigo = $gam_codigo;
    }

    /**
     * @return string
     */
    public function getGamNome(): string
    {
        return $this->gam_nome;
    }

    /**
     * @param  string $gam_nome
     * @return void
     */
    public function setGamNome(string $gam_nome): void
    {
        $this->gam_nome = $gam_nome;
    }

    /**
     * @return string
     */
    public function getGamDescricao(): string
    {
        return $this->gam_descricao;
    }

    /**
     * @param  string $gam_descricao
     * @return void
     */
    public function setGamDescricao(string $gam_descricao): void
    {
        $this->gam_descricao = $gam_descricao;
    }

    /**
     * @return string
     */
    public function getGamSituacao(): string
    {
        return $this->gam_situacao;
    }

    /**
     * @param  string $gam_situacao
     * @return void
     */
    public function setGamSituacao(string $gam_situacao): void
    {
        $this->gam_situacao = $gam_situacao;
    }

    /**
     * @return bool
     */
    public function getGamAtivo(): bool
    {
        return $this->gam_ativo;
    }

    /**
     * @param  bool $gam_ativo
     * @return void
     */
    public function setGamAtivo(bool $gam_ativo): void
    {
        $this->gam_ativo = $gam_ativo;
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
