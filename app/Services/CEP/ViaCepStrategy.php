<?php

declare(strict_types=1);

namespace App\Services\CEP;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class ViaCepStrategy implements BuscaCepStrategy
{
    public function buscar(string $cep): ?array
    {

        try {
            $response = Http::timeout(10)->get("viacep.com.br/ws/$cep/json/");
        } catch (ConnectionException) {
            Log::warning('ConsultasHelper::cep timeout');

            return null;
        }

        return $response->json();
    }
}
