<div id="blogs-carousel" class="carousel-wrapper w-full relative overflow-hidden">
    <div class="carousel-track flex transition-transform duration-300">
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/first-blog',
                    'blogImgSrc' => '/assets/images/blog-1_385x300.png',
                    'blogTitle' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                    'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha yo`l ko`rsatadi.....'
                ])
        </div>
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/second-blog',
                    'blogImgSrc' => '/assets/images/blog-2_385x300.png',
                    'blogTitle' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                    'blogCaption' => 'Qanday qilib yangi pudratchi portaliga muammosiz o`tish va pudratchi boshqaruv tizimini yaxshiroq integratsiya, yaxshilangan ish oqimlari va yanada samarali aloqa bilan yaxshilash mumkin...'
                ])
        </div>
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/third-blog',
                    'blogImgSrc' => '/assets/images/blog-3_385x300.png',
                    'blogTitle' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                    'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha...'
                ])
        </div>
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/first-blog',
                    'blogImgSrc' => '/assets/images/blog-1_385x300.png',
                    'blogTitle' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                    'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha yo`l ko`rsatadi.....'
                ])
        </div>
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/second-blog',
                    'blogImgSrc' => '/assets/images/blog-2_385x300.png',
                    'blogTitle' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                    'blogCaption' => 'Qanday qilib yangi pudratchi portaliga muammosiz o`tish va pudratchi boshqaruv tizimini yaxshiroq integratsiya, yaxshilangan ish oqimlari va yanada samarali aloqa bilan yaxshilash mumkin...'
                ])
        </div>
        <div class="carousel-item">
            @include('website.components.blog_card', [
                    'blogLink' => 'blogs/third-blog',
                    'blogImgSrc' => '/assets/images/blog-3_385x300.png',
                    'blogTitle' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                    'blogCaption' => 'Yangi mijoz portaliga o`tmoqchimisiz? Ushbu post sizga ma`lumotlar migratsiyasi, integratsiya va foydalanuvchini qabul qilishni o`z ichiga olgan silliq o`tish uchun asosiy qadamlar bo`yicha...'
                ])
        </div>
    </div>

    <button class="prev size-11 flex items-center justify-center absolute left-0 top-1/3 transform bg-secondary/5 rounded-full backdrop-blur-md cursor-pointer">
        <svg class="rotate-180 text-secondary" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <button class="next size-11 flex items-center justify-center absolute right-0 top-1/3 transform bg-secondary/5 rounded-full backdrop-blur-md cursor-pointer">
        <svg class="text-secondary" width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.3125 15.5L11.2525 10.56C11.531 10.2775 11.6871 9.89675 11.6871 9.5C11.6871 9.10325 11.531 8.72247 11.2525 8.43995L6.3125 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
