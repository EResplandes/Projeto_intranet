<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensFolha extends Model
{

    protected $table = 'itens_folha';

    protected $fillable = [
        'funcionario_folha_id',
        'descricao',
        'tipo',
        'valor',
        'horas_qtd'
    ];


    public function funcionarioFolha()
    {
        return $this->belongsTo(FuncionariosFolha::class, 'funcionario_folha_id');
    }
}
