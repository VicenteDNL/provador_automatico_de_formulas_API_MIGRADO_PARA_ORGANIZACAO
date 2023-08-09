<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Recompensa;
use Faker\Generator as Faker;

$factory->define(Recompensa::class, function (Faker $faker) {
    return [
        'nome'      => $faker->name,
        'imagem'    => $faker->text,
        'pontuacao' => $faker->randomNumber(3),
    ];
});
