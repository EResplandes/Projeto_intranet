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
        'nome',
        'liberado'
    ];

    public function folha()
    {
        return $this->belongsTo(FolhasPagamento::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(ItensFolha::class, 'funcionario_folha_id');
    }
}
