<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores;

enum RegrasEnum: string
{
    case DUPLANEGACAO = 'Negação Negada';
    case CONJUNCAO = 'Conjunção';
    case DISJUNCAONEGADA = 'Disjunção Negada';
    case CONDICIONALNEGADA = 'Condicional Negado';
    case DISJUNCAO = 'Disjunção';
    case CONDICIONAL = 'Condicional';
    case BICONDICIONAL = 'Bicondicional';
    case CONJUNCAONEGADA = 'Conjunção Negada';
    case BICONDICIONALNEGADA = 'Bicondicional Negado';

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

    // /**
    //  * @param  RegrasEnum[] $filters
    //  * @return RegrasEnum[]
    //  */
    // public function toArrayfilter(array $filters = []): array
    // {
    //     $fil = [];

    //     foreach ($filters as $filter) {
    //         $fil[$filter] = match ($filter) {
    //             RegrasEnum::DUPLANEGACAO        => 'Negação Negada',
    //             RegrasEnum::CONJUNCAO           => 'Conjunção',
    //             RegrasEnum::DISJUNCAONEGADA     => 'Disjunção Negada',
    //             RegrasEnum::CONDICIONALNEGADA   => 'Condicional Negado',
    //             RegrasEnum::DISJUNCAO           => 'Disjunção',
    //             RegrasEnum::CONDICIONAL         => 'Condicional',
    //             RegrasEnum::BICONDICIONAL       => 'Bicondicional',
    //             RegrasEnum::CONJUNCAONEGADA     => 'Conjunção Negada',
    //             RegrasEnum::BICONDICIONALNEGADA => 'Bicondicional Negado',
    //         };
    //     }
    //     return $fil;
    // }
}
