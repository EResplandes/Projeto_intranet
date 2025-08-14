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
}
