<?php

namespace App\Rules;

class PassoDerivarRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'passo.idNoDerivacao'       => 'required|int',
            'passo.idsNoInsercoes'      => 'required|array',
            'passo.idsNoInsercoes.*'    => 'int',
            'passo.regra'               => 'required|string',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [

            'passo.idNoDerivacao.required'  => 'O campo passo.idNoDerivacao é obrigatório',
            'passo.idNoDerivacao.int'       => 'O campo passo.idNoDerivacao deve ser um inteiro positivo',
            'passo.idsNoInsercoes.required' => 'O campo passo.idsNoInsercoes é obrigatório',
            'passo.idsNoInsercoes.array'    => 'O campo passo.idsNoInsercoes deve ser uma lista',
            'passo.idsNoInsercoes.*.int'    => 'O campo passo.idsNoInsercoes deve ser uma lista de inteiro positivo',
            'passo.regra.required'          => 'O campo passo.regra é obrigatório',
            'passo.regra.string'            => 'O campo passo.regra deve ser uma string',
        ];
    }
}
