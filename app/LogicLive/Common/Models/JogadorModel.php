<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class JogadorModel extends Serializa
{
    protected int $jog_codigo;
    protected string $jog_nome;
    protected string $jog_usunome;
    protected string $jog_email;
    protected string $jog_avatar;
    protected string $jog_provedor;
    protected bool $jog_ativo;

    /**
     * @return int
     */
    public function getJogCodigo(): int
    {
        return $this->jog_codigo;
    }

    /**
     * @param  int  $jog_codigo
     * @return void
     */
    public function setJogCodigo(int $jog_codigo): void
    {
        $this->jog_codigo = $jog_codigo;
    }

    /**
     * @return string
     */
    public function getJogNome(): string
    {
        return $this->jog_nome;
    }

    /**
     * @param  string $jog_nome
     * @return void
     */
    public function setJogNome(string $jog_nome): void
    {
        $this->jog_nome = $jog_nome;
    }

    /**
     * @return string
     */
    public function getJogUsunome(): string
    {
        return $this->jog_usunome;
    }

    /**
     * @param  string $jog_usunome
     * @return void
     */
    public function setJogUsunome(string $jog_usunome): void
    {
        $this->jog_usunome = $jog_usunome;
    }

    /**
     * @return string
     */
    public function getJogEmail(): string
    {
        return $this->jog_email;
    }

    /**
     * @param  string $jog_email
     * @return void
     */
    public function setJogEmail(string $jog_email): void
    {
        $this->jog_email = $jog_email;
    }

    /**
     * @return string
     */
    public function getJogAvatar(): string
    {
        return $this->jog_avatar;
    }

    /**
     * @param  string $jog_avatar
     * @return void
     */
    public function setJogAvatar(string $jog_avatar): void
    {
        $this->jog_avatar = $jog_avatar;
    }

    /**
     * @return string
     */
    public function getJogProvedor(): string
    {
        return $this->jog_provedor;
    }

    /**
     * @param  string $jog_provedor
     * @return void
     */
    public function setJogProvedor(string $jog_provedor): void
    {
        $this->jog_provedor = $jog_provedor;
    }

    /**
     * @return bool
     */
    public function getJogAtivo(): bool
    {
        return $this->jog_ativo;
    }

    /**
     * @param  bool $jog_ativo
     * @return void
     */
    public function setJogAtivo(bool $jog_ativo): void
    {
        $this->jog_ativo = $jog_ativo;
    }
}
