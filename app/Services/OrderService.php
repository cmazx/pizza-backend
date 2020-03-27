<?php


namespace App\Services;


use App\MenuPosition;
use App\Order;
use App\OrderPosition;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * @var \App\Services\CurrencyService
     */
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function create(array $orderData, ?User $user)
    {
        $this->validate($orderData);
        $order = $this->buildOrder($orderData, $user);
        $positions = $this->buildOrderPositions($orderData);

        DB::transaction(static function () use ($order, $positions) {
            if ($order->save()) {
                foreach ($positions as $position) {
                    $position->order_id = $order->id;
                }
                $order->positions()->saveMany($positions);
            }
        }, 2);

        return $order;
    }

    public function validate(array $orderData)
    {
        Validator::make($orderData, [
            'address' => 'required|between:5,255',
            'phone' => 'required|regex:/\+[0-9]{6,20}/',
            'positions' => 'required|array|between:1,100',
            'positions.*.id' => 'required|exists:menu_positions,id,active,1',
            'positions.*.count' => 'required|integer|max:100',
        ])->validate();
    }

    private function buildOrder(array $orderData, ?User $user): Order
    {
        $order = new Order();
        $order->address = $orderData['address'];
        $order->phone = $orderData['phone'];
        $order->user_id = $user ? $user->id : null;
        $order->status = Order::STATUS_NEW;
        $order->token = base64_encode(Str::random(40));

        return $order;
    }

    private function buildOrderPositions(array $orderData)
    {
        $positions = [];

        $menuPositions = MenuPosition::active()
            ->whereIn('id', array_column($orderData['positions'], 'id'))
            ->get()
            ->keyBy('id');

        foreach ($orderData['positions'] as $position) {
            $menuPosition = $menuPositions[$position['id']];
            $price = $menuPosition->price;
            $positions[] = new OrderPosition([
                'position_id' => $position['id'],
                'count' => $position['count'],
                'name' => $menuPosition->name,
                'price' => $price,
                'priceUSD' => $this->currencyService->convert('EUR', 'USD', $price)
            ]);
        }

        return $positions;
    }
}
