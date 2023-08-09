<?php

use App\Models\ExercicioMVFLP;
use Illuminate\Database\Seeder;

class ExercicioMvflpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ExercicioMVFLP::class, 4)->create();
    }
}
