<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'formula'                => $this->faker->name(),
            'xml'                    => $this->faker->text(),
            'quantidade_regras'      => $this->faker->numberBetween(1, 11),
            'ticar_automaticamente'  => $this->faker->boolean(),
            'fechar_automaticamente' => $this->faker->boolean(),
            'inicio_personalizado'   => $this->faker->boolean(),
        ];
    }
}
