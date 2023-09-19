<?php

namespace App\Rules;

class PassoFinalizarRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'passo.resposta'    => 'required|string',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [

            'passo.resposta.required'     => 'O campo passo.resposta é obrigatório',
            'passo.resposta.string'       => 'O campo passo.resposta dever ser um texto',
        ];
    }
}
