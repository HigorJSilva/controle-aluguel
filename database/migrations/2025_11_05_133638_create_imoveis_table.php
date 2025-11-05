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
        Schema::create('imoveis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('titulo', 255);
            $table->string('tipo', 2)->default('3');
            $table->decimal('valor_aluguel_sugerido', 15, 2)->nullable();
            $table->string('quartos', 3)->nullable();
            $table->string('banheiros', 3)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('status', 3)->default('disponivel');
            $table->text('descricao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
