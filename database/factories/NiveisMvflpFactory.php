<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\NivelMVFLP;
use App\Recompensa;

use Faker\Generator as Faker;


$factory->define(NivelMVFLP::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'id_recompensa' =>1,
        'descricao' => $faker->text, 
        'ativo' =>$faker->boolean,
    ];
});
