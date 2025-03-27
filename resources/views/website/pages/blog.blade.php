@extends('website.layouts.layout')

@section('title', $blog['title'] . ' - Avtomato')

@section('content')
    <!-- Single Blog Start -->
    <section class="pt-3 md:pt-6 lg:pt-10 pb-10 md:pb-[60x]">
        <!-- Go Back Button Start -->
        <div class="container mx-auto px-4">
            <a class="h-12 inline-flex items-center justify-center space-x-1.5 px-4 bg-third rounded-2xl"
               href="{{ url()->previous() }}" title="Orqaga">
                <svg class="text-secondary" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 12H20" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                          stroke-linecap="round"/>
                    <path d="M11.0339 4.33984L4.46101 10.9127C4.31746 11.0551 4.20346 11.2245 4.12572 11.4113C4.04786 11.598 4.00781 11.7982 4.00781 12.0005C4.00781 12.2028 4.04786 12.403 4.12572 12.5897C4.20346 12.7765 4.31746 12.9459 4.46101 13.0882L11.0339 19.6611"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm text-secondary">Orqaga</span>
            </a>
        </div>
        <!-- Go Back Button End -->

        <!-- Blog Content Start -->
        <div class="pt-8 lg:pt-[60px] pb-[60px]">
            <div class="max-w-[820px] mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl text-secondary font-semibold">{{ $blog['title'] }}</h2>
                <p class="mt-3 sm:mt-4 text-secondary/55 font-light">{{ $blog['created_at'] }}</p>
                <a class="mt-5 h-12 inline-flex items-center justify-center space-x-2 px-4 bg-third rounded-2xl text-secondary"
                   href="https://t.me/share/url?url={{ urlencode(url()->current()) }}" target="_blank" title="Ulashish">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.01471 15.8087C7.81775 15.8087 9.27941 14.3471 9.27941 12.544C9.27941 10.741 7.81775 9.2793 6.01471 9.2793C4.21166 9.2793 2.75 10.741 2.75 12.544C2.75 14.3471 4.21166 15.8087 6.01471 15.8087Z"
                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.9834 9.27941C19.7864 9.27941 21.2481 7.81775 21.2481 6.01471C21.2481 4.21166 19.7864 2.75 17.9834 2.75C16.1803 2.75 14.7188 4.21166 14.7188 6.01471C14.7188 7.81775 16.1803 9.27941 17.9834 9.27941Z"
                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.9834 21.2501C19.7864 21.2501 21.2481 19.7884 21.2481 17.9854C21.2481 16.1823 19.7864 14.7207 17.9834 14.7207C16.1803 14.7207 14.7188 16.1823 14.7188 17.9854C14.7188 19.7884 16.1803 21.2501 17.9834 21.2501Z"
                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.0132 16.636L8.98438 13.8936" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.1154 7.58203L8.89062 10.9774" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm ms:text-base">Ulashish</span>
                </a>
                <img class="my-8 border border-third w-full h-[200px] sm:h-[420px] object-cover rounded-[32px]"
                     src="{{ asset($blog['image']) }}" alt="{{ $blog['title'] }}">
                <p class="sm:text-lg text-secondary/75 sm:text-secondary/55">{!! $blog['content'] !!}</p>
            </div>
        </div>
        <!-- Blog Content End -->

        <!-- Recommendations Start -->
        <section class="md:pt-[60px]">
            <div class="container mx-auto px-4 overflow-hidden">
                <h2 class="text-center text-3xl lg:text-4xl text-secondary font-semibold">Tavsiya qilinadi</h2>

                <!-- Blog Grid Columns Start -->
                <div class="mt-[42px] lg:mt-[60px]">
                    @include('website.components.blogs_carousel', ['blogs' => $recommendations])
                </div>
                <!-- Blog Grid Columns End -->
            </div>
        </section>
        <!-- Recommendations End -->
    </section>
    <!-- Single Blog End -->
@endsection
