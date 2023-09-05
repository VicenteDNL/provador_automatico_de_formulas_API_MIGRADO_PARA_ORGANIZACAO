<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsuarioSeeder::class);
        // $this->call(RecompensaSeeder::class);
        // $this->call(NivelSeeder::class);
        // $this->call(FormulaSeeder::class);
        // $this->call(ExercicioSeeder::class);
        $this->call(JogadorSeeder::class);
        $this->call(RespostaSeeder::class);
    }
}
