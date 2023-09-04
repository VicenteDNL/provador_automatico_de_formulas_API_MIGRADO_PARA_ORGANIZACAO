<?php

namespace App\Rules;

class PassoTicarRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'passo.idNo'    => 'required|int',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [

            'passo.idNo.required'  => 'O campo passo.idNo é obrigatório',
            'passo.idNo.int'       => 'O campo passo.idNo deve ser um inteiro positivo',
        ];
    }
}
