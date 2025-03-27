<div id="blogs-carousel" class="carousel-wrapper w-full relative overflow-hidden">
    <div id="carousel-track" class="flex transition-transform duration-300">
        @foreach ($blogs as $blog)
            <div class="carousel-item">
                @include('website.components.blog_card', ['blog' => $blog])
            </div>
        @endforeach
    </div>

    <button id="carousel-prev" class="size-11 flex items-center justify-center absolute left-0 top-1/3 transform bg-secondary/5 rounded-full backdrop-blur-md cursor-pointer">
        <svg class="rotate-180 text-secondary" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <button id="carousel-next" class="size-11 flex items-center justify-center absolute right-0 top-1/3 transform bg-secondary/5 rounded-full backdrop-blur-md cursor-pointer">
        <svg class="text-secondary" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
