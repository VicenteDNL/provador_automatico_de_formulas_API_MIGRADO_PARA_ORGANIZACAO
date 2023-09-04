<?php

namespace App\Http\Requests\API\Admin\ArvoreRefutacao;

use App\Rules\ArvoreRule;
use App\Rules\PassoTicarRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoTicaRequest extends FormRequest
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
            ...PassoTicarRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
            ...PassoTicarRule::messages(),
        ];
    }
}
