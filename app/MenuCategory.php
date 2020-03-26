<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuCategory
 * @method static \Illuminate\Database\Eloquent\Builder ordered
 */
class MenuCategory extends Model
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
