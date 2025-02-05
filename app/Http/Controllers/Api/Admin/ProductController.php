<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::when(request()->search, function ($products) {
            $products = $products->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate('5');

        $products->appends(['search' => request()->search]);

        return new ProductResource(true, 'List Data Product', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|mimes:png,jpg,jpeg|max:2000',
            'title'     => 'required',
            'content'   => 'required',
            'owner'     => 'required',
            'price'     => 'required',
            'address'   => 'required',
            'phone'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('products', $image->hashName());

        $product = Product::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'slug'      => Str::slug($request->title, '-'),
            'content'   => $request->content,
            'owner'     => $request->owner,
            'price'     => $request->price,
            'address'   => $request->address,
            'phone'     => $request->phone,
            'user_id'   => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $product);
        }

        return new ProductResource(false, 'Data Product Gagal Ditambahkan!', null);
    }

    public function show($id)
    {
        $product = Product::whereId($id)->first();

        if ($product) {
            return new ProductResource(true, 'Details Data Product', $product);
        }

        return new ProductResource(false, 'Details Data Product Tidak Ditemukan!', null);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'owner' => 'required',
            'price' => 'required',
            'address' => 'required',
            'phone'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('public')->delete('products' . basename($product->image));

            $image = $request->file('image');
            $image->storeAs('products', $image->hashName());

            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'slug'  => Str::slug($request->title, '-'),
                'content' => $request->content,
                'owner'     => $request->owner,
                'price'     => $request->price,
                'address'   => $request->address,
                'phone'     => $request->phone,
                'user_id'   => auth()->guard('api')->user()->id,
            ]);
        }

        $product->update([
            'title'     => $request->title,
            'slug'      => Str::slug($request->title, '-'),
            'content'   => $request->content,
            'owner'     => $request->owner,
            'price'     => $request->price,
            'address'   => $request->address,
            'phone'     => $request->phone,
            'user_id'   => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new ProductResource(true, 'Data Product Berhasil Diupdate!', $product);
        }

        return new ProductResource(false, 'Data Product Gagal Diupdate!', null);
    }

    public function destroy(Product $product)
    {
        Storage::disk('public')->delete('products', basename($product->image));

        if ($product->delete()) {
            return new ProductResource(true, 'Data Product Berhasil Dihapus!', null);
        }

        return new ProductResource(false, 'Data Product Gagal Dihapus!', null);
    }
}
