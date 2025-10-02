<?php

namespace App\Repositories;

use App\Repositories\Criteria\CustomRequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

abstract class Repository extends BaseRepository
{
    public function boot()
    {
        parent::boot();

        $this->pushCriteria(app(CustomRequestCriteria::class));
    }
}
