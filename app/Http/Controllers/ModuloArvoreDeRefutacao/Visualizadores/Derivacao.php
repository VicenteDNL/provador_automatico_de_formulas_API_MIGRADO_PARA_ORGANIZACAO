<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;

class Derivacao
{
    /** @var PassoDerivacao[] */
    protected array $passosExecutados;

    public function __construct()
    {
        $this->passosExecutados = [];
    }

    /**
     *
     * @param  array $lista
     * @return void
     */
    public function setPassosExecutados(array $lista): void
    {
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
