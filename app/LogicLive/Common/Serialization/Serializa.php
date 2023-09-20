<?php

namespace App\LogicLive\Common\Serialization;

use JsonSerializable;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionProperty;
use Throwable;

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

            if (!empty($findproperty)) {
                $type = $findproperty[key($findproperty)]->getType()->getName();

                if (gettype($value) != $type) {
                    if (enum_exists($type) && is_string($value)) {
                        $reflectionEnum = new ReflectionEnum($type);

                        if ($reflectionEnum->isBacked()) {
                            $reflectionEnum = new ReflectionEnumBackedCase($type, $value);
                            $treatedValue = $reflectionEnum->getValue();
                        } else {
                            $treatedValue = $reflectionEnum->getCase($value)->getValue();
                        }
                    } elseif (class_exists($type) && is_array($value)) {
                        $treatedValue = new $type($value);
                    }
                }
            }

            if (!empty($findMethod)) {
                $this->$method(isset($treatedValue) ? $treatedValue : $value);
                continue;
            }

            if (!empty($findproperty)) {
                $this->$property = isset($treatedValue) ? $treatedValue : $value;
            }
        }
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
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

            try {
                if (!empty($findMethod)) {
                    $values[$property] = $this->$method();
                    continue;
                }
                $values[$property] = $this->$property;
            } catch(Throwable $e) {
            }
        }
        return $values;
    }

    protected function arrayToObject(?array &$lista, string $classe)
    {
        if (is_array($lista)) {
            foreach ($lista as $key => $item) {
                if (!($lista[$key] instanceof $classe)) {
                    $lista[$key] = new $classe($item);
                }
            }
        }
    }
}
