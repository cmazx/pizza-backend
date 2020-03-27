<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuPosition
 *
 * @method static Builder activeSorted
 * @method static Builder active
 * @property \App\MenuPositionOptionValue|\Illuminate\Database\Eloquent\Collection options
 */
class MenuPosition extends Model
{
    public $timestamps = false;
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveSorted(Builder $query): Builder
    {
        return $this->scopeActive($query)->orderBy('position');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', true);
    }

    public function options()
    {
        return $this->hasMany(MenuPositionOptionValue::class);
    }
}
