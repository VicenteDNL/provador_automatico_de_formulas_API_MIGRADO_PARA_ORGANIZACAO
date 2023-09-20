<?php

namespace App\LogicLive\Common\Enums;

enum Actions
{
    case DELETE;
    case GET;
    case POST;
    case PUT;
    case PATCH;

    public function descricao(): string
    {
        return match ($this) {
            Actions::DELETE => 'DELETE',
            Actions::GET    => 'GET',
            Actions::POST   => 'POST',
            Actions::PUT    => 'PUT',
            Actions::PATCH  => 'PATCH',
        };
    }
}
