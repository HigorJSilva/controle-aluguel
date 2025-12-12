<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locacoes';

    protected $fillable = [
        'imovel_id',
        'inquilino_id',
        'valor',
        'dia_vencimento',
        'data_inicio',
        'data_fim',
        'status',
        'dias_antecedencia_geracao',
        'proxima_geracao_fatura',
        'proxima_fatura',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'imovel_id');
    }

     public function inquilino(): BelongsTo
    {
        return $this->belongsTo(Inquilino::class, 'inquilino_id');
    }
}
