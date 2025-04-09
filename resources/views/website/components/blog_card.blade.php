@php
/**
 * @var \App\Models\Blog $blog
 */
@endphp

<a class="group" href="{{ route('blog.show', ['slug' => $blog->slug]) }}" title="{{ $blog->title }}">
    <img class="w-full h-[250px] xl:h-[300px] object-cover rounded-[32px] pointer-events-none select-none" src="{{ $blog->image_url }}" alt="Blog">
    <p style="--truncate-line: 2;" class="mt-5 text-2xl text-secondary group-hover:text-secondary/75 font-semibold custom-truncate">{{ $blog->title }}</p>
    <p style="--truncate-line: 4;" class="mt-3.5 text-secondary/50 custom-truncate">{{ $blog->body }}</p>
</a>
