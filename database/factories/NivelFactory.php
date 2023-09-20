<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NivelFactory extends Factory
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
            'descricao'     => $this->faker->text(),
            'ativo'         => $this->faker->boolean(),
        ];
    }
}
