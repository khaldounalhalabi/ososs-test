<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::paginate(request('per_page', 10));
        $paginationData = $this->paginationData($countries);
        return rest()
            ->ok()
            ->getSuccess()
            ->data(CountryResource::collection($countries))
            ->paginationData($paginationData)
            ->send();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateCountryRequest $request)
    {
        $country = Country::create($request->validated());
        return rest()
            ->ok()
            ->storeSuccess()
            ->data(CountryResource::make($country))
            ->send();
    }

    /**
     * Display the specified resource.
     */
    public function show($countryId)
    {
        $country = Country::find($countryId);
        if (is_null($country)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->getSuccess()
            ->data(CountryResource::make($country))
            ->ok()
            ->send();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateCountryRequest $request, $countryId)
    {
        $country = Country::find($countryId);
        if (is_null($country)) {
            return rest()
                ->noData()
                ->send();
        }

        $country->update($request->validated());
        return rest()
            ->ok()
            ->updateSuccess()
            ->data(CountryResource::make($country))
            ->send();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($countryId)
    {
        $country = Country::find($countryId);
        if (is_null($country)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->when(
                $country->delete(),
                fn($rest) => $rest->ok()->deleteSuccess(),
                fn($rest) => $rest->unknown()->unknownError(),
            )->send();
    }
}
