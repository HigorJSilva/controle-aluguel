<?php

declare(strict_types=1);

namespace App\DTO\Pagamento;

final readonly class EditPagamentoDTO
{
    public function __construct(
        public ?string $dataPagamento,
        public string $dataVencimento,
        public string $dataReferencia,
        public string $valor,
        public ?string $descricao,
        public string $status,
    ) {}

    public function toArray(): array
    {
        return [
            'data_pagamento' => $this->dataPagamento,
            'data_vencimento' => $this->dataVencimento,
            'data_referencia' => $this->dataReferencia,
            'valor' => $this->valor,
            'descricao' => $this->descricao,
            'status' => $this->status,
        ];
    }
}
