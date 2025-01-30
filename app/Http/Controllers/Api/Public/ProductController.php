<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(9);

        return new ProductResource(true, 'List Data Products', $products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            return new ProductResource(true, 'Details Data Product', $product);
        }

        return new ProductResource(false, 'Details Data Product Tidak Ditemukan!', null);
    }

    public function homePage()
    {
        $product = Product::latest()->take(6)->get();

        return new ProductResource(true, 'List Data Product HomePage', $product);
    }
}
