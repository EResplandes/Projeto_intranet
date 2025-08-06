<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AvisosResource extends JsonResource
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
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'categoria' => $this->categoria,
            'ativo' => $this->ativo,
            'usuario' => $this->usuario,
            'dt_abertura' => $this->formataData($this->created_at)
        ];
    }

    public function formataData($data)
    {
        return Carbon::parse($data)->format('d/m/Y');
    }
}
