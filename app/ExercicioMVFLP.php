<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExercicioMVFLP extends Model
{
    protected $table = 'exercicios_mvflp';
    protected $fillable = ['nome', 'ativo', 'descricao','enunciado'];

    public function resposta()
    {
        return $this->belongsTo('App\Models\Resposta');
    }
}
