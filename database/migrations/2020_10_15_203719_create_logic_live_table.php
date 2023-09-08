<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogicLiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logic_live', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); //recompensa , exercicio, resposta, modulo, game, etc..
            $table->text('modelo');
            $table->integer('meu_id');
            $table->text('hash')->nullable(); //ver se ainda é necessario
            $table->text('link')->nullable(); //ver se ainda é necessario
            $table->integer('exercicio_id')->nullable();
            $table->integer('game_id')->nullable();
            $table->integer('modulo_id')->nullable();
            $table->integer('nivel_id')->nullable();
            $table->text('nome')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logic_live');
    }
}
