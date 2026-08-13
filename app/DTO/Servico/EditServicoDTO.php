<?php

declare(strict_types=1);

namespace App\DTO\Servico;

final readonly class EditServicoDTO
{
    public function __construct(
        public string $nome,
        public string $documento,
        public ?string $telefone,
        public string $tipoServico,
        public ?string $endereco = null,
        public ?string $observacao = null,
    ) {}

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'documento' => $this->documento,
            'telefone' => $this->telefone,
            'endereco' => $this->endereco,
            'tipoServico' => $this->tipoServico,
            'observacao' => $this->observacao,
        ];
    }
}
