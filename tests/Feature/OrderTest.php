<?php

namespace Tests\Feature;

use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        (new \MenuCategorySeeder())->run();
    }

    /**
     * @param $requestData
     *
     * @dataProvider validationProvider
     */
    public function testCreateValidationError($requestData)
    {
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

    public function testCreateSuccessfull()
    {
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
            'token' => $order->token,
            'address' => $order->address,
            'status' => $order->status,
            'positions' => $positions,
            'total' => $total,
            'totalUSD' => $totalUSD
        ], $data['data']);
    }

    public function testIdempotenceCreate()
    {
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

    public function testOrderCancel()
    {
        $createResponse = $this->createOrder();

        $cancelledResponse = $this->patchJson(
            '/api/v1/orders/' . $createResponse['data']['token'],
            ['status' => Order::STATUS_CANCELLED], ['Idempotency-key' => 555])
            ->assertStatus(200)
            ->json();

        $createResponse['data']['status'] = Order::STATUS_CANCELLED;
        self::assertEquals($createResponse['data'], $cancelledResponse['data']);
    }

    public function testOrderCancelNotFound()
    {
        $createResponse = $this->createOrder();

        $this->patchJson(
            '/api/v1/orders/' . $createResponse['data']['token'] . 'invalid',
            ['status' => Order::STATUS_CANCELLED], ['Idempotency-key' => 555])
            ->assertStatus(404);
    }

    public function testOrderCancelInvalidStatus()
    {
        $createResponse = $this->createOrder();

        $this->patchJson(
            '/api/v1/orders/' . $createResponse['data']['token'],
            ['status' => Order::STATUS_DELIVERED], ['Idempotency-key' => 555])
            ->assertStatus(422);
    }

    public function testOrderCancelInvalidStatusInDb()
    {
        $createResponse = $this->createOrder();
        $order = Order::query()->where('token', '=', $createResponse['data']['token'])->first();
        $order->status = Order::STATUS_APPROVED;
        static::assertTrue($order->save());

        $this->patchJson(
            '/api/v1/orders/' . $createResponse['data']['token'],
            ['status' => Order::STATUS_CANCELLED], ['Idempotency-key' => 555])
            ->assertStatus(422);
    }


    public function testOrderView()
    {
        $createResponse = $this->createOrder();
        $vewResponse = $this->get('/api/v1/orders/' . $createResponse['data']['token'])
            ->assertStatus(200)
            ->json();

        self::assertEquals($createResponse['data'], $vewResponse['data']);
    }

    public function testOrderViewNotFound()
    {
        $token = $this->createOrder()['data']['token'];
        $this->get('/api/v1/orders/' . $token . 'invalid')->assertStatus(404);
    }


    private function createOrder($idempotencyKey = 333)
    {
        return $this->postJson('/api/v1/orders', [
            'address' => 'Some adresss',
            'phone' => '+7955441112',
            'positions' => [['id' => 1, 'count' => 1]]
        ], ['Idempotency-key' => $idempotencyKey])
            ->assertStatus(201)
            ->json();
    }


}
