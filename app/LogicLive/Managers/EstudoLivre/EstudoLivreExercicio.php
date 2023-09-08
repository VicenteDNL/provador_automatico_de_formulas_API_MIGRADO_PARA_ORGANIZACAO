<?php

namespace App\LogicLive\Managers\EstudoLivre;

use App\LogicLive\Common\Models\ExercicioModel;
use App\LogicLive\Common\Models\RecompensaModel;
use App\LogicLive\Config;

class EstudoLivreExercicio
{
    private readonly string $exe_nome;
    private readonly string $exe_descricao;
    private readonly string $exe_link;
    private readonly string $exe_ativo;

    public function __construct()
    {
        $config = new Config();
        $this->exe_nome = 'Estudo livre';
        $this->exe_descricao = 'Exercicio para estudo livre';
        $this->exe_link = $config->urlGame('exercicio/livre');
        $this->exe_ativo = 1;
    }

    /**
     * @return ExercicioModel[]
     */
    public function getDefaulModels(): array
    {
        return  [
            new ExercicioModel([
                'exe_nome'          => $this->exe_nome,
                'exe_descricao'     => $this->exe_descricao,
                'exe_link'          => $this->exe_link,
                'exe_ativo'         => $this->exe_ativo,
            ]),
        ];
    }

    /**
     * @return RecompensaModel[]
     */
    public function getRecompensasModels(): array
    {
        $rec = new EstudoLivreRecompensa();
        return  [
            $rec->getDefaulModels(),
        ];
    }
}
