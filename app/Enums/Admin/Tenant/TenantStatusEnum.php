<?php

namespace App\Enums\Admin\Tenant;

enum TenantStatusEnum: string
{
    case 已開通 = 'activated';
    case 未開通 = 'unactivated';

    public function label(): string
    {
        return match ($this) {
            self::已開通 => '已開通',
            self::未開通 => '未開通',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::已開通 => 'bg-label-primary',
            self::未開通 => 'bg-label-secondary',
        };
    }
}
