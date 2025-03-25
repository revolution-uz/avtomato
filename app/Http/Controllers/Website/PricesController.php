<?php

namespace App\Http\Controllers\Website;

use App\Models\BaseModule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PricesController
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $baseModules = BaseModule::all();

        $moduleNames = [
            'installment' => 'Nasiya savdo moduli',
            'scoring' => 'Skoring moduli',
            'marketing' => 'Marketing moduli',
            'ecommerce' => 'Internet do’kon moduli',
            'sbp_pay' => 'QR pay moduli',
            'loyalty_program' => 'Loyalty moduli',
        ];

        $filteredModules = [];
        $multibranchModule = null;
        $hasMultibranch = false; // Флаг наличия multibranch_*

        foreach ($baseModules as $module) {
            if (str_starts_with($module['name']->value, 'multibranch_')) {
                $multibranchModule = $module; // Сохраняем multibranch-модуль
                $hasMultibranch = true;
            } else {
                $filteredModules[] = $module;
            }
        }

        return view('website.pages.prices', compact('baseModules', 'moduleNames', 'filteredModules', 'multibranchModule', 'hasMultibranch'));
    }

    public function navigateToInstallment(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Nasiya savdoni avtomatlashtirish',
            'heading' => 'Nasiya savdoni<br> avtomatlashtirish',
            'description' => 'Nasiya savdolarini boshqarish endi yanada qulay!<br class="hidden md:block"> Mijozlaringiz uchun mos to‘lov muddatlarini belgilang va to‘lov monitoringini avtomatik tarzda olib boring.',
            'services' => [
                [
                    'title' => 'Avtospisaniya orqali to‘lovlarni undirish',
                    'desc' => 'To‘lovlar avtomatik tarzda mijoz hisobidan yechiladi. Siz esa kechikkan to‘lovlarni qo‘lda talab qilish',
                    'img' => '/assets/images/price_1.png',
                ],
                [
                    'title' => 'Mijoz to‘lov grafigini<br> nazorat qilish',
                    'desc' => 'Har bir mijoz uchun individual to‘lov rejalari yarating va ularning to‘lovlarini real vaqt rejimida kuzatib boring. Barcha jarayonlar tizimda avtomatik ravishda qayd etiladi.',
                    'img' => '/assets/images/price_2.png',
                ],
                [
                    'title' => 'Moliyaviy oqimni doimiy nazorat qilish',
                    'desc' => 'Barcha kirim-chiqim operatsiyalarini kuzatib boring va to‘lovlarni rejalashtirish orqali biznesingizni barqaror boshqaring.',
                    'img' => '/assets/images/price_3.png'
                ],
            ],
            'prices' => [
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun',
                    'desc' => 'Tizimga muddatli to’lov yaratish va mijozni ma’lumotlarini qo’shish.',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => '',
                ],
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun (oldindan karta qo’shilgan holatida)',
                    'desc' => 'Yaratilgan muddatli to’lov uchun qo’shilgan kartadan pul yechish xizmati.',
                    'price' => '5 000 so‘m',
                    'note' => 'Ushbu narx muddatli to’lov<br> davomiyligining har 3 oyi uchun ko’rsatilgan',
                    'icon' => '',
                ],
                [
                    'title' => 'Har bir yaratilgan shartnoma uchun (PINFL orqali pul yechish yoqilganda)',
                    'desc' => 'Yaratilgan muddatli to’lov uchun PINFL orqali pul yechish xizmati yoqilganda. Bu xizmat ichiga har 3 oyda PINFL orqali yangi ochilgan kartalar ro’yxatini olish va shu kartalardan pul yechish uchun.',
                    'price' => '8 000 so‘m',
                    'note' => 'Ushbu nar muddatli to’lov<br> davomiyligining har 3 oyi uchun ko’rsatilgan',
                    'icon' => '',
                ],
            ],
        ];

        return view('website.pages.module_installment', compact('data'));
    }
    public function navigateToMarketing(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Marketing',
            'heading' => 'Marketing',
            'description' => 'Mijozlarga moliyaviy majburiyatlarini o‘z vaqtida bajarishda yordam beruvchi<br> avtomatlashtirilgan eslatmalar va ogohlantirish tizimi orqali xavflarni minimallashtiring.',
            'services' => [
                [
                    'title' => 'Kechikkan to‘lovlar haqida xabarnomalar',
                    'desc' => 'Mijozlaringizga kechikkan to‘lovlar haqida SMS orqali avtomatik eslatmalar yuboring.',
                    'img' => '/assets/images/price_10.png',
                ],
                [
                    'title' => 'Qarzdorlikni kamaytirish strategiyasi',
                    'desc' => 'To‘lov muddatlari o‘tishidan oldin mijozlarga eslatmalar jo‘natish orqali qarzdorlik darajasini kamaytiring.',
                    'img' => '/assets/images/price_11.png',
                ],
                [
                    'title' => 'Avtospisaniya orqali moliyaviy nazorat',
                    'desc' => 'Avtospisaniya orqali yechilgan pullar haqida SMS xabar to’lovlar bo’yicha mijozlarni ogohlantirish.',
                    'img' => '/assets/images/price_12.png',
                ],
            ],
            'prices' => [
                [
                    'title' => '34 SMS yuborish xizmati',
                    'desc' => 'Har bir mijozning oldingi to‘lov tarixi asosida uning moliyaviy ishonchliligini baholang.',
                    'price' => '100 so‘m',
                    'note' => 'Har bir yuborilgan sms uchun',
                    'icon' => '',
                ]
            ],
        ];

        return view('website.pages.module_marketing', compact('data'));
    }

    public function navigateToScoring(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Skoring',
            'heading' => 'Skoring – mijoz moliyaviy<Br> tarixini tahlil qilish',
            'description' => 'Skoring tizimi mijozlarning oldingi to‘lov tarixini tahlil qilib, qaror qabul qilish jarayonini ancha osonlashtiradi.<br> Bu esa biznesingiz xavf-xatarlardan himoyalangan holda rivojlanishiga yordam beradi.',
            'services' => [
                [
                    'title' => 'Mijozlarning to‘lov qobiliyatini baholash',
                    'desc' => 'Har bir mijozning oldingi to‘lov tarixi asosida uning moliyaviy ishonchliligini baholang.',
                    'img' => '/assets/images/price_4.png',
                ],
                [
                    'title' => 'Qaror qabul qilishni soddalashtirish',
                    'desc' => 'Avtomatik tahlil tizimi mijozning qarzlarni o‘z vaqtida to‘lash imkoniyatini baholab, xavf-xatarlarni kamaytirishga yordam beradi.',
                    'img' => '/assets/images/price_5.png',
                ],
                [
                    'title' => 'Xavfsiz nasiya savdosi',
                    'desc' => 'Skoring tizimi asosida faqat ishonchli mijozlarga nasiya berish orqali biznesingizni moliyaviy yo‘qotishlardan himoya qiling.',
                    'img' => '/assets/images/price_6.png',
                ],
            ],
            'prices' => [
                [
                    'title' => '38 Ruyxat.uz bazasidan tekshirish',
                    'desc' => 'Ro‘yxat — mas‘uliyatsiz mijozlarning savdo sotiq tashkilotlaridagi kechikkan yoki kechikayotgan to‘lovlari haqidagi elektron ma‘lumotlar bazasi',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '26 Majburiy ijro ma’lumotlari',
                    'desc' => 'MIB (Majburiy Ijro Byurosi) qarzdor shaxs yoki tashkilotning to‘lov qobiliyatini baholash va scoring tizimi qarzdorlarning to‘lov imkoniyatlarini aniq baholashga yordam berib, moliyaviy xavflarni kamaytirishga xizmat qiladi.',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '27 Soliq qarzini tekshirish',
                    'desc' => 'Soliq qarzini tekshirish – bu soliq to‘lovchilar (jismoniy va yuridik shaxslar) uchun ularning soliq majburiyatlarini bajarish intizomini baholovchi tizim',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '39 Uzum nasiya orqali limit tekshirish',
                    'desc' => 'Uzum nasiya limiti – bu foydalanuvchining nasiya (bo‘lib to‘lash) orqali xarid qilish imkoniyatini aniqlash uchun amalga oshiriladigan baholash jarayoni. Uzum Market yoki Uzum Bank foydalanuvchining moliyaviy holatini tahlil qilib, unga ajratilgan limit miqdorini tekshirish.',
                    'price' => 'Bepul',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '24 Mijozning oylik ish haqi ma’lumotlari',
                    'desc' => 'Yillik oylik ish haqqi haqida ma’lumotni qo‘lga kiriting va ehtimoliy risklarni qisqartiring.',
                    'price' => '8 000 so‘m',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '33 Pensiya ma‘lumotlari',
                    'desc' => 'Pensiya oluvchi mijozlarga muddatli to‘lov berish vaqtida o‘z vaqtida to‘lov qila olish darajasini tekshirish. 12 oylik hisoblangan pensiya summalari ma’lumoti.',
                    'price' => '10 000 so‘m',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '23 KATM, kredit tarixi ma’lumotlari',
                    'desc' => 'KATM – bu mijozlarning kredit tarixi ma’lumotlari asosida, tahlil qilib ishonchlilik darajasini baholab beradi. Kechiktirilgan to‘lovlar va vaqti bo‘yicha skoring.',
                    'price' => '18 000 so‘m',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '28 Karta bir yillik aylanmasi ma’lumotlari (HUMO)',
                    'desc' => 'Karta aylanmasi – bu foydalanuvchining Humo yoki Uzcard plastik kartasi orqali amalga oshirgan barcha moliyaviy operatsiyalarining umumiy miqdori. 1 yillik kirim chiqimlari haqida ma’lumot.',
                    'price' => '6 000 so‘m',
                    'note' => '',
                    'icon' => ''
                ],
                [
                    'title' => '29 Karta bir yillik aylanmasi ma’lumotlari (UZCARD)',
                    'desc' => 'Karta aylanmasi – bu foydalanuvchining Humo yoki Uzcard plastik kartasi orqali amalga oshirgan barcha moliyaviy operatsiyalarining umumiy miqdori. 1 yillik kirim chiqimlari haqida ma’lumot.',
                    'price' => '6 000 so‘m',
                    'note' => '',
                    'icon' => ''
                ],
            ],
        ];

        return view('website.pages.module_scoring', compact('data'));
    }

    public function navigateToQR(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'QR pay',
            'heading' => 'QR pay',
            'description' => 'Rossiya MIR kartasi bilan O‘zbekistonda to‘lov',
            'services' => [
                [
                    'title' => 'SBP orqali tezkor to‘lov xizmati',
                    'desc' => 'O‘zbekistonda MIR kartasi orqali to‘lov qilish oson! QR Pay SBP orqali so‘mda to‘lovni amalga oshirish uchun QR kod generatsiya qilib beradi.',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Har qanday sharoitda oson to‘lov',
                    'desc' => 'Mehmonxonalar, restoranlar va kafelarda Rossiya MIR kartasidan so‘mda to‘lov qilish imkoniyati. QR kod orqali tezkor o‘tkazma amalga oshirish va cheklovlarsiz xizmatlardan foydalanish mumkin.',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Ishonchli va xavfsiz to‘lov yechimi',
                    'desc' => 'To‘lov summasini kiriting va Rossiya MIR kartasidan O‘zbek so‘mida tezkor hamda xavfsiz to‘lovni qabul qiling. SBP orqali qulay tranzaksiya amalga oshiring!',
                    'img' => '/assets/images/empty-frame.png',
                ],
            ],
            'prices' => [],
        ];

        return view('website.pages.module_qr', compact('data'));
    }

    public function navigateToEcommerce(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Internet do‘kon',
            'heading' => 'Internet do‘kon yaratish',
            'description' => 'Bir necha bosqichda o‘z internet-do‘koningizni yaratib, onlayn savdoni boshlang. Hech qanday texnik bilim talab etilmaydi!',
            'services' => [
                [
                    'title' => 'Savdoni avtomatlashtiring va foydani oshiring!',
                    'desc' => 'Online savdoga o‘ting, mijozlar sonini ko‘paytiring va buyurtmalarni yetkazib bering. Buyurtmalar, to‘lovlar va yetkazib berish jarayonlarini avtomatik boshqaring. Mijozlaringiz bilan doimiy aloqada bo‘lish uchun xabarnomalar tizimidan foydalaning.',
                    'img' => '/assets/images/price_7.png',
                ],
                [
                    'title' => 'Onlayn do‘kon bilan biznesingizni yangi bosqichga olib chiqing!',
                    'desc' => 'Internet do‘kon yaratib, raqobatda oldinda bo‘ling. Mijozlarga qulay xarid qilish imkonini bering, savdo hajmini oshiring va zamonaviy tendensiyalarga moslashing!',
                    'img' => '/assets/images/price_8.png',
                ],
                [
                    'title' => 'Cheklovlarsiz biznes va yuqori daromad',
                    'desc' => 'Hozir hamma internet orqali xarid qiladi! Onlayn do‘kon oching, mijozlarni jalb qiling, buyurtmalarni yetkazib bering va biznesingizni kengaytiring.',
                    'img' => '/assets/images/price_9.png',
                ],
            ],
            'prices' => [],
        ];

        return view('website.pages.module_ecommerce', compact('data'));
    }

    public function navigateToLoyalty(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Loyalty',
            'heading' => 'Loyalty program (Sodiqlik dasturi)',
            'description' => 'Mijozlarni bonus va chegirmalar bilan ushlab qoling, obuna tizimi orqali ularning sonini oshiring!',
            'services' => [
                [
                    'title' => 'Savdoni oshiring, mijozlarni ushlab qoling!',
                    'desc' => 'Bonus va chegirmalar bilan mijozlarni qayta xarid qilishga undang. Sodiqlik dasturi orqali ishonchni oshiring, o‘rtacha chekni ko‘tarib, savdoni rivojlantiring!',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Bonus va chegirmalar bilan savdo hajmini oshiring!',
                    'desc' => 'Mijozlaringizni qayta kelishga undang! Har bir xaridda ularga bonus yoki chegirma taqdim eting, o‘rtacha chek miqdorini oshiring va doimiy mijozlar sonini ko‘paytiring.',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Mijozlaringizni jalb qilish yo‘li',
                    'desc' => 'Bonus va chegirmalar orqali mijozlarni ushlab qoling! Sodiqlik dasturi nafaqat ularning ishonchini oshiradi, balki o‘rtacha chekni va qayta tashrif sonini ham ko‘paytiradi.',
                    'img' => '/assets/images/empty-frame.png',
                ],
            ],
            'prices' => [],
        ];

        return view('website.pages.module_loyalty', compact('data'));
    }

    public function navigateToOthers(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $data = [
            'title' => 'Qo’shimcha xizmatlar',
            'heading' => 'Qo’shimcha xizmatlar',
            'description' => 'Filiallar va Xodimlar ustida nazorat qiling va ish jarayonlarini biz bilan birga soddalashtiring!',
            'services' => [
                [
                    'title' => 'Filiallar sonini oshirish va qo‘shimcha xodimlarga ruxsat berish',
                    'desc' => 'Biznesingizni kengaytiring! Filiallar sonini ko‘paytirib, yangi xodimlarni tizimga qo‘shing. Ish jarayonlarini avtomatlashtirish orqali samaradorlikni oshiring va boshqaruvni qulaylashtiring.',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Muddatli to‘lovlar orqali kelajakdagi daromadlarni ko‘rish',
                    'desc' => 'Moliya oqimini oldindan rejalashtiring! Muddatli to‘lovlar tizimi orqali kelajakdagi daromadlaringizni kuzatib boring va biznesingiz rivojlanishini aniq prognoz qiling.',
                    'img' => '/assets/images/empty-frame.png',
                ],
                [
                    'title' => 'Kechikkan to‘lovlarni nazorat qilib vaqtida undirish',
                    'desc' => 'Kechikkan to‘lovlarni tizimli nazorat qiling va mijozlardan to‘lovlarni o‘z vaqtida undirib, moliyaviy barqarorlikni ta’minlang.',
                    'img' => '/assets/images/empty-frame.png',
                ],
            ],
            'prices' => [],
        ];

        return view('website.pages.module_others', compact('data'));
    }
}