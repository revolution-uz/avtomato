<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

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
            'slide-1_260x340.jpg',
            'slide-2_260x340.jpg',
            'slide-3_260x340.jpg',
            'slide-4_260x340.jpg',
            'slide-5_260x340.jpg',
            'slide-6_260x340.jpg',
            'slide-7_260x340.jpg',
        ];

        $feedbacksTemplate = function ($feedbacks) {
            $html = '';
            foreach ($feedbacks as $feedback) {
                $html .= '
            <div class="w-[300px] lg:w-[500px] p-6 lg:p-8 rounded-[32px] shadow-[0_0_100px_0_#00000012]">
                <img class="h-8 md:h-auto" src="' . asset("/assets/icons/{$feedback}") . '" alt="Layers">
                <p class="mt-4 lg:mt-6 lg:text-lg text-secondary font-medium text-wrap">Mijozlarni boshqarishning ilg`or funksionalligi, hisob-kitoblarning aniqligi va ma`lumotlar auditini BILLZ dasturining afzalliklari qatoriga kiritishimiz mumkin. BILLZ bizga o`z vaqtida qaror qabul qilish va do`kon uchun sarflanayotgan vaqtni tejashga yordam beradi</p>
                <div class="mt-4 lg:mt-6 flex items-center space-x-4">
                    <img class="size-[42px] lg:size-[62px] object-cover" src="' . asset('/assets/images/avatar.jpg') . '" alt="Avatar">
                    <div>
                        <p class="text-secondary font-semibold">Asadbek Shomurodov</p>
                        <p class="text-sm text-[#71717A]">Indesco agency egasi</p>
                    </div>
                </div>
            </div>
            ';
            }

            return $html;
        };

        $feedbacks = [
            'feedback-company-1.svg',
            'feedback-company-1.svg',
            'feedback-company-1.svg',
            'feedback-company-1.svg',
            'feedback-company-1.svg',
        ];

        return view('website.pages.home', compact('marketPhotos', 'feedbacks', 'feedbacksTemplate', 'marketsPhotoTemplate'));
    }
}
