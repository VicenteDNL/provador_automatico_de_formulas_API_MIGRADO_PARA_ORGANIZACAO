<?php

namespace App\LogicLive\Common\Enums;

enum Types
{
    case GAME;
    case MODULO;
    case NIVEL;
    case EXERCICIO;
    case RECOMPENSA;

    public function descricao(): string
    {
        return match ($this) {
            Types::GAME        => 'GAME',
            Types::MODULO      => 'MODULO',
            Types::NIVEL       => 'NIVEL',
            Types::EXERCICIO   => 'EXERCICIO',
            Types::RECOMPENSA  => 'RECOMPENSA',
        };
    }
}
