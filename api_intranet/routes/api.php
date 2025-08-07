<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvisosController;
use App\Http\Controllers\ColaboradoresController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('painel')->group(function () {

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
        Route::put('/editar/{id}', [ColaboradoresController::class, 'editaColaborador']);
        Route::get('/indicadores', [ColaboradoresController::class, 'buscaIndicadores']);
    });
});
