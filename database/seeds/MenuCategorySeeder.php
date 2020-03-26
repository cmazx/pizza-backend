<?php

use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\MenuCategory::class, 5)->create();
    }
}
