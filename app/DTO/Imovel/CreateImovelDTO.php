<?php

declare(strict_types=1);

namespace App\DTO\Imovel;

final readonly class CreateImovelDTO
{
    public function __construct(
        public string $titulo,
        public string $tipo,
        public int $userId,
        public string $valorAluguelSugerido,
        public ?int $quartos,
        public ?int $banheiros,
        public ?string $area,
        public string $status,
        public ?string $descricao,
        public string $cep,
        public string $endereco,
        public string $bairro,
        public string $cidade,
        public string $estado,
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
