@php
    /** @var \App\Models\Company\Transaction $record */
@endphp

💸 <b>Tranzaksiya:</b> {{ $record->id }}
#️⃣ <b>Muddatli to'lov Id si:</b> #I{{ $record->installment_id }}
🚹 <b>Mijoz:</b> {{ $record->customer->full_name }}
🧮 <b>Miqdor:</b> {{ number_format(round($record->amount), thousands_separator: ' ') }} so'm
@if ($record->card)
💳 <b>Karta raqami:</b> {{ $record->card->pan }}
👤 <b>Karta egasi:</b> {{ $record->card->owner }}
@else
💳 <b>Karta raqami:</b> {{ $record->card_pan ?? '-' }}
👤 <b>Karta egasi:</b> {{ $record->card_owner ?? '-' }}
@endif
💰 <b>Turi:</b> {{ $record->transactionType->name_uz }}
🏢 <b>Filial:</b> {{ $record->branch->name }}

🕰 <b>Sana va vaqt:</b> {{ $record->created_at->format('d.m.Y H:i:s') }}
