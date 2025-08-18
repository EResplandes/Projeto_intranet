<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ColaboradoresResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'matricula' => $this->matricula ?? null,
            'nome' => $this->name,
            'cpf' => $this->cpf,
            'cargo' => $this->cargo,
            'imagem' => $this->imagem,
            'dt_nascimento' => $this->formataData($this->dt_nascimento),
            'dt_admissao' => $this->formataData($this->dt_admissao),
            'email' => $this->email,
            'ativo' => $this->ativo,
            'departamento' => $this->departamento,
            'dt_cadastro' => $this->formataData($this->created_at)
        ];
    }

    public function formataData($data)
    {
        return Carbon::parse($data)->format('d/m/Y');
    }
}
