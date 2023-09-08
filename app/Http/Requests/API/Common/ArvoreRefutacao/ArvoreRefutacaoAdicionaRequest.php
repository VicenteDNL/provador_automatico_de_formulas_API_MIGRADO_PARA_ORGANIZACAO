<?php

namespace App\Http\Requests\API\Common\ArvoreRefutacao;

use App\Rules\ArvoreRule;
use App\Rules\PassoAdicionarRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoAdicionaRequest extends FormRequest
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
            ...PassoAdicionarRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
            ...PassoAdicionarRule::messages(),
        ];
    }
}
