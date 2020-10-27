<?php

namespace App\Http\Controllers\LogicLive\modulos\validacaoFormulas;


use App\Http\Controllers\LogicLive\request\RequestPost;

class ExercicioVF
{
    public function __construct()
    {
        $this->post = new RequestPost;
    }

    public function criarNivel($dados)
    {    
        return $this->post->httppost('exercicio',$dados);

    }
}
