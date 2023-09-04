<?php

namespace App\Rules;

class ArvoreRule
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'arvore.visualizacao.nos'            => 'nullable|array',
            'arvore.visualizacao.arestas'        => 'nullable|array',
            'arvore.derivacao.regras'            => 'nullable|array',
            'arvore.derivacao.passosExcutados'   => 'array',
            'arvore.fechar.passosExcutados'      => 'array',
            'arvore.fechar.fechamentoAutomatico' => 'nullable|boolean',
            'arvore.inicio.isCompleto'           => 'nullable|boolean',
            'arvore.inicio.passosExcutados'      => 'array',
            'arvore.inicio.opcoes'               => 'nullable|array',
            'arvore.ticar.ticagemAutomatica'     => 'nullable|boolean',
            'arvore.ticar.passosExcutados'       => 'array',
            'arvore.formula.xml'                 => 'required|string',
            'arvore.formula.strformula'          => 'nullable|string',
            'canvas.width'                       => 'nullable|numeric',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'canvas.width.numeric'     => 'O campo width deve ser numérico',
        ];
    }
}
