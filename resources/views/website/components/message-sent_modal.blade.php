<div id="messageSentModalWrap" class="hide_modal--wrap w-screen h-screen fixed left-0 bg-secondary/50 z-[1001] transition-all">
    <!-- Modal Inner Start -->
    <div id="messageSentModalInner" class="hide_modal w-[90%] sm:w-full max-w-[580px] max-h-[520px] bg-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-[32px] md:rounded-[60px] p-6 sm:p-8 md:p-[42px] transition-all">
        <!-- Modal Close Button Start -->
        <button class="messageSentModalClose flex items-center justify-center size-8 md:size-12 rounded-[10px] bg-third absolute top-4 right-4 md:top-8 md:right-8 cursor-pointer" type="button">
            <img class="pointer-events-none" src="{{ asset('/assets/icons/close.svg') }}" alt="Close icon">
        </button>
        <!-- Modal Close Button End -->

        <!-- Modal Content Start -->
        <div class="flex items-center justify-center">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_662_4257" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="120" height="120">
                    <path d="M0 0H120V120H0V0Z" fill="white"/>
                </mask>
                <g mask="url(#mask0_662_4257)">
                    <mask id="mask1_662_4257" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="120" height="120">
                        <path d="M0 0H120V120H0V0Z" fill="white"/>
                    </mask>
                    <g mask="url(#mask1_662_4257)">
                        <path d="M16.3359 64.6598C39.2168 87.22 45.0008 93.1299 45.0008 93.1299C45.0008 93.1299 86.889 51.2868 105.519 32.6582" stroke="#259E49" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </g>
            </svg>
        </div>
        <h2 class="mt-5 text-center text-2xl sm:text-3xl text-secondary font-semibold">So’rovingiz yuborildi!</h2>
        <p class="mt-2.5 md:mt-4 text-center text-secondary/75">Rahmat! Menejerlarimiz siz bilan tez orada bog‘lanib, barcha savollaringizga javob berishadi.</p>
        <button class="messageSentModalClose mt-6 md:mt-8 w-full h-[58px] bg-secondary rounded-2xl cursor-pointer" type="button">
            <span class="text-sm md:text-base text-white font-semibold">Tushunarli</span>
        </button>
        <!-- Modal Content End -->
    </div>
    <!-- Modal Inner End -->
</div>
