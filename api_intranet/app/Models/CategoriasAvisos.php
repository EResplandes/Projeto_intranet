<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasAvisos extends Model
{
    protected $table = 'categorias_avisos';

    protected $fillable = [
        'categoria'
    ];
}
