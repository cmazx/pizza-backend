<?php

namespace Tests\Feature;

use App\MenuCategory;
use App\MenuPosition;
use App\MenuPositionOptionValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MenuPositionsTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        $categories = factory(MenuCategory::class, 2)->create();
        $firstCategory = $categories->get(0);
        $secondCategory = $categories->get(1);

        $positions = factory(MenuPosition::class, 2)
            ->create(['menu_category_id' => $firstCategory->id]);

        $expectedData = array_values($this->convertToResponseArray($positions));

        //category with positions check
        $this->get('/api/v1/categories/' . $firstCategory->id . '/positions')
            ->assertStatus(200)->assertJson([
                'data' => $expectedData,
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'path' => 'http://localhost/api/v1/categories/1/positions',
                    'per_page' => 15,
                    'to' => 2,
                    'total' => 2,
                ]
            ]);

        //Empty category check
        $this->get('/api/v1/categories/' . $secondCategory->id . '/positions')
            ->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    private function convertToResponseArray(Collection $positions)
    {
        return $positions
            ->sortBy('position')
            ->map(function (MenuPosition $position) {
                $options = $position->options->map(function (MenuPositionOptionValue $value) {
                    return ['option_id' => $value->menu_position_option_id, 'value' => $value->value, 'addPrice' => $value->price];
                })->toArray();

                return [
                    'id' => $position->id,
                    'name' => $position->name,
                    'price' => $position->price,
                    'description' => $position->description,
                    'position' => $position->position,
                    'image' => '//mazx.ru/pizza/menu/' . $position->image . '.jpg',
                    'options' => $options
                ];
            })->toArray();
    }
}
