<?php

namespace App\Repositories\Eloquent;

use App\Models\Hosting;
use App\Repositories\Contracts\HostingRepositoryInterface;

class HostingRepository extends BaseRepository implements HostingRepositoryInterface
{
    public function __construct(Hosting $model)
    {
        parent::__construct($model);
    }
}
