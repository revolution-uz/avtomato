<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\NewCustomer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $marketsPhotoTemplate = function ($marketPhotos) {
            $html = '';
            foreach ($marketPhotos as $photo) {
                $html .= '
            <div class="flex items-center px-2 w-[215px] h-[280px] lg:w-[260px] lg:h-[340px] hover:scale-105 transition-all">
                <img class="object-contain pointer-events-none select-none" src="' . asset("/assets/images/{$photo}") . '" alt="Slide">
            </div>';
            }

            return $html;
        };

        $marketPhotos = [
            'slide-1_260x340.webp',
            'slide-2_260x340.webp',
            'slide-3_260x340.webp',
            'slide-4_260x340.webp',
            'slide-5_260x340.webp',
            'slide-6_260x340.webp',
            'slide-7_260x340.webp',
        ];

        //        $feedbacksTemplate = function ($feedbacks) {
        //            $html = '';
        //            foreach ($feedbacks as $feedback) {
        //                $html .= '
        //            <div class="w-[300px] lg:w-[500px] p-6 lg:p-8 rounded-[32px] shadow-[0_0_100px_0_#00000012]">
        //                <img class="h-8 md:h-auto" src="' . asset("/assets/icons/{$feedback}") . '" alt="Layers">
        //                <p class="mt-4 lg:mt-6 lg:text-lg text-secondary font-medium text-wrap">Mijozlarni boshqarishning ilg`or funksionalligi, hisob-kitoblarning aniqligi va ma`lumotlar auditini BILLZ dasturining afzalliklari qatoriga kiritishimiz mumkin. BILLZ bizga o`z vaqtida qaror qabul qilish va do`kon uchun sarflanayotgan vaqtni tejashga yordam beradi</p>
        //                <div class="mt-4 lg:mt-6 flex items-center space-x-4">
        //                    <img class="size-[42px] lg:size-[62px] object-cover" src="' . asset('/assets/images/avatar.jpg') . '" alt="Avatar">
        //                    <div>
        //                        <p class="text-secondary font-semibold">Asadbek Shomurodov</p>
        //                        <p class="text-sm text-[#71717A]">Indesco agency egasi</p>
        //                    </div>
        //                </div>
        //            </div>
        //            ';
        //            }
        //
        //            return $html;
        //        };
        //
        //        $feedbacks = [
        //            'feedback-company-1.svg',
        //            'feedback-company-1.svg',
        //            'feedback-company-1.svg',
        //            'feedback-company-1.svg',
        //            'feedback-company-1.svg',
        //        ];

        // Тестовые данные
        $blogs = [
            [
                'slug' => 'first-blog',
                'image' => '/assets/images/blog-1_385x300.webp',
                'title' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
            [
                'slug' => 'second-blog',
                'image' => '/assets/images/blog-2_385x300.webp',
                'title' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
            [
                'slug' => 'third-blog',
                'image' => '/assets/images/blog-3_385x300.webp',
                'title' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
        ];

        return view('website.pages.home', [
            'marketPhotos' => $marketPhotos,
            'marketsPhotoTemplate' => $marketsPhotoTemplate,
            'blogs' => $blogs,
        ]);
    }

    public function newCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|digits:9',
        ]);

        $newCustomer = new NewCustomer;
        $newCustomer->full_name = $request->full_name;
        $newCustomer->phone = "+998{$request->phone}";
        $newCustomer->saveOrFail();

        return redirect()->back()->with('success', true);
    }
}
