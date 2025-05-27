<div class="relative">
    <button id="langBtn"
            class="flex items-center h-11 gap-x-1.5 p-3 border border-secondary/[12%] rounded-2xl cursor-pointer">
        <img class="size-5 shrink-0 pointer-events-none" src="{{ asset('/assets/icons/lang-globe.svg') }}" alt="globe">
        <span class="text-sm font-medium pointer-events-none">UZ</span>
    </button>

    <ul id="langDropdown" style="--dropdown-top: 65px; --dropdown-top-hover: 70px;"
        class="dropdown hide_dropdown absolute -left-1 w-[135px] bg-white rounded-2xl p-1 transition-all ease-linear before:absolute before:-top-2 before:left-1/5 before:rotate-45 before:size-5 before:bg-white before:rounded-[4px] before:z-10">
        <li class="z-20 relative">
            <button class="w-full h-11 px-4 flex items-center space-x-1.5 bg-third rounded-[10px] pointer-events-auto cursor-pointer">
                <span class="text-sm text-secondary">O`zbekcha</span>
                <svg class="text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </li>
        <li>
            <button class="w-full h-11 px-4 flex items-center space-x-1.5 rounded-[10px] pointer-events-auto cursor-pointer">
                <span class="text-sm text-secondary">Русский</span>
                <svg class="hidden text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </li>
        <li>
            <button class="w-full h-11 px-4 flex items-center space-x-1.5 rounded-[10px] pointer-events-auto cursor-pointer">
                <span class="text-sm text-secondary">Узбекский</span>
                <svg class="hidden text-primary" width="16" height="16" viewBox="0 0 16 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 7.86295L5.81429 10.6773C5.91329 10.7775 6.03118 10.8571 6.16115 10.9114C6.29112 10.9656 6.43057 10.9936 6.57143 10.9936C6.71233 10.9936 6.85173 10.9656 6.98173 10.9114C7.11167 10.8571 7.22953 10.7775 7.3286 10.6773L13 5.00586"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </li>
    </ul>
</div>