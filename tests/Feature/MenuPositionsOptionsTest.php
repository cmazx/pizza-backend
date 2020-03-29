<?php

namespace Tests\Feature;

use App\MenuPositionOption;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MenuPositionsOptionsTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(MenuPositionOption::class, 2)->create();
        $optionsFromDb = MenuPositionOption::all();
        $expectedResponse = [];
        foreach ($optionsFromDb as $option) {
            $expectedResponse[] = [
                'id' => $option->id,
                'name' => $option->name
            ];
        }
        //category with positions check
        $this->get('/api/v1/options')
            ->assertStatus(200)
            ->assertJson([
                'data' => $expectedResponse
            ]);
    }

}
