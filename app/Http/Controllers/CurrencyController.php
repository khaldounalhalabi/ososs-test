<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::paginate(request('per_page', 10));
        $paginationData = $this->paginationData($currencies);
        return rest()
            ->ok()
            ->getSuccess()
            ->data(CurrencyResource::collection($currencies))
            ->paginationData($paginationData)
            ->send();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateCurrencyRequest $request)
    {
        $currency = Currency::create($request->validated());
        return rest()
            ->ok()
            ->storeSuccess()
            ->data(CurrencyResource::make($currency))
            ->send();

    }

    /**
     * Display the specified resource.
     */
    public function show($currencyId)
    {
        $currency = Currency::find($currencyId);
        if (is_null($currency)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->getSuccess()
            ->data(CurrencyResource::make($currency))
            ->ok()
            ->send();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateCurrencyRequest $request, $currencyId)
    {
        $currency = Currency::find($currencyId);
        if (is_null($currency)) {
            return rest()
                ->noData()
                ->send();
        }

        $currency->update($request->validated());
        return rest()
            ->ok()
            ->updateSuccess()
            ->data(CurrencyResource::make($currency))
            ->send();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($currencyId)
    {
        $currency = Currency::find($currencyId);
        if (is_null($currency)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->when(
                $currency->delete(),
                fn($rest) => $rest->ok()->deleteSuccess(),
                fn($rest) => $rest->unknown()->unknownError(),
            )->send();
    }
}
