<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelMVFLP extends Model
{
    protected $table = 'niveis_mvflp';
    protected $fillable = ['nome', 'ativo', 'descricao', 'id_recompensa'];

    public function id_recompensa()
    {
        return $this->belongsTo(Recompensa::class, 'id_recompensa');
    }
}
