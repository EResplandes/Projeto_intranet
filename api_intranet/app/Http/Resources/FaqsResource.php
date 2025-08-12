<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FaqsResource extends JsonResource
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
            'pergunta' => $this->pergunta,
            'resposta' => $this->resposta,
            'categoria' => $this->categoria,
            'ativo' => $this->ativo,
            'dt_criacao' => $this->formataData($this->created_at),
        ];
    }

    public function formataData($data)
    {
        return Carbon::parse($data)->format('d/m/Y');
    }
}
