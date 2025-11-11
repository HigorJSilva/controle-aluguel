<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case SUSPENDED = 3;

    public static function all(string $key = 'id', string $value = 'name'): array
    {
        return [
            [$key => self::ACTIVE, $value => self::ACTIVE->label()],
            [$key => self::INACTIVE, $value => self::INACTIVE->label()],
            [$key => self::SUSPENDED, $value => self::SUSPENDED->label()],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }
}
