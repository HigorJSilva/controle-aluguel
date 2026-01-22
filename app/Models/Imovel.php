<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Imovel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'imoveis';

    protected $fillable = [
        'titulo',
        'tipo',
        'user_id',
        'valor_aluguel_sugerido',
        'quartos',
        'banheiros',
        'area',
        'status',
        'descricao',
    ];

    public function endereco(): HasOne
    {
        return $this->hasOne(Endereco::class, 'imovel_id');
    }

    public function locacaoAtiva(): HasOne
    {
        return $this->hasOne(Locacao::class)
            ->where('status', true)
            ->latest();
    }

    public function locacao(): HasOne
    {
        return $this->hasOne(Locacao::class)
            ->where('status', true)
            ->latest();
    }
}
