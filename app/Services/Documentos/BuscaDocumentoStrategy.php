<?php

declare(strict_types=1);

namespace App\Services\Documentos;

interface BuscaDocumentoStrategy
{
    public string $baseUrl { get; }
    public function buscar(string $documento): ?array;
}
