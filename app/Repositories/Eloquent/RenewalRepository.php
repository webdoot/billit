<?php

namespace App\Repositories\Eloquent;

use App\Models\Renewal;
use App\Repositories\Contracts\RenewalRepositoryInterface;

class RenewalRepository extends BaseRepository implements RenewalRepositoryInterface
{
    public function __construct(Renewal $model)
    {
        parent::__construct($model);
    }
}
