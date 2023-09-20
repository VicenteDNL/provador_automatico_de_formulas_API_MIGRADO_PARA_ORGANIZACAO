<?php

namespace App\Core\Common\Models\Enums;

enum RespostaEnum
{
    case TAUTOLOGIA;
    case CONTRADICAO;

    public function descricao(): string
    {
        return match ($this) {
            RespostaEnum::TAUTOLOGIA  => 'tautologia',
            RespostaEnum::CONTRADICAO => 'contradicao',
        };
    }

    public function jsonSerialize()
    {
        return match ($this) {
            RespostaEnum::TAUTOLOGIA  => 'TAUTOLOGIA',
            RespostaEnum::CONTRADICAO => 'CONTRADICAO',
        };
    }
}
