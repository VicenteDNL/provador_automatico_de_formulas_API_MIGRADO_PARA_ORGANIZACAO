<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores;

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
            PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::,
            PredicadoTipoEnum::BICONDICIONAL    => '<->',
            PredicadoTipoEnum::DISJUNCAO        => 'v',
            PredicadoTipoEnum::CONJUNCAO        => '^',
            PredicadoTipoEnum::PREDICATIVO      => '',
        };
    }
    public function regra(int $qntNegado): RegrasEnum
    {
        switch($qntNegado){
            case 0:
                return match ($this) {
                    PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::CONDICIONAL,
                    PredicadoTipoEnum::BICONDICIONAL    => RegrasEnum::BICONDICIONAL,
                    PredicadoTipoEnum::DISJUNCAO        => RegrasEnum::DISJUNCAO,
                    PredicadoTipoEnum::CONJUNCAO        => RegrasEnum::CONJUNCAO,
                };
            case 1:
                return match ($this) {
                    PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::CONDICIONALNEGADA,
                    PredicadoTipoEnum::BICONDICIONAL    => RegrasEnum::BICONDICIONALNEGADA,
                    PredicadoTipoEnum::DISJUNCAO        => RegrasEnum::DISJUNCAONEGADA,
                    PredicadoTipoEnum::CONJUNCAO        => RegrasEnum::CONJUNCAONEGADA,
                };
            default :
                return match ($this) {
                    PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::DUPLANEGACAO,
                    PredicadoTipoEnum::BICONDICIONAL    => RegrasEnum::DUPLANEGACAO,
                    PredicadoTipoEnum::DISJUNCAO        => RegrasEnum::DUPLANEGACAO,
                    PredicadoTipoEnum::CONJUNCAO        => RegrasEnum::DUPLANEGACAO,
                    PredicadoTipoEnum::PREDICATIVO      => RegrasEnum::DUPLANEGACAO,
                };
        }

    }

}
