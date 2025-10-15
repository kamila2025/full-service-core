<?php

namespace App\Enums\Tenant\Member;

enum MemberStatusEnum: string
{
  case 正常 = 'active';
  case 停用 = 'inactive';

  public function label(): string
  {
    return match ($this) {
      self::正常 => '正常',
      self::停用 => '停用',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::正常 => 'bg-label-primary',
      self::停用 => 'bg-label-danger',
    };
  }
}
