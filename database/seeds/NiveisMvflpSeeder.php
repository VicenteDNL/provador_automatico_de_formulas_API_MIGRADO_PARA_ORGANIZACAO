<?php

use App\Models\NivelMVFLP;
use Illuminate\Database\Seeder;

class NiveisMvflpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(NivelMVFLP::class, 1)->create()
        // ->each(function($u) {
        //     var_dump($u);
        //     $u->recompensas()->save(factory(App\Recompensa::class)->make());
        // })
        ;
    }
}
