<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusTarefas extends Model
{
    protected $table = 'status_tarefas';

    protected $fillable = [
        'status',
    ];
}
