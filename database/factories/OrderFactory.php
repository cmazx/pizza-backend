<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Order::class, function (Faker $faker) {

    $array = [Order::STATUS_APPROVED, Order::STATUS_DELIVERED, Order::STATUS_NEW];
    $status = $array[array_rand($array)];

    return [
        'token' => base64_encode(Str::random(40)),
        'phone' => $faker->unique()->e164PhoneNumber,
        'address' => $faker->unique()->address,
        'status' => $status,
        'delivered_at' => $status == Order::STATUS_DELIVERED ? date('Y-m-d H:i:s') : null
    ];
});


