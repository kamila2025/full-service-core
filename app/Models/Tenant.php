<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Model;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * 自定義列
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'sort',
            'user_id',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * 租戶管理員
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function (Model $model) {
            $model->sort = $model->newQuery()->max('sort') + 1;
        });
    }
}
