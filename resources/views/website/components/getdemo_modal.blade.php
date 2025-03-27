<div id="getDemoModalWrap" class="hide_modal--wrap w-screen h-screen fixed left-0 bg-secondary/50 z-[1001] transition-all">
    <!-- Modal Inner Start -->
    <div id="getDemoModalInner" class="hide_modal w-[90%] sm:w-full max-w-[580px] max-h-[520px] bg-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-[32px] md:rounded-[60px] p-6 sm:p-8 md:p-[42px] transition-all">
        <!-- Modal Close Button Start -->
        <button id="getDemoModalClose" class="flex items-center justify-center size-8 md:size-12 rounded-[10px] bg-third absolute top-4 right-4 md:top-8 md:right-8 cursor-pointer" type="button">
            <img class="pointer-events-none" src="{{ asset('/assets/icons/close.svg') }}" alt="Close icon">
        </button>
        <!-- Modal Close Button End -->

        <!-- Modal Content Start -->
        <h2 class="text-center text-xl sm:text-3xl text-secondary font-semibold">So‘rov qoldiring,<br> menejerlarimiz bog‘lanadi!</h2>
        <form class="mt-6 md:mt-8 flex flex-col gap-y-3 w-full">
            <input class="h-12 sm:h-16 px-4 sm:px-5 bg-third outline-1 -outline-offset-1 outline-[#D9D9D9]/40 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-blue-600 rounded-2xl placeholder-secondary/55" type="text" placeholder="Ism familiya">
            <div class="group flex items-center space-x-1 h-12 sm:h-16 px-4 sm:px-5 bg-third rounded-2xl outline-1 -outline-offset-1 outline-[#D9D9D9]/40 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-blue-600">
                <div class="flex items-center space-x-3 shrink-0">
                    <img src="{{ asset('/assets/icons/flag-uzbekistan.svg') }}" alt="Flag Uzbeksitan">
                    <p>+998</p>
                </div>
                <input id="phoneInput" maxlength="13" class="grow placeholder-secondary/55 focus:outline-none" type="tel" placeholder="(00) 000 0000">
            </div>
            <button class="messageSentModalShow mt-3 md:mt-5 h-[58px] bg-secondary rounded-2xl cursor-pointer" type="button">
                <span class="text-sm md:text-base text-white font-medium">So’rov yuborish</span>
            </button>
        </form>
        <!-- Modal Content End -->
    </div>
    <!-- Modal Inner End -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const phoneInput = document.getElementById("phoneInput");

        phoneInput.addEventListener("input", (e) => {
            let value = e.target.value.replace(/\D/g, ""); // Убираем все, кроме цифр

            // Ограничиваем ввод до 9 цифр
            if (value.length > 9) {
                value = value.substring(0, 9);
            }

            // Форматируем номер
            let formattedValue = "";
            if (value.length > 0) formattedValue += `(${value.substring(0, 2)}`;
            if (value.length > 2) formattedValue += `) ${value.substring(2, 5)}`;
            if (value.length > 5) formattedValue += ` ${value.substring(5, 9)}`;

            e.target.value = formattedValue;
        });

        phoneInput.addEventListener("keydown", (e) => {
            const cursorPos = phoneInput.selectionStart;

            // Запрещаем удаление скобок и пробелов
            if (e.key === "Backspace") {
                if (cursorPos === 1 || cursorPos === 4 || cursorPos === 5 || cursorPos === 9) {
                    e.preventDefault();
                }
            }
        });
    });
</script>