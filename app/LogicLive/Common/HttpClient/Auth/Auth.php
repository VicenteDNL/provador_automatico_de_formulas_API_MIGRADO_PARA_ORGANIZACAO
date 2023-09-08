<?php

namespace App\LogicLive\Common\HttpClient\Auth;

use App\LogicLive\Common\Serialization\Serializa;

class Auth extends Serializa
{
    protected string $token;

    /**
     * @return mixed
     */
    public function getToken(): mixed
    {
        return $this->token;
    }
}
