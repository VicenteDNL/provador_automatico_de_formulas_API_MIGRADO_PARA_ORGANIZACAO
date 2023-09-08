<?php

namespace App\Http\Requests\API\Common\ArvoreRefutacao;

use App\Rules\ArvoreRule;
use App\Rules\PassoDerivarRule;
use Illuminate\Foundation\Http\FormRequest;

class ArvoreRefutacaoDerivaRequest extends FormRequest
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
            ...PassoDerivarRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
            ...PassoDerivarRule::messages(),
        ];
    }
}
