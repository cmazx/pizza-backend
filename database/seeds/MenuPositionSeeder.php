<?php

use App\MenuPosition;
use Illuminate\Database\Seeder;

class MenuPositionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $option = factory(App\MenuPositionOption::class, 1)->create(['Size']);
        factory(App\MenuPosition::class, 5)
            ->create()
            ->each(function (MenuPosition $position) use ($option) {
                $position->options()->save(
                    factory(App\MenuPositionOptionValue::class)->make([
                        'menu_position_id' => $position->id,
                        'menu_position_option_id' => $option->id,
                        'value' => 'L',
                        'price' => 0
                    ])
                );
            });
    }
}
