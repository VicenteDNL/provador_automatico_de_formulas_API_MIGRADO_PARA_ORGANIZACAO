<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNiveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niveis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recompensa_id')->nullable()->unsigned();
            $table->string('nome');
            $table->text('descricao');
            $table->boolean('ativo');
            $table->timestamps();
            $table->foreign('recompensa_id')->references('id')->on('recompensas');
            $table->integer('logic_live_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('niveis');
    }
}
