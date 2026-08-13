<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Servico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'servicos';

    protected $fillable = [
        'user_id',
        'nome',
        'documento',
        'tipoServico',
        'telefone',
        'endereco',
        'observacao',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
