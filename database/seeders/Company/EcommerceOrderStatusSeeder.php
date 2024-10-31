<?php

namespace Database\Seeders\Company;

use App\Enums\Company\OrderStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommerceOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name_uz' => 'Yaratildi',
                'name_ru' => 'Создан',
                'name_en' => OrderStatusEnum::CREATED->value,
                'name_oz' => 'Яратилди',
                'is_active' => true,
            ],
            [
                'name_uz' => 'Bekor qilindi',
                'name_ru' => 'Отменен',
                'name_en' => OrderStatusEnum::CANCELLED->value,
                'name_oz' => 'Бекор қилинди',
                'is_active' => true,
            ],
            [
                'name_uz' => 'Yetkazib berildi',
                'name_ru' => 'Доставлено',
                'name_en' => OrderStatusEnum::DELIVERED->value,
                'name_oz' => 'Еткaзиб берилди',
                'is_active' => true,
            ],
        ];

        DB::table('e_commerce_order_statuses')->insert($statuses);
    }
}
