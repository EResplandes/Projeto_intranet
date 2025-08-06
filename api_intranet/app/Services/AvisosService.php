<?php

namespace App\Services;

use App\Models\Avisos;
use App\Http\Resources\AvisosResource;
use App\Models\CategoriasAvisos;
use Illuminate\Support\Facades\DB;

class AvisosService
{
    public function buscaTodosAvisos()
    {
        try {

            $avisos = AvisosResource::collection(Avisos::all());

            return [
                'status' => 'sucesso',
                'avisos' => $avisos,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }

        return $avisos;
    }

    public function cadastraCategoriaAviso($request)
    {
        DB::beginTransaction();

        try {

            $categoria = Avisos::create([
                'categoria' => $request->categoria,
            ]);

            $categorias = CategoriasAvisos::all();

            DB::commit();

            return [
                'status' => 'sucesso',
                'categorias' => $categorias,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function alteraStatusAviso($id)
    {
        try {
            $aviso = Avisos::find($id);
            $aviso->ativo = !$aviso->ativo;
            $aviso->save();

            return [
                'status' => 'sucesso',
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function deletaAviso($id)
    {
        DB::beginTransaction();

        try {
            $aviso = Avisos::find($id);
            $aviso->delete();

            $avisos = AvisosResource::collection(
                Avisos::all()
            );

            DB::commit();

            return [
                'status' => 'sucesso',
                'http_code' => 200,
                'avisos' => $avisos
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function buscaIndicadores()
    {
        try {
            $totalAvisos = Avisos::all()->count();
            $avisosAtivos = Avisos::where('ativo', true)->count();
            $avisosInativos = Avisos::where('ativo', false)->count();

            return [
                'status' => 'sucesso',
                'indicadores' => [
                    'totalAvisos' => $totalAvisos,
                    'avisosAtivos' => $avisosAtivos,
                    'avisosInativos' => $avisosInativos
                ],
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function cadastraAviso($request)
    {

        DB::beginTransaction();

        try {
            $aviso = Avisos::create([
                'categoria_id' => $request->categoria_id,
                'titulo' => $request->titulo,
                'usuario_id' => $request->usuario_id,
                'texto' => $request->texto,
                'ativo' => true,
            ]);

            $avisos = AvisosResource::collection(
                Avisos::all()
            );

            DB::commit();

            return [
                'status' => 'sucesso',
                'http_code' => 200,
                'avisos' => $avisos
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function buscaCategorias()
    {
        try {
            $categorias = CategoriasAvisos::all();

            return [
                'status' => 'sucesso',
                'categorias' => $categorias,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                'status' => 'erro',
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }
}
