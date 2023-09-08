<?php

namespace App\Http\Requests\API\Common\ArvoreRefutacao;

use App\Rules\ArvoreIniciarRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoIniciaRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ...ArvoreIniciarRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreIniciarRule::messages(),
        ];
    }
}
