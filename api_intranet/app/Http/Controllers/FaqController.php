<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastroFaqRequest;
use Illuminate\Http\Request;
use App\Services\FaqService;

class FaqController extends Controller
{

    protected $faqService;

    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    public function buscaFaqs()
    {
        $query = $this->faqService->buscaFaqs();
        return response()->json([
            'status' => $query['status'],
            'faqs' => $query['faqs'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function buscaIndicadores()
    {
        $query = $this->faqService->buscaIndicadores();
        return response()->json([
            'status' => $query['status'],
            'indicadores' => $query['indicadores'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function cadastraFaq(CadastroFaqRequest $request)
    {
        $query = $this->faqService->cadastraFaq($request);
        return response()->json([
            'status' => $query['status'],
            'faqs' => $query['faqs'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function editaFaq(Request $request, $id)
    {
        $query = $this->faqService->editaFaq($request, $id);
        return response()->json([
            'status' => $query['status'],
            'faqs' => $query['faqs'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function alteraStatus($id)
    {
        $query = $this->faqService->alteraStatus($id);
        return response()->json([
            'status' => $query['status'],
            'faqs' => $query['faqs'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function deletaFaq($id)
    {
        $query = $this->faqService->deletaFaq($id);
        return response()->json([
            'status' => $query['status'],
            'faqs' => $query['faqs'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }

    public function buscaPerguntasIntranet()
    {
        $query = $this->faqService->buscaPerguntasIntranet();
        return response()->json([
            'status' => $query['status'],
            'perguntas' => $query['perguntas'] ?? null,
            'erro' => $query['erro'] ?? null
        ], $query['http_code']);
    }
}
