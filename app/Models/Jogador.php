<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Jogador
 *
 * @property int       $id
 * @property int       $famous_id
 * @property MediaType $type
 * @property string    $url
 * @property int       $position
 * @property DateTime  $created_at
 * @property DateTime  $updated_at
 */
class Jogador extends Model
{
    protected $table = 'jogadores';
}
