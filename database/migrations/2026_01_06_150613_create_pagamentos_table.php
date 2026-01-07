<?php

declare(strict_types=1);

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
        Schema::create('pagamentos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes');
            $table->date('data_pagamento')->nullable();
            $table->date('data_vencimento');
            $table->date('data_referencia');
            $table->decimal('valor', 15, 2);
            $table->mediumText('descricao')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
