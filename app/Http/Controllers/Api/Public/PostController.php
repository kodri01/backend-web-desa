<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'category')->latest()->paginate(10);

        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function show($slug)
    {
        $post = Post::with('user', 'category')->where('slug', $slug)->first();

        if ($post) {
            return new PostResource(true, 'Details Data Post', $post);
        }

        return new PostResource(false, 'Details Data Post Tidak Ditemukan!', null);
    }

    public function homePage()
    {
        $post = Post::with('user', 'category')->latest()->take(6)->get();

        return new PostResource(true, 'List Data HomePage', $post);
    }
}
