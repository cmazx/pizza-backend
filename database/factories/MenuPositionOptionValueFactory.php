<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MenuPositionOptionValue;
use Faker\Generator as Faker;

$factory->define(MenuPositionOptionValue::class, function (Faker $faker) {

    return [
        'menu_position_id' => 1,
        'menu_position_option_id' => 1,
        'value' => 'VALUE0',
        'price' => 0
    ];
});
