<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common;

use ReflectionClass;

class Serializa
{
    public function __construct(array $values)
    {
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties();

        foreach ($values as  $key => $value) {
            $find = array_filter($properties, fn ($v) => $v->getName() == $key);

            if (!empty($find)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this));
    }
}
