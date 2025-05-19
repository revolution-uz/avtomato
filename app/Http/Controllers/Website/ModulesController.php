<?php

namespace App\Http\Controllers\Website;

use App\Models\BaseFeature;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ModulesController
{
    public function installment(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Nasiya savdoni avtomatlashtirish',
            'heading' => 'Nasiya savdoni<br> avtomatlashtirish',
            'description' => 'Nasiya savdolarini boshqarish endi yanada qulay!<br class="hidden md:block"> Mijozlaringiz uchun mos to‘lov muddatlarini belgilang va to‘lov monitoringini avtomatik tarzda olib boring.',
            'services' => [
                [
                    'title' => 'Avtospisaniya orqali to‘lovlarni undirish',
                    'desc' => 'To‘lovlar avtomatik tarzda mijoz hisobidan yechiladi. Siz esa kechikkan to‘lovlarni qo‘lda talab qilish',
                    'img' => '/assets/images/price-1_550x550.webp',
                ],
                [
                    'title' => 'Mijoz to‘lov grafigini<br> nazorat qilish',
                    'desc' => 'Har bir mijoz uchun individual to‘lov rejalari yarating va ularning to‘lovlarini real vaqt rejimida kuzatib boring. Barcha jarayonlar tizimda avtomatik ravishda qayd etiladi.',
                    'img' => '/assets/images/price-2_550x550.webp',
                ],
                [
                    'title' => 'Moliyaviy oqimni doimiy nazorat qilish',
                    'desc' => 'Barcha kirim-chiqim operatsiyalarini kuzatib boring va to‘lovlarni rejalashtirish orqali biznesingizni barqaror boshqaring.',
                    'img' => '/assets/images/price-3_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun',
                    'desc' => 'Tizimga muddatli to’lov yaratish va mijozni ma’lumotlarini qo’shish.',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => '/assets/icons/document-plus.svg',
                ],
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun (oldindan karta qo’shilgan holatida)',
                    'desc' => 'Yaratilgan muddatli to’lov uchun qo’shilgan kartadan pul yechish xizmati.',
                    'price' => '5 000 so‘m',
                    'note' => 'Ushbu narx muddatli to’lov<br> davomiyligining har 3 oyi uchun ko’rsatilgan',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun (PINFL orqali pul yechish yoqilganda)',
                    'desc' => 'Yaratilgan muddatli to’lov uchun PINFL orqali pul yechish xizmati yoqilganda. Bu xizmat ichiga har 3 oyda PINFL orqali yangi ochilgan kartalar ro’yxatini olish va shu kartalardan pul yechish uchun.',
                    'price' => '8 000 so‘m',
                    'note' => 'Ushbu nar muddatli to’lov<br> davomiyligining har 3 oyi uchun ko’rsatilgan',
                    'icon' => '/assets/icons/id-card.svg',
                ],
            ],
        ];

        return view('website.pages.module_installment', ['data' => $data]);
    }

    public function marketing(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Marketing',
            'heading' => 'Marketing',
            'description' => 'Mijozlarga moliyaviy majburiyatlarini o‘z vaqtida bajarishda yordam beruvchi<br> avtomatlashtirilgan eslatmalar va ogohlantirish tizimi orqali xavflarni minimallashtiring.',
            'services' => [
                [
                    'title' => 'Kechikkan to‘lovlar haqida xabarnomalar',
                    'desc' => 'Mijozlaringizga kechikkan to‘lovlar haqida SMS orqali avtomatik eslatmalar yuboring.',
                    'img' => '/assets/images/price-10_550x550.webp',
                ],
                [
                    'title' => 'Qarzdorlikni kamaytirish strategiyasi',
                    'desc' => 'To‘lov muddatlari o‘tishidan oldin mijozlarga eslatmalar jo‘natish orqali qarzdorlik darajasini kamaytiring.',
                    'img' => '/assets/images/price-11_550x550.webp',
                ],
                [
                    'title' => 'Avtospisaniya orqali moliyaviy nazorat',
                    'desc' => 'Avtospisaniya orqali yechilgan pullar haqida SMS xabar to’lovlar bo’yicha mijozlarni ogohlantirish.',
                    'img' => '/assets/images/price-12_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'SMS yuborish xizmati',
                    'desc' => 'Har bir mijozning oldingi to‘lov tarixi asosida uning moliyaviy ishonchliligini baholang.',
                    'price' => $this->getPrice('sms'),
                    'note' => 'Har bir yuborilgan sms uchun',
                    'icon' => '/assets/icons/sms-plus.svg',
                ],
            ],
        ];

        return view('website.pages.module_marketing', ['data' => $data]);
    }

    public function scoring(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Skoring',
            'heading' => 'Skoring – mijoz moliyaviy<Br> tarixini tahlil qilish',
            'description' => 'Skoring tizimi mijozlarning oldingi to‘lov tarixini tahlil qilib, qaror qabul qilish jarayonini ancha osonlashtiradi.<br> Bu esa biznesingiz xavf-xatarlardan himoyalangan holda rivojlanishiga yordam beradi.',
            'services' => [
                [
                    'title' => 'Mijozlarning to‘lov qobiliyatini baholash',
                    'desc' => 'Har bir mijozning oldingi to‘lov tarixi asosida uning moliyaviy ishonchliligini baholang.',
                    'img' => '/assets/images/price-4_550x550.webp',
                ],
                [
                    'title' => 'Qaror qabul qilishni soddalashtirish',
                    'desc' => 'Avtomatik tahlil tizimi mijozning qarzlarni o‘z vaqtida to‘lash imkoniyatini baholab, xavf-xatarlarni kamaytirishga yordam beradi.',
                    'img' => '/assets/images/price-5_550x550.webp',
                ],
                [
                    'title' => 'Xavfsiz nasiya savdosi',
                    'desc' => 'Skoring tizimi asosida faqat ishonchli mijozlarga nasiya berish orqali biznesingizni moliyaviy yo‘qotishlardan himoya qiling.',
                    'img' => '/assets/images/price-6_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'Ruyxat.uz bazasidan tekshirish',
                    'desc' => 'Ro‘yxat — mas‘uliyatsiz mijozlarning savdo sotiq tashkilotlaridagi kechikkan yoki kechikayotgan to‘lovlari haqidagi elektron ma‘lumotlar bazasi',
                    'price' => $this->getPrice('scoring_royxat_credit_history'),
                    'note' => '',
                    'icon' => '/assets/icons/globe-filled.svg',
                ],
                [
                    'title' => 'Majburiy ijro ma’lumotlari',
                    'desc' => 'MIB (Majburiy Ijro Byurosi) qarzdor shaxs yoki tashkilotning to‘lov qobiliyatini baholash va scoring tizimi qarzdorlarning to‘lov imkoniyatlarini aniq baholashga yordam berib, moliyaviy xavflarni kamaytirishga xizmat qiladi.',
                    'price' => $this->getPrice('scoring_mib_info'),
                    'note' => '',
                    'icon' => '/assets/icons/document-filled.svg',
                ],
                [
                    'title' => 'Soliq qarzini tekshirish',
                    'desc' => 'Soliq qarzini tekshirish – bu soliq to‘lovchilar (jismoniy va yuridik shaxslar) uchun ularning soliq majburiyatlarini bajarish intizomini baholovchi tizim',
                    'price' => $this->getPrice('scoring_tax_info'),
                    'note' => '',
                    'icon' => '/assets/icons/search-filled.svg',
                ],
                [
                    'title' => 'Uzum nasiya orqali limit tekshirish',
                    'desc' => 'Uzum nasiya limiti – bu foydalanuvchining nasiya (bo‘lib to‘lash) orqali xarid qilish imkoniyatini aniqlash uchun amalga oshiriladigan baholash jarayoni. Uzum Market yoki Uzum Bank foydalanuvchining moliyaviy holatini tahlil qilib, unga ajratilgan limit miqdorini tekshirish.',
                    'price' => $this->getPrice('scoring_uzum_nasiya_limit'),
                    'note' => '',
                    'icon' => '/assets/icons/circle-statistic-filled.svg',
                ],
                [
                    'title' => 'Mijozning oylik ish haqi ma’lumotlari',
                    'desc' => 'Yillik oylik ish haqqi haqida ma’lumotni qo‘lga kiriting va ehtimoliy risklarni qisqartiring.',
                    'price' => $this->getPrice('scoring_inps_history'),
                    'note' => '',
                    'icon' => '/assets/icons/info-doc-filled.svg',
                ],
                [
                    'title' => 'Pensiya ma‘lumotlari',
                    'desc' => 'Pensiya oluvchi mijozlarga muddatli to‘lov berish vaqtida o‘z vaqtida to‘lov qila olish darajasini tekshirish. 12 oylik hisoblangan pensiya summalari ma’lumoti.',
                    'price' => $this->getPrice('scoring_pension_info'),
                    'note' => '',
                    'icon' => '/assets/icons/doc-bag-filled.svg',
                ],
                [
                    'title' => 'KATM, kredit tarixi ma’lumotlari',
                    'desc' => 'KATM – bu mijozlarning kredit tarixi ma’lumotlari asosida, tahlil qilib ishonchlilik darajasini baholab beradi. Kechiktirilgan to‘lovlar va vaqti bo‘yicha skoring.',
                    'price' => $this->getPrice('scoring-credit_history'),
                    'note' => '',
                    'icon' => '/assets/icons/id-card.svg',
                ],
                [
                    'title' => 'Karta bir yillik aylanmasi ma’lumotlari (HUMO)',
                    'desc' => 'Karta aylanmasi – bu foydalanuvchining Humo yoki Uzcard plastik kartasi orqali amalga oshirgan barcha moliyaviy operatsiyalarining umumiy miqdori. 1 yillik kirim chiqimlari haqida ma’lumot.',
                    'price' => $this->getPrice('scoring_humo'),
                    'note' => '',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
                [
                    'title' => 'Karta bir yillik aylanmasi ma’lumotlari (UZCARD)',
                    'desc' => 'Karta aylanmasi – bu foydalanuvchining Humo yoki Uzcard plastik kartasi orqali amalga oshirgan barcha moliyaviy operatsiyalarining umumiy miqdori. 1 yillik kirim chiqimlari haqida ma’lumot.',
                    'price' => $this->getPrice('scoring_uzcard'),
                    'note' => '',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
            ],
        ];

        return view('website.pages.module_scoring', ['data' => $data]);
    }

    public function sbpPay(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'QR pay',
            'heading' => 'QR pay',
            'description' => 'Rossiya MIR kartasi bilan O‘zbekistonda to‘lov',
            'services' => [
                [
                    'title' => 'SBP orqali tezkor to‘lov xizmati',
                    'desc' => 'O‘zbekistonda MIR kartasi orqali to‘lov qilish oson! QR Pay SBP orqali so‘mda to‘lovni amalga oshirish uchun QR kod generatsiya qilib beradi.',
                    'img' => '/assets/images/price-13_550x550.webp',
                ],
                [
                    'title' => 'Har qanday sharoitda oson to‘lov',
                    'desc' => 'Mehmonxonalar, restoranlar va kafelarda Rossiya MIR kartasidan so‘mda to‘lov qilish imkoniyati. QR kod orqali tezkor o‘tkazma amalga oshirish va cheklovlarsiz xizmatlardan foydalanish mumkin.',
                    'img' => '/assets/images/price-14_550x550.webp',
                ],
                [
                    'title' => 'Ishonchli va xavfsiz to‘lov yechimi',
                    'desc' => 'To‘lov summasini kiriting va Rossiya MIR kartasidan O‘zbek so‘mida tezkor hamda xavfsiz to‘lovni qabul qiling. SBP orqali qulay tranzaksiya amalga oshiring!',
                    'img' => '/assets/images/price-15_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'Oylik to’lov',
                    'desc' => 'Rossiya MIR kartasi bilan O‘zbekistonda to‘lov',
                    'price' => '50 000 so’m',
                    'note' => '',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
            ],
        ];

        return view('website.pages.module_qr', ['data' => $data]);
    }

    public function ecommerce(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Internet do‘kon',
            'heading' => 'Internet do‘kon yaratish',
            'description' => 'Bir necha bosqichda o‘z internet-do‘koningizni yaratib, onlayn savdoni boshlang. Hech qanday texnik bilim talab etilmaydi!',
            'services' => [
                [
                    'title' => 'Savdoni avtomatlashtiring va foydani oshiring!',
                    'desc' => 'Online savdoga o‘ting, mijozlar sonini ko‘paytiring va buyurtmalarni yetkazib bering. Buyurtmalar, to‘lovlar va yetkazib berish jarayonlarini avtomatik boshqaring. Mijozlaringiz bilan doimiy aloqada bo‘lish uchun xabarnomalar tizimidan foydalaning.',
                    'img' => '/assets/images/price-7_550x550.webp',
                ],
                [
                    'title' => 'Onlayn do‘kon bilan biznesingizni yangi bosqichga olib chiqing!',
                    'desc' => 'Internet do‘kon yaratib, raqobatda oldinda bo‘ling. Mijozlarga qulay xarid qilish imkonini bering, savdo hajmini oshiring va zamonaviy tendensiyalarga moslashing!',
                    'img' => '/assets/images/price-8_550x550.webp',
                ],
                [
                    'title' => 'Cheklovlarsiz biznes va yuqori daromad',
                    'desc' => 'Hozir hamma internet orqali xarid qiladi! Onlayn do‘kon oching, mijozlarni jalb qiling, buyurtmalarni yetkazib bering va biznesingizni kengaytiring.',
                    'img' => '/assets/images/price-9_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'Bir martalik to’lov',
                    'desc' => 'Sizning brendinggiz bilan birga har qanday turdaki online savdo dasturini ishga tushurib beramiz',
                    'price' => '400 000 so’m',
                    'note' => '',
                    'icon' => '/assets/icons/info-doc-filled.svg',
                ],
                [
                    'title' => 'Oylik to’lov',
                    'desc' => 'Sizning brendinggiz bilan birga har qanday turdaki online savdo dasturini ishga tushurib beramiz',
                    'price' => '50 000 so’m',
                    'note' => '',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
            ],
        ];

        return view('website.pages.module_ecommerce', ['data' => $data]);
    }

    public function loyalty(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Loyalty',
            'heading' => 'Loyalty program (Sodiqlik dasturi)',
            'description' => 'Mijozlarni bonus va chegirmalar bilan ushlab qoling, obuna tizimi orqali ularning sonini oshiring!',
            'services' => [
                [
                    'title' => 'Savdoni oshiring, mijozlarni ushlab qoling!',
                    'desc' => 'Bonus va chegirmalar bilan mijozlarni qayta xarid qilishga undang. Sodiqlik dasturi orqali ishonchni oshiring, o‘rtacha chekni ko‘tarib, savdoni rivojlantiring!',
                    'img' => '/assets/images/price-16_550x550.webp',
                ],
                [
                    'title' => 'Bonus va chegirmalar bilan savdo hajmini oshiring!',
                    'desc' => 'Mijozlaringizni qayta kelishga undang! Har bir xaridda ularga bonus yoki chegirma taqdim eting, o‘rtacha chek miqdorini oshiring va doimiy mijozlar sonini ko‘paytiring.',
                    'img' => '/assets/images/price-17_550x550.webp',
                ],
                [
                    'title' => 'Mijozlaringizni jalb qilish yo‘li',
                    'desc' => 'Bonus va chegirmalar orqali mijozlarni ushlab qoling! Sodiqlik dasturi nafaqat ularning ishonchini oshiradi, balki o‘rtacha chekni va qayta tashrif sonini ham ko‘paytiradi.',
                    'img' => '/assets/images/price-18_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'title' => 'Oylik to’lov',
                    'desc' => 'Mijozlarni bonus va chegirmalar bilan ushlab qoling, obuna tizimi orqali ularning sonini oshiring!',
                    'price' => '50 000 so’m',
                    'note' => '',
                    'icon' => '/assets/icons/credit-card.svg',
                ],
            ],
        ];

        return view('website.pages.module_loyalty', ['data' => $data]);
    }

    public function others(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Qo’shimcha xizmatlar',
            'heading' => 'Qo’shimcha xizmatlar',
            'description' => 'Filiallar va Xodimlar ustida nazorat qiling va ish jarayonlarini biz bilan birga soddalashtiring!',
            'services' => [
                [
                    'title' => 'Filiallar sonini oshirish va qo‘shimcha xodimlarga ruxsat berish',
                    'desc' => 'Biznesingizni kengaytiring! Filiallar sonini ko‘paytirib, yangi xodimlarni tizimga qo‘shing. Ish jarayonlarini avtomatlashtirish orqali samaradorlikni oshiring va boshqaruvni qulaylashtiring.',
                    'img' => '/assets/images/price-19_550x550.webp',
                ],
                [
                    'title' => 'Muddatli to‘lovlar orqali kelajakdagi daromadlarni ko‘rish',
                    'desc' => 'Moliya oqimini oldindan rejalashtiring! Muddatli to‘lovlar tizimi orqali kelajakdagi daromadlaringizni kuzatib boring va biznesingiz rivojlanishini aniq prognoz qiling.',
                    'img' => '/assets/images/price-20_550x550.webp',
                ],
                [
                    'title' => 'Kechikkan to‘lovlarni nazorat qilib vaqtida undirish',
                    'desc' => 'Kechikkan to‘lovlarni tizimli nazorat qiling va mijozlardan to‘lovlarni o‘z vaqtida undirib, moliyaviy barqarorlikni ta’minlang.',
                    'img' => '/assets/images/price-21_550x550.webp',
                ],
            ],
            'prices' => [
                [
                    'Foydalanuvchilar',
                    '1',
                    '2-5',
                    '5-10',
                    '10-20',
                ],
                [
                    'Filiallar',
                    '1',
                    '2',
                    '3',
                    '5',
                ],
                [
                    'Oylik to‘lov',
                    'Bepul',
                    '200 000 so‘m',
                    '350 000 so‘m',
                    '500 000 so‘m',
                ],
            ],
        ];

        return view('website.pages.module_others', ['data' => $data]);
    }

    private function getPrice($operator): string
    {
        if (! empty(BaseFeature::query()->where('name', $operator)->first()->original_amount)) {
            return number_format(BaseFeature::query()->where('name', $operator)->first()->original_amount, 0, '.', ' ') . ' so‘m';
        } else {
            return 'Bepul';
        }
    }
}
