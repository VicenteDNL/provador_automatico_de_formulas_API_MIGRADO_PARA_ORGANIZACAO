<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercicio extends Model
{
    use HasFactory;
    protected $table = 'exercicios';
    protected $fillable = ['nome', 'ativo', 'descricao', 'enunciado'];

    public function resposta()
    {
        return $this->belongsTo(Resposta::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class);
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
}
