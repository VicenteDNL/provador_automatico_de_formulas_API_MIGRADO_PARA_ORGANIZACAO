<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class Formula extends Serializa
{
    /** @var Premissa[] */
    protected array $premissas;
    protected Conclusao $conclusao;

    /**
     * @return Premissa[]
     */
    public function getPremissas(): array
    {
        return $this->premissas;
    }

    /**
     * @param  Premissa[] $premissas
     * @return void
     */
    public function setPremissas(array $premissas): void
    {
        $this->premissas = $premissas;
    }

    /**
     * @param  Premissa $premissa
     * @return void
     */
    public function addPremissa(Premissa $premissa): void
    {
        array_push($this->premissas, $premissa);
    }

    /**
     * @return Conclusao
     */
    public function getConclusao(): Conclusao
    {
        return $this->conclusao;
    }

    /**
     * @param  Conclusao $premissas
     * @param  Conclusao $conclusao
     * @return void
     */
    public function setConclusao(Conclusao $conclusao): void
    {
        $this->conclusao = $conclusao;
    }
}
