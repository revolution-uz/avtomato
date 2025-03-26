<div class="bg-third p-6 md:p-[42px] rounded-[42px]">
    <p class="text-3xl md:text-4xl text-secondary font-semibold">Qo’shimcha xodim va filiallar qo’shish uchun modul</p>

    <div class="mt-5 md:mt-8 flex flex-col sm:flex-row gap-5 sm:gap-0 overflow-hidden rounded-[32px]">
        @foreach($list as $fields)
            <ul class="grow bg-white rounded-[18px] sm:rounded-[0]">
                @foreach($fields as $field)
                    <li class="w-full py-4 lg:py-[22px] px-6 md:px-[42px] even:bg-[#F9F9F9] first:font-semibold font-medium">
                        <p class="md:text-lg lg:text-2xl text-secondary">{{ $field }}</p>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>