<?php

declare(strict_types=1);

use App\Services\Documentos\BuscaDocumentoStrategy;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

const CNPJ_CORREIOS = '34.028.316/0001-03';

test('Deve retorar dados da empresa caso CNPJ seja válido', function () {

    $service = app()->make(BuscaDocumentoStrategy::class);
    $baseUrl = $service->baseUrl;

    Http::fake([
        $baseUrl . '34028316000103' => Http::response([
            'estabelecimento' => [
                'telefone1' => '32144316',
                'email' => 'acgtescnpj@correios.com.br',
            ],
        ], 200),
    ]);

    $response = $service->buscar(CNPJ_CORREIOS);

    expect($response)->not()->toBeNull();

    expect($response['estabelecimento']['telefone1']);
    expect($response['estabelecimento']['email']);
});

test('Deve retorar null caso tenha timeout ou ConnectionException', function () {
    $service = app()->make(BuscaDocumentoStrategy::class);
    $baseUrl = $service->baseUrl;

    Http::fake([
        $baseUrl . '34028316000103' => function () {
            throw new ConnectionException();
        },
    ]);

    $service = app()->make(BuscaDocumentoStrategy::class);
    $response = $service->buscar(CNPJ_CORREIOS);

    expect($response)->toBeNull();
});

test('Deve retorar dados da empresa caso CNPJ seja inválido', function () {
    $service = app()->make(BuscaDocumentoStrategy::class);
    $baseUrl = $service->baseUrl;

    Http::fake([
        $baseUrl . '11111111111' => Http::response([
            'status' => 400,
            'titulo' => 'Requisição inválida',
            'detalhes' => 'CNPJ inválido',
        ], 400),
    ]);

    $service = app()->make(BuscaDocumentoStrategy::class);
    $response = $service->buscar('11111111111');

    expect($response)->toBeNull();
});
