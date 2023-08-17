<?php

namespace App\Core\Common\Models\Enums;

enum PredicadoTipoEnum
{
    case CONDICIONAL;
    case BICONDICIONAL;
    case DISJUNCAO;
    case CONJUNCAO;
    case PREDICATIVO;

    public function simbolo(): string
    {
        return match ($this) {
            PredicadoTipoEnum::CONDICIONAL      => '->',
            PredicadoTipoEnum::BICONDICIONAL    => '<->',
            PredicadoTipoEnum::DISJUNCAO        => 'v',
            PredicadoTipoEnum::CONJUNCAO        => '^',
            PredicadoTipoEnum::PREDICATIVO      => '',
        };
    }

    public function regra(int $qntNegado): ?RegrasEnum
    {
        switch($qntNegado) {
            case 0:
                return match ($this) {
                    PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::CONDICIONAL,
                    PredicadoTipoEnum::BICONDICIONAL    => RegrasEnum::BICONDICIONAL,
                    PredicadoTipoEnum::DISJUNCAO        => RegrasEnum::DISJUNCAO,
                    PredicadoTipoEnum::CONJUNCAO        => RegrasEnum::CONJUNCAO,
                    PredicadoTipoEnum::PREDICATIVO      => null
                };
            case 1:
                return match ($this) {
                    PredicadoTipoEnum::CONDICIONAL      => RegrasEnum::CONDICIONALNEGADA,
                    PredicadoTipoEnum::BICONDICIONAL    => RegrasEnum::BICONDICIONALNEGADA,
                    PredicadoTipoEnum::DISJUNCAO        => RegrasEnum::DISJUNCAONEGADA,
                    PredicadoTipoEnum::CONJUNCAO        => RegrasEnum::CONJUNCAONEGADA,
                    PredicadoTipoEnum::PREDICATIVO      => null
                };
            default:
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
