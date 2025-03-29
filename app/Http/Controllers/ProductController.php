<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::when(auth()->user()->isCustomer(), function (Builder|Product $query) {
            $query->withApplicablePrice();
        })->when(auth()->user()?->isAdmin(), function (Builder $query) {
            $query->with('priceLists');
        })->paginate(request('per_page', 10));
        $paginationData = $this->paginationData($products);
        return rest()
            ->ok()
            ->getSuccess()
            ->data(ProductResource::collection($products))
            ->paginationData($paginationData)
            ->send();

    }

    public function show($productId)
    {
        $product = Product::when(auth()->user()->isCustomer(), function (Builder|Product $query) {
            $query->withApplicablePrice();
        })->when(auth()->user()?->isAdmin(), function (Builder $query) {
            $query->with('priceLists');
        })->where('id', $productId)
            ->first();

        if (is_null($product)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->getSuccess()
            ->data(ProductResource::make($product))
            ->ok()
            ->send();

    }

    public function destroy($productId)
    {
        $product = Product::find($productId);
        if (is_null($product)) {
            return rest()
                ->noData()
                ->send();
        }

        return rest()
            ->when(
                $product->delete(),
                fn($rest) => $rest->ok()->deleteSuccess(),
                fn($rest) => $rest->unknown()->unknownError(),
            )->send();
    }

    public function store(StoreUpdateProductRequest $request)
    {
        $product = Product::create($request->validated());
        return rest()
            ->ok()
            ->storeSuccess()
            ->data(ProductResource::make($product))
            ->send();
    }

    public function update(StoreUpdateProductRequest $request, $productId)
    {
        $product = Product::find($productId);
        if (is_null($product)) {
            return rest()
                ->noData()
                ->send();
        }

        $product->update($request->validated());
        return rest()
            ->ok()
            ->updateSuccess()
            ->data(ProductResource::make($product))
            ->send();
    }
}
