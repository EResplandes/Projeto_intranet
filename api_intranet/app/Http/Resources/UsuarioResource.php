<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
            'nome' => $this->name,
            'email' => $this->email,
            'ativo' => $this->ativo,
            'cpf' => $this->cpf,
            'cargo' => $this->cargo,
            'admin' => $this->admin,
            'imagem' => $this->imagem,
            'dt_nascimento' => $this->formataData($this->dt_nascimento),
            'dt_admissao' => $this->formataData($this->dt_admissao),
            'departamento' => $this->departamento,
            'dt_cadastro' => $this->formataData($this->created_at)
        ];
    }

    public function formataData($data)
    {
        return date('d/m/Y', strtotime($data));
    }
}
