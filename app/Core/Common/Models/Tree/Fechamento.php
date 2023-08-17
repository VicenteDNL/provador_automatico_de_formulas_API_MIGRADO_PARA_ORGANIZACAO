<?php

namespace App\Core\Common\Models\Tree;

use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Serialization\Serializa;

class Fechamento extends Serializa
{
    /** @var PassoFechamento[] */
    protected array $passosExecutados;
    protected bool $isAutomatico = false;

    public function __construct()
    {
        $this->passosExecutados = [];
    }

    /**
     *
     * @param  PassoFechamento[] $lista
     * @return void
     */
    public function setPassosExecutados(array $lista): void
    {
        parent::arrayToObject($lista, PassoFechamento::class);
        $this->passosExecutados = $lista;
    }

    /**
     * @return PassoFechamento[]
     */
    public function getPassosExecutados(): array
    {
        return $this->passosExecutados;
    }

    /**
     * @param  PassoFechamento $passo
     * @return void
     */
    public function addPasso(PassoFechamento $passo): void
    {
        array_push($this->passosExecutados, $passo);
    }

    /**
     *@return bool
     */
    public function isAutomatico(): bool
    {
        return $this->isAutomatico;
    }

    /**
     * @param bool $isAutomatico
     *@return void
     */
    public function setAutomatico(bool $isAutomatico): void
    {
        $this->isAutomatico = $isAutomatico;
    }
}
