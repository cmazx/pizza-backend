<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MenuCategory;
use Faker\Generator as Faker;

$factory->define(MenuCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName,
        'description' => $faker->unique()->text,
        'order' => $faker->unique()->numberBetween(0, 127)
    ];
});
