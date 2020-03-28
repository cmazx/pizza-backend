<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    public function toArray($request)
    {
        $positions = [];
        $total = 0;
        $totalUSD = 0;
        foreach ($this->positions as $position) {
            $total += $position->price;
            $totalUSD += $position->priceUSD;
            $positions[] = new OrderPosition($position);
        }

        return [
            'token' => $this->token,
            'address' => $this->address,
            'status' => $this->status,
            'positions' => $positions,
            'total' => $total,
            'totalUSD' => $totalUSD
        ];
    }
}
