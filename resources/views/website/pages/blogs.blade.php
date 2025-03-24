@extends('website.layout')

@section('title', 'Blog - Avtomato')

@section('content')
    <!-- All Blogs Start -->
    <section class="pt-10 pb-10 md:pb-[60x]">
        <div class="container mx-auto px-4">
            <!-- Blogs Top Content Start -->
            <div class="flex flex-col items-center">
                <div class="size-[52px] flex items-center justify-center rounded-2xl bg-primary/5">
                    <svg class="text-primary" width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M29.832 7.72994V22.0767C29.8476 22.4044 29.7931 22.7316 29.672 23.0367C29.4844 23.5245 29.1475 23.9407 28.7094 24.2256C28.2711 24.5105 27.7542 24.6497 27.2322 24.6233C25.7322 24.6019 24.2342 24.7449 22.7654 25.05C21.0207 25.2803 19.3418 25.8656 17.832 26.77V6.95664C19.2846 6.25757 20.8439 5.80689 22.4454 5.6233C24.0778 5.29766 25.7414 5.15456 27.4055 5.19662C27.732 5.19085 28.0556 5.25922 28.352 5.39661C28.6582 5.52656 28.9348 5.71705 29.1654 5.95664C29.391 6.20577 29.5674 6.49528 29.6854 6.80994C29.7939 7.10409 29.8438 7.41665 29.832 7.72994Z" fill="currentColor"/>
                        <path d="M10.6319 5.58311C8.98232 5.2508 7.30084 5.1032 5.61858 5.14308C5.29716 5.13951 4.97823 5.2002 4.68059 5.32155C4.38294 5.44291 4.11263 5.6225 3.88534 5.84979C3.65806 6.07708 3.47838 6.34751 3.35702 6.64516C3.23567 6.94282 3.17506 7.2617 3.17863 7.58311V22.0764C3.13916 22.4321 3.18012 22.7921 3.29859 23.1297C3.41714 23.4652 3.60842 23.7703 3.85864 24.0231C4.09836 24.2737 4.38903 24.47 4.71103 24.5988C5.03303 24.7276 5.37892 24.7859 5.72535 24.7697C7.26632 24.75 8.80463 24.9021 10.3119 25.2231C12.0267 25.424 13.6801 25.9828 15.1654 26.8631V6.95644C13.7437 6.24303 12.2104 5.77854 10.6319 5.58311ZM7.96526 10.2764H9.63192C9.89714 10.2764 10.1516 10.3818 10.3391 10.5693C10.5267 10.7569 10.6319 11.0112 10.6319 11.2764C10.6319 11.5416 10.5267 11.796 10.3391 11.9835C10.1516 12.1711 9.89714 12.2764 9.63192 12.2764H7.96526C7.70004 12.2764 7.44576 12.1711 7.25823 11.9835C7.07068 11.796 6.96526 11.5416 6.96526 11.2764C6.96526 11.0112 7.07068 10.7569 7.25823 10.5693C7.44576 10.3818 7.70004 10.2764 7.96526 10.2764ZM10.4186 20.2897H7.96526C7.70004 20.2897 7.44576 20.1844 7.25823 19.9969C7.07068 19.8093 6.96526 19.5549 6.96526 19.2897C6.96526 19.0245 7.07068 18.7701 7.25823 18.5827C7.44576 18.3951 7.70004 18.2897 7.96526 18.2897H10.4186C10.6838 18.2897 10.9382 18.3951 11.1257 18.5827C11.3133 18.7701 11.4186 19.0245 11.4186 19.2897C11.4186 19.5549 11.3133 19.8093 11.1257 19.9969C10.9382 20.1844 10.6838 20.2897 10.4186 20.2897ZM12.6453 16.2897H8.17863C7.91342 16.2897 7.65898 16.1844 7.47145 15.9969C7.28391 15.8093 7.17863 15.5549 7.17863 15.2897C7.17863 15.0245 7.28391 14.7701 7.47145 14.5827C7.65898 14.3951 7.91342 14.2897 8.17863 14.2897H12.6453C12.9105 14.2897 13.1649 14.3951 13.3525 14.5827C13.54 14.7701 13.6453 15.0245 13.6453 15.2897C13.6453 15.5549 13.54 15.8093 13.3525 15.9969C13.1649 16.1844 12.9105 16.2897 12.6453 16.2897Z" fill="currentColor"/>
                    </svg>
                </div>

                <h1 class="mt-4 text-3xl lg:text-5xl text-secondary font-semibold">Blog</h1>

                <div id="blogsTags" class="mt-8 px-4 flex flex-wrap justify-center gap-3">
                    <button class="h-[42px] flex items-center px-5 border rounded-2xl text-sm cursor-pointer bg-secondary border-secondary text-white" type="button">Barchasi</button>
                    <button class="h-[42px] flex items-center px-5 border rounded-2xl cursor-pointer bg-white border-secondary text-secondary" type="button">Avtomato imkoniyatlari</button>
                    <button class="h-[42px] flex items-center px-5 border rounded-2xl cursor-pointer bg-white border-secondary text-secondary" type="button">Tashkilot yangiliklari</button>
                    <button class="h-[42px] flex items-center px-5 border rounded-2xl cursor-pointer bg-white border-secondary text-secondary" type="button">Foydali</button>
                </div>
            </div>
            <!-- Blogs Top Content End -->

            <!-- Blogs Grid Columns Start -->
            <div class="my-[60px] grid sm:grid-cols-2 lg:grid-cols-3 gap-y-11 gap-x-5">
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/first-blog',
                        'blogImgSrc' => '/assets/images/blog-1_385x300.png',
                        'blogTitle' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha yo`l ko`rsatadi.....'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/second-blog',
                        'blogImgSrc' => '/assets/images/blog-2_385x300.png',
                        'blogTitle' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                        'blogCaption' => 'Qanday qilib yangi pudratchi portaliga muammosiz o`tish va pudratchi boshqaruv tizimini yaxshiroq integratsiya, yaxshilangan ish oqimlari va yanada samarali aloqa bilan yaxshilash mumkin...'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/third-blog',
                        'blogImgSrc' => '/assets/images/blog-3_385x300.png',
                        'blogTitle' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha...'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/first-blog',
                        'blogImgSrc' => '/assets/images/blog-1_385x300.png',
                        'blogTitle' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha yo`l ko`rsatadi.....'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/second-blog',
                        'blogImgSrc' => '/assets/images/blog-2_385x300.png',
                        'blogTitle' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                        'blogCaption' => 'Qanday qilib yangi pudratchi portaliga muammosiz o`tish va pudratchi boshqaruv tizimini yaxshiroq integratsiya, yaxshilangan ish oqimlari va yanada samarali aloqa bilan yaxshilash mumkin...'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/third-blog',
                        'blogImgSrc' => '/assets/images/blog-3_385x300.png',
                        'blogTitle' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha...'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/first-blog',
                        'blogImgSrc' => '/assets/images/blog-1_385x300.png',
                        'blogTitle' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha yo`l ko`rsatadi.....'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/second-blog',
                        'blogImgSrc' => '/assets/images/blog-2_385x300.png',
                        'blogTitle' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                        'blogCaption' => 'Qanday qilib yangi pudratchi portaliga muammosiz o`tish va pudratchi boshqaruv tizimini yaxshiroq integratsiya, yaxshilangan ish oqimlari va yanada samarali aloqa bilan yaxshilash mumkin...'
                    ])
                @include('website.components.blog_card', [
                        'blogLink' => 'blogs/third-blog',
                        'blogImgSrc' => '/assets/images/blog-3_385x300.png',
                        'blogTitle' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                        'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha...'
                    ])
            </div>
            <!-- Blogs Grid Columns End -->

            <!-- Blogs Pagination Start -->
            <div class="w-full flex items-center justify-center gap-3">
                <button class="size-12 flex items-center justify-center bg-secondary border border-secondary rounded-2xl cursor-pointer" type="button">
                    <svg class="rotate-180 text-white" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="size-12 flex items-center justify-center border border-secondary rounded-2xl cursor-pointer" type="button"><span class="text-secondary">1</span></button>
                <button class="size-12 flex items-center justify-center border border-secondary rounded-2xl cursor-pointer" type="button"><span class="text-secondary">2</span></button>
                <button class="size-12 flex items-center justify-center border border-secondary rounded-2xl cursor-pointer" type="button"><span class="text-secondary">3</span></button>
                <button class="size-12 flex items-center justify-center border border-secondary rounded-2xl cursor-pointer" type="button"><span class="text-secondary">...</span></button>
                <button class="size-12 flex items-center justify-center bg-secondary border border-secondary rounded-2xl cursor-pointer" type="button">
                    <svg class="text-white" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <!-- Blogs Pagination End -->
        </div>
    </section>
    <!-- All Blogs End -->
@endsection
