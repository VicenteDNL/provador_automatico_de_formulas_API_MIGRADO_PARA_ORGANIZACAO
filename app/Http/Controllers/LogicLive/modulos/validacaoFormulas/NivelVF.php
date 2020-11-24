<?php


namespace App\Http\Controllers\LogicLive\modulos\validacaoFormulas;

use App\Http\Controllers\LogicLive\request\RequestDelete;
use App\Http\Controllers\LogicLive\request\RequestPost;
use App\Http\Controllers\LogicLive\request\RequestPut;

class NivelVF
{

    public function __construct()
    {
        $this->post = new RequestPost;
        $this->put = new RequestPut;
        $this->delete = new RequestDelete;
    }

    public function criarNivel($dados)
    {     
        return $this->post->httppost('nivel',$dados);
    }

    public function atualizarNivel($id,$dados)
    {
        return $this->put->httpput('nivel/',$dados,$id);
    }

    public function deletarNivel($id)
    {
        return $this->delete->httpdelete('nivel/',$id);
    }
}
