<?php

use Illuminate\Database\Seeder;

class RecompensaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Recompensa::factory()->count(10)->create();
    }
}
