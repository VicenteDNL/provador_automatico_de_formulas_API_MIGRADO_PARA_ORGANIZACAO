<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class ExercicioModel extends Serializa
{
    protected int $exe_codigo;
    protected int $rec_codigo;
    protected int $niv_codigo;
    protected string $exe_nome;
    protected string $exe_descricao;
    protected string $exe_hash;
    protected string $exe_link;
    protected ?int $exe_tempoexecucao;
    protected string $exe_situacao;
    protected bool $exe_ativo;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return int
     */
    public function getExeCodigo(): int
    {
        return $this->exe_codigo;
    }

    /**
     * @param  int  $exe_codigo
     * @return void
     */
    public function setExeCodigo(int $exe_codigo): void
    {
        $this->exe_codigo = $exe_codigo;
    }

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
     * @return string
     */
    public function getExeNome(): string
    {
        return $this->exe_nome;
    }

    /**
     * @param  string $exe_nome
     * @return void
     */
    public function setExeNome(string $exe_nome): void
    {
        $this->exe_nome = $exe_nome;
    }

    /**
     * @return string
     */
    public function getExeDescricao(): string
    {
        return $this->exe_descricao;
    }

    /**
     * @param  string $exe_descricao
     * @return void
     */
    public function setExeDescricao(string $exe_descricao): void
    {
        $this->exe_descricao = $exe_descricao;
    }

    /**
     * @return string
     */
    public function getExeHash(): string
    {
        return $this->exe_hash;
    }

    /**
     * @param  string $exe_hash
     * @return void
     */
    public function setExeHash(string $exe_hash): void
    {
        $this->exe_hash = $exe_hash;
    }

    /**
     * @return string
     */
    public function getExeLink(): string
    {
        return $this->exe_link;
    }

    /**
     * @param  string $exe_link
     * @return void
     */
    public function setExeLink(string $exe_link): void
    {
        $this->exe_link = $exe_link;
    }

    /**
     * @return ?int
     */
    public function getExeTempoexecucao(): ?int
    {
        return $this->exe_tempoexecucao;
    }

    /**
     * @param  ?int $exe_tempoexecucao
     * @return void
     */
    public function setExeTempoexecucao(?int $exe_tempoexecucao): void
    {
        $this->exe_tempoexecucao = $exe_tempoexecucao;
    }

    /**
     * @return string
     */
    public function getExeSituacao(): string
    {
        return $this->exe_situacao;
    }

    /**
     * @param  string $exe_situacao
     * @return void
     */
    public function setExeSituacao(string $exe_situacao): void
    {
        $this->exe_situacao = $exe_situacao;
    }

    /**
     * @return bool
     */
    public function getExeAtivo(): bool
    {
        return $this->exe_ativo;
    }

    /**
     * @param  bool $exe_ativo
     * @return void
     */
    public function setExeAtivo(bool $exe_ativo): void
    {
        $this->exe_ativo = $exe_ativo;
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
