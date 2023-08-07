<?php

namespace App\Http\Controllers\LogicLive\modulos;

use App\Http\Controllers\LogicLive\request\RequestDelete;
use App\Http\Controllers\LogicLive\request\RequestPost;
use App\Http\Controllers\LogicLive\request\RequestPut;

class Resposta
{
    private $post;

    public function __construct()
    {

        $this->post = new RequestPost;
        
    }

    public function enviarResposta($dados, $hash){
       return $this->post->httppost('respostas',$dados,$hash );
    }

}
