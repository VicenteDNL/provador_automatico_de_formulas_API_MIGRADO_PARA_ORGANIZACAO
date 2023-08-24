<?php

namespace App\Http\Requests\API\Admin\Recompensa;

use Illuminate\Foundation\Http\FormRequest;

class RecompensaUpdateRequest extends FormRequest
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
            'imagem'    => 'nullable|string',
            'nome'      => 'nullable|string',
            'pontuacao' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'imagem.string'      => 'O campo imagem dever ser um texto',
            'nome.string'        => 'O campo nome dever ser um texto',
            'pontuacao.integer'  => 'O campo pontuacao dever ser um inteiro positivo',

        ];
    }
}
