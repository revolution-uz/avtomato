<?php

namespace App\Http\Controllers\Website;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class BlogsController
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('website.pages.blogs');
    }

    public function navigateToBlog($slug): View|\Illuminate\Foundation\Application|Factory|Application
    {
        // Тестовые данные
        $blogs = [
            'first-blog' => [
                'image' => '/assets/images/blog-1_385x300.png',
                'title' => 'Qanday qilib yangi mijoz portaliga muvaffaqiyatli o`tish mumkin',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
            'second-blog' => [
                'image' => '/assets/images/blog-2_385x300.png',
                'title' => 'Yangi pudratchi portaliga qanday o`tish mumkin',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
            'third-blog' => [
                'image' => '/assets/images/blog-3_385x300.png',
                'title' => 'Buxgalterlar uchun mijoz portalida nimani izlash kerak',
                'content' => '"Yangi mijoz portaliga o‘tish – bu biznes jarayonlarini optimallashtirish va mijozlar bilan samarali ishlashning muhim bosqichi. To‘g‘ri yondashuv bilan o‘tish jarayonini silliq tashkil etish nafaqat ish jarayonlarini tezlashtiradi, balki mijozlar uchun qulay va intuitiv tajriba yaratadi.<br><br> Birinchi navbatda, o‘tish jarayonining aniq strategiyasini ishlab chiqish kerak. Mavjud ma‘lumotlarni tahlil qilib, ularni yangi portalga mos shaklda joylashtirish lozim. Mijozlar va jamoa a’zolari uchun yangi tizim bilan ishlashni osonlashtirish maqsadida o‘quv materiallari va qo‘llanmalar tayyorlash tavsiya etiladi.<br><br> Keyingi muhim qadam – sinov bosqichidir. Yangi portal to‘liq ishga tushishidan oldin barcha funksiyalar sinab ko‘rilishi va mumkin bo‘lgan xatolar bartaraf etilishi lozim. Shu bilan birga, jamoaning barcha bo‘limlari yangi tizim bilan ishlashga tayyor ekaniga ishonch hosil qilish muhim. Mijozlarga qulay sharoit yaratish ham o‘tish jarayonining ajralmas qismidir. Ularga yangi portal qanday ishlashi haqida aniq ma’lumot berish, foydali qo‘llanmalar va texnik yordam ko‘rsatish orqali foydalanish jarayonini soddalashtirish mumkin.<br><br> Yangi mijoz portaliga muvaffaqiyatli o‘tish – bu bir martalik jarayon emas, balki doimiy takomillashtirishni talab qiladigan bosqichdir. Jarayondan keyin foydalanuvchilarning fikr-mulohazalarini yig‘ish va zaruriy o‘zgarishlarni amalga oshirish orqali tizimni yanada qulay va samarali qilish mumkin. Tizimli yondashuv va puxta rejalashtirish orqali mijoz portaliga o‘tish jarayonini muammosiz amalga oshirish va biznesni yangi darajaga olib chiqish mumkin."',
                'created_at' => '03.03.2025',
            ],
        ];

        // Проверяем, существует ли пост
        if (! array_key_exists($slug, $blogs)) {
            abort(404);
        }

        return view('website.pages.single_blog', ['blog' => $blogs[$slug]]);
    }
}
