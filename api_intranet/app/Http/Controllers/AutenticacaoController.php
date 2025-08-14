<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Services\AutenticacaoService;

class AutenticacaoController extends Controller
{
    protected $autenticacaoService;

    public function __construct(AutenticacaoService $autenticacaoService)
    {
        $this->autenticacaoService = $autenticacaoService;
    }

    public function login(LoginRequest $request)
    {
        $query = $this->autenticacaoService->login($request);

        return response()->json([
            'token' => $query['token'] ?? null,
            'erro' => $query['erro'] ?? null,
            'usuario' => $query['usuario'] ?? null,
            'permissoes' => $query['permissoes'] ?? null,
            'status' => $query['status'] ?? null
        ], $query['http_code']);
    }
    
}
