<?php

use Illuminate\Database\Seeder;

class JogadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Jogador::factory()->count(10)->create();
    }
}
