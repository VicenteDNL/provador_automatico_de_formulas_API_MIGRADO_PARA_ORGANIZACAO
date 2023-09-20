<?php

namespace App\Http\Requests\API\Admin\Exercicio;

use Illuminate\Foundation\Http\FormRequest;

class ExercicioUpdateRequest extends FormRequest
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
            'ativo'         => 'nullable|boolean',
            'descricao'     => 'nullable|string',
            'enunciado'     => 'nullable|string',
            'nome'          => 'nullable|string',

            'nivel_id'      => 'nullable|integer|exists:niveis,id',
            'recompensa_id' => 'nullable|integer|exists:recompensas,id',
            'nome'          => 'nullable|string',

            'formula.formula'                => 'nullable|string',
            'formula.inicio_personalizado'   => 'nullable|boolean',
            'formula.ticar_automaticamente'  => 'nullable|boolean',
            'formula.fechar_automaticamente' => 'nullable|boolean',
            'formula.quantidade_regras'      => 'nullable|integer',
            'formula.xml'                    => 'nullable|string',

            'formula.lista_derivacoes'                    => 'nullable|array',
            'formula.lista_derivacoes.*.idsNoInsercoes'   => 'nullable|array',
            'formula.lista_derivacoes.*.idsNoInsercoes.*' => 'nullable|integer',
            'formula.lista_derivacoes.*.idNoDerivacao'    => 'nullable|integer',
            'formula.lista_derivacoes.*.regra'            => 'nullable|string',

            'formula.lista_fechamento'                     => 'nullable|array',
            'formula.lista_fechamento.*.idNoFolha'         => 'nullable|integer',
            'formula.lista_fechamento.*.idNoContraditorio' => 'nullable|integer',

            'formula.lista_ticagem'        => 'nullable|array',
            'formula.lista_ticagem.*.idNo' => 'nullable|integer',

            'formula.lista_passos'           => 'nullable|array',
            'formula.lista_passos.*.idNo'    => 'nullable|string',
            'formula.lista_passos.*.negacao' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ativo.boolean'          => 'O campo ativo deve ser booleano',

            'descricao.string'       => 'O campo descricao dever ser um texto',

            'enunciado.string'       => 'O campo enunciado dever ser um texto',

            'nome.string'            => 'O campo nome dever ser um texto',

            'nivel_id.integer'       => 'O campo nivel_id dever ser um inteiro positivo',
            'nivel_id.exists'        => 'O valor nivel_id não foi encontrado',

            'recompensa_id.integer'  => 'O campo recompensa_id dever ser um inteiro positivo',
            'recompensa_id.exists'   => 'O valor recompensa_id não foi encontrado',

            'nome.string'            => 'O campo nome dever ser um texto',

            'formula.formula.string'                  => 'O campo formula dever ser um texto',

            'formula.inicio_personalizado.boolean'    => 'O campo formula.inicio_personalizado deve ser booleano',

            'formula.ticar_automaticamente.boolean'   => 'O campo formula.ticar_automaticamente deve ser booleano',

            'formula.fechar_automaticamente.boolean'  => 'O campo formula.fechar_automaticamente deve ser booleano',

            'formula.quantidade_regras.integer'       => 'O campo formula.quantidade_regras dever ser um inteiro positivo',

            'formula.xml.string'                      => 'O campo xml dever ser um texto',

            'formula.lista_derivacoes.array'                       => 'O campo formula.lista_derivacoes deve ser uma lista',

            'formula.lista_derivacoes.*.idsNoInsercoes.array'      => 'O campo formula.lista_derivacoes.*.idsNoInsercoes deve ser uma lista',

            'formula.lista_derivacoes.*.idsNoInsercoes.*.integer' => 'O campo formula.lista_derivacoes.*.idsNoInsercoes.* dever ser um inteiro positivo',

            'formula.lista_derivacoes.*.idNoDerivacao.integer'     => 'O campo formula.lista_derivacoes.*.idNoDerivacao dever ser um inteiro positivo',

            'formula.lista_derivacoes.*.regra.string'              => 'O campo regra dever ser um texto',

            'formula.lista_fechamento.array'                        => 'O campo formula.lista_fechamento deve ser uma lista',

            'formula.lista_fechamento.*.idNoFolha.integer'          => 'O campo formula.lista_fechamento.*.idNoFolha dever ser um inteiro positivo',

            'formula.lista_fechamento.*.idNoContraditorio.integer'  => 'O campo formula.lista_fechamento.*.idNoContraditorio dever ser um inteiro positivo',

            'formula.lista_ticagem.array'           => 'O campo formula.lista_ticagem deve ser uma lista',

            'formula.lista_ticagem.*.idNo.integer'  => 'O campo formula.lista_ticagem.*.idNo dever ser um inteiro positivo',

            'formula.lista_passos.array'              => 'O campo formula.lista_passos deve ser uma lista',

            'formula.lista_passos.*.idNo.string'      => 'O campo idNo dever ser um texto',

            'formula.lista_passos.*.negacao.boolean'  => 'O campo formula.lista_passos.*.negacao deve ser booleano',
        ];
    }
}
