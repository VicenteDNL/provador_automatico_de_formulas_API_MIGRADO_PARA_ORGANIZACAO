<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NivelMVFLP;

use Faker\Generator as Faker;

$factory->define(NivelMVFLP::class, function (Faker $faker) {
    return [
        'nome'          => $faker->name,
        'id_recompensa' => 1,
        'descricao'     => $faker->text,
        'ativo'         => $faker->boolean,
    ];
});
