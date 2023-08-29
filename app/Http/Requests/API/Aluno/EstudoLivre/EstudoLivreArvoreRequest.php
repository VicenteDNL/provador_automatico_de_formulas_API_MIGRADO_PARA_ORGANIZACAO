<?php

namespace App\Http\Requests\API\Aluno\EstudoLivre;

use App\Rules\ArvoreAutomaticaRule;
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
            ...ArvoreAutomaticaRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreAutomaticaRule::messages(),
        ];
    }
}
