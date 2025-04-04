<?php

namespace App\Http\Controllers\Website;

use App\Models\Blog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class BlogController
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $blogs = Blog::query()->latest()->paginate(6);

        return view('website.pages.blogs', ['blogs' => $blogs]);
    }

    public function show($slug): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();
        $blogs = Blog::query()->whereNot('id', $blog->id)->latest()->paginate(6);

        return view('website.pages.blog', ['blog' => $blog, 'recommendations' => $blogs]);
    }
}
