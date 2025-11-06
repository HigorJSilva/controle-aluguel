<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusImoveis: string
{
    case DISPONIVEL = '1';
    case ALUGADO = '2';
    case AGUARDANDO_LOCACAO = '3';
    case EM_MANUTENCAO = '4';
    case INDISPONIVEL = '5';

    public static function fromLabel(string $label): ?self
    {
        return match (mb_strtolower($label)) {
            'Disponível' => self::DISPONIVEL,
            'Alugado' => self::ALUGADO,
            'Aguandando locação' => self::AGUARDANDO_LOCACAO,
            'Em manutenção' => self::EM_MANUTENCAO,
            'Indisponível' => self::INDISPONIVEL,
            default => null,
        };
    }

    public static function all(string $key = 'id', string $value = 'name'): array
    {
        return [
            [$key => self::DISPONIVEL, $value => self::DISPONIVEL->label()],
            [$key => self::ALUGADO, $value => self::ALUGADO->label()],
            [$key => self::AGUARDANDO_LOCACAO, $value => self::AGUARDANDO_LOCACAO->label()],
            [$key => self::EM_MANUTENCAO, $value => self::EM_MANUTENCAO->label()],
            [$key => self::INDISPONIVEL, $value => self::INDISPONIVEL->label()],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::DISPONIVEL => 'Disponível',
            self::ALUGADO => 'Alugado',
            self::AGUARDANDO_LOCACAO => 'Aguandando locação',
            self::EM_MANUTENCAO => 'Em manutenção',
            self::INDISPONIVEL => 'Indisponível',
        };
    }
}
