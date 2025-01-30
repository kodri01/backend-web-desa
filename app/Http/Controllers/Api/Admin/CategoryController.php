<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Laravel\Prompts\search;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::when(request()->search, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $categories->appends(['search' => request()->search]);

        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data Category Berhasil Disimpan!', $category);
        }

        return new CategoryResource(false, 'Data Category Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            return new CategoryResource(true, 'Details Data Categories', $category);
        }

        return new CategoryResource(false, 'Details Data Categories Tidak Ditemukan!', null);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        return new CategoryResource(false, 'Data Category Gagal Diupdate!', null);
    }

    public function destroy(Category $category)
    {
        if ($category->delete()) {
            return new CategoryResource(true, 'Data Category Berhasil Dihapus!', null);
        };

        return new CategoryResource(false, 'Data Category Gagal Dihapus!', null);
    }

    public function all()
    {
        $categories = Category::latest()->get();

        return new CategoryResource(true, 'List Data Categories', $categories);
    }
}
