<header class="flex items-center justify-between py-5 container mx-auto px-5 md:px-4 xl:px-0 z-[101] bg-white">
    <a href="{{ route('home') }}" title="Avtomato">
        <img class="pointer-events-none" src="{{ asset('/assets/images/logo-1.svg') }}" alt="Avtomato"/>
    </a>

    <!-- Navigation List Start -->
    <ul class="hidden lg:flex items-center gap-8">
        <li class="group relative">
            <p id="solutionsLink" class="flex items-center gap-2 text-secondary/[75%] hover:text-primary cursor-pointer">
                <span>Yechimlar</span>
                <svg class="group-hover:-rotate-180 transition-all" width="14" height="7" viewBox="0 0 14 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.2787 0.966797L7.93208 5.31346C7.41875 5.8268 6.57875 5.8268 6.06542 5.31346L1.71875 0.966797" stroke="currentColor" stroke-opacity="0.75" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </p>

            <!-- Navigation SubList Start -->
            <div id="solutionsDropdown"
                 class="dropdown hide_dropdown absolute -left-[150%] w-[700px] bg-white rounded-2xl p-6 transition-all ease-linear before:absolute before:-top-2 before:left-1/4 before:rotate-45 before:size-5 before:bg-white before:rounded-[4px]">
                <div class="grid grid-cols-2 gap-y-4 gap-x-10">
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/nasiya" title="Nasiya savdoni avtomatlashtirish">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.8525 2.75H9.68577C6.84764 2.75 4.54688 5.05076 4.54688 7.88889V16.1111C4.54688 18.9492 6.84764 21.25 9.68577 21.25H15.8525C18.6906 21.25 20.9913 18.9492 20.9913 16.1111V7.88889C20.9913 5.05076 18.6906 2.75 15.8525 2.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.00781 8.40234H6.09115" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.00781 15.5977H6.09115" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.6698 16.7448C16.6698 15.0921 14.8274 13.749 13.1747 13.749C11.522 13.749 9.67969 15.0921 9.67969 16.7448" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.1762 11.368C14.473 11.368 15.5243 10.3167 15.5243 9.01997C15.5243 7.72315 14.473 6.67188 13.1762 6.67188C11.8793 6.67188 10.8281 7.72315 10.8281 9.01997C10.8281 10.3167 11.8793 11.368 13.1762 11.368Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">Nasiya savdoni avtomatlashtirish</span>
                    </a>
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/marketing" title="Marketing">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.6621 10.6602C10.3053 11.0149 10.1037 11.4966 10.1016 11.9997C10.1016 12.5036 10.3018 12.9869 10.6581 13.3432C11.0144 13.6995 11.4977 13.8997 12.0016 13.8997C12.5047 13.8976 12.9864 13.696 13.3411 13.3392" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12.0008 6.2998C10.8734 6.2998 9.77139 6.6341 8.83403 7.26043C7.89667 7.88675 7.16609 8.77697 6.73467 9.81851C6.30325 10.86 6.19037 12.0061 6.4103 13.1118C6.63024 14.2175 7.17311 15.2332 7.97027 16.0303C8.76743 16.8275 9.78307 17.3703 10.8888 17.5903C11.9945 17.8102 13.1405 17.6973 14.1821 17.2659C15.2236 16.8345 16.1138 16.1039 16.7402 15.1666C17.3665 14.2292 17.7008 13.1272 17.7008 11.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 2.5C10.1211 2.5 8.28435 3.05717 6.72209 4.10104C5.15982 5.14491 3.94218 6.62861 3.22315 8.36451C2.50412 10.1004 2.31598 12.0105 2.68254 13.8534C3.0491 15.6962 3.95389 17.3889 5.28249 18.7175C6.61109 20.0461 8.30383 20.9509 10.1466 21.3175C11.9895 21.684 13.8996 21.4959 15.6355 20.7769C17.3714 20.0578 18.8551 18.8402 19.899 17.2779C20.9428 15.7156 21.5 13.8789 21.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.5237 8.47559L12.9492 11.0501" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20.9401 5.84387L17.7006 6.29987L18.1566 3.06038C18.1602 3.02261 18.1523 2.98464 18.1341 2.95136C18.1159 2.91808 18.0882 2.89101 18.0544 2.87364C18.0207 2.85626 17.9826 2.84938 17.9449 2.85388C17.9072 2.85838 17.8718 2.87405 17.8431 2.89888L15.6961 5.03637C15.4756 5.25854 15.3133 5.53177 15.2238 5.83175C15.1343 6.13173 15.1203 6.44918 15.1831 6.75587L15.5251 8.47537L17.2446 8.81737C17.5513 8.88014 17.8687 8.86615 18.1687 8.77665C18.4687 8.68715 18.7419 8.52492 18.9641 8.30437L21.1016 6.15737C21.1264 6.12869 21.1421 6.09323 21.1466 6.05556C21.1511 6.0179 21.1442 5.97975 21.1268 5.94602C21.1095 5.9123 21.0824 5.88455 21.0491 5.86634C21.0158 5.84814 20.9779 5.84031 20.9401 5.84387Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">Marketing</span>
                    </a>
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/skoring" title="Skoring">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.2526 8.81368V6.75813C21.2526 5.94038 20.9277 5.15612 20.3495 4.57789C19.7713 3.99965 18.987 3.6748 18.1692 3.6748H15.0859" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.0859 22.175H18.1692C18.987 22.175 19.7713 21.8501 20.3495 21.2719C20.9277 20.6936 21.2526 19.9094 21.2526 19.0916V17.0361" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.75 17.0361V19.0916C2.75 19.9094 3.07485 20.6936 3.65309 21.2719C4.23132 21.8501 5.01558 22.175 5.83333 22.175H8.91667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.91667 3.6748H5.83333C5.01558 3.6748 4.23132 3.99965 3.65309 4.57789C3.07485 5.15612 2.75 5.94038 2.75 6.75813V8.81368" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.1772 22.175C18.1772 19.2544 14.9213 16.8809 12.0007 16.8809C9.0801 16.8809 5.82422 19.2544 5.82422 22.175" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.9982 14.2336C13.9474 14.2336 15.5276 12.6534 15.5276 10.7042C15.5276 8.75497 13.9474 7.1748 11.9982 7.1748C10.049 7.1748 8.46875 8.75497 8.46875 10.7042C8.46875 12.6534 10.049 14.2336 11.9982 14.2336Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">Skoring</span>
                    </a>
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/qr-pay" title="QR Pay">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.43203 5.83301H6.73203C6.23498 5.83301 5.83203 6.23596 5.83203 6.73301V9.43301C5.83203 9.93007 6.23498 10.333 6.73203 10.333H9.43203C9.92909 10.333 10.3321 9.93007 10.3321 9.43301V6.73301C10.3321 6.23596 9.92909 5.83301 9.43203 5.83301Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.43203 13.6475H6.73203C6.23498 13.6475 5.83203 14.0505 5.83203 14.5475V17.2475C5.83203 17.7446 6.23498 18.1475 6.73203 18.1475H9.43203C9.92909 18.1475 10.3321 17.7446 10.3321 17.2475V14.5475C10.3321 14.0505 9.92909 13.6475 9.43203 13.6475Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.268 5.83301H14.568C14.071 5.83301 13.668 6.23596 13.668 6.73301V9.43301C13.668 9.93007 14.071 10.333 14.568 10.333H17.268C17.7651 10.333 18.168 9.93007 18.168 9.43301V6.73301C18.168 6.23596 17.7651 5.83301 17.268 5.83301Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.268 13.667H14.568C14.071 13.667 13.668 14.07 13.668 14.567V17.267C13.668 17.7641 14.071 18.167 14.568 18.167H17.268C17.7651 18.167 18.168 17.7641 18.168 17.267V14.567C18.168 14.07 17.7651 13.667 17.268 13.667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21.2487 8.91667V5.83333C21.2487 5.01558 20.9238 4.23132 20.3456 3.65309C19.7674 3.07485 18.9831 2.75 18.1653 2.75H15.082" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.082 21.2497H18.1653C18.9831 21.2497 19.7674 20.9248 20.3456 20.3466C20.9238 19.7684 21.2487 18.9841 21.2487 18.1663V15.083" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.75 15.083V18.1663C2.75 18.9841 3.07485 19.7684 3.65309 20.3466C4.23132 20.9248 5.01558 21.2497 5.83333 21.2497H8.91667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.91667 2.75H5.83333C5.01558 2.75 4.23132 3.07485 3.65309 3.65309C3.07485 4.23132 2.75 5.01558 2.75 5.83333V8.91667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">QR pay</span>
                    </a>
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/loyalty" title="Loyalty">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.9254 19.6341H20.063C20.2512 19.6373 20.4374 19.5949 20.6056 19.5104C20.7739 19.4259 20.9191 19.3019 21.0289 19.1489C21.1386 18.996 21.2097 18.8187 21.2359 18.6323C21.2622 18.4459 21.2428 18.2559 21.1795 18.0786C20.6343 16.9117 19.7712 15.9221 18.6893 15.2234C17.6074 14.5246 16.3504 14.1449 15.0625 14.1279" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.0625 11.3697C15.5438 11.3697 16.0203 11.275 16.4649 11.0908C16.9095 10.9066 17.3134 10.6367 17.6537 10.2964C17.994 9.95614 18.2639 9.55217 18.4481 9.10758C18.6322 8.66298 18.727 8.18648 18.727 7.70525C18.7283 7.22323 18.6344 6.74569 18.4508 6.29999C18.2672 5.8543 17.9975 5.44921 17.6571 5.10792C17.3167 4.76663 16.9123 4.49585 16.4671 4.3111C16.0219 4.12635 15.5446 4.03125 15.0625 4.03125" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.0251 20.8272C14.3771 20.8264 14.7221 20.7282 15.0218 20.5435C15.3215 20.3588 15.5643 20.0948 15.7233 19.7808C15.8823 19.4667 15.9513 19.1147 15.9228 18.7639C15.8942 18.413 15.7692 18.0768 15.5615 17.7926C14.8329 16.8247 13.8947 16.0343 12.8171 15.4806C11.7396 14.927 10.5507 14.6244 9.33953 14.5957C8.1284 14.6244 6.93948 14.927 5.86193 15.4806C4.78437 16.0343 3.84614 16.8247 3.11758 17.7926C2.90988 18.0768 2.78484 18.413 2.75629 18.7639C2.72774 19.1147 2.79678 19.4667 2.95579 19.7808C3.11481 20.0948 3.35759 20.3588 3.6573 20.5435C3.957 20.7282 4.30195 20.8264 4.65398 20.8272H14.0251Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.33865 11.4847C10.4405 11.4822 11.4963 11.0427 12.2745 10.2627C13.0527 9.48267 13.4898 8.42583 13.4898 7.324C13.4898 6.22305 13.0525 5.16718 12.274 4.38869C11.4955 3.6102 10.4397 3.17285 9.33865 3.17285C8.2377 3.17285 7.18184 3.6102 6.40335 4.38869C5.62486 5.16718 5.1875 6.22305 5.1875 7.324C5.1875 8.42583 5.62455 9.48267 6.40277 10.2627C7.18099 11.0427 8.23683 11.4822 9.33865 11.4847Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">Loyalty</span>
                    </a>
                    <a class="flex items-center space-x-3 text-secondary" href="/prices/others" title="Qo`shimcha xizmatlar">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.25 2.75H8.75C5.43629 2.75 2.75 5.43629 2.75 8.75V15.25C2.75 18.5637 5.43629 21.25 8.75 21.25H15.25C18.5637 21.25 21.25 18.5637 21.25 15.25V8.75C21.25 5.43629 18.5637 2.75 15.25 2.75Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7.67188 16.2221V11.123" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M12.125 16.2221V7.77734" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M16.3281 16.2223V9.81934" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <span class="font-medium">Qo`shimcha xizmatlar</span>
                    </a>
                    <a class="flex items-center space-x-3 text-[#95989D] pointer-events-none" href="/prices" title="Internet do’kon yaratish (soon)">
                        <div class="bg-third size-[42px] flex items-center justify-center rounded-2xl opacity-50">
                            <svg class="text-secondary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.25 3.33008H5.75C4.95435 3.33008 4.19129 3.64615 3.62868 4.20876C3.06607 4.77137 2.75 5.53443 2.75 6.33008V14.2001C2.75 14.9958 3.06607 15.7588 3.62868 16.3214C4.19129 16.884 4.95435 17.2001 5.75 17.2001H18.25C19.0456 17.2001 19.8087 16.884 20.3713 16.3214C20.9339 15.7588 21.25 14.9958 21.25 14.2001V6.33008C21.25 5.53443 20.9339 4.77137 20.3713 4.20876C19.8087 3.64615 19.0456 3.33008 18.25 3.33008Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 17.2002V20.6702" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.53125 20.6699H15.4712" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-medium opacity-50">Internet do’kon yaratish</span>
                        <span class="text-primary font-medium">(soon)</span>
                    </a>
                </div>
            </div>
            <!-- Navigation SubList End -->
        </li>
        <li><a class="text-secondary/[75%] hover:text-primary" href="#" title="Nima uchun Avtomato?">Nima uchun Avtomato?</a></li>
        <li><a class="text-secondary/[75%] hover:text-primary" href="{{ route('prices') }}" title="Narxlar">Narxlar</a></li>
        <li><a class="text-secondary/[75%] hover:text-primary" href="{{ route('blogs') }}" title="Blog">Blog</a></li>
    </ul>
    <!-- Navigation List End -->

    <div class="hidden lg:flex items-center gap-x-5">
        <!-- Language Dropdown Start -->
        <div class="relative">
            <button id="langBtn" class="flex items-center h-11 gap-x-1.5 p-3 border border-secondary/[12%] rounded-2xl cursor-pointer">
                <img class="size-5 shrink-0 pointer-events-none" src="{{ asset('/assets/icons/lang-globe.svg') }}" alt="globe">
                <span class="text-sm font-medium pointer-events-none">UZ</span>
            </button>

            <ul id="langDropdown" style="--dropdown-top: 65px; --dropdown-top-hover: 70px;" class="dropdown hide_dropdown absolute -left-1 w-[135px] bg-white rounded-2xl p-1 transition-all ease-linear before:absolute before:-top-2 before:left-1/5 before:rotate-45 before:size-5 before:bg-white before:rounded-[4px] before:z-10">
                <li class="z-20 relative">
                    <button class="w-full h-11 px-4 flex items-center space-x-1.5 bg-third rounded-[10px] pointer-events-auto cursor-pointer">
                        <span class="text-sm text-secondary">O`zbekcha</span>
                        <svg class="text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </li>
                <li>
                    <button class="w-full h-11 px-4 flex items-center space-x-1.5 rounded-[10px] pointer-events-auto cursor-pointer">
                        <span class="text-sm text-secondary">Русский</span>
                        <svg class="hidden text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </li>
                <li>
                    <button class="w-full h-11 px-4 flex items-center space-x-1.5 rounded-[10px] pointer-events-auto cursor-pointer">
                        <span class="text-sm text-secondary">Узбекский</span>
                        <svg class="hidden text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
        <!-- Language Dropdown End -->

        <!-- Vertical Divider Start -->
        <div class="h-6 w-[1px] bg-[#D9D9D9]/[40%]"></div>
        <!-- Vertical Divider End -->

        <!-- Sign in and Demo Start -->
        <div class="flex items-center gap-x-6">
            <!-- <a class="text-sm text-secondary hover:text-primary font-medium" href="#">Kirish</a> -->
            <button class="getDemoModalShow py-3.5 px-6 rounded-2xl bg-secondary text-sm text-white font-medium hover:bg-secondary/[90%] transition-all cursor-pointer" type="button">Demo olish</button>
        </div>
        <!-- Sign in and Demo End -->
    </div>

    <!-- Burger Menu Button Start -->
    <button class="bg-third size-12 flex items-center justify-center lg:hidden rounded-2xl text-secondary pointer-events-none">
                    <span class="size-6 relative overflow-hidden flex items-center justify-center flex-col pointer-events-none">
                        <svg id="burgerMenuShow" class="show_burger-btn absolute transition-all" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.5 12H19.5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
                            <path d="M4.5 17.7695H19.5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
                            <path d="M4.5 6.23047H19.5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
                        </svg>

                        <svg id="burgerMenuClose" class="hide_burger-btn absolute transition-all" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 5L5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 19L5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
    </button>
    <!-- Burger Menu Button End -->
</header>
