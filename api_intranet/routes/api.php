<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvisosController;

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
    });
});
