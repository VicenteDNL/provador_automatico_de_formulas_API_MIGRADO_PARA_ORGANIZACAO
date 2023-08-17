<?php

namespace App\Http\Controllers\LogicLive\Modulos\ValidacaoFormulas;

use App\Http\Controllers\LogicLive\Request\RequestDelete;
use App\Http\Controllers\LogicLive\Request\RequestPost;
use App\Http\Controllers\LogicLive\Request\RequestPut;

class NivelVF
{
    private $post;
    private $put;
    private $delete;

    public function __construct()
    {
        $this->post = new RequestPost();
        $this->put = new RequestPut();
        $this->delete = new RequestDelete();
    }

    public function criarNivel($dados)
    {
        return $this->post->httppost('nivel', $dados);
    }

    public function atualizarNivel($id, $dados)
    {
        return $this->put->httpput('nivel/', $dados, $id);
    }

    public function deletarNivel($id)
    {
        return $this->delete->httpdelete('nivel/', $id);
    }
}
