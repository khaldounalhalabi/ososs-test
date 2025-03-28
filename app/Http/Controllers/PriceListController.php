<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePriceListRequest;
use App\Http\Resources\PriceListResource;
use App\Models\PriceList;

class PriceListController extends Controller
{
    public function store(StoreUpdatePriceListRequest $request)
    {
        $priceList = PriceList::create($request->validated());
        return rest()
            ->ok()
            ->storeSuccess()
            ->data(PriceListResource::make($priceList))
            ->send();
    }

    public function destroy($priceListId)
    {
        $priceList = PriceList::find($priceListId);
        if (is_null($priceList)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->when(
                $priceList->delete(),
                fn($rest) => $rest->ok()->deleteSuccess(),
                fn($rest) => $rest->unknown()->unknownError(),
            )->send();
    }
}
