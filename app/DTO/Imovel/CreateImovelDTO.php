<?php

declare(strict_types=1);

namespace App\DTO\Imovel;

final class CreateImovelDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $tipo,
        public readonly int $userId,
        public readonly string $valorAluguelSugerido,
        public readonly ?int $quartos = null,
        public readonly ?int $banheiros = null,
        public readonly ?string $area = null,
        public readonly string $status,
        public readonly ?string $descricao = null,
        public readonly string $cep,
        public readonly string $endereco,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $estado,
    ) {}

    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'tipo' => $this->tipo,
            'user_id' => $this->userId,
            'valor_aluguel_sugerido' => $this->valorAluguelSugerido,
            'endereco' => [
                'cep' => $this->cep,
                'endereco' => $this->endereco,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
            ],
            'quartos' => $this->quartos,
            'banheiros' => $this->banheiros,
            'area' => $this->area,
            'status' => $this->status,
            'descricao' => $this->descricao,
        ];
    }
}
