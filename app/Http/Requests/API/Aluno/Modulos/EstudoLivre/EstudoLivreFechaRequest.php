<?php

namespace App\Http\Requests\API\Aluno\Modulos\EstudoLivre;

use Illuminate\Foundation\Http\FormRequest;

class EstudoLivreFechaRequest extends FormRequest
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

            'passo.idNoFolha'         => 'required|numeric',
            'passo.idNoContraditorio' => 'required|numeric',
            'canvas.width'            => 'nullable|numeric',

        ];
    }

    public function messages()
    {
        return [
            'passo.idNoFolha.required'          => 'O campo idNoFolha é obrigatório',
            'passo.idNoContraditorio.required'  => 'O campo idNoContraditorio é obrigatório',
            'passo.idNoFolha.numeric'           => 'O campo idNoFolha deve ser numérico',
            'passo.idNoContraditorio.numeric'   => 'O campo idNoContraditorio deve ser numérico',
            'passo.idNo.numeric'                => 'O campo idNo deve ser numérico',
            'canvas.width.numeric'              => 'O campo deve ser numérico',
        ];
    }
}
