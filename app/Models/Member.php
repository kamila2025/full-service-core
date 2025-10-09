<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
  protected $guarded = [];

  /**
   * 關聯的 Line 用戶
   */
  public function lineUsers()
  {
    return $this->hasMany(LineUser::class);
  }
}
