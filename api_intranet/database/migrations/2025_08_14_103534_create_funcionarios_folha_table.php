<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funcionarios_folha', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folha_id');
            $table->unsignedBigInteger('funcionario_id');
            $table->float('total_proventos');
            $table->float('total_descontos');
            $table->float('total_liquido');
            $table->float('salario_base');
            $table->string('nome');
            $table->foreign('folha_id')->references('id')->on('folhas_pagamento');
            $table->foreign('funcionario_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios_folha');
    }
};
