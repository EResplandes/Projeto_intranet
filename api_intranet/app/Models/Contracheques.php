<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contracheques extends Model
{
    protected $table = 'contracheques';

    protected $fillable = [
        'id',
        'usuario_id',
        'anexo',
        'visualizado',
        'created_at',
        'updated_at',
    ];
}
