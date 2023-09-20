<?php

namespace App\Core\Common\Models\Enums;

use JsonSerializable;

enum RegrasEnum implements JsonSerializable
{
    case DUPLANEGACAO;
    case CONJUNCAO;
    case DISJUNCAONEGADA;
    case CONDICIONALNEGADA;
    case DISJUNCAO;
    case CONDICIONAL;
    case BICONDICIONAL;
    case CONJUNCAONEGADA;
    case BICONDICIONALNEGADA;

    public function descricao(): string
    {
        return match ($this) {
            RegrasEnum::DUPLANEGACAO        => 'Negação Negada',
            RegrasEnum::CONJUNCAO           => 'Conjunção',
            RegrasEnum::DISJUNCAONEGADA     => 'Disjunção Negada',
            RegrasEnum::CONDICIONALNEGADA   => 'Condicional Negado',
            RegrasEnum::DISJUNCAO           => 'Disjunção',
            RegrasEnum::CONDICIONAL         => 'Condicional',
            RegrasEnum::BICONDICIONAL       => 'Bicondicional',
            RegrasEnum::CONJUNCAONEGADA     => 'Conjunção Negada',
            RegrasEnum::BICONDICIONALNEGADA => 'Bicondicional Negado',
        };
    }

    public function jsonSerialize()
    {
        return match ($this) {
            RegrasEnum::DUPLANEGACAO        => 'DUPLANEGACAO',
            RegrasEnum::CONJUNCAO           => 'CONJUNCAO',
            RegrasEnum::DISJUNCAONEGADA     => 'DISJUNCAONEGADA',
            RegrasEnum::CONDICIONALNEGADA   => 'CONDICIONALNEGADA',
            RegrasEnum::DISJUNCAO           => 'DISJUNCAO',
            RegrasEnum::CONDICIONAL         => 'CONDICIONAL',
            RegrasEnum::BICONDICIONAL       => 'BICONDICIONAL',
            RegrasEnum::CONJUNCAONEGADA     => 'CONJUNCAONEGADA',
            RegrasEnum::BICONDICIONALNEGADA => 'BICONDICIONALNEGADA',
        };
    }
}
