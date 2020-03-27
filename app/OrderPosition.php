<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @property \App\MenuPositionOptionValue|\Illuminate\Database\Eloquent\Collection options
 */
class OrderPosition extends Model
{
    protected array $fillable = [
        'position_id',
        'count',
        'name',
        'price',
        'priceUSD'
    ];

}
