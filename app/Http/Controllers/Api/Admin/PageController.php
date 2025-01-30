<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::when(request()->search, function ($pages) {
            $pages = $pages->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $pages->appends(['search' => request()->search]);

        return new PageResource(true, 'List Data Pages', $pages);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page = Page::create([
            'title' => $request->title,
            'slug'  => Str::slug($request->title, '-'),
            'content' => $request->content,
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        if ($page) {
            return new PageResource(true, 'Data Pages Berhasil Ditambahkan!', $page);
        }

        return new PageResource(false, 'Data Pages Gagal Ditambahkan!', null);
    }

    public function show($id)
    {
        $page = Page::whereId($id)->first();

        if ($page) {
            return new PageResource(true, 'Details Data Pages', $page);
        }

        return new PageResource(false, 'Details Data Pages Tidak Ditemukan!', null);
    }

    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'content' => $request->content,
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        if ($page) {
            return new PageResource(true, 'Data Pages Berhasil Diupdate!', $page);
        }

        return new PageResource(false, 'Data Pages Gagal Diupdate!', null);
    }

    public function destroy(Page $page)
    {
        if ($page->delete()) {
            return new PageResource(true, 'Data Pages Berhasil Dihapus', null);
        }

        return new PageResource(false, 'Data Pages Gagal Dihapus!', null);
    }
}
