<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerService;
use App\Repositories\Contracts\CustomerServiceRepositoryInterface;

class CustomerServiceRepository extends BaseRepository implements CustomerServiceRepositoryInterface
{
    public function __construct(CustomerService $model)
    {
        parent::__construct($model);
    }
}
