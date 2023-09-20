<?php

use Illuminate\Database\Seeder;

class RespostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Resposta::factory()->count(10)->create();
    }
}
