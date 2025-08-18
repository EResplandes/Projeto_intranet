<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColaboradorFolhasResource extends JsonResource
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
            'liberado' => $this->liberado,
            'total_proventos' => $this->total_proventos,
            'total_descontos' => $this->total_descontos,
            'total_liquido' => $this->total_liquido,
            'salario_base' => $this->salario_base,
            'folha' => $this->folha,
            'itens' => $this->itens
        ];
    }
}
