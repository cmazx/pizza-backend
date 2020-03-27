<?php

use App\MenuPosition;
use App\Order;
use App\OrderPosition;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $positions = MenuPosition::activeSorted()->get();
        factory(Order::class, 5)
            ->create()
            ->each(function (Order $order) use ($positions) {
                foreach ($positions->random(random_int(1, 5)) as $position) {
                    factory(OrderPosition::class)->create([
                        'order_id' => $order->id,
                        'position_id' => $position->id,
                        'count' => random_int(1, 3),
                        'name' => $position->name,
                        'price' => $position->price,
                        'priceUSD' => $position->price * 1.11
                    ]);
                }
            });
    }
}
