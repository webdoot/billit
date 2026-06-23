<?php

namespace App\Repositories\Eloquent;

use App\Models\Server;
use App\Repositories\Contracts\ServerRepositoryInterface;

class ServerRepository extends BaseRepository implements ServerRepositoryInterface
{
    public function __construct(Server $model)
    {
        parent::__construct($model);
    }
}
