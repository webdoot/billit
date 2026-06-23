<?php

namespace App\Repositories\Eloquent;

use App\Models\Receipt;
use App\Repositories\Contracts\ReceiptRepositoryInterface;

class ReceiptRepository extends BaseRepository implements ReceiptRepositoryInterface
{
    public function __construct(Receipt $model)
    {
        parent::__construct($model);
    }
}
