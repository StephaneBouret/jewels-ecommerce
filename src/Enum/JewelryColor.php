<?php

declare(strict_types=1);

namespace App\Enum;

enum JewelryColor: string
{
    case SILVER = 'silver';
    case GOLD = 'gold';

    public function label(): string
    {
        return match ($this) {
            self::SILVER => 'Argent',
            self::GOLD   => 'Or',
        };
    }

    public static function values(): array
    {
        return array_map(static fn(self $c) => $c->value, self::cases());
    }
}
