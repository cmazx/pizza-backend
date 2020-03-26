<?php

namespace Tests\Feature;

use App\MenuCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MenuCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        $categories = factory(MenuCategory::class, 2)->createMany([]);
        $this->get('/api/v1/categories')
            ->assertStatus(200)
            ->assertJson($categories->sortBy('order')->toArray());
    }
}
