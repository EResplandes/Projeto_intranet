<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissoesUsers extends Model
{
    protected $table = 'permissoes_users';

    protected $fillable = [
        'slug',
        'usuario_id',
        'created_at',
        'updated_at',
    ];
}
