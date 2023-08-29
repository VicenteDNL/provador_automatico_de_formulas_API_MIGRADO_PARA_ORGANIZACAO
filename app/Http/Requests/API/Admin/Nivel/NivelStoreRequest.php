<?php

namespace App\Http\Requests\API\Admin\Nivel;

use Illuminate\Foundation\Http\FormRequest;

class NivelStoreRequest extends FormRequest
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
            'recompensa_id' => 'nullable|integer',
            'nome'          => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'ativo.required'         => 'O campo ativo é obrigátorio',
            'ativo.boolean'          => 'O campo ativo deve ser booleano',
            'descricao.required'     => 'O campo descricao é obrigátorio',
            'descricao.string'       => 'O campo descricao dever ser um texto',
            'recompensa_id.integer'  => 'O campo pontuacao dever ser um inteiro positivo',
            'nome.required'          => 'O campo pontuacao é obrigátorio',
            'nome.string'            => 'O campo nome dever ser um texto',
        ];
    }
}
