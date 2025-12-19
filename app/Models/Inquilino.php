<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Inquilino extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inquilinos';

    protected $fillable = [
        'user_id',
        'nome',
        'documento',
        'email',
        'telefone',
        'observacao',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function locacao(): HasMany
    {
        return $this->hasMany(Locacao::class, 'inquilino_id');
    }

    public function locacaoAtiva(): HasOne
    {
        return $this->hasOne(Locacao::class)
            ->where('status', true)
            ->latest();
    }
}
