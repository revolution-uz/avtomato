@extends('website.layout')

@section('title', 'Narxlar - Avtomato')

@section('content')
    <!-- Prices Top Content Start -->
    <section class="pt-10 relative">
        <!-- Gradient Circles Start -->
        <div class="gradient-circle red-gradient -top-1/3 right-1/3 md:-top-1/3 md:-right-1/3 lg:top-0 lg:right-0 pointer-events-none"></div>
        <div class="gradient-circle yellow-gradient -top-[70%] left-1/3 md:-top-1/2 md:-left-1/4 lg:-top-1/3 lg:left-0 pointer-events-none"></div>
        <div class="gradient-circle small purple-gradient top-1/2 left-1/3 md:top-1/3 md:left-0 pointer-events-none"></div>
        <!-- Gradient Circles End -->

        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center">
                <div class="size-[52px] flex items-center justify-center rounded-2xl bg-primary/5">
                    <svg class="text-primary" width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.5026 0.333008C11.7996 0.333008 9.15734 1.13454 6.90988 2.63625C4.6624 4.13797 2.91074 6.27242 1.87634 8.76968C0.841943 11.2669 0.571183 14.0149 1.09852 16.6659C1.62585 19.317 2.9276 21.7522 4.8389 23.6634C6.75022 25.5747 9.1854 26.8765 11.8365 27.4038C14.4876 27.9311 17.2354 27.6605 19.7326 26.6261C22.2298 25.5917 24.3644 23.8399 25.8661 21.5925C27.3678 19.345 28.1693 16.7027 28.1693 13.9997C28.1658 10.3761 26.7248 6.90202 24.1625 4.33978C21.6002 1.77755 18.1262 0.336541 14.5026 0.333008ZM15.5026 21.6663V22.7197C15.5026 22.9849 15.3972 23.2393 15.2097 23.4267C15.0221 23.6143 14.7678 23.7197 14.5026 23.7197C14.2374 23.7197 13.9832 23.6143 13.7956 23.4267C13.6081 23.2393 13.5026 22.9849 13.5026 22.7197V21.6663C12.6337 21.5365 11.8198 21.1618 11.156 20.5863C10.6827 20.1886 10.2929 19.701 10.0092 19.1518C9.72538 18.6025 9.55321 18.0025 9.50265 17.3863C9.48044 17.123 9.5634 16.8617 9.73328 16.6593C9.90316 16.457 10.1462 16.3301 10.4094 16.3063C10.6741 16.2839 10.937 16.3665 11.1415 16.5361C11.346 16.7057 11.4756 16.9487 11.5026 17.213C11.5281 17.5697 11.6252 17.9174 11.7878 18.2358C11.9505 18.5542 12.1753 18.8366 12.4493 19.0663C12.9996 19.5386 13.7122 19.7777 14.436 19.733C16.516 19.733 17.5026 18.933 17.5026 17.3063C17.5026 16.2263 16.556 15.0663 14.5026 15.0663C10.156 15.0663 9.50265 12.3997 9.50265 10.8663C9.52448 10.2181 9.68349 9.5819 9.96928 8.99967C10.244 8.43439 10.6301 7.93042 11.1044 7.51802C11.5786 7.10561 12.1314 6.79323 12.7294 6.59967C12.9822 6.52153 13.2406 6.46357 13.5026 6.42634V5.38634C13.5026 5.12113 13.6081 4.86677 13.7956 4.67923C13.9832 4.49169 14.2374 4.38634 14.5026 4.38634C14.7678 4.38634 15.0221 4.49169 15.2097 4.67923C15.3972 4.86677 15.5026 5.12113 15.5026 5.38634V6.46634C16.5494 6.64091 17.509 7.15653 18.2325 7.93298C18.9558 8.70943 19.4025 9.70318 19.5026 10.7597C19.5136 11.0147 19.4266 11.2643 19.2594 11.4573C19.0924 11.6502 18.8577 11.7721 18.6037 11.7975C18.3497 11.8231 18.0956 11.7506 17.8933 11.5949C17.691 11.439 17.5561 11.2118 17.516 10.9597C17.4418 10.2361 17.0942 9.56809 16.544 9.09226C15.9938 8.61644 15.2828 8.3687 14.5561 8.39968H14.316C13.9912 8.39711 13.6678 8.44202 13.356 8.533C13.0177 8.64322 12.7048 8.82002 12.436 9.053C12.1602 9.28801 11.9376 9.57886 11.7826 9.90633C11.6211 10.2255 11.5302 10.5756 11.516 10.933C11.516 12.0263 11.876 13.093 14.516 13.093C17.796 13.093 19.516 15.2263 19.516 17.333C19.5337 17.8819 19.4422 18.429 19.2468 18.9423C19.0514 19.4557 18.756 19.9251 18.3777 20.3233C17.9993 20.7215 17.5456 21.0406 17.0429 21.2619C16.5402 21.4834 15.9985 21.6027 15.4493 21.613L15.5026 21.6663Z" fill="currentColor"/>
                    </svg>
                </div>

                <h1 class="mt-4 text-3xl md:text-5xl text-secondary font-semibold"><span class="text-primary">Biznesingiz</span> uchun<br> mos tarifni tanlang</h1>

                <p class="mt-2.5 lg:mt-4 text-center text-secondary/75">Ehtiyojlaringizga javob beradigan rejalar bilan AI kuchidan foydalaning.<br class="hidden sm:block"> O'zingizga mos keladigan narsani toping va bugunoq optimallashtirishni boshlang</p>
            </div>
        </div>
    </section>
    <!-- Prices Top Content End -->

    <!-- Prices Start -->
    <section class="mt-[60px] lg:mt-[120px] z-[100] relative">
        <div class="container mx-auto px-4">
            <!-- Prices Main Content Start -->
            <div class="flex flex-col space-y-8">
                <!-- Modules Start -->
                <div class="bg-third p-6 md:p-[42px] rounded-[42px]">
                    <p class="text-3xl md:text-4xl text-secondary font-semibold">Modullar</p>

                    <!-- Price List Start -->
                    <div class="mt-5 md:mt-8 flex flex-col space-y-5">

                        @foreach($filteredModules as $module)
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-5 lg:space-y-0 bg-white p-5 md:p-8 rounded-[32px] md:rounded-[42px]">
                                <p class="text-2xl text-secondary font-semibold">{{ $moduleNames[$module['name']->value] ?? $module['name'] }}</p>
                                <div class="lg:basis-[57%] flex flex-col lg:flex-row lg:items-center justify-between space-y-5 lg:space-y-0">
                                    <div>
                                        <p class="text-secondary/55 font-light">Oylik to‘lov</p>
                                        <p class="mt-1 text-xl md:text-2xl text-primary font-semibold">{{ $module['original_amount'] == 0 ? 'Bepul' : number_format($module['original_amount'], 0, '.', ' ') . ' so‘m' }}</p>
                                    </div>
                                    <div>
                                        <a class="group h-[52px] md:h-[58px] flex sm:inline-flex items-center justify-center px-6 bg-white hover:bg-secondary border border-secondary rounded-[20px] transition-all" href="{{'/prices/' . $module['name']->value}}" title="Narxlarni ko‘rish">
                                            <span class="text-secondary group-hover:text-white">Narxlarni ko‘rish</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($hasMultibranch && $multibranchModule)
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-5 lg:space-y-0 bg-white p-5 md:p-8 rounded-[32px] md:rounded-[42px]">
                                <p class="text-2xl text-secondary font-semibold">Qo’shimcha xizmatlar</p>
                                <div class="lg:basis-[57%] sm:flex lg:items-center lg:justify-end">
                                    <a class="group h-[52px] md:h-[58px] flex sm:inline-flex items-center justify-center px-6 bg-white hover:bg-secondary border border-secondary rounded-[20px] transition-all"
                                       href="{{'/prices/multibranch'}}" title="Narxlarni ko‘rish">
                                        <span class="text-secondary group-hover:text-white">Narxlarni ko‘rish</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                    <!-- Price List End -->
                </div>
                <!-- Modules End -->

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
            <!-- Prices Main Content End -->
        </div>
    </section>
    <!-- Prices End -->
@endsection
