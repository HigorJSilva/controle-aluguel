<?php

declare(strict_types=1);

use App\Helpers\Cidades;

test('Cidades devem retornar dado um id válido', function () {

    $cidade = Cidades::getById('5201108');

    expect($cidade['nome'])->toBe('Anápolis');
});

test('Cidades devem retornar null dado um id inválido', function () {

    $cidade = Cidades::getById('5201108223');

    expect($cidade)->toBe(null);
});
