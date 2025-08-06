<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefas extends Model
{

    protected $table = 'tarefas';

    protected $fillable = [
        'tarefa',
        'solicitante_id',
        'destinatario_id',
        'status_id',
        'created_at',
        'updated_at'
    ];
}
