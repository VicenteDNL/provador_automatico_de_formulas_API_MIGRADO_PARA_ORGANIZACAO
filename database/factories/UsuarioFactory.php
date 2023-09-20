<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nome'        => 'Danilo Saraiva Vicente',
            'email'       => 'danilo.saraiva68@gmail.com',
            'password'    => Hash::make('logiclive123'),
            'ativo'       => true,
        ];
    }
}
