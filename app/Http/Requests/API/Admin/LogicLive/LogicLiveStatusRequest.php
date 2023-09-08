<?php

namespace App\Http\Requests\API\Admin\LogicLive;

use Illuminate\Foundation\Http\FormRequest;

class LogicLiveStatusRequest extends FormRequest
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
            'ativo'    => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ativo.required'         => 'O campo ativo é obrigátorio',
            'ativo.boolean'          => 'O campo ativo deve ser booleano',
        ];
    }
}
