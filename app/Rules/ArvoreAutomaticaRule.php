<?php

namespace App\Rules;

class ArvoreAutomaticaRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'xml'             => 'required|string',
            'canvas.width'    => 'nullable|numeric',
            'exibirLinhas'    => 'nullable|boolean',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'xml.required'             => 'O campo xml é obrigatório',
            'xml.string'               => 'O campo xml deve ser uma string',
            'canvas.width.numeric'     => 'O campo width deve ser numérico',
            'exibirLinhas.boolean'     => 'O campo exibirLinhas deve ser booleano',

        ];
    }
}
