<?php

namespace App\LogicLive\Managers\EstudoConceitos;

use App\LogicLive\Common\Models\ExercicioModel;
use App\LogicLive\Common\Models\RecompensaModel;
use App\LogicLive\Config;

class EstudoConceitosExercicio
{
    private readonly string $exe_nome_01;
    private readonly string $exe_descricao_01;
    private readonly string $exe_link_01;
    private readonly string $exe_ativo_01;
    private readonly string $exe_nome_02;
    private readonly string $exe_descricao_02;
    private readonly string $exe_link_02;
    private readonly string $exe_ativo_02;

    public function __construct()
    {
        $config = new Config();
        $this->exe_nome_01 = 'Árvore de Refutação';
        $this->exe_descricao_01 = 'Exercicio para estudo dos conceitos';
        $this->exe_link_01 = $config->urlGame('exercicio/conceitos/arvore');
        $this->exe_ativo_01 = 1;

        $this->exe_nome_02 = 'Regras da Árvore de Refutação';
        $this->exe_descricao_02 = 'Exercicio para estudo dos conceitos';
        $this->exe_link_02 = $config->urlGame('exercicio/conceitos/regras');
        $this->exe_ativo_02 = 1;
    }

    /**
     * @return ExercicioModel[]
     */
    public function getDefaulModels(): array
    {
        return [
            new ExercicioModel([
                'exe_nome'          => $this->exe_nome_01,
                'exe_descricao'     => $this->exe_descricao_01,
                'exe_link'          => $this->exe_link_01,
                'exe_ativo'         => $this->exe_ativo_01,
            ]),
            new ExercicioModel([
                'exe_nome'          => $this->exe_nome_02,
                'exe_descricao'     => $this->exe_descricao_02,
                'exe_link'          => $this->exe_link_02,
                'exe_ativo'         => $this->exe_ativo_02,
            ]),
        ];
    }

    /**
     * @return RecompensaModel[]
     */
    public function getRecompensasModels(): array
    {
        $rec = new EstudoConceitosRecompensa();
        return  [
            $rec->getDefaulModels(),
            $rec->getDefaulModels(),
        ];
    }
}
