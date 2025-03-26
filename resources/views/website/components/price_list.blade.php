<div class="bg-third p-6 md:p-[42px] rounded-[42px]">
    <p class="text-3xl md:text-4xl text-secondary font-semibold">Narxlar</p>

    <div class="mt-5 md:mt-8 flex flex-col space-y-5">
        @foreach ($list as $coast)
            <div class="bg-white p-5 md:p-8 rounded-[32px] md:rounded-[42px]">
                <div class="size-14 flex items-center justify-center rounded-2xl bg-primary/5">
                    <img src="{{ URL::asset($coast['icon']) }}" alt="Icon">
                </div>

                <p class="mt-5 text-xl sm:text-2xl text-secondary font-semibold">{{ $coast['title'] }}</p>
                <p class="mt-2.5 sm:text-lg text-secondary/75 font-light">{{ $coast['desc'] }}</p>

                <!-- Divider Start -->
                <div class="my-6 sm:my-8 h-[1px] bg-[#E6EBF5]"></div>
                <!-- Divider End -->

                <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                    <div>
                        <p class="text-lg/[26px] text-secondary/75 font-light">Xizmat narxi</p>
                        <p class="mt-2 text-2xl/[28px] text-primary font-semibold">{{ $coast['price'] }}</p>
                    </div>

                    @if (!empty($coast['note']))
                        <div class="hidden sm:block w-[1px] h-full bg-[#E6EBF5] py-4"></div>
                        <p class="text-[15px] md:text-lg text-secondary/75 font-light">{!! $coast['note'] !!}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>