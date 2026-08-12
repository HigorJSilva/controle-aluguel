<?php

declare(strict_types=1);

namespace App\DTO\Servico;

final readonly class CreateServicoDTO
{
    public function __construct(
        public string $nome,
        public string $documento,
        public int $userId,
        public ?string $telefone = null,
        public ?string $tipoServico = null,
        public ?string $endereco = null,
        public ?string $observacao = null,
    ) {}

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'documento' => $this->documento,
            'user_id' => $this->userId,
            'telefone' => $this->telefone,
            'tipoServico' => $this->tipoServico,
            'endereco' => $this->endereco,
            'observacao' => $this->observacao,
        ];
    }
}
