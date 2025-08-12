<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastroFaqRequest extends FormRequest
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
            'pergunta' => 'required',
            'resposta' => 'required',
            'categoria' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pergunta.required' => 'A pergunta é obrigatória.',
            'resposta.required' => 'A resposta é obrigatória.',
            'categoria.required' => 'A categoria é obrigatória.',
        ];
    }
}
