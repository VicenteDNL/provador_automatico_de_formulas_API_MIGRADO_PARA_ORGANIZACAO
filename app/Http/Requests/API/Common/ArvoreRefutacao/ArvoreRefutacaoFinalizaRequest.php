<?php

namespace App\Http\Requests\API\Common\ArvoreRefutacao;

use App\Core\Common\Models\Enums\RespostaEnum;
use App\Rules\ArvoreRule;
use App\Rules\PassoFinalizarRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ArvoreRefutacaoFinalizaRequest extends FormRequest
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
            ...PassoFinalizarRule::rules(),
        ];
    }

    public function messages()
    {
        return [
            ...ArvoreRule::messages(),
            ...PassoFinalizarRule::messages(),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            $date = $validator->getData();

            if (!in_array($date['passo']['resposta'], array_column(RespostaEnum::cases(), 'name'))) {
                $validator->errors()->add('passo.resposta', 'não é uma resposta válida');
            }
        });
    }
}
