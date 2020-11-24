<?php

namespace App\Http\Controllers\LogicLive\modulos\validacaoFormulas;

use App\Http\Controllers\LogicLive\request\RequestDelete;
use App\Http\Controllers\LogicLive\request\RequestPost;
use App\Http\Controllers\LogicLive\request\RequestPut;

class ExercicioVF
{
    public function __construct()
    {
        $this->post = new RequestPost;
        $this->delete = new RequestDelete;
        $this->put = new RequestPut;
    }

    public function criarExercicio($dados)
    {    
        return $this->post->httppost('exercicio',$dados);

    }

    public function deletarExercicio($id)
    {
        return $this->delete->httpdelete('exercicio/',$id);
    }

    public function atualizarExercicio($id,$dados)
    {
        return $this->put->httpput('exercicio/',$dados,$id);
    }
    
    
}
