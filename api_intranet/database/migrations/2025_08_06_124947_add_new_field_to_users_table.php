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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('ativo')->nullable()->default(true);
            $table->string('sobrenome')->nullable();
            $table->string('cpf')->nullable();
            $table->string('cargo')->nullable();
            $table->string('imagem')->nullable();
            $table->date('dt_nascimento')->nullable();
            $table->date('dt_admissao')->nullable();
            $table->unsignedBigInteger('departamento_id')->nullable();

            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
