<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecompensaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome'      => $this->faker->name,
            'imagem'    => $this->faker->text,
            'pontuacao' => $this->faker->randomNumber(3),
        ];
    }
}
