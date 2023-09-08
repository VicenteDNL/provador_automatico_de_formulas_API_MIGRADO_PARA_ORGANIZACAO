<?php

namespace App\LogicLive\Managers\EstudoLivre;

use App\LogicLive\Common\Models\ModuloModel;

class EstudoLivreModulo
{
    private readonly string $mod_nome;
    private readonly string $mod_descricao;
    private readonly string $mod_ativo;

    public function __construct()
    {
        $this->mod_nome = 'Módulo de Estudo Livre';
        $this->mod_descricao = 'Módulo de estudo livre do método de árvore de refutação';
        $this->mod_ativo = 1;
    }

    /**
     * @return ModuloModel
     */
    public function getDefaulModels(): ModuloModel
    {
        return new ModuloModel([
            'mod_nome'      => $this->mod_nome,
            'mod_descricao' => $this->mod_descricao,
            'mod_ativo'     => $this->mod_ativo,
        ]);
    }
}
