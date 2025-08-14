<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuncionariosFolha extends Model
{

    protected $table = 'funcionarios_folha';

    protected $fillable = [
        'folha_id',
        'funcionario_id',
        'total_proventos',
        'total_descontos',
        'total_liquido',
        'salario_base',
        'nome'
    ];

    public function folha()
    {
        return $this->belongsTo(FolhasPagamento::class);
    }
}
