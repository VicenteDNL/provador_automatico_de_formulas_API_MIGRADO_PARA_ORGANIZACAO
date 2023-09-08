<?php

namespace App\Http\Requests\API\Aluno\EstudoConceitos;

use Illuminate\Foundation\Http\FormRequest;

class EstudoConceitosConcluirRequest extends FormRequest
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
            'usuHash' => 'required|string',
            'exeHash' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'usuHash.required' => 'O campo hash é obrigatório',
            'usuHash.string'   => 'O campo hash deve ser texto',
            'exeHash.required' => 'O campo hash é obrigatório',
            'exeHash.string'   => 'O campo hash deve ser texto',
        ];
    }
}
