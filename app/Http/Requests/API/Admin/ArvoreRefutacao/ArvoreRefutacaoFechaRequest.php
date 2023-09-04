<?php

namespace App\Http\Requests\API\Admin\ArvoreRefutacao;

use App\Rules\ArvoreRule;
use App\Rules\PassoFecharRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoFechaRequest extends FormRequest
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
            ...PassoFecharRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
            ...PassoFecharRule::messages(),
        ];
    }
}
