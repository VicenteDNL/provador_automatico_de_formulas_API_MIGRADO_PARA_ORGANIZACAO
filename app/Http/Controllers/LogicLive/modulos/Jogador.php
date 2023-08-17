<?php

namespace App\Http\Controllers\LogicLive\Modulos;

use App\Http\Controllers\LogicLive\Request\RequestGet;

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
