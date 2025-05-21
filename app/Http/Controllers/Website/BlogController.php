<?php

namespace App\Http\Controllers\Website;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class BlogController
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $blogs = Blog::query()->latest()->paginate(6);
        $categories = BlogCategory::query()->latest()->get();

        return view('website.pages.blogs', [
            'blogs' => $blogs,
            'categories' => $categories,
            'meta_description' => __('website.meta_blog_description'),
            'meta_keywords' => __('website.meta_blog_keyboards'),
        ]);
    }

    public function show($slug): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();
        $blogs = Blog::query()->whereNot('id', $blog->id)->latest()->paginate(6);

        return view('website.pages.blog', ['blog' => $blog, 'recommendations' => $blogs]);
    }

    public function byCategory(string $category)
    {
        $category = BlogCategory::query()->where('slug', $category)->firstOrFail();
        $blogs = Blog::query()->where('category_id', $category->id)->latest('created_at')->paginate(6);
        $categories = BlogCategory::query()->latest()->get();

        return view('website.pages.blogs', [
            'blogs' => $blogs,
            'categoryOrTagName' => $category->name,
            'categories' => $categories,
            'meta_description' => $category->seo_description,
            'meta_keywords' => $category->seo_keywords,
        ]);
    }
}
