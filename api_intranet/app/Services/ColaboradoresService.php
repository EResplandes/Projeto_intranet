<?php

namespace App\Services;

use App\Models\User;
use App\Models\Departamentos;
use App\Http\Resources\ColaboradoresResource;
use Illuminate\Support\Facades\DB;

class ColaboradoresService
{
    public function buscaTodosColaboradores()
    {
        try {
            $colaboradores = ColaboradoresResource::collection(User::all());

            return [
                'status' => true,
                'colaboradores' => $colaboradores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function buscaDepartamentos()
    {
        try {

            $departamentos = Departamentos::all();

            return [
                'status' => true,
                'departamentos' => $departamentos,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function cadastraColaborador($request)
    {

        DB::beginTransaction();

        try {
            if ($request->hasFile('imagem')) {
                $path = $request->file('imagem')->store('colaboradores');
                $request->merge(['imagem' => $path]);
            }

            $directory = "\imagens\colaboradores\ $request->cpf ";

            if ($request->hasFile('imagem')) {
                $imagem = $request->file('imagem')->store($directory, 'public');
            }

            $colaborador = User::create([
                'name' => $request->nome,
                'cpf' => $request->cpf,
                'password' => bcrypt(random_bytes(32)),
                'imagem' => $imagem,
                'cargo' => $request->cargo,
                'dt_nascimento' => $request->dt_nascimento,
                'dt_admissao' => $request->dt_admissao,
                'email' => $request->email,
                'ativo' => 1,
                'departamento_id' => $request->departamento_id,
            ]);

            $colaboradores = ColaboradoresResource::collection(User::all());

            DB::commit();

            return [
                'status' => true,
                'colaboradores' => $colaboradores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function alteraStatusColaborador($id)
    {
        try {
            $colaborador = User::find($id);
            $colaborador->ativo = !$colaborador->ativo;
            $colaborador->save();

            $colaboradores = ColaboradoresResource::collection(User::all());

            return [
                'status' => true,
                'colaboradores' => $colaboradores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function cadastraDepartamento($request)
    {
        try {

            $departamento = Departamentos::create([
                'departamento' => $request->departamento,
            ]);

            $departamentos = Departamentos::all();

            return [
                'status' => true,
                'departamentos' => $departamentos,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function editaColaborador($request)
    {
        try {

            if ($request->hasFile('imagem')) {
                $path = $request->file('imagem')->store('colaboradores');
                $request->merge(['imagem' => $path]);
            }

            $colaborador = User::find($request->id);
            $colaborador->name = $request->name;
            $colaborador->sobrenome = $request->sobrenome;
            $colaborador->cpf = $request->cpf;
            $colaborador->imagem = $request->imagem;
            $colaborador->cargo = $request->cargo;
            $colaborador->dt_nascimento = $request->dt_nascimento;
            $colaborador->dt_admissao = $request->dt_admissao;
            $colaborador->email = $request->email;
            $colaborador->ativo = $request->ativo;
            $colaborador->departamento = $request->departamento;
            $colaborador->save();


            $colaboradores = ColaboradoresResource::collection(User::all());

            return [
                'status' => true,
                'colaboradores' => $colaboradores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function buscaIndicadores()
    {
        try {
            $indicadores = [
                'totalColaboradores' => User::all()->count(),
                'colaboradoresAtivos' => User::where('ativo', true)->count(),
                'colaboradoresInativos' => User::where('ativo', false)->count(),
            ];

            return [
                'status' => true,
                'indicadores' => $indicadores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function deletaColaborador($id)
    {
        try {
            $colaboradore = User::find($id);
            $colaboradore->delete();

            $colaboradores = ColaboradoresResource::collection(User::all());

            return [
                'status' => true,
                'colaboradores' => $colaboradores,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }
}
