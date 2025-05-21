<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\NewCustomer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
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

        $blogs = Blog::query()->latest()->limit(6)->get();

        return view('website.pages.home', ['marketPhotos' => $marketPhotos, 'blogs' => $blogs]);
    }

    public function newCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|digits:9',
        ]);

        if (
            NewCustomer::query()
                ->where('is_checked', false)
                ->where('phone', "+998{$request->phone}")
                ->doesntExist()
        ) {
            $newCustomer = new NewCustomer;
            $newCustomer->full_name = $request->full_name;
            $newCustomer->phone = "+998{$request->phone}";
            $newCustomer->saveOrFail();
        }

        return redirect()->back()->with('success', true);
    }
}
