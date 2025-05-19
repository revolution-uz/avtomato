@if ($paginator->hasPages())
    <div class="w-full flex items-center justify-center gap-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="size-12 flex items-center justify-center bg-secondary/40 border border-secondary/40 rounded-2xl cursor-not-allowed">
                <svg class="rotate-180 text-white" width="18" height="19" viewBox="0 0 18 19" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="size-12 flex items-center justify-center bg-secondary hover:bg-secondary/90 border border-secondary hover:border-secondary/90 rounded-2xl cursor-pointer">
                <svg class="rotate-180 text-white" width="18" height="19" viewBox="0 0 18 19" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <button class="group size-12 flex items-center justify-center border border-secondary hover:border-secondary/70 rounded-2xl cursor-pointer"
                        type="button">
                    <span class="text-secondary group-hover:text-secondary/70">{{ $element }}</span>
                </button>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="size-12 flex items-center justify-center bg-secondary border border-secondary rounded-2xl text-white font-semibold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="group size-12 flex items-center justify-center border border-secondary hover:border-secondary/70 rounded-2xl cursor-pointer"
                           type="button">
                            <span class="text-secondary group-hover:text-secondary/70">{{ $page }}</span>
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="size-12 flex items-center justify-center bg-secondary hover:bg-secondary/90 border border-secondary hover:border-secondary/90 rounded-2xl cursor-pointer">
                <svg class="text-white" width="18" height="19" viewBox="0 0 18 19" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @else
            <span class="size-12 flex items-center justify-center bg-secondary/40 border border-secondary/40 rounded-2xl cursor-not-allowed">
                <svg class="text-white" width="18" height="19" viewBox="0 0 18 19" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endif
    </div>
@endif
