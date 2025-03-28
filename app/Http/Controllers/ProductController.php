<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(request('per_page', 10));
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
        $product = Product::find($productId);
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
