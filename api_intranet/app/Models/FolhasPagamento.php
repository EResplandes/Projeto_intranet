<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolhasPagamento extends Model
{

    protected $table = 'folhas_pagamento';

    protected $fillable = [
        'mes',
        'ano',
        'dt_importacao',
        'arquivo_original'
    ];
}
