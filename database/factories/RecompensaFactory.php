<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Recompensa;
use Faker\Generator as Faker;

$factory->define(Recompensa::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'imagem' => $faker->text, 
        'pontuacao' =>$faker->randomNumber(3),
    ];
});
