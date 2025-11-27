<?php

declare(strict_types=1);

use App\Rules\CnpjCpfRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

const CNPJ = '34.028.316/0001-03';
const CNPJ_INVALIDO = '21.121.1212/2311-03';
const CNPJ_DIGITO_INVALIDO = '58.837.739/2419-39';
const CPF = '661.301.443-50';
const CPF_INVALIDO = '121.121.212-11';
const CPF_DIGITO_INVALIDO = '963.973.461-10';
const DOCUMENTO_GENERICO = '213123123131231231332';

test('deve retornar true para um documento válido', function ($documento) {

    $validator = Validator::make(['documento' => $documento], [
        'documento' => ['required', new CnpjCpfRule],
    ]);

    expect($validator->fails())->toBe(false);
})->with(
    [
        'para CNPJ' => CNPJ,
        'para CPF' => CPF,
    ]
);

test('deve retornar false para um documento inválido', function ($documento) {

    $validator = Validator::make(['documento' => $documento], [
        'documento' => ['required', new CnpjCpfRule],
    ]);

    expect($validator->fails())->toBe(true);
})->with(
    [
        'para CNPJ' => CNPJ_INVALIDO,
        'para CNPJ dígito inválido' => CNPJ_DIGITO_INVALIDO,
        'para CPF' => CNPJ_INVALIDO,
        'para CPF dígito inválido' => CPF_DIGITO_INVALIDO,
        'para documento qualquer' => DOCUMENTO_GENERICO,
    ]
);
