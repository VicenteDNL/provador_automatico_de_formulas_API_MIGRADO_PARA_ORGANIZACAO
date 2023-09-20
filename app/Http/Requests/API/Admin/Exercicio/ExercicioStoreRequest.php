<?php

namespace App\Http\Requests\API\Admin\Exercicio;

use Illuminate\Foundation\Http\FormRequest;

class ExercicioStoreRequest extends FormRequest
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
            'ativo'         => 'required|boolean',
            'descricao'     => 'required|string',
            'enunciado'     => 'required|string',
            'nome'          => 'required|string',

            'nivel_id'      => 'required|integer|exists:niveis,id',
            'recompensa_id' => 'required|integer|exists:recompensas,id',
            'nome'          => 'required|string',

            'formula.formula'                => 'required|string',
            'formula.inicio_personalizado'   => 'required|boolean',
            'formula.ticar_automaticamente'  => 'required|boolean',
            'formula.fechar_automaticamente' => 'required|boolean',
            'formula.quantidade_regras'      => 'required|integer',
            'formula.xml'                    => 'required|string',

            'formula.lista_derivacoes'                    => 'nullable|array',
            'formula.lista_derivacoes.*.idsNoInsercoes'   => 'required|array',
            'formula.lista_derivacoes.*.idsNoInsercoes.*' => 'required|integer',
            'formula.lista_derivacoes.*.idNoDerivacao'    => 'required|integer',
            'formula.lista_derivacoes.*.regra'            => 'required|string',

            'formula.lista_fechamento'                     => 'nullable|array',
            'formula.lista_fechamento.*.idNoFolha'         => 'required|integer',
            'formula.lista_fechamento.*.idNoContraditorio' => 'required|integer',

            'formula.lista_ticagem'        => 'nullable|array',
            'formula.lista_ticagem.*.idNo' => 'required|integer',

            'formula.lista_passos'           => 'nullable|array',
            'formula.lista_passos.*.idNo'    => 'required|string',
            'formula.lista_passos.*.negacao' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ativo.required'         => 'O campo ativo é obrigátorio',
            'ativo.boolean'          => 'O campo ativo deve ser booleano',

            'descricao.required'     => 'O campo descricao é obrigátorio',
            'descricao.string'       => 'O campo descricao dever ser um texto',

            'enunciado.required'     => 'O campo enunciado é obrigátorio',
            'enunciado.string'       => 'O campo enunciado dever ser um texto',

            'nome.required'          => 'O campo nome é obrigátorio',
            'nome.string'            => 'O campo nome dever ser um texto',

            'nivel_id.required'      => 'O campo nivel_id é obrigátorio',
            'nivel_id.integer'       => 'O campo nivel_id dever ser um inteiro positivo',
            'nivel_id.exists'        => 'O valor nivel_id não foi encontrado',

            'recompensa_id.required' => 'O campo recompensa_id é obrigátorio',
            'recompensa_id.integer'  => 'O campo recompensa_id dever ser um inteiro positivo',
            'recompensa_id.exists'   => 'O valor recompensa_id não foi encontrado',

            'nome.required'          => 'O campo nome é obrigátorio',
            'nome.string'            => 'O campo nome dever ser um texto',

            'formula.formula.required'                => 'O campo formula.formula é obrigátorio',
            'formula.formula.string'                  => 'O campo formula dever ser um texto',

            'formula.inicio_personalizado.required'   => 'O campo formula.inicio_personalizado é obrigátorio',
            'formula.inicio_personalizado.boolean'    => 'O campo formula.inicio_personalizado deve ser booleano',

            'formula.ticar_automaticamente.required'  => 'O campo formula.ticar_automaticamente é obrigátorio',
            'formula.ticar_automaticamente.boolean'   => 'O campo formula.ticar_automaticamente deve ser booleano',

            'formula.fechar_automaticamente.required' => 'O campo formula.fechar_automaticamente é obrigátorio',
            'formula.fechar_automaticamente.boolean'  => 'O campo formula.fechar_automaticamente deve ser booleano',

            'formula.quantidade_regras.required'      => 'O campo formula.quantidade_regras é obrigátorio',
            'formula.quantidade_regras.integer'       => 'O campo formula.quantidade_regras dever ser um inteiro positivo',

            'formula.xml.required'                    => 'O campo formula.xml é obrigátorio',
            'formula.xml.string'                      => 'O campo xml dever ser um texto',

            'formula.lista_derivacoes.required'                    => 'O campo formula.lista_derivacoes é obrigátorio',
            'formula.lista_derivacoes.array'                       => 'O campo formula.lista_derivacoes deve ser uma lista',

            'formula.lista_derivacoes.*.idsNoInsercoes.required'   => 'O campo formula.lista_derivacoes.*.idsNoInsercoes é obrigátorio',
            'formula.lista_derivacoes.*.idsNoInsercoes.array'      => 'O campo formula.lista_derivacoes.*.idsNoInsercoes deve ser uma lista',

            'formula.lista_derivacoes.*.idsNoInsercoes.*.integer' => 'O campo formula.lista_derivacoes.*.idsNoInsercoes.* dever ser um inteiro positivo',

            'formula.lista_derivacoes.*.idNoDerivacao.required'    => 'O campo formula.lista_derivacoes.*.idNoDerivacao é obrigátorio',
            'formula.lista_derivacoes.*.idNoDerivacao.integer'     => 'O campo formula.lista_derivacoes.*.idNoDerivacao dever ser um inteiro positivo',

            'formula.lista_derivacoes.*.regra.required'            => 'O campo formula.lista_derivacoes.*.regra é obrigátorio',
            'formula.lista_derivacoes.*.regra.string'              => 'O campo regra dever ser um texto',

            'formula.lista_fechamento.required'                     => 'O campo formula.lista_fechamento é obrigátorio',
            'formula.lista_fechamento.array'                        => 'O campo formula.lista_fechamento deve ser uma lista',

            'formula.lista_fechamento.*.idNoFolha.required'         => 'O campo formula.lista_fechamento.*.idNoFolha é obrigátorio',
            'formula.lista_fechamento.*.idNoFolha.integer'          => 'O campo formula.lista_fechamento.*.idNoFolha dever ser um inteiro positivo',

            'formula.lista_fechamento.*.idNoContraditorio.required' => 'O campo formula.lista_fechamento.*.idNoContraditorio é obrigátorio',
            'formula.lista_fechamento.*.idNoContraditorio.integer'  => 'O campo formula.lista_fechamento.*.idNoContraditorio dever ser um inteiro positivo',

            'formula.lista_ticagem.required'        => 'O campo formula.lista_ticagem é obrigátorio',
            'formula.lista_ticagem.array'           => 'O campo formula.lista_ticagem deve ser uma lista',

            'formula.lista_ticagem.*.idNo.required' => 'O campo formula.lista_ticagem.*.idNo é obrigátorio',
            'formula.lista_ticagem.*.idNo.integer'  => 'O campo formula.lista_ticagem.*.idNo dever ser um inteiro positivo',

            'formula.lista_passos.required'           => 'O campo formula.lista_passos é obrigátorio',
            'formula.lista_passos.array'              => 'O campo formula.lista_passos deve ser uma lista',

            'formula.lista_passos.*.idNo.required'    => 'O campo formula.lista_passos.*.idNo é obrigátorio',
            'formula.lista_passos.*.idNo.string'      => 'O campo idNo dever ser um texto',

            'formula.lista_passos.*.negacao.required' => 'O campo formula.lista_passos.*.negacao é obrigátorio',
            'formula.lista_passos.*.negacao.boolean'  => 'O campo formula.lista_passos.*.negacao deve ser booleano',
        ];
    }
}
