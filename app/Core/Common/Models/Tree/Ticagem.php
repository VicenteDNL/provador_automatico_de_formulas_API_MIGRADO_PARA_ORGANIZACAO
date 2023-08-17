<?php

namespace App\Core\Common\Models\Tree;

use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Common\Serialization\Serializa;

class Ticagem extends Serializa
{
    /** @var PassoTicagem[] */
    protected array $passosExecutados;
    protected bool $isAutomatico = false;

    public function __construct()
    {
        $this->passosExecutados = [];
    }

    /**
     *
     * @param  PassoTicagem[] $lista
     * @return void
     */
    public function setPassosExecutados(array $lista): void
    {
        parent::arrayToObject($lista, PassoTicagem::class);
        $this->passosExecutados = $lista;
    }

    /**
     * @return PassoTicagem[]
     */
    public function getPassosExecutados(): array
    {
        return $this->passosExecutados;
    }

    /**
     * @param  PassoTicagem $passo
     * @return void
     */
    public function addPasso(PassoTicagem $passo): void
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
