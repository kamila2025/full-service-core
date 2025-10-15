<?php

namespace App\Enums\Tenant\Member;

enum MemberStatusEnum: string
{
  case 啟用 = 'active';
  case 停用 = 'inactive';

  public function label(): string
  {
    return match ($this) {
      self::啟用 => '啟用',
      self::停用 => '停用',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::啟用 => 'bg-label-primary',
      self::停用 => 'bg-label-danger',
    };
  }
}
