<?php

namespace App\Http\Requests\API\Admin\ArvoreRefutacao;

use App\Rules\ArvoreRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoRecriarRequest extends FormRequest
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
            ...ArvoreRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
        ];
    }
}
