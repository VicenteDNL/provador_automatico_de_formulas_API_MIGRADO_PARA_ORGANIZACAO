<?php

use App\Recompensa;
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
        factory(Recompensa::class,10)->create();
    }
}
