<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExercicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome'          => $this->faker->name(),
            'recompensa_id' => $this->faker->numberBetween(1, 10),
            'nivel_id'      => $this->faker->numberBetween(1, 10),
            'formula_id'    => $this->faker->numberBetween(1, 10),
            'url'           => $this->faker->text(),
            'enunciado'     => $this->faker->text(),
            'hash'          => $this->faker->text(),
            'url'           => $this->faker->text(),
            'tempo'         => $this->faker->numberBetween(1, 60),
            'descricao'     => $this->faker->text(),
            'ativo'         => $this->faker->boolean(),
        ];
    }
}
