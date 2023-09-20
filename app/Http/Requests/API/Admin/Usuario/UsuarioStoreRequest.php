<?php

namespace App\Http\Requests\API\Admin\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioStoreRequest extends FormRequest
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
            'nome'     => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|min:4',
            'ativo'    => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'nome.required'     => 'O campo nome é obrigátorio',
            'nome.string'       => 'O campo nome dever ser um textp',
            'email.required'    => 'O campo email é obrigátorio',
            'email.email'       => 'O campo deve ter um formato de email válido',
            'password.required' => 'O campo senha é obrigátorio',
            'password.min'      => 'O campo senha precisa ter no minino 4 caracteres',
            'ativo.required'    => 'O campo ativo é obrigátorio',
            'ativo.boolean'     => 'O campo deve ser um boleano',
        ];
    }
}
