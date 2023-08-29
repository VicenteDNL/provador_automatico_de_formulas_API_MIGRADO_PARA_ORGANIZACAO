<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recompensa_id');
            $table->unsignedBigInteger('nivel_id');
            $table->unsignedBigInteger('formula_id');
            $table->string('nome');
            $table->text('enunciado');
            $table->string('hash');
            $table->string('url');
            $table->integer('tempo')->nullable();
            $table->integer('qndt_erros')->nullable();
            $table->text('descricao');
            $table->boolean('ativo');
            $table->timestamps();
            $table->foreign('recompensa_id')->references('id')->on('recompensas');
            $table->foreign('nivel_id')->references('id')->on('niveis');
            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');
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
        Schema::dropIfExists('exercicios');
    }
}
