<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceProduct;
use App\Repositories\Contracts\ServiceProductRepositoryInterface;

class ServiceProductRepository extends BaseRepository implements ServiceProductRepositoryInterface
{
    public function __construct(ServiceProduct $model)
    {
        parent::__construct($model);
    }
}
