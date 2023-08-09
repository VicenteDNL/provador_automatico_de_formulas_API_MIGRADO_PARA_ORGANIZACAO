<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula;

enum PredicadoTipoEnum
{
    case CONDICIONAL;
    case BICONDICIONAL;
    case DISJUNCAO;
    case CONJUNCAO;
    case PREDICATIVO;

    public function symbol(): string
    {
        return match ($this) {
            PredicadoTipoEnum::CONDICIONAL      => '->',
            PredicadoTipoEnum::BICONDICIONAL    => '<->',
            PredicadoTipoEnum::DISJUNCAO        => 'v',
            PredicadoTipoEnum::CONJUNCAO        => '^',
            PredicadoTipoEnum::PREDICATIVO      => '',
        };
    }
}
