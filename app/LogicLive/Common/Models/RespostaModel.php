<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class RespostaModel extends Serializa
{
    protected bool $status;
    protected string $mensagem;

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param  bool $status
     * @return void
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMensagem(): string
    {
        return $this->mensagem;
    }

    /**
     * @param  string $mensagem
     * @return void
     */
    public function setMensagem(string $mensagem): void
    {
        $this->mensagem = $mensagem;
    }
}
