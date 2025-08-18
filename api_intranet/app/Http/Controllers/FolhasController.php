<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FolhasService;
use App\Http\Requests\ImportacaoFolhaRequest;

class FolhasController extends Controller
{

    protected $folhasService;

    public function __construct(FolhasService $folhasService)
    {
        $this->folhasService = $folhasService;
    }

    public function importacaoFolha(ImportacaoFolhaRequest $request)
    {
        $query = $this->folhasService->importacaoFolha($request);

        return response()->json([
            'status' => $query['status'] ?? null,
            'erro' => $query['erro'] ?? null,
            'avisos' => $query['avisos'] ?? null
        ], $query['http_code'] ?? 400);
    }

    public function buscaFolhasColaborador($idColaborador)
    {
        $query = $this->folhasService->buscaFolhasColaborador($idColaborador);

        return response()->json([
            'status' => $query['status'] ?? null,
            'folhas' => $query['folhas'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code'] ?? 400);
    }

    public function validacaoFolha($idFolha)
    {
        $query = $this->folhasService->validacaoFolha($idFolha);

        return response()->json([
            'status' => $query['status'] ?? null,
            'folha' => $query['folha'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code'] ?? 400);
    }

    public function painelColaborar($idColaborador)
    {

        $query = $this->folhasService->painelColaborar($idColaborador);

        return response()->json([
            'status' => $query['status'] ?? null,
            'folhas' => $query['folhas'] ?? null,
            'erro' => $query['erro'] ?? null,
            'historicoSalario' => $query['historicoSalario'] ?? null,
            'salarioBase' => $query['salarioBase'] ?? null
        ], $query['http_code'] ?? 400);
    }

    public function secullum($cpf)
    {

        $query = $this->folhasService->secullum($cpf);

        return response()->json([
            'status' => $query['status'] ?? null,
            'erro' => $query['erro'] ?? null,
            'ponto' => $query['ponto'] ?? null,
            'horas' => $query['horas'] ?? null
        ], $query['http_code'] ?? 400);
    }
}
