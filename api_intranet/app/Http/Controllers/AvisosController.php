<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarAvisoRequest;
use App\Http\Requests\CadastrarAvisoResource;
use App\Http\Requests\CadastroCategoriaAvisosRequest;
use App\Models\Avisos;
use Illuminate\Http\Request;
use App\Services\AvisosService;

class AvisosController extends Controller
{

    protected $avisosService;

    public function __construct(AvisosService $avisosService)
    {
        $this->avisosService = $avisosService;
    }

    public function buscaTodosAvisos()
    {
        $query = $this->avisosService->buscaTodosAvisos();

        return response()->json([
            'status' => $query['status'],
            'avisos' => $query['avisos'] ?? null,
            'erro' => $query['erro'] ?? null,
        ], $query['http_code']);
    }

    public function cadastraCategoriaAviso(CadastroCategoriaAvisosRequest $request)
    {
        $query = $this->avisosService->cadastraCategoriaAviso($request);

        return response()->json([
            'status' => $query['status'],
            'categorias' => $query['categorias'] ?? null,
            'erro' => $query['erro'] ?? null,
        ], $query['http_code']);
    }

    public function alteraStatusAviso($id)
    {
        $query = $this->avisosService->alteraStatusAviso($id);

        return response()->json([
            'status' => $query['status'],
            'erro' => $query['erro'] ?? null,
        ], $query['http_code']);
    }

    public function deletaAviso($id)
    {
        $query = $this->avisosService->deletaAviso($id);

        return response()->json([
            'status' => $query['status'],
            'erro' => $query['erro'] ?? null,
            'avisos' => $query['avisos'] ?? null
        ], $query['http_code']);
    }

    public function buscaIndicadores()
    {
        $query = $this->avisosService->buscaIndicadores();

        return response()->json([
            'status' => $query['status'],
            'indicadores' => $query['indicadores'] ?? null,
            'erro' => $query['erro'] ?? null
        ]);
    }

    public function cadastraAviso(CadastrarAvisoRequest $request)
    {
        $query = $this->avisosService->cadastraAviso($request);

        return response()->json([
            'status' => $query['status'],
            'avisos' => $query['avisos'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function buscaCategorias()
    {
        $query = $this->avisosService->buscaCategorias();

        return response()->json([
            'status' => $query['status'],
            'categorias' => $query['categorias'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

}
