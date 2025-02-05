<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PostController extends Controller
{
    public function index()
    {
        $modelrole = DB::table('model_has_roles')->where('model_id', auth()->guard('api')->user()->id)->first();
        $role = Role::where('id', $modelrole->role_id)->first();

        if ($role->name == 'admin') {

            $posts = Post::with('user', 'category')->when(request()->search, function ($posts) {
                $posts = $posts->where('title', 'like', '%' . request()->search . '%');
            })->latest()->paginate(5);

            $posts->appends(['search' => request()->search]);

            return new PostResource(true, 'List Data Post', $posts);
        } else {

            $posts = Post::with('user', 'category')->when(request()->search, function ($posts) {
                $posts = $posts->where('title', 'like', '%' . request()->search . '%');
            })->where('user_id', auth()->guard('api')->user()->id)->latest()->paginate(5);

            $posts->appends(['search' => request()->search]);

            return new PostResource(true, 'List Data Post', $posts);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg, jpeg|max:2000',
            'title' => 'required|unique:posts',
            'category_id' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('posts', $image->hashName());

        $posts = Post::create([
            'image'        => $image->hashName(),
            'title'        => $request->title,
            'slug'         => Str::slug($request->title, '-'),
            'category_id'  => $request->category_id,
            'user_id'      => auth()->guard('api')->user()->id,
            'content'      => $request->content
        ]);

        if ($posts) {
            return new PostResource(true, 'Data Post Berhasil Disimpan!', $posts);
        }

        return new PostResource(false, 'Data Post Gagal Disimpan!', null);
    }

    public function show($id)
    {

        $post = Post::with('category')->whereId($id)->first();

        if ($post) {
            return new PostResource(true, 'Details Data Post', $post);
        }

        return new PostResource(false, 'Details Data Post Tidak Ditemukan!', null);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required|unique:posts,title,' . $post->id,
            'category_id'  => 'required',
            'content'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('public')->delete('posts' . basename($post->image));

            $image = $request->file('image');
            $image->storeAs('posts', $image->hashName());

            $post->update([
                'image'        => $image->hashName(),
                'title'        => $request->title,
                'slug'         => Str::slug($request->title, '-'),
                'category_id'  => $request->category_id,
                'user_id'      => auth()->guard('api')->user()->id,
                'content'      => $request->content,
            ]);
        }

        $post->update([
            'title'        => $request->title,
            'slug'         => Str::slug($request->title, '-'),
            'category_id'  => $request->category_id,
            'user_id'      => auth()->guard('api')->user()->id,
            'content'      => $request->content,
        ]);

        if ($post) {
            return new PostResource(true, 'Data Post Berhasil Diupdate!', $post);
        }

        return new PostResource(false, 'Data Post Gagal Diupdate!', null);
    }

    public function destroy(Post $post)
    {
        Storage::disk('public')->delete('posts' . basename($post->image));

        if ($post->delete()) {
            return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
        }

        return new PostResource(false, 'Data Post Gagal Dihapus!', null);
    }
}
