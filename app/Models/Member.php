<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
  use SoftDeletes;

  protected $guarded = [];

  protected $casts = [
    'birthday' => 'date',
    'gender'   => \App\Enums\Tenant\Member\MemberGenderEnum::class,
    'status'   => \App\Enums\Tenant\Member\MemberStatusEnum::class,
  ];

  public function lineUser()
  {
    return $this->belongsTo(LineUser::class);
  }

  protected static function booted()
  {
    static::creating(function ($model) {
      $model->status = \App\Enums\Tenant\Member\MemberStatusEnum::啟用;
    });
  }
}
