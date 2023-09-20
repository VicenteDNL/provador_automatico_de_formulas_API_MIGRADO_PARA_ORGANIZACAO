<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RespostaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'exercicio_id'         => $this->faker->numberBetween(1, 4),
            'jogador_id'           => $this->faker->numberBetween(1, 10),
            'tempo'                => $this->faker->numberBetween(60, 120),
            'ativa'                => $this->faker->boolean(),
            'tentativas_invalidas' => $this->faker->numberBetween(0, 10),
            'pontuacao'            => $this->faker->numberBetween(0, 100),
            'repeticao'            => $this->faker->numberBetween(0, 10),
            'concluida'            => $this->faker->boolean(),
        ];
    }
}
