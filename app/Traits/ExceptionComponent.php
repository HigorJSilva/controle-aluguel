<?php

declare(strict_types=1);

namespace App\Traits;

use DomainException;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

trait ExceptionComponent
{
    public function exception($e, $stopPropagation): void
    {
        if ($e instanceof DomainException) {
            $this->error($e->getMessage(), timeout: 5000);
            $stopPropagation();

            return;
        }

        $this->error('Houve um erro ao realizar a operação', timeout: 5000);
        error('ERROR AT: ' . $this->__name . ' ' . $e->getMessage());
        Log::error('ERROR AT: ' . $this->__name . ' ', [$e->getFile() . $e->getLine(), $e->getMessage()]);

        $stopPropagation();
    }
}
