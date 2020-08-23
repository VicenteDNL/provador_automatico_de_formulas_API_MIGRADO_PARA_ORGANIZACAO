<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ExercicioMVFLP;
use App\Model;
use Faker\Generator as Faker;

$factory->define(ExercicioMVFLP::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'id_recompensa' =>1,
        'id_nivel' =>1,
        'id_formula' =>1,
        'url' =>$faker->text,
        'enunciado' =>$faker->text,
        'hash' =>$faker->text,
        'url' =>$faker->text,
        'tempo' =>$faker->time,
        'descricao' => $faker->text, 
        'ativo' =>$faker->boolean,
    ];
});
