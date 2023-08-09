<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores;

enum RegrasEnum
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
}
