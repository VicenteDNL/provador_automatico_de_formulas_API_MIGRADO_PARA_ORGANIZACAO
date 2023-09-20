<?php

namespace App\Rules;

class PassoAdicionarRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'passo.idNo'    => 'required|string',
            'passo.negacao' => 'required|boolean',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'passo.idNo.required'    => 'O campo passo.idNo é obrigátorio',
            'passo.idNo.string'      => 'O campo passo.idNo dever ser um texto',
            'passo.negacao.required' => 'O campo passo.negacao é obrigátorio',
            'passo.negacao.boolean'  => 'O campo passo.negacao dever ser um boleano',
        ];
    }
}
