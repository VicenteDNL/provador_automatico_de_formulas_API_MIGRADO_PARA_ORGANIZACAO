<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogador extends Model
{
    use HasFactory;
    protected $table = 'jogadores';
    protected $hidden = ['token', 'token_valido'];

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
}
