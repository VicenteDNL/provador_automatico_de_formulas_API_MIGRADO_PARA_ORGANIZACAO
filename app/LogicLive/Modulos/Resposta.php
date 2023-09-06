<?php

namespace App\LogicLive\Modulos;

use App\LogicLive\Request\RequestPost;

class Resposta
{
    private $post;

    public function __construct()
    {
        $this->post = new RequestPost();
    }

    public function enviarResposta($dados, $hash)
    {
        return $this->post->httppost('respostas', $dados, $hash);
    }
}
