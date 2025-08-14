<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportacaoFolhaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mes' => 'required|integer',
            'ano' => 'required|integer',
            'folha' => 'required|mimes:txt',
        ];
    }

    public function messages()
    {
        return [
            'mes.required' => 'O campo mês é obrigatório.',
            'ano.required' => 'O campo ano é obrigatório.',
            'folha.required' => 'O campo folha é obrigatório.',
            'folha.mimes' => 'O arquivo deve ser do tipo txt.',
        ];
    }
}
