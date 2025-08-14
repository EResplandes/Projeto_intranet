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
        Schema::create('itens_folha', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('funcionario_folha_id');
            $table->string('descricao');
            $table->enum('tipo', ['Provento', 'Desconto']);
            $table->float('valor');
            $table->string('horas_qtd');
            $table->foreign('funcionario_folha_id')->references('id')->on('funcionarios_folha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_folha');
    }
};
