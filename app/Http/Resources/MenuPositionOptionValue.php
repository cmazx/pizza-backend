<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuPositionOptionValue extends JsonResource
{
    public function toArray($request)
    {
        return [
            'option_id' => $this->menu_position_option_id,
            'value' => $this->value,
            'addPrice' => $this->price
        ];
    }
}
