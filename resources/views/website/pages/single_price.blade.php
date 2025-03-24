@extends('welcome')

@section('title', $price['title'] . ' - Avtomato')

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
                <h1 class="mt-5 md:mt-8 text-center text-3xl md:text-5xl text-secondary font-semibold">{!! $price['heading'] !!}</h1>
                <p class="mt-2 lg:mt-3 text-center text-lg text-secondary/75">{!! $price['description'] !!}</p>
                <button class="getDemoModalShow mt-8 h-[58px] flex items-center justify-center px-8 bg-primary hover:bg-primary/90 rounded-2xl transition-all cursor-pointer" type="button">
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
                    @foreach($price['services'] as $service)
                        <div class="flex flex-col md:{{ $loop->index % 2 == 0 ? 'flex-row' : 'flex-row-reverse' }} items-center justify-between gap-4 sm:gap-8">
                            <div class="basis-1/2">
                                <p class="text-2xl sm:text-3xl lg:text-5xl text-secondary font-semibold">{!! $service['title'] !!}</p>
                                <p class="mt-3 sm:mt-5 text-sm sm:text-lg lg:text-xl text-secondary/75">{{ $service['desc'] }}</p>
                            </div>
                            <div class="basis-1/2 overflow-hidden rounded-[42px]">
                                <img src="{{ asset($service['img']) }}" alt="Soddalashtirilgan nasiya jarayoni">
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Advantages End -->
            </div>

            <div class="pt-[60px] lg:pt-[120px] flex flex-col space-y-8">
                <!-- Price List Start -->
                <div class="bg-third p-6 md:p-[42px] rounded-[42px]">
                    <p class="text-3xl md:text-4xl text-secondary font-semibold">Narxlar</p>

                    <!-- Price List Start -->
                    <div class="mt-5 md:mt-8 flex flex-col space-y-5">
                        @foreach($price['prices'] as $coast)
                            <div class="bg-white p-5 md:p-8 rounded-[32px] md:rounded-[42px]">
                                <div class="size-14 flex items-center justify-center rounded-2xl bg-primary/5">
                                    <svg class="text-primary" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.3051 28.5848C8.92941 27.6903 6.85479 26.1427 5.32043 24.1203C3.78607 22.098 2.8544 19.6832 2.63281 17.1543H8.39296C8.37109 21.1507 9.37372 25.0862 11.3051 28.5848Z" fill="currentColor"/>
                                        <path d="M21.3115 17.1543C21.0678 24.4411 18.4122 29.4316 16.0131 29.4316C13.6142 29.4316 10.9586 24.4411 10.7148 17.1543H21.3115Z" fill="currentColor"/>
                                        <path d="M29.3688 17.1543C29.1536 19.687 28.2228 22.1063 26.6851 24.13C25.1473 26.1539 23.066 27.699 20.6836 28.5848C22.6255 25.0896 23.6329 21.1527 23.6087 17.1543H29.3688Z" fill="currentColor"/>
                                        <path d="M29.3688 14.8455H23.6087C23.6364 10.8468 22.6287 6.90895 20.6836 3.41504C23.066 4.30101 25.1473 5.84596 26.6851 7.86979C28.2228 9.89361 29.1536 12.3129 29.3688 14.8455Z" fill="currentColor"/>
                                        <path d="M21.3115 14.8455H10.7148C10.9586 7.54593 13.6142 2.56836 16.0131 2.56836C18.4122 2.56836 21.055 7.54593 21.3115 14.8455Z" fill="currentColor"/>
                                        <path d="M11.3051 3.479C9.37049 6.9765 8.36765 10.9127 8.39296 14.9095H2.63281C2.84992 12.3793 3.77987 9.96242 5.31483 7.93927C6.84979 5.91612 8.92688 4.36958 11.3051 3.479Z" fill="currentColor"/>
                                    </svg>
                                </div>

                                <p class="mt-5 text-xl sm:text-2xl text-secondary font-semibold">{{ $coast['title'] }}</p>
                                <p class="mt-2.5 sm:text-lg text-secondary/75 font-light">{{ $coast['desc'] }}</p>

                                <!-- Divider Start -->
                                <div class="my-6 sm:my-8 h-[1px] bg-[#E6EBF5]"></div>
                                <!-- Divider End -->

                                <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                                    <div>
                                        <p class="text-lg/[26px] text-secondary/75 font-light">Xizmat narxi</p>
                                        <p class="mt-2 text-2xl/[28px] text-primary font-semibold">{{ $coast['price'] }}</p>
                                    </div>

                                    @if (!empty($coast['note']))
                                        <div class="hidden sm:block w-[1px] h-full bg-[#E6EBF5] py-4"></div>
                                        <p class="text-[15px] md:text-lg text-secondary/75 font-light">{!! $coast['note'] !!}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Price List End -->
                </div>
                <!-- Price List End -->

                <!-- Free Trial Start -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 lg:gap-10 bg-third py-6 sm:py-8 px-6 sm:px-7 md:px-[62px] rounded-[42px]">
                    <p class="lg:basis-1/3 text-2xl md:text-[32px]/[40px] text-secondary font-semibold"><span class="text-primary">Avtomato</span>’ni hoziroq bepul sinab ko‘ring</p>

                    <div class="lg:basis-1/3">
                        <p class="text-sm md:text-base text-secondary/[75%] font-medium">Hech qanday to‘lovsiz ro‘yxatdan o‘ting va barcha funksiyalarni cheklovlarsiz sinab ko‘ring.</p>
                        <button class="getDemoModalShow w-full sm:w-auto mt-4 sm:mt-5 h-12 sm:h-[52px] px-5 flex sm:inline-flex items-center justify-center bg-secondary hover:bg-secondary/[90%] transition-all rounded-2xl cursor-pointer" type="button">
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
