<?php

namespace App\Core\Common\Exceptions;

use Exception;

class ArvoreDeRefutacaoExecption extends Exception
{
    public function __construct(string $msg = 'Erro no interno')
    {
        parent::__construct($msg);
    }
}
