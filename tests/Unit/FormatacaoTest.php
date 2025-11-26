<?php

declare(strict_types=1);

use App\Helpers\Formatacao;

test('Formatacao::documento deve retornar valor de documento formatado para frontend', function ($documento) {

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

test('Formatacao::telefone deve retornar valor de telefone formatado para frontend', function ($telefone) {

    $formatado = Formatacao::telefone($telefone);

    if (mb_strlen($telefone) === 11) {
        expect($formatado)->toBe('(62) 99999-9999');

        return;
    }

    expect($formatado)->toBe('(62) 9999-9999');
})->with(
    [
        'para celular' => ['telefone' => '62999999999'],
        'para telefoneFixo' => ['telefone' => '6299999999'],
    ]
);

test('Formatacao::retornarDigitos deve retornar valores sem formatação', function ($value) {
    $formatado = Formatacao::retornarDigitos($value);

    if(is_array($formatado)){
        expect($formatado)->each->toBeDigits();
        return;
    }

    expect($formatado)->toBeDigits();
})->with(
    [
        'para CPF' => ['value' => '488.479.297-17'],
        'para CNPJ' => ['value' => '12.346.238/2680-40'],
        'para CEP' => ['value' => '71920-230'],
        'para array' => ['value' => ['488.479.297-17', '12.346.238/2680-40', '71920-230' ]],
    ]
);
