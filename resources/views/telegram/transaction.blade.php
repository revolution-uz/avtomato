@php
    /** @var \App\Models\Company\Transaction $record */
@endphp

💸 <b>Tranzaksiya:</b> {{ $record->id }}
#️⃣ <b>Muddatli to'lov Id si:</b> #I{{ $record->installment_id }}
🚹 <b>Mijoz:</b> {{ $record->customer->full_name }}
🧮 <b>Miqdor:</b> {{ number_format(round($record->amount), thousands_separator: ' ') }} so'm
💳 <b>Turi:</b> {{ $record->transactionType->name_uz }}
🏢 <b>Filial:</b> {{ $record->branch->name }}

🕰 <b>Sana va vaqt:</b> {{ $record->created_at }}
