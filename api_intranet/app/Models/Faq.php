<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faq';

    protected $fillable = [
        'pergunta',
        'resposta',
        'categoria',
        'ativo',
        'created_at',
        'updated_at'
    ];
}
