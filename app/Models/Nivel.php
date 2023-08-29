<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;
    protected $table = 'niveis';
    protected $fillable = ['nome', 'ativo', 'descricao', 'id_recompensa'];

    public function id_recompensa()
    {
        return $this->belongsTo(Recompensa::class, 'id_recompensa');
    }

    public function exercicios()
    {
        return $this->hasMany(Exercicio::class);
    }
}
