<?php

namespace App\LogicLive\Managers;

use App\LogicLive\Common\Models\GameModel;

class ArvoreRefutacaoGame
{
    private readonly string $gam_nome;
    private readonly string $gam_descricao;
    private readonly string $gam_ativo;

    public function __construct()
    {
        $this->gam_nome = 'Árvore de Refutação';
        $this->gam_descricao = 'Módulo de validação de fórmulas da lógica proposicional através do método de Árvore de Refutação';
        $this->gam_ativo = 1;
    }

    /**
     * @return GameModel
     */
    public function getDefaulModels(): GameModel
    {
        return new GameModel([
            'gam_nome'      => $this->gam_nome,
            'gam_descricao' => $this->gam_descricao,
            'gam_ativo'     => $this->gam_ativo,
        ]);
    }
}
