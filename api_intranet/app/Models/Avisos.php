<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avisos extends Model
{
    protected $table = 'avisos';

    protected $fillable = [
        'titulo',
        'texto',
        'categoria_id',
        'usuario_id',
        'ativo',
        'created_at',
        'updated_at'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriasAvisos::class);
    }
}
