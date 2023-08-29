<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    use HasFactory;
    protected $table = 'recompensas';
    protected $fillable = ['nome', 'imagem', 'pontuacao', 'logic_live_id'];
}
