<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPagamentos: string
{
    case PENDENTE = 'pendente';
    case RECEBIDO = 'recebido';
    case ATRASADO = 'atrasado';
    case RECEBIDO_PARCIALMENTE = 'recebido_parcial';
    case CANCELADO = 'cancelado';

    public static function all(string $key = 'id', string $value = 'name'): array
    {
        return [
            [$key => self::PENDENTE, $value => self::PENDENTE->label()],
            [$key => self::RECEBIDO, $value => self::RECEBIDO->label()],
            [$key => self::RECEBIDO_PARCIALMENTE, $value => self::RECEBIDO_PARCIALMENTE->label()],
            [$key => self::ATRASADO, $value => self::ATRASADO->label()],
            [$key => self::CANCELADO, $value => self::CANCELADO->label()],
        ];
    }

    public static function getCssClass(string $status): string
    {
        return match ($status) {
            self::RECEBIDO->value => 'badge-success badge-outline',
            self::RECEBIDO_PARCIALMENTE->value => 'badge-success badge-dash',
            self::ATRASADO->value => 'badge-error badge-outline',
            self::PENDENTE->value => 'badge-warning badge-outline',
            self::CANCELADO->value => 'badge-error badge-soft',
            default => 'badge-error badge-outline'
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::RECEBIDO => 'Recebido',
            self::RECEBIDO_PARCIALMENTE => 'Recebido parcialmente',
            self::ATRASADO => 'Atrasado',
            self::CANCELADO => 'Cancelado',
        };
    }
}
