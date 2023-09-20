<?php

use Illuminate\Database\Seeder;

class ExercicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Exercicio::factory()->count(4)->create();
    }
}
