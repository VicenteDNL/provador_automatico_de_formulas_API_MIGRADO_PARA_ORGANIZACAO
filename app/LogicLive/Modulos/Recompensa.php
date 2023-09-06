<?php

namespace App\LogicLive\Modulos;

use App\LogicLive\Request\RequestDelete;
use App\LogicLive\Request\RequestPost;
use App\LogicLive\Request\RequestPut;

class Recompensa
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

    public function criarRecompensa($dados)
    {
        return $this->post->httppost('recompensa', $dados);
    }

    public function atualizarRecompensa($id, $dados)
    {
        return $this->delete->httpdelete('recompensa', $dados, $id);
    }

    public function deletarRecompensa($id)
    {
        return $this->put->httpput('recompensa', $id);
    }
}
