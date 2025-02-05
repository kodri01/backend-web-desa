<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aparatur;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $modelrole = DB::table('model_has_roles')->where('model_id', auth()->guard('api')->user()->id)->first();
        $role = Role::where('id', $modelrole->role_id)->first();

        if ($role->name == 'admin') {
            $categories = Category::count();

            //count posts
            $posts = Post::count();

            //count products
            $products = Product::count();

            //count aparaturs
            $aparaturs = Aparatur::count();

            return response()->json([
                'success'   => true,
                'message'   => 'List Data on Dashboard',
                'data'      => [
                    'categories' => $categories,
                    'posts'      => $posts,
                    'products'   => $products,
                    'aparaturs'  => $aparaturs,
                ]
            ]);
        } else {
            $posts = Post::where('user_id', Auth::user()->id)->count();

            //count products
            $products = Product::where('user_id', Auth::user()->id)->count();

            $aparaturs = Aparatur::count();
            $categories = Category::count();



            return response()->json([
                'success'   => true,
                'message'   => 'List Data on Dashboard',
                'data'      => [
                    'categories' => $categories,
                    'posts'      => $posts,
                    'products'   => $products,
                    'aparaturs'  => $aparaturs,
                ]
            ]);
        }
    }
}
