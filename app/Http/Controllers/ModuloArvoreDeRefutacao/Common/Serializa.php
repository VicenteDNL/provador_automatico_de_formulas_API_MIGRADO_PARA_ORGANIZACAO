<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common;

use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

class Serializa implements JsonSerializable
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

    public function jsonSerialize()
    {
        $values = [];
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC);
        $methods = $reflect->getMethods();

        foreach ($properties as  $key => $property) {
            $method = 'get' . implode(array_map(fn ($v) => ucfirst($v), explode('_', $property->getName())));
            $property = $property->getName();

            $findMethod = array_filter($methods, fn ($v) => $v->getName() == $method);

            if (!empty($findMethod)) {
                $values[$property] = $this->$method();
            }

            if (!empty($findproperty)) {
                $values[$property] = $this->$property;
            }
        }
        return $values;
    }

    protected function arrayToObject(?array &$lista, string $classe)
    {
        if (is_array($lista)) {
            for ($i = 0; $i < count($lista); ++$i) {
                if (!($lista[$i] instanceof $classe)) {
                    $lista[$i] = new $classe($lista[$i]);
                }
            }
        }
    }
}
