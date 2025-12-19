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
        Schema::create('locacoes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('imovel_id')->references('id')->on('imoveis')->index('locacoes_imovel_id_index');
            $table->foreignId('inquilino_id')->references('id')->on('inquilinos')->index('locacoes_inquilino_id_index');
            $table->decimal('valor', 15, 2);
            $table->string('dia_vencimento', 2);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('status')->default(true);
            $table->string('dias_antecedencia_geracao', 2);
            $table->date('proxima_geracao_fatura')->nullable();
            $table->date('proxima_fatura')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacoes');
    }
};
