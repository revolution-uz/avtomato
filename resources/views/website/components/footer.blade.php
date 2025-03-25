<footer class="mt-10 lg:mt-[60px] bg-linear-245 md:bg-linear-200 from-black via-gray-950 to-[#2d638e] relative overflow-hidden">
    <p class="absolute bottom-0 lg:bottom-0 lg:left-1/2 text-[135px] sm:text-[10em] lg:text-[13em] text-white/[3%] sm:text-white/5 leading-[0.7] lg:leading-none font-[Poppins] font-bold lg:-translate-x-1/2 translate-y-1/5 lg:translate-y-2/5 z-[1] pointer-events-none">Avto<br class="sm:hidden">mato</p>

    <div class="container mx-auto px-4 2xl:px-[100px] mb-14 sm:mb-12 pt-14 lg:pt-[100px] z-10">
        <div class="pt-10 lg:pb-[60px] md:w-3/5 lg:w-[55%] mx-auto flex flex-col items-center">
            <h2 class="text-center text-3xl md:text-5xl text-white font-bold">Avtomato’ni hoziroq bepul sinab ko‘ring</h2>
            <p class="mt-4 md:mt-6 text-center md:text-lg text-white/75">Hech qanday to‘lovsiz ro‘yxatdan o‘ting va barcha funksiyalarni cheklovlarsiz sinab ko‘ring.</p>
            <button class="getDemoModalShow mt-5 md:mt-8 h-[52px] md:h-[58px] px-6 inline-flex items-center rounded-2xl bg-primary text-white md:shadow-[0_0_100px_0_#E21010] md:hover:shadow-[0_0_160px_0_#ff0000] transition-all cursor-pointer" type="button">Sinovni boshlash</button>
        </div>

        <div class="pt-10 lg:pt-[60px] flex flex-col lg:flex-row justify-between gap-y-[42px]">
            <div>
                <img class="h-[42px] object-contain" src="{{ asset('/assets/images/logo-2.svg') }}" alt="Avtomato">
                <p class="mt-5 text-white/75">Qiyin jarayonlarni<br> biz bilan birga oddiy qiling!</p>
                <a class="mt-6 inline-block text-[#F0F5FF]" href="mailto:info@automoto.uz" title="info@automoto.uz">info@automoto.uz</a>
            </div>
            <div class="basis-[70%] grid grid-cols-2 md:grid-cols-4 gap-y-8">
                <div>
                    <h3 class="text-lg text-white font-semibold">Sahifalar</h3>
                    <ul class="mt-5">
                        <li><a class="text-white/75" href="{{ route('home') }}" title="Bosh sahifa">Bosh sahifa</a></li>
                        <li class="mt-3"><a class="text-white/75" href="{{ route('installment') }}" title="Yechimlar">Yechimlar</a></li>
                        <li class="mt-3"><a class="text-white/75" href="{{ route('prices') }}" title="Narxlar">Narxlar</a></li>
                        <li class="mt-3"><a class="text-white/75" href="{{ route('blogs') }}" title="Blog">Blog</a></li>
                    </ul>
                </div>
                <div class="order-1 md:order-0 col-span-2 md:col-span-1">
                    <h3 class="text-lg text-white font-semibold">Biz bilan aloqa</h3>
                    <ul class="mt-5">
                        <li><a class="text-white/75" href="mailto:info@automoto.uz" title="info@automoto.uz">info@automoto.uz</a></li>
                    </ul>
                </div>
                <div class="order-0 md:order-0">
                    <h3 class="text-lg text-white font-semibold">Ijtimoiy tarmoqlar</h3>
                    <ul class="mt-5">
                        <li><a class="text-white/75" href="https://instagram.com" target="_blank" title="Instagram link">Instagram</a></li>
                        <li class="mt-3"><a class="text-white/75" href="https://telegram.org" target="_blank" title="Telegram link">Telegram</a></li>
                        <li class="mt-3"><a class="text-white/75" href="https://facebook.com" target="_blank" title="Facebook link">Facebook</a></li>
                        <li class="mt-3"><a class="text-white/75" href="https://linkedin.com" target="_blank" title="LinkedIn link">Linkedin</a></li>
                    </ul>
                </div>
                <div class="order-1 md:order-0 col-span-2 md:col-span-1">
                    <h3 class="text-lg text-white font-semibold">Manzil</h3>
                    <ul class="mt-5">
                        <li><a class="text-white/75" href="https://maps.app.goo.gl/BeJjkwTLKRezd29w6" title="O'zbekiston, Samarqand sh., Turkiston k., 157-uy">O'zbekiston, Samarqand sh., Turkiston k., 157-uy</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <p class="mt-[42px] md:mt-[100px] text-sm md:text-base text-white/75">&copy; 2025 «AUTOMOTO» AJ Barcha huquqlar himoyalangan.</p>
    </div>
</footer>
