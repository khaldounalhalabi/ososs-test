<?php

namespace App\Http\Resources;

use App\Models\PriceList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PriceList */
class PriceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'priority' => $this->priority,
            'product_id' => $this->product_id,
            'country_code' => $this->country_code,
            'currency_code' => $this->currency_code,
            'country' => new CountryResource($this->whenLoaded('country')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
