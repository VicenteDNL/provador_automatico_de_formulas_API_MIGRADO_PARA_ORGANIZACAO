<?php

namespace App\Rules;

use Illuminate\Validation\Validator;

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

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $teste = '';
            },
        ];
    }
}
