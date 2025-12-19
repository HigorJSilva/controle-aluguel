<?php

declare(strict_types=1);

namespace App\DTO\Locacao;

final readonly class CreateLocacaoDTO
{

    public function __construct(
        public int $imovelId,
        public int $inquilinoId,
        public string $valor,
        public string $diaVencimento,
        public string $dataInicio,
        public ?string $dataFim = null,
        public bool $status = true,
        public string $diasAntecedenciaGeracao,
        public ?string $proximaGeracaoFatura = null,
        public ?string $proximaFatura = null,
    ) {}

    public function toArray(): array
    {
        return [
            'imovel_id' => $this->imovelId,
            'inquilino_id' => $this->inquilinoId,
            'valor' => $this->valor,
            'dia_vencimento' => $this->diaVencimento,
            'data_inicio' => $this->dataInicio,
            'data_fim' => $this->dataFim,
            'status' => $this->status,
            'dias_antecedencia_geracao' => $this->diasAntecedenciaGeracao,
            'proxima_geracao_fatura' => $this->proximaGeracaoFatura,
            'proxima_fatura' => $this->proximaFatura,
        ];
    }
}
