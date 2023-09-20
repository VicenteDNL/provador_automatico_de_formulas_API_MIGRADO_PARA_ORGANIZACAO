<?php

namespace App\Rules;

class PassoFecharRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'passo.idNoFolha'         => 'required|int',
            'passo.idNoContraditorio' => 'required|int',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [

            'passo.idNoFolha.required'              => 'O campo passo.idNoFolha é obrigatório',
            'passo.idNoFolha.int'                   => 'O campo passo.idNoFolha deve ser um inteiro positivo',
            'passo.idNoContraditorio.required'      => 'O campo passo.idNoContraditorio é obrigatório',
            'passo.idNoContraditorio.int'           => 'O campo passo.idNoContraditorio deve ser um inteiro positivo',
        ];
    }
}
