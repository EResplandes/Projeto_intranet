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
        Schema::create('folhas_pagamento', function (Blueprint $table) {
            $table->id();
            $table->integer('mes');
            $table->integer('ano');
            $table->datetime('dt_importacao');
            $table->string('arquivo_original');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folhas_pagamento');
    }
};
