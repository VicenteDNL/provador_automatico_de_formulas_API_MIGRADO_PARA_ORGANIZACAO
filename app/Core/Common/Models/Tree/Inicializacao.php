<?php

namespace App\Core\Common\Models\Tree;

use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Serialization\Serializa;

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
    protected bool $isCompleto;

    public function __construct()
    {
        $this->opcoesDisponiveis = [];
        $this->passosExecutados = [];
        $this->isCompleto = false;
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
    public function isCompleto(): bool
    {
        return $this->isCompleto;
    }

    /**
     * @param bool $isCompleto
     *@return void
     */
    public function setCompleto(bool $isCompleto): void
    {
        $this->isCompleto = $isCompleto;
    }
}
