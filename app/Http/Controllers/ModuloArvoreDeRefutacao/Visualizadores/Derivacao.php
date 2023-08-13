<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

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
