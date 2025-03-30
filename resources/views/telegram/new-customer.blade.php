@php
    /** @var \App\Models\NewCustomer $record */
@endphp

🎫 <b>Yangi mijoz:</b> {{ $record->id }}
🚹 <b>Mijoz:</b> {{ $record->full_name }}
📱<b>Tel:</b> {{ $record->phone }}

🕰 <b>Sana va vaqt:</b> {{ $record->created_at->timezone('Asia/Tashkent')->format('d.m.Y H:i:s') }}
