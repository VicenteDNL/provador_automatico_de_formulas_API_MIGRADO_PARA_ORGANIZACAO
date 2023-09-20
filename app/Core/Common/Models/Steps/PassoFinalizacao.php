<?php

namespace App\Core\Common\Models\Steps;

use App\Core\Common\Models\Enums\RespostaEnum;
use App\Core\Common\Serialization\Serializa;

class PassoFinalizacao extends Serializa
{
    protected RespostaEnum $resposta;

    /**
     * @return RespostaEnum
     */
    public function getResposta(): RespostaEnum
    {
        return $this->resposta;
    }

    /**
     * @param  RespostaEnum $resposta
     * @return void
     */
    public function setResposta(RespostaEnum $resposta): void
    {
        $this->resposta = $resposta;
    }
}
