<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JogadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'nome'           => $this->faker->name(),
            'usunome'        => $this->faker->userName(),
            'email'          => $this->faker->email(),
            'avatar'         => $this->faker->url(),
            'token'          => $this->faker->md5(),
            'token_valido'   => $this->faker->boolean(),
            'ativo'          => $this->faker->boolean(),
            'logic_live_id'  => null,
            'provedor'       => 'email',
        ];
    }
}
