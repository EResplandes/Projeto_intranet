<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastroColaboradorRequest;
use App\Services\ColaboradoresService;
use Illuminate\Http\Request;

class ColaboradoresController extends Controller
{
    protected $colaboradoresService;

    public function __construct(ColaboradoresService $colaboradoresService)
    {
        $this->colaboradoresService = $colaboradoresService;
    }

    public function buscaTodosColaboradores()
    {
        $query = $this->colaboradoresService->buscaTodosColaboradores();

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function buscaDepartamentos()
    {

        $query = $this->colaboradoresService->buscaDepartamentos();

        return response()->json(
            [
                'status' => $query['status'],
                'departamentos' => $query['departamentos'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function cadastraColaborador(Request $request)
    {
        $query = $this->colaboradoresService->cadastraColaborador($request);

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function alteraStatusColaborador($id)
    {
        $query = $this->colaboradoresService->alteraStatusColaborador($id);

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function cadastraDepartamento(Request $request)
    {
        $query = $this->colaboradoresService->cadastraDepartamento($request);

        return response()->json(
            [
                'status' => $query['status'],
                'departamentos' => $query['departamentos'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function editaColaborador(Request $request, $id)
    {
        $query = $this->colaboradoresService->editaColaborador($request, $id);

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function buscaIndicadores()
    {
        $query = $this->colaboradoresService->buscaIndicadores();

        return response()->json(
            [
                'status' => $query['status'],
                'indicadores' => $query['indicadores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function deletaColaborador($id)
    {
        $query = $this->colaboradoresService->deletaColaborador($id);

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function buscaAniversariantes()
    {
        $query = $this->colaboradoresService->buscaAniversariantes();

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }

    public function buscaColaboradoresMural()
    {
        $query = $this->colaboradoresService->buscaColaboradoresMural();

        return response()->json(
            [
                'status' => $query['status'],
                'colaboradores' => $query['colaboradores'] ?? null,
                'erro' => $query['erro'] ?? null
            ],
            $query['http_code']
        );
    }
}
