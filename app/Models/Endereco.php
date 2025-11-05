<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Endereco extends Model
{
    protected $table = 'enderecos';

    protected $fillable = [
        'imovel_id',
        'cep',
        'endereco',
        'bairro',
        'cidade',
        'estado',
    ];
}
