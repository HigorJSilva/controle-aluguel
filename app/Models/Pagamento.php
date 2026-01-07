<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Pagamento extends Model
{
    /** @use HasFactory<\Database\Factories\PagamentoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'locacao_id',
        'data_pagamento',
        'data_vencimento',
        'data_referencia',
        'valor',
        'descricao',
        'status',
    ];

     public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class, 'locacao_id');
    }
}
