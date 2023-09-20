<?php

namespace App\Http\Requests\API\Common\ArvoreRefutacao;

use App\Rules\ArvoreAutomaticaRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoOtimizadaRequest extends FormRequest
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
