<?php

namespace App\Http\Requests\API\Aluno\EstudoLivre;

use Illuminate\Foundation\Http\FormRequest;

class EstudoLivreAdicionaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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

            'passo.idNo'    => 'required|string',
            'passo.negacao' => 'required|boolean',

            'canvas.width' => 'nullable|numeric',

        ];
    }

    public function messages()
    {
        return [

            'canvas.width.numeric'  => 'O campo deve ser numérico',
        ];
    }
}
