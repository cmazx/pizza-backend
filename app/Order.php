<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @property \App\MenuPositionOptionValue|\Illuminate\Database\Eloquent\Collection options
 */
class Order extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_DELIVERED = 'delivered';

    public function positions()
    {
        return $this->hasMany(OrderPosition::class);
    }
}
