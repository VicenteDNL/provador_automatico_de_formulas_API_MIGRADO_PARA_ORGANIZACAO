<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common;

use ReflectionClass;

class Serializa
{
    public function __construct(array $values = [])
    {
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties();
        $methods = $reflect->getMethods();

        foreach ($values as  $key => $value) {
            $method = 'set' . implode(array_map(fn ($v) => ucfirst($v), explode('_', $key)));
            $property = $key;

            $findproperty = array_filter($properties, fn ($v) => $v->getName() == $key);
            $findMethod = array_filter($methods, fn ($v) => $v->getName() == $method);

            if (!empty($findMethod)) {
                $this->$method($value);
            }

            if (!empty($findproperty)) {
                $this->$property = $value;
            }
        }
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this));
    }
}
