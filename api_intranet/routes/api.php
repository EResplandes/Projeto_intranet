<?php

use App\Http\Controllers\AutenticacaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvisosController;
use App\Http\Controllers\ColaboradoresController;
use App\Http\Controllers\FaqController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('painel')->group(function () {

    Route::prefix('autenticacao')->group(function () {
        Route::post('/login', [AutenticacaoController::class, 'login']);
    });

    Route::prefix('avisos')->group(function () {
        Route::get('/', [AvisosController::class, 'buscaTodosAvisos']);
        Route::post('/cadastrar-aviso', [AvisosController::class, 'cadastraAviso']);
        Route::post('/cadastrar-categoria', [AvisosController::class, 'cadastraCategoriaAviso']);
        Route::get('/busca-categorais', [AvisosController::class, 'buscaCategorias']);
        Route::get('/alterar-status/{id}', [AvisosController::class, 'alteraStatusAviso']);
        Route::delete('/{id}', [AvisosController::class, 'deletaAviso']);
        Route::get('/indicadores', [AvisosController::class, 'buscaIndicadores']);
        Route::put('/editar/{id}', [AvisosController::class, 'editaAviso']);
    });

    Route::prefix('colaboradores')->group(function () {
        Route::get('/', [ColaboradoresController::class, 'buscaTodosColaboradores']);
        Route::get('/departamentos', [ColaboradoresController::class, 'buscaDepartamentos']);
        Route::post('/cadastrar', [ColaboradoresController::class, 'cadastraColaborador']);
        Route::post('/cadastrar-departamento', [ColaboradoresController::class, 'cadastraDepartamento']);
        Route::get('/alterar-status/{id}', [ColaboradoresController::class, 'alteraStatusColaborador']);
        Route::delete('/{id}', [ColaboradoresController::class, 'deletaColaborador']);
        Route::get('/indicadores', [ColaboradoresController::class, 'buscaIndicadores']);
        Route::post('/editar/{id}', [ColaboradoresController::class, 'editaColaborador']);
        Route::get('/indicadores', [ColaboradoresController::class, 'buscaIndicadores']);
    });

    Route::prefix('faq')->group(function () {
        Route::get('/', [FaqController::class, 'buscaFaqs']);
        Route::get('/categorias', [FaqController::class, 'buscaCategorias']);
        Route::post('/cadastrar', [FaqController::class, 'cadastraFaq']);
        Route::put('/editar/{id}', [FaqController::class, 'editaFaq']);
        Route::get('/indicadores', [FaqController::class, 'buscaIndicadores']);
        Route::get('/alterar-status/{id}', [FaqController::class, 'alteraStatus']);
        Route::delete('/{id}', [FaqController::class, 'deletaFaq']);
    });
});

Route::prefix('intranet')->group(function () {

    Route::prefix('avisos')->group(function () {
        Route::get('/busca-avisos', [AvisosController::class, 'buscaAvisosIntranet']);
    });

    Route::prefix('colaboradores')->group(function () {
        Route::get('/busca-anivesariantes', [ColaboradoresController::class, 'buscaAniversariantes']);
        Route::get('/busca-colaboradores-mural', [ColaboradoresController::class, 'buscaColaboradoresMural']);
    });

    Route::prefix('faq')->group(function () {
        Route::get('/busca-perguntas', [FaqController::class, 'buscaPerguntasIntranet']);
    });
});
