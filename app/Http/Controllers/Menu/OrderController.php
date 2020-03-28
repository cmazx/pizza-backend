<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order;
use App\Services\OrderService;
use App\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var \App\Services\OrderService
     */
    private OrderService $orderService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->middleware('guest');
        $this->orderService = $orderService;
    }

    public function create(Request $request, OrderService $orderService, User $user = null)
    {
        $order = $this->orderService->create($request->json()->all(), $user);

        return new Order($order);
    }

    public function patch(string $token, Request $request, OrderService $orderService)
    {
        $order = $this->orderService->updateByToken($token, $request->json()->all());

        return response()->json(['data' => new Order($order)]);
    }

    public function view(string $token, OrderService $orderService)
    {
        $orderService->validateToken($token);
        $order = \App\Order::query()->where('token', '=', $token)->first();
        if (!$order) {
            abort(404, 'Order not  found');
        }

        return response()->json(['data' => new Order($order)]);
    }


}
