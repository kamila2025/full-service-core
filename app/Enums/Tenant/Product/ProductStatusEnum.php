<?php

namespace App\Enums\Tenant\Product;

enum ProductStatusEnum: string
{
    case 已發佈 = 'active';
    case 未發佈 = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::已發佈 => '已發佈',
            self::未發佈 => '未發佈',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::已發佈 => 'bg-label-primary',
            self::未發佈 => 'bg-label-secondary',
        };
    }
}
