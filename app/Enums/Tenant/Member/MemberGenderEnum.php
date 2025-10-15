<?php

namespace App\Enums\Tenant\Member;

enum MemberGenderEnum: string
{
  case 男 = 'male';
  case 女 = 'female';

  public function label(): string
  {
    return match ($this) {
      self::男 => '男',
      self::女 => '女',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::男 => 'bg-label-primary',
      self::女 => 'bg-label-danger',
    };
  }
}
