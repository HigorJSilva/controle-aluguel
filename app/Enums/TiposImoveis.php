<?php

declare(strict_types=1);

namespace App\Enums;

enum TiposImoveis: string
{
    case APARTAMENTO = '1';
    case KITNET = '2';
    case CASA = '3';
    case COMERCIAL = '4';
    case SALA_COMERCIAL = '5';

    public static function fromLabel(string $label): ?self
    {
        return match (mb_strtolower($label)) {
            'Apartamento' => self::APARTAMENTO,
            'Kitnet' => self::KITNET,
            'Casa' => self::CASA,
            'Comercial' => self::COMERCIAL,
            'Sala comercial' => self::SALA_COMERCIAL,
            default => null,
        };
    }

    public static function all(string $key = 'id', string $value = 'name'): array
    {
        return [
            [$key => self::APARTAMENTO, $value => self::APARTAMENTO->label()],
            [$key => self::KITNET, $value => self::KITNET->label()],
            [$key => self::CASA, $value => self::CASA->label()],
            [$key => self::COMERCIAL, $value => self::COMERCIAL->label()],
            [$key => self::SALA_COMERCIAL, $value => self::SALA_COMERCIAL->label()],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::APARTAMENTO => 'Apartamento',
            self::KITNET => 'Kitnet',
            self::CASA => 'Casa',
            self::COMERCIAL => 'Comercial',
            self::SALA_COMERCIAL => 'Sala comercial',
        };
    }
}
