<?php

namespace App\Http\Requests\API\Aluno\Modulos\EstudoLivre;

use Illuminate\Foundation\Http\FormRequest;

class EstudoLivreArvoreRequest extends FormRequest
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
            'xml'          => 'required|string',
            'canvas.width' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'xml.required'          => 'O campo xml é obrigatório',
            'xml.string'            => 'O campo xml deve ser uma string',
            'canvas.width.numeric'  => 'O campo deve ser numérico',

        ];
    }
}
