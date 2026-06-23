<?php

namespace App\Repositories\Eloquent;

use App\Models\Domain;
use App\Repositories\Contracts\DomainRepositoryInterface;

class DomainRepository extends BaseRepository implements DomainRepositoryInterface
{
    public function __construct(Domain $model)
    {
        parent::__construct($model);
    }
}
