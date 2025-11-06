<?php

declare(strict_types=1);

namespace App\Services\CEP;

interface BuscaCepStrategy
{
    public function buscar(string $cep): ?array;
}
