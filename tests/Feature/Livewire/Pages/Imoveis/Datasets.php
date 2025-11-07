<?php

declare(strict_types=1);

use App\Enums\StatusImoveis;
use App\Enums\TiposImoveis;

dataset('required_fields', [
    'titulo' => ['titulo', 'required'],
    'tipo' => ['tipo', 'required'],
    'endereco' => ['endereco', 'required'],
    'bairro' => ['bairro', 'required'],
    'cidade' => ['cidade', 'required'],
    'cep' => ['cep', 'required'],
    'valorAluguelSugerido' => ['valorAluguelSugerido', 'required'],
    'status' => ['status', 'required'],
]);

dataset('max_length_fields', [
    'titulo max 255' => ['titulo', fake()->name . str_repeat('a', 255)],
    'endereco max 255' => ['endereco', fake()->name . str_repeat('a', 255)],
    'bairro max 100' => ['bairro', fake()->name . str_repeat('a', 100)],
    'estado max 2' => ['estado', fake()->regexify('[A-Za-z0-9]{3}')],
    'descricao max 2000' => ['descricao', (fake()->realText(2001))],
]);

dataset('imovel_basico', [
    [
        'titulo' => 'Novo apartamento',
        'tipo' => TiposImoveis::APARTAMENTO->value,
        'endereco' => 'Rua do meu apartamento',
        'bairro' => 'Centro',
        'cidade' => '5201108',
        'cep' => '75020-040',
        'valorAluguelSugerido' => '1000.00',
        'status' => StatusImoveis::DISPONIVEL->value,
    ],
]);
