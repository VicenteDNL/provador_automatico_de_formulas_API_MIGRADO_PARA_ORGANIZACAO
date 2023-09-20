<?php

namespace App\LogicLive\Managers\EstudoLivre;

use App\LogicLive\Common\Models\NivelModel;

class EstudoLivreNivel
{
    private readonly string $mod_codigo;
    private readonly string $niv_nome;
    private readonly string $niv_descricao;
    private readonly string $niv_ativo;

    public function __construct()
    {
        $this->niv_nome = 'Estudo Livre';
        $this->niv_descricao = 'Nivel do estudo livre';
        $this->niv_ativo = 1;
    }

    /**
     * @return NivelModel[]
     */
    public function getDefaulModels(): array
    {
        return [
            new NivelModel([
                'niv_nome'          => $this->niv_nome,
                'niv_descricao'     => $this->niv_descricao,
                'niv_ativo'         => $this->niv_ativo,
            ]),
        ];
    }
}
