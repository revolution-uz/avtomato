@extends('website.layouts.layout')

@section('title', $data['title'] . ' - Avtomato')

@section('content')
    <!-- Single Price Top Content Start -->
    <section class="mt-3 md:mt-6 lg:mt-10 relative">
        <!-- Gradient Circles Start -->
        <div class="gradient-circle red-gradient -top-1/3 right-1/3 md:-top-1/3 md:-right-1/3 lg:top-0 lg:right-0 pointer-events-none"></div>
        <div class="gradient-circle yellow-gradient -top-[70%] left-1/3 md:-top-1/2 md:-left-1/4 lg:-top-1/3 lg:left-0 pointer-events-none"></div>
        <div class="gradient-circle small purple-gradient top-1/2 left-1/3 md:top-1/3 md:left-0 pointer-events-none"></div>
        <!-- Gradient Circles End -->

        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center">
                <p class="text-xl text-primary font-medium">Yechimlar va narxlar</p>
                <h1 class="mt-5 md:mt-8 text-center text-3xl md:text-5xl text-secondary font-semibold">{!! $data['heading'] !!}</h1>
                <p class="mt-2 lg:mt-3 text-center text-lg text-secondary/75">{!! $data['description'] !!}</p>
                <button class="getDemoModalShow mt-8 h-[58px] flex items-center justify-center px-8 bg-primary hover:bg-primary/90 rounded-2xl transition-all cursor-pointer"
                        type="button">
                    <span class="text-lg text-white font-medium">Sinab ko’rish</span>
                </button>
            </div>
        </div>
    </section>
    <!-- Single Price Top Content End -->

    <!-- Single Price Start -->
    <section class="mt-[60px] lg:mt-[120px]">
        <div class="container mx-auto px-4">
            <div>
                <!-- Advantages Start -->
                <div class="bg-third p-5 sm:p-8 lg:p-[60px] rounded-[42px] sm:rounded-[60px] space-y-[42px] lg:space-y-[60px]">
                    @foreach ($data['services'] as $service)
                        <div class="flex flex-col {{ $loop->index % 2 == 0 ? 'md:flex-row' : 'md:flex-row-reverse' }} items-center justify-between gap-4 sm:gap-8">
                            <div class="basis-1/2">
                                <p class="text-2xl sm:text-4xl xl:text-5xl text-secondary font-semibold">{!! $service['title'] !!}</p>
                                <p class="mt-3 sm:mt-5 text-sm sm:text-lg lg:text-xl text-secondary/75">{{ $service['desc'] }}</p>
                            </div>
                            <div class="basis-1/2 overflow-hidden rounded-[42px] max-h-[550px]">
                                <img src="{{ asset($service['img']) }}" alt="{{ $service['title'] }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Advantages End -->
            </div>

            <div class="pt-[60px] lg:pt-[120px] flex flex-col space-y-8">

                <!-- Prices Start -->
                @yield('price_list')
                <!-- Price List End -->

                <!-- Free Trial Start -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 lg:gap-10 bg-third py-6 sm:py-8 px-6 sm:px-7 md:px-[62px] rounded-[42px]">
                    <p class="lg:basis-1/3 text-2xl md:text-[32px]/[40px] text-secondary font-semibold"><span
                                class="text-primary">Avtomato</span>’ni hoziroq bepul sinab ko‘ring</p>

                    <div class="lg:basis-1/3">
                        <p class="text-sm md:text-base text-secondary/[75%] font-medium">Hech qanday to‘lovsiz
                            ro‘yxatdan o‘ting va barcha funksiyalarni cheklovlarsiz sinab ko‘ring.</p>
                        <button class="getDemoModalShow w-full sm:w-auto mt-4 sm:mt-5 h-12 sm:h-[52px] px-5 flex sm:inline-flex items-center justify-center bg-secondary hover:bg-secondary/[90%] transition-all rounded-2xl cursor-pointer"
                                type="button">
                            <span class="text-sm sm:text-base text-white font-semibold">Sinovni boshlash</span>
                        </button>
                    </div>
                </div>
                <!-- Free Trial End -->
            </div>
        </div>
    </section>
    <!-- Single Price End -->
@endsection
