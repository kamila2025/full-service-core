<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
  protected $guarded = [];

  protected $casts = [
    'profile' => 'array',
  ];

  /**
   * 關聯的會員
   */
  public function member()
  {
    return $this->belongsTo(Member::class);
  }
}
