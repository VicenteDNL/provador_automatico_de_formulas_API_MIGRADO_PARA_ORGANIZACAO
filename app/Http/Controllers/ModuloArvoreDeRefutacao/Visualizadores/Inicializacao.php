<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\OpcaoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class Inicializacao extends Serializa
{
    /**
     * Nos disponiveis para insercao na arvore
     *  @var OpcaoInicializacao[]
     */
    protected array $opcoesDisponiveis;

    /**
     * Nos disponiveis para insercao na arvore
     *  @var PassoInicializacao[]
     */
    protected array $passosExecutados;
    protected bool $finalizado;

    public function __construct()
    {
        $this->opcoesDisponiveis = [];
        $this->passosExecutados = [];
        $this->finalizado = false;
    }

    /**
     * @return OpcaoInicializacao[]
     */
    public function getOpcoesDisponiveis(): array
    {
        return $this->opcoesDisponiveis;
    }

    /**
     *
     * @param OpcaoInicializacao[] $opcoesDisponiveis
     */
    public function setOpcoesDisponiveis(array $opcoesDisponiveis): void
    {
        $this->opcoesDisponiveis = $opcoesDisponiveis;
    }

    /**
     * @return PassoInicializacao[]
     */
    public function getPassosExecutados(): array
    {
        return $this->passosExecutados;
    }

    /**
     * @param PassoInicializacao[] $passos
     */
    public function setPassosExecutados(array $passos)
    {
        parent::arrayToObject($passos, PassoInicializacao::class);
        $this->passosExecutados = $passos;
    }

    /**
     *@return bool
     */
    public function isFinalizado(): bool
    {
        return $this->finalizado;
    }

    /**
     * @param bool
     * @param bool $finalizado
     *@return void
     */
    public function setFinalizado(bool $finalizado): void
    {
        $this->finalizado = $finalizado;
    }
}
