<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OrderPosition;
use Faker\Generator as Faker;

$factory->define(OrderPosition::class, function (Faker $faker) {

    $cents = random_int(1, 1000);

    return [
        'order_id' => 1,
        'position_id' => 1,
        'count' => 1,
        'name' => $faker->unique()->colorName,
        'price' => $cents / 10,
        'priceUSD' => ($cents * 1.11) / 10
    ];
});


