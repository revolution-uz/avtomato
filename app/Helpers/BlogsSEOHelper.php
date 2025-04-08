<?php

namespace App\Helpers;

class BlogsSEOHelper
{
    public static function getSeoTitle($page, $tag_or_category = null): string
    {
        if ($tag_or_category) {
            return $page != 1
                ? trans('website.meta_blogs_with_tag_pagination', ['tag_or_category' => $tag_or_category, 'page' => $page])
                : trans('website.meta_blogs_with_tag', ['tag_or_category' => $tag_or_category]);
        }

        return $page != 1
            ? trans('website.meta_blogs_with_pagination', ['page' => $page])
            : trans('website.meta_blogs_title');
    }

    public static function getSeoDescription($page, $tag_or_category = null): string
    {
        if ($tag_or_category) {
            return $page != 1
                ? trans('website.meta_blogs_description_with_tag_pagination', ['tag_or_category' => $tag_or_category, 'page' => $page])
                : trans('website.meta_blogs_description_with_tag', ['tag_or_category' => $tag_or_category]);
        }

        return $page != 1
            ? trans('website.meta_blogs_description_with_pagination', ['page' => $page])
            : trans('website.meta_blogs_description');
    }
}
