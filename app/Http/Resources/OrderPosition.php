<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class OrderPosition extends JsonResource
{
    public function toArray($request)
    {
        return [
            'count' => $this->count,
            'position_id' => $this->position_id,
            'price' => $this->price,
            'priceUSD' => $this->priceUSD
        ];
    }
}
