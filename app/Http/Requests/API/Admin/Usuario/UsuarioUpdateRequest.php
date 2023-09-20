<?php

namespace App\Http\Requests\API\Admin\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioUpdateRequest extends FormRequest
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
            'nome'     => 'nullable|string',
            'email'    => 'nullable|email',
            'password' => 'nullable|min:4',
            'ativo'    => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nome.string'       => 'O campo nome dever ser um textp',
            'email.email'       => 'O campo deve ter um formato de email válido',
            'password.min'      => 'O campo senha precisa ter no minino 4 caracteres',
            'ativo.boolean'     => 'O campo deve ser um boleano',
        ];
    }
}
