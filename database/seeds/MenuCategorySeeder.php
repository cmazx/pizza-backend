<?php

use App\MenuCategory;
use App\MenuPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class MenuCategorySeeder extends Seeder
{
    const POSITIONS_NAMES = [
        'Pizza' => [
            'Margherita', 'Funghi', 'Capricciosa', 'Quattro Stagioni', 'Vegetariana', 'Quattro Formaggi', 'Marinara',
            'Peperoni', 'Napolitana', 'Hawaii', 'Maltija', 'Calzone', 'Rucola', 'Bolognese', 'Meat Feast', 'Kebabpizza',
            'Mexicana'
        ],
        'Drinks' => [
            'Cola', 'Ice-Tea', 'Beer', 'Tea', 'Coffee', 'Smoothie', 'Fresh', 'Juice'
        ],
        'Starters' => [
            'Prawns', 'Nuggets', 'Cheese', 'Doritos'
        ]
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $option = factory(App\MenuPositionOption::class)->create(
            [
                'name' => 'Size',
                'values' => ['S', 'M', 'L', 'XL']
            ]
        )->refresh();

        factory(MenuCategory::class)
            ->createMany([
                ['name' => 'Pizza', 'description' => 'Tasty pizza', 'position' => 0],
                ['name' => 'Drinks', 'description' => 'Icy drinks', 'position' => 1],
                ['name' => 'Starters', 'description' => 'Cool starters', 'position' => 2]
            ])
            ->each(function (MenuCategory $category) use ($option) {

                $createList = [];
                foreach (self::POSITIONS_NAMES[$category->name] as $posName) {
                    $createList[] = [
                        'menu_category_id' => $category->id,
                        'name' => $posName,
                        'image' => strtolower($category->name) . random_int(1, 8)
                    ];
                }

                $positions = factory(MenuPosition::class)->createMany($createList);
                $category->positions()->saveMany($positions);

                $positions->each(function (MenuPosition $position) use ($option) {
                    $position->options()->save(
                        factory(App\MenuPositionOptionValue::class)->create([
                            'menu_position_id' => $position->id,
                            'menu_position_option_id' => $option->id,
                            'value' => Collection::wrap($option->values)->random()
                        ])
                    );
                });

            });
    }
}
