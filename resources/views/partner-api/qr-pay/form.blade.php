@extends('layouts.qrPayLayout')

@section('content')
    <div class="mt-[60px] flex flex-col items-center">
        <div class="w-[204px] h-[52px]">
            <img alt="{{ tenant('name') }}" class="w-[204px] h-full object-contain" src="{{ tenant('logo_url') }}"/>
        </div>

        <form method="POST" action="{{ route('qr-pay.create') }}" class="w-full relative bg-white rounded-xl overflow-hidden mt-[42px]">
            @csrf
            <label class="absolute top-[10px] left-4 text-[13px] text-[#0F12258C]">To’lov summasi</label>
            <input class="w-full h-full px-4 py-[10px] pt-[30px] bg-transparent outline-none text-[17px] font-medium"
                   placeholder="1000" name="amount" id="payment-input"/>
        </form>
        @error('amount')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex flex-col items-center pb-3">
        <div class="mb-6 w-[70px]">
            <img src="{{ global_asset('website/images/frontend/qr_pay/sbp-logo.png') }}" alt="SBP logo" class="w-full object-contain">
        </div>

        <button type="submit" class="w-full inactive-button text-lg font-medium py-3 px-6 rounded-lg" id="submit-button">
            Davom etish
        </button>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const inputField = document.getElementById('payment-input');
        const submitButton = document.getElementById('submit-button');

        if (form && inputField && submitButton) {
          inputField.addEventListener('input', () => {
            if (inputField.value.trim() !== '') {
              submitButton.classList.remove('inactive-button');
              submitButton.classList.add('active-button');
            } else {
              submitButton.classList.add('inactive-button');
              submitButton.classList.remove('active-button');
            }
          });

          submitButton.addEventListener('click', (event) => {
            if (!submitButton.classList.contains('inactive-button')) {
              event.preventDefault();
              form.submit();
            }
          });
        }
      });
    </script>
@endsection
