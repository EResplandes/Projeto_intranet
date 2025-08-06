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
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->text('tarefa');
            $table->unsignedBigInteger('solicitante_id');
            $table->unsignedBigInteger('destinatario_id');
            $table->unsignedBigInteger('status_id');

            $table->foreign('solicitante_id')->references('id')->on('users');
            $table->foreign('destinatario_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('status_tarefas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
