<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute maydoni qabul qilinishi kerak.',
    'active_url' => ':attribute maydoni noto‘g‘ri URL bo‘lishi kerak.',
    'after' => ':attribute maydoni :date dan keyin bo‘lishi kerak.',
    'after_or_equal' => ':attribute maydoni :date ga teng yoki keyin bo‘lishi kerak.',
    'alpha' => ':attribute maydoni faqat harflarni qabul qiladi.',
    'alpha_dash' => ':attribute maydoni faqat harflar, raqamlar, chiziqchalarni va pastki chiziqni qabul qiladi.',
    'alpha_num' => ':attribute maydoni faqat harflar va raqamlarni qabul qiladi.',
    'array' => ':attribute maydoni ro‘yxat bo‘lishi kerak.',
    'before' => ':attribute maydoni :date gacha bo‘lishi kerak.',
    'before_or_equal' => ':attribute maydoni :date ga teng yoki oldin bo‘lishi kerak.',
    'between' => [
        'array' => ':attribute maydoni :min va :max elementlar orasida bo‘lishi kerak.',
        'file' => ':attribute maydoni :min va :max kilobayt orasida bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :min va :max oralig‘ida bo‘lishi kerak.',
        'string' => ':attribute maydoni :min va :max belgilar orasida bo‘lishi kerak.',
    ],
    'boolean' => ':attribute maydoni faqat mantiqiy qiymatni qabul qiladi.',
    'confirmed' => ':attribute maydoni tasdiqlash bilan mos kelmaydi.',
    'date' => ':attribute maydoni sana bo‘lishi kerak.',
    'date_equals' => ':attribute maydoni :date ga teng bo‘lishi kerak.',
    'date_format' => ':attribute maydoni :format formatga mos kelmaydi.',
    'different' => ':attribute va :other maydonlari farqli bo‘lishi kerak.',
    'digits' => ':attribute maydoni :digits raqamdan iborat bo‘lishi kerak.',
    'digits_between' => ':attribute maydoni uzunligi :min va :max oralig‘ida bo‘lishi kerak.',
    'dimensions' => ':attribute maydonining tasvirlari noto‘g‘ri.',
    'distinct' => ':attribute maydoni takrorlanuvchi qiymatlarga ega.',
    'email' => ':attribute maydoni haqiqiy elektron pochta bo‘lishi kerak.',
    'ends_with' => ':attribute maydoni quyidagi qiymatlardan biri bilan tugashi kerak: :values.',
    'enum' => 'Tanlangan :attribute noto‘g‘ri.',
    'exists' => 'Tanlangan :attribute noto‘g‘ri.',
    'file' => ':attribute maydoni fayl bo‘lishi kerak.',
    'filled' => ':attribute maydoni to‘ldirilishi shart.',
    'gt' => [
        'array' => ':attribute maydoni :value dan ko‘proq elementlarga ega bo‘lishi kerak.',
        'file' => ':attribute maydoni :value kilobaytdan katta bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :value dan katta bo‘lishi kerak.',
        'string' => ':attribute maydoni :value dan katta belgilarga ega bo‘lishi kerak.',
    ],
    'gte' => [
        'array' => ':attribute maydoni :value yoki undan ko‘proq elementlarga ega bo‘lishi kerak.',
        'file' => ':attribute maydoni :value kilobaytdan katta yoki teng bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :value dan katta yoki teng bo‘lishi kerak.',
        'string' => ':attribute maydoni :value dan katta yoki teng belgilarga ega bo‘lishi kerak.',
    ],
    'image' => ':attribute maydoni rasm bo‘lishi kerak.',
    'in' => 'Tanlangan :attribute noto‘g‘ri.',
    'in_array' => ':attribute maydoni :other da mavjud emas.',
    'integer' => ':attribute maydoni butun son bo‘lishi kerak.',
    'ip' => ':attribute maydoni haqiqiy IP manzil bo‘lishi kerak.',
    'ipv4' => ':attribute maydoni haqiqiy IPv4 manzil bo‘lishi kerak.',
    'ipv6' => ':attribute maydoni haqiqiy IPv6 manzil bo‘lishi kerak.',
    'json' => ':attribute maydoni JSON qatori bo‘lishi kerak.',
    'lt' => [
        'array' => ':attribute maydoni :value dan kam elementlarga ega bo‘lishi kerak.',
        'file' => ':attribute maydoni :value kilobaytdan kichik bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :value dan kichik bo‘lishi kerak.',
        'string' => ':attribute maydoni :value dan kichik belgilarga ega bo‘lishi kerak.',
    ],
    'lte' => [
        'array' => ':attribute maydoni :value yoki undan kam elementlarga ega bo‘lishi kerak.',
        'file' => ':attribute maydoni :value kilobaytdan kichik yoki teng bo‘lishi kerak.',
        'numeric' => ':attribute maydoni :value dan kichik yoki teng bo‘lishi kerak.',
        'string' => ':attribute maydoni :value dan kichik yoki teng belgilarga ega bo‘lishi kerak.',
    ],
    'max' => [
        'array' => ':attribute maydoni :max dan ko‘p elementlarga ega bo‘lmasligi kerak.',
        'file' => ':attribute maydoni :max kilobaytdan katta bo‘lmasligi kerak.',
        'numeric' => ':attribute maydoni :max dan katta bo‘lmasligi kerak.',
        'string' => ':attribute maydoni :max belgidan ko‘p bo‘lmasligi kerak.',
    ],
    'mimes' => ':attribute maydoni quyidagi turlardan biri bo‘lishi kerak: :values.',
    'min' => [
        'array' => ':attribute maydoni kamida :min elementlarga ega bo‘lishi kerak.',
        'file' => ':attribute maydoni kamida :min kilobaytdan iborat bo‘lishi kerak.',
        'numeric' => ':attribute maydoni kamida :min ga teng bo‘lishi kerak.',
        'string' => ':attribute maydoni kamida :min belgilaridan iborat bo‘lishi kerak.',
    ],
    'not_in' => 'Tanlangan :attribute noto‘g‘ri.',
    'not_regex' => ':attribute maydoni formati noto‘g‘ri.',
    'numeric' => ':attribute maydoni raqam bo‘lishi kerak.',
    'present' => ':attribute maydoni ko‘rsatilishi shart.',
    'regex' => ':attribute maydoni formati noto‘g‘ri.',
    'required' => ':attribute maydoni to‘ldirilishi shart.',
    'required_if' => ':other maydoni :value ga teng bo‘lsa, :attribute maydoni to‘ldirilishi shart.',
    'required_unless' => ':other maydoni :values ga teng bo‘lmasa, :attribute maydoni to‘ldirilishi shart.',
    'required_with' => ':values ko‘rsatilgan bo‘lsa, :attribute maydoni to‘ldirilishi shart.',
    'required_with_all' => ':values ko‘rsatilgan bo‘lsa, :attribute maydoni to‘ldirilishi shart.',
    'required_without' => ':values ko‘rsatilmagan bo‘lsa, :attribute maydoni to‘ldirilishi shart.',
    'required_without_all' => ':values laridan hech biri ko‘rsatilmagan bo‘lsa, :attribute maydoni to‘ldirilishi shart.',
    'same' => ':attribute va :other maydonlari bir xil bo‘lishi kerak.',
    'size' => [
        'array' => ':attribute maydoni :size elementlarga teng bo‘lishi kerak.',
        'file' => ':attribute maydoni hajmi :size kilobaytga teng bo‘lishi kerak.',
        'numeric' => ':attribute maydoni qiymati :size ga teng bo‘lishi kerak.',
        'string' => ':attribute maydoni uzunligi :size belgiga teng bo‘lishi kerak.',
    ],
    'starts_with' => ':attribute maydoni quyidagi qiymatlardan biri bilan boshlanishi kerak: :values.',
    'string' => ':attribute maydoni qator bo‘lishi kerak.',
    'timezone' => ':attribute maydoni haqiqiy mintaqaga mos kelishi kerak.',
    'unique' => ':attribute maydoni allaqachon band qilingan.',
    'uploaded' => ':attribute maydoni yuklash muvaffaqiyatsiz tugadi.',
    'url' => ':attribute maydoni noto‘g‘ri formatga ega.',
    'ulid' => ':attribute maydoni haqiqiy ULID bo‘lishi kerak.',
    'uuid' => ':attribute maydoni haqiqiy UUID bo‘lishi kerak.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'invalid_credentials' => 'Login yoki parol noto\'g\'ri',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Ism',
        'username' => 'Foydalanuvchi nomi',
        'email' => 'Elektron pochta',
        'first_name' => 'Ism',
        'last_name' => 'Familiya',
        'password' => 'Parol',
        'password_confirmation' => 'Parolni tasdiqlash',
        'city' => 'Shahar',
        'country' => 'Davlat',
        'address' => 'Manzil',
        'phone' => 'Telefon',
        'mobile' => 'Mobil telefon',
        'age' => 'Yosh',
        'avatar' => 'Avatar',
    ],

];
