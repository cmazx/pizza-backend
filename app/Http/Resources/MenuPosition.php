<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class MenuPosition extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'position' => $this->position,
            'image' => URL::to('/images/' . $this->image . '.jpg'),
            'options' => MenuPositionOptionValue::collection($this->options)
        ];
    }
}
