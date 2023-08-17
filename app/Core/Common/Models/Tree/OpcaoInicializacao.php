<?php

namespace App\Core\Common\Models\Tree;

use App\Core\Common\Serialization\Serializa;

class OpcaoInicializacao extends Serializa
{
    protected string $id;
    protected string $texto;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param  string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param  string $texto
     * @return void
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }
}
