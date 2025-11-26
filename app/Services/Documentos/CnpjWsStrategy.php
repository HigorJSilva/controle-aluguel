<?php

namespace App\Services\Documentos;

use App\Helpers\Formatacao;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class CnpjWsStrategy implements BuscaDocumentoStrategy
{
    public string $baseUrl = 'https://publica.cnpj.ws/cnpj/';

    public function buscar(string $documento): ?array
    {
        $documento = Formatacao::retornarDigitos($documento);
        try {
            $response = Http::timeout(3)->get("$this->baseUrl$documento");
        } catch (ConnectionException) {
            Log::warning('CnpjWsStrategy::buscar timeout');

            return null;
        }

        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }
}
