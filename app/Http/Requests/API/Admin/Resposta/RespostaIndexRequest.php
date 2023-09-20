<?php

namespace App\Http\Requests\API\Admin\Resposta;

use Illuminate\Foundation\Http\FormRequest;

class RespostaIndexRequest extends FormRequest
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

            'ativa'        => 'nullable|boolean',
            'completa'     => 'nullable|boolean',
            'exercicio_id' => 'nullable|integer',
            'jogador_id'   => 'nullable|integer',

        ];
    }

    public function messages()
    {
        return [
            'ativa.boolean'        => 'O Campo ativa deve ser um boleano',
            'completa.boolean'     => 'O Campo completa deve ser um boleano',
            'exercicio_id.integer' => 'O campo exercicio_id dever ser um inteiro positivo',
            'jogador_id.integer'   => 'O campo jogador_id dever ser um inteiro positivo',
        ];
    }
}
