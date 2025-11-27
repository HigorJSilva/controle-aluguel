<?php

declare(strict_types=1);

namespace App\DTO\Inquilino;

final readonly class CreateInquilinoDTO
{
    public function __construct(
        public string $nome,
        public string $documento,
        public int $userId,
        public ?string $telefone = null,
        public ?string $email = null,
        public ?string $observacao = null,
    ) {}

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'documento' => $this->documento,
            'user_id' => $this->userId,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'observacao' => $this->observacao,
        ];
    }
}
