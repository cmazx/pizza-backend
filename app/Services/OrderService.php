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
    const TOKEN_LENGTH = 40;
    /**
     * @var \App\Services\CurrencyService
     */
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function create(array $orderData, ?User $user): Order
    {
        $this->validateCreate($orderData);
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

    public function validateCreate(array $orderData)
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
        $order->token = base64_encode(Str::random(static::TOKEN_LENGTH));

        return $order;
    }

    private function buildOrderPositions(array $orderData): array
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

    /**
     * Currently only status change to cancelled supported
     *
     * @param string $token
     * @param array $data
     *
     * @return \App\Order
     */
    public function updateByToken(string $token, array $data): Order
    {
        $this->validateToken($token);
        Validator::make(['status' => $data['status'] ?? ''],
            ['status' => 'required|in:' . Order::STATUS_CANCELLED,])->validate();
        /**
         * @var Order $order
         */
        $order = Order::query()->where('token', '=', $token)->first();
        if (!$order) {
            abort(404, 'Order not found.');
        }
        if ($order->status == Order::STATUS_CANCELLED) {
            return $order;
        }
        if (!$order->isCancellable()) {
            abort(422, json_encode(['message' => 'Order can not be cancelled', 'code' => 101]));
        }


        return $this->cancelOrder($order);
    }

    private function cancelOrder(Order $order): Order
    {
        $executed = DB::statement('UPDATE "orders" SET status=:s_to WHERE id=:id and status=:s_from', [
            ':id' => $order->id,
            ':s_to' => Order::STATUS_CANCELLED,
            ':s_from' => $order->status
        ]);
        if (!$executed) {
            abort(422, json_encode(['message' => 'Order can not be cancelled', 'code' => 101]));
        }
        $order->refresh();
        if ($order->status != Order::STATUS_CANCELLED) {
            abort(422, json_encode(['message' => 'Order can not be cancelled', 'code' => 101]));
        }

        return $order;
    }

    public function validateToken(string $token): void
    {
        Validator::make(['token' => $token], ['token' => 'required|string|between:40,255',])->validate();
    }
}
