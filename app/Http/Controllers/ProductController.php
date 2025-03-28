<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

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
}
