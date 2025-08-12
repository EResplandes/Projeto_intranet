<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\FaqsResource;

class FaqService
{

    public function buscaFaqs()
    {
        try {

            $faqs = FaqsResource::collection(
                Faq::orderBy('created_at', 'desc')->get()
            );

            return [
                'status' => true,
                'faqs' => $faqs,
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
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

            $totalFaqs = Faq::all()->count();
            $faqsAtivos = Faq::where('ativo', true)->count();
            $faqsInativos = Faq::where('ativo', false)->count();

            return [
                'status' => true,
                'indicadores' => [
                    'total' => $totalFaqs,
                    'ativas' => $faqsAtivos,
                    'inativas' => $faqsInativos
                ],
                'http_code' => 200
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function cadastraFaq($request)
    {
        DB::beginTransaction();

        try {

            $faq = Faq::create([
                'pergunta' => $request->pergunta,
                'resposta' => $request->resposta,
                'categoria' => $request->categoria,
                'ativo' => true
            ]);

            $faqs = FaqsResource::collection(Faq::orderBy('created_at', 'desc')->get());

            DB::commit();

            return [
                'status' => true,
                'http_code' => 200,
                'faqs' => $faqs
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function editaFaq($request)
    {
        DB::beginTransaction();

        try {

            $faq = Faq::find($request->id);
            $faq->pergunta = $request->pergunta;
            $faq->resposta = $request->resposta;
            $faq->categoria = $request->categoria;
            $faq->save();

            $faqs = FaqsResource::collection(Faq::orderBy('created_at', 'desc')->get());

            DB::commit();

            return [
                'status' => true,
                'http_code' => 200,
                'faqs' => $faqs
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function alteraStatus($id)
    {
        DB::beginTransaction();

        try {

            $faq = Faq::find($id);
            $faq->ativo = !$faq->ativo;
            $faq->save();

            $faqs = FaqsResource::collection(Faq::orderBy('created_at', 'desc')->get());

            DB::commit();

            return [
                'status' => true,
                'http_code' => 200,
                'faqs' => $faqs
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function deletaFaq($id)
    {
        DB::beginTransaction();

        try {

            $faq = Faq::find($id);
            $faq->delete();

            $faqs = FaqsResource::collection(Faq::orderBy('created_at', 'desc')->get());

            DB::commit();

            return [
                'status' => true,
                'http_code' => 200,
                'faqs' => $faqs
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }

    public function buscaPerguntasIntranet()
    {
        try {

            $faqs = FaqsResource::collection(
                Faq::where('ativo', true)->orderBy('created_at', 'desc')->get()
            );

            return [
                'status' => true,
                'http_code' => 200,
                'perguntas' => $faqs
            ];
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return [
                'status' => false,
                'erro' => $e,
                'http_code' => 500
            ];
        }
    }
}
