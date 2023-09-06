<?php

namespace App\LogicLive\Modulos;

use App\LogicLive\Request\RequestGet;

class Jogador
{
    private $get;

    public function __construct()
    {
        $this->get = new RequestGet();
    }

    public function getJogador($hash)
    {
        return $this->get->httpget('jogador', '', $hash);
    }
}
