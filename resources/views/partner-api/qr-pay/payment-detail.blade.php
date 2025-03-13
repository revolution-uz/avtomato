@php
    /**
     * @var \App\Models\Company\SbpTransaction $transaction
     * @var \App\Models\SbpAccount $terminal
     */
@endphp

@extends('layouts.qrPayLayout')

@section('content')
    <div class="w-full py-4 relative ">
        <button class="absolute">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M11.1436 4.72303C10.8423 4.42566 10.3539 4.42566 10.0526 4.72303L3.22595 11.4616C3.08128 11.6044 3 11.798 3 12C3 12.202 3.08128 12.3956 3.22595 12.5384L10.0526 19.277C10.3539 19.5743 10.8423 19.5743 11.1436 19.277C11.4449 18.9796 11.4449 18.4975 11.1436 18.2001L5.63383 12.7615L20.2286 12.7615C20.6546 12.7615 21 12.4205 21 12C21 11.5794 20.6546 11.2385 20.2286 11.2385L5.63383 11.2385L11.1436 5.79991C11.4449 5.50254 11.4449 5.0204 11.1436 4.72303Z"
                      fill="#050507"/>
            </svg>
        </button>
        <h1 class="flex-grow text-center font-medium">
            To'lov summasi
        </h1>
    </div>

    <div class="flex-auto mt-5 flex flex-col justify-between ">
        <div class="w-full bg-[#FFF] rounded-[16px] p-5 overflow-hidden">
            <div class="flex justify-between mb-3">
                <span class="custom-text-gray">Terminal nomi:</span>
                <span class="custom-text-black">{{ $terminal->company_name }}</span>
            </div>

            <div class="flex justify-between mb-4">
                <span class="custom-text-gray">To'lov summasi:</span>
                <span class="custom-text-black">{{ $transaction->debit->amount / 100 }} UZS</span>
            </div>

            <hr class="border-[#F4F4F4] mb-4">

            <div class="flex justify-between">
                <span class="custom-text-gray">To'lov summasi:</span>
                <span class="custom-text-black">{{ $transaction->credit->amount / 100 }} RUB</span>
            </div>
        </div>

        <div class="flex flex-col items-center pb-3">
            <div class="mb-6 w-[70px]">
                <img src="{{ global_asset('website/images/frontend/qr_pay/sbp-logo.png') }}" alt="SBP logo"
                     class="w-full object-contain">
            </div>

            <a href="{{ $transaction->debit->url }}"
               class="w-full bg-green-500 text-white text-lg text-center font-medium py-3 px-6 rounded-lg shadow hover:bg-green-600">
                Davom etish
            </a>
        </div>
    </div>
@endsection