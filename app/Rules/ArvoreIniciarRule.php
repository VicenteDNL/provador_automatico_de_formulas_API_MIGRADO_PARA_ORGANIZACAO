<?php

namespace App\Rules;

class ArvoreIniciarRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'xml'             => 'required|string',
            'canvas.width'    => 'nullable|numeric',
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
        ];
    }
}
