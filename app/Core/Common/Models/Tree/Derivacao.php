<?php

namespace App\Core\Common\Models\Tree;

use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Serialization\Serializa;

class Derivacao extends Serializa
{
    /** @var PassoDerivacao[] */
    protected array $passosExecutados;

    public function __construct()
    {
        $this->passosExecutados = [];
    }

    /**
     *
     * @param  PassoDerivacao[] $lista
     * @return void
     */
    public function setPassosExecutados(array $lista): void
    {
        parent::arrayToObject($lista, PassoDerivacao::class);
        $this->passosExecutados = $lista;
    }

    /**
     * @return PassoDerivacao[]
     */
    public function getPassosExecutados(): array
    {
        return $this->passosExecutados;
    }

    /**
     * @param  PassoDerivacao $passo
     * @return void
     */
    public function addPasso(PassoDerivacao $passo): void
    {
        array_push($this->passosExecutados, $passo);
    }
}
