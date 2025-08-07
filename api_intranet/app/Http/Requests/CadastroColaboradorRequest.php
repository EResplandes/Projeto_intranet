<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastroColaboradorRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'departamento' => 'required|integer',
            'dt_nascimento' => 'required|date',
            'dt_admissao' => 'required|date',

        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cargo.required' => 'O campo cargo é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'imagem.required' => 'O campo imagem é obrigatório.',
            'departamento.required' => 'O campo departamento é obrigatório.',
            'dt_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'dt_admissao.required' => 'O campo data de admissão é obrigatório.',
        ];
    }
}
