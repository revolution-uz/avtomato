<div id="getDemoModalWrap"
     class="hide_modal--wrap w-screen h-screen fixed top-0 right-0 bottom-0 left-0 bg-secondary/50 z-[103] transition-all">
    <!-- Modal Inner Start -->
    <div id="getDemoModalInner"
         class="hide_modal w-[90%] sm:w-full max-w-[580px] max-h-[520px] bg-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-[32px] md:rounded-[60px] p-6 sm:p-8 md:p-[42px] transition-all">
        <!-- Modal Close Button Start -->
        <button id="getDemoModalClose"
                class="flex items-center justify-center size-8 md:size-12 rounded-[10px] bg-third absolute top-4 right-4 md:top-8 md:right-8 cursor-pointer"
                type="button">
            <img class="pointer-events-none" src="{{ asset('/assets/icons/close.svg') }}" alt="Close icon">
        </button>
        <!-- Modal Close Button End -->

        <!-- Modal Content Start -->
        <h2 class="text-center text-xl sm:text-3xl text-secondary font-semibold">So‘rov qoldiring,<br> menejerlarimiz
            bog‘lanadi!</h2>
        <form method="POST" action="{{ route('new_customer') }}" class="mt-6 md:mt-8 flex flex-col gap-y-3 w-full">
            @csrf
            <input
                    class="h-12 sm:h-16 px-4 sm:px-5 bg-third outline-1 -outline-offset-1 outline-[#D9D9D9]/40 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-blue-600 rounded-2xl placeholder-secondary/55"
                    type="text"
                    placeholder="Ism familiya"
                    required
                    name="full_name"
                    id="fioValue"
            />
            <div class="group flex items-center space-x-1 h-12 sm:h-16 px-4 sm:px-5 bg-third rounded-2xl outline-1 -outline-offset-1 outline-[#D9D9D9]/40 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-blue-600">
                <div class="flex items-center space-x-3 shrink-0">
                    <img src="{{ asset('/assets/icons/flag-uzbekistan.svg') }}" alt="Flag Uzbeksitan">
                    <p>+998</p>
                </div>
                <input
                        id="phoneDisplay"
                        class="grow placeholder-secondary/55 focus:outline-none"
                        type="tel"
                        placeholder="(00) 000 0000"
                        maxlength="13"
                />
            </div>
            <input type="hidden" id="phoneValue" name="phone"/>
            <button id="form-submit"
                    class="messageSentModalShow mt-3 md:mt-5 h-[58px] bg-secondary hover:bg-secondary/90 rounded-2xl cursor-pointer disabled:bg-secondary/50 disabled:cursor-not-allowed"
                    type="submit" disabled="disabled">
                <span class="text-sm md:text-base text-white font-medium">So’rov yuborish</span>
            </button>
        </form>
        <!-- Modal Content End -->
    </div>
    <!-- Modal Inner End -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const fioValue = document.getElementById("fioValue");
        const phoneDisplay = document.getElementById("phoneDisplay");
        const phoneValue = document.getElementById("phoneValue");
        const formSubmitBtn = document.getElementById("form-submit");

        let valid = {
            fio: false,
            phone: false
        }

        fioValue.addEventListener('input', (e) => {
            let newValue = e.target.value.replace(/[^a-zA-Zа-яА-ЯёЁ'` -]/g, "");

            if (newValue.length > 50) {
                e.target.value = newValue.substring(0, 50);
            }

            e.target.value = newValue;

            valid.fio = fioValue.value.length > 3;

            if (valid.fio && valid.phone) {
                formSubmitBtn.removeAttribute('disabled');
            } else {
                formSubmitBtn.setAttribute('disabled', 'disabled');
            }
        })

        phoneDisplay.addEventListener("input", (e) => {
            let value = e.target.value.replace(/\D/g, "");

            if (value.length > 9) {
                value = value.substring(0, 9);
            }

            let formattedValue = "";
            if (value.length > 0) formattedValue += `(${value.substring(0, 2)}`;
            if (value.length > 2) formattedValue += `) ${value.substring(2, 5)}`;
            if (value.length > 5) formattedValue += ` ${value.substring(5, 9)}`;

            e.target.value = formattedValue;

            phoneValue.value = value;

            valid.phone = phoneValue.value.length === 9;

            if (valid.fio && valid.phone) {
                formSubmitBtn.removeAttribute('disabled');
            } else {
                formSubmitBtn.setAttribute('disabled', 'disabled');
            }
        });
    });

    @if (session()->has('success'))
    window.addEventListener('load', function () {
        const messageSentModalInner = document.querySelector('#messageSentModalInner');
        const messageSentModalWrap = document.querySelector('#messageSentModalWrap');

        messageSentModalWrap.classList.add('show_modal--wrap');
        messageSentModalWrap.classList.remove('hide_modal--wrap');
        messageSentModalInner.classList.remove('hide_modal');
        messageSentModalInner.classList.add('show_modal');
    })
    @endif
</script>
