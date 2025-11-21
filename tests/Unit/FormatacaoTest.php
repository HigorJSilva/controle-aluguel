<?php

declare(strict_types=1);

use App\Helpers\Formatacao;

test('Cidades devem retornar dado um id válido', function ($documento) {

    $formatado = Formatacao::documento($documento);

    if (mb_strlen($documento) === 11) {
        expect($formatado)->toBe('488.479.297-17');

        return;
    }

    expect($formatado)->toBe('12.346.238/2680-40');
})->with(
    [
        'para cnpj' => ['documento' => '12346238268040'],
        'para CPF' => ['documento' => '48847929717'],
    ]
);

test('Cidades devem retornar null dado um id inválido', function ($telefone) {

    $formatado = Formatacao::telefone($telefone);

    if (mb_strlen($telefone) === 11) {
        expect($formatado)->toBe('(62) 99999-9999');

        return;
    }

    expect($formatado)->toBe('(62) 9999-9999');
})->with(
    [
        'para cnpj' => ['telefone' => '62999999999'],
        'para CPF' => ['telefone' => '6299999999'],
    ]
);
