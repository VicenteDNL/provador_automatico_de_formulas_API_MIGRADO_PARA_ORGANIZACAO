<?php

namespace App\Http\Requests\API\Aluno\Jogador;

use Illuminate\Foundation\Http\FormRequest;

class JogadorHashRequest extends FormRequest
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
            'hash' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'hash.required'  => 'O campo hash é obrigatório',
            'hash.string'    => 'O campo hash deve ser texto',
        ];
    }
}
