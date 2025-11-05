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
        Schema::create('enderecos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('imovel_id')->references('id')->on('imoveis')->constrained()->onDelete('cascade');
            $table->string('cep', 8);
            $table->string('endereco', 255);
            $table->string('bairro', 100);
            $table->string('cidade', 8);
            $table->string('estado', 2);
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
