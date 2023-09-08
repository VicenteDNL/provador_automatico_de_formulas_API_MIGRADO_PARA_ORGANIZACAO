<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class UsuarioModel extends Serializa
{
    protected string $usu_nome;
    protected string $usu_login;
    protected string $usu_email;
    protected string $created_at;
    protected string $updated_at;

    /**
     * @return string
     */
    public function getUsuNome(): string
    {
        return $this->usu_nome;
    }

    /**
     * @param  string $usu_nome
     * @return void
     */
    public function setUsuNome(string $usu_nome): void
    {
        $this->usu_nome = $usu_nome;
    }

    /**
     * @return string
     */
    public function getUsuLogin(): string
    {
        return $this->usu_login;
    }

    /**
     * @param  string $usu_login
     * @return void
     */
    public function setUsuLogin(string $usu_login): void
    {
        $this->usu_login = $usu_login;
    }

    /**
     * @return string
     */
    public function getUsuEmail(): string
    {
        return $this->usu_email;
    }

    /**
     * @param  string $usu_email
     * @return void
     */
    public function setUsuEmail(string $usu_email): void
    {
        $this->usu_email = $usu_email;
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
