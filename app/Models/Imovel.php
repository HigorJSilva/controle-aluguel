<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Imovel extends Model
{
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
}
