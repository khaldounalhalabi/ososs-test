<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_price' => $this->base_price,
            'description' => $this->description,
            $this->mergeWhen(auth()->user()?->isCustomer(), fn() => [
                'applicable_price' => $this->applicable_price ?? $this->base_price
            ]),
            'price_lists' => PriceListResource::collection($this->whenLoaded('priceLists')),
        ];
    }
}
