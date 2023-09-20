<?php

namespace App\LogicLive\Managers\EstudoLivre;

use App\LogicLive\Common\Models\RecompensaModel;

class EstudoLivreRecompensa
{
    private readonly string $rec_nome;
    private readonly string $rec_pontuacao;
    private readonly string $rec_imagem;

    public function __construct()
    {
        $this->rec_nome = 'Recompensa do modulo livre';
        $this->rec_pontuacao = 0;
        $this->rec_imagem = 'vazio';
    }

    /**
     * @return RecompensaModel
     */
    public function getDefaulModels(): RecompensaModel
    {
        return new RecompensaModel([
            'rec_nome'       => $this->rec_nome,
            'rec_pontuacao'  => $this->rec_pontuacao,
            'rec_imagem'     => $this->rec_imagem,
        ]);
    }
}
