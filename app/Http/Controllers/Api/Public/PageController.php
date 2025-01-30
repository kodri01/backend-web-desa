<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::oldest()->get();

        return new PageResource(true, 'List Data Pages', $pages);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if ($page) {
            return new PageResource(true, 'Details Data Page', $page);
        }

        return new PageResource(false, 'Details Data Page Tidak Ditemukan!', null);
    }
}
