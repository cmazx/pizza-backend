<?php

namespace Tests\Feature;

use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @param $requestData
     *
     * @dataProvider validationProvider
     */
    public function testValidationError($requestData)
    {
        (new \MenuCategorySeeder())->run();
        $status = $this->postJson('/api/v1/orders', $requestData, ['Idempotency-key' => 1])
            ->getStatusCode();
        self::assertEquals(422, $status);
    }

    public function validationProvider()
    {
        return [
            'invalid count' => [
                [
                    'address' => 'Some address',
                    'phone' => '+7955441112',
                    'positions' => [
                        ['id' => 1, 'count' => 101],
                        ['id' => 2, 'count' => 2],
                    ]
                ]
            ],
            'invalid address' => [
                [
                    'address' => '',
                    'phone' => '+7955441112',
                    'positions' => [
                        ['id' => 1, 'count' => 1],
                        ['id' => 2, 'count' => 2],
                    ]
                ]
            ],
            'invalid phone' => [
                [
                    'address' => 'Some address',
                    'phone' => '+99',
                    'positions' => [
                        ['id' => 1, 'count' => 1],
                        ['id' => 2, 'count' => 2],
                    ]
                ]
            ],
            'invalid phone2' => [
                [
                    'address' => 'Some address',
                    'phone' => '',
                    'positions' => [
                        ['id' => 1, 'count' => 1],
                        ['id' => 2, 'count' => 2],
                    ]
                ]
            ],
            'no positions' => [
                [
                    'address' => 'Some address',
                    'phone' => '+7955441112',
                    'positions' => [
                    ]
                ]
            ],
            'not existing position' => [
                [
                    'address' => 'Some address',
                    'phone' => '+7955441112',
                    'positions' => [
                        ['id' => 10000000, 'count' => 1],
                    ]
                ]
            ],
        ];
    }

    public function testCreate()
    {
        (new \MenuCategorySeeder())->run();
        $request = [
            'address' => 'Some adresss',
            'phone' => '+7955441112',
            'positions' => [
                ['id' => 1, 'count' => 1],
                ['id' => 2, 'count' => 2],
            ]
        ];

        $data = $this->postJson('/api/v1/orders', $request, ['Idempotency-key' => 1])
            ->assertStatus(201)
            ->json();

        $order = Order::query()->find(1);
        $positions = [];
        $total = 0;
        $totalUSD = 0;
        foreach ($order->positions as $position) {
            $positions[] = [
                'count' => $position->count,
                'position_id' => $position->position_id,
                'price' => $position->price,
                'priceUSD' => $position->priceUSD
            ];
            $total += $position->price;
            $totalUSD += $position->priceUSD;
        }

        self::assertEquals([
            'id' => 1,
            'token' => $order->token,
            'address' => $order->address,
            'status' => $order->status,
            'positions' => $positions,
            'total' => $total,
            'totalUSD' => $totalUSD
        ], $data['data']);
    }

    public function testIdempotence()
    {
        (new \MenuCategorySeeder())->run();
        $request = [
            'address' => 'Some adresss',
            'phone' => '+7955441112',
            'positions' => [
                ['id' => 1, 'count' => 1],
                ['id' => 2, 'count' => 2],
            ]
        ];
        $data = $this->postJson('/api/v1/orders', $request, ['Idempotency-key' => 1])
            ->assertStatus(201)
            ->json();

        //check response is same and response same
        $data2 = $this->postJson('/api/v1/orders', $request, ['Idempotency-key' => 1])
            ->assertStatus(201)
            ->json();
        self::assertEquals($data['data'], $data2['data']);

        //check new order created with new idempotency key
        $data3 = $this->postJson('/api/v1/orders', $request, ['Idempotency-key' => 2])
            ->assertStatus(201)
            ->json();

        self::assertNotEquals($data['data'], $data3['data']);
    }
}
