<?php

namespace Database\Seeders\Company;

use App\Models\Company\Ecommerce\OrderStatus;
use Illuminate\Database\Seeder;

class EcommerceOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $orderStatuses = [
            [
                'id' => OrderStatus::CREATED,
                'name_uz' => 'Yaratildi',
                'name_ru' => 'Создан',
                'name_en' => 'Created',
                'name_oz' => 'Яратилди',
                'is_active' => true,
            ],
            [
                'id' => OrderStatus::CANCELLED,
                'name_uz' => 'Bekor qilindi',
                'name_ru' => 'Отменен',
                'name_en' => 'Cancelled',
                'name_oz' => 'Бекор қилинди',
                'is_active' => true,
            ],
            [
                'id' => OrderStatus::FINISHED,
                'name_uz' => 'Yetkazib berildi',
                'name_ru' => 'Доставлено',
                'name_en' => 'Finished',
                'name_oz' => 'Еткaзиб берилди',
                'is_active' => true,
            ],
        ];

        foreach ($orderStatuses as $orderStatus) {
            OrderStatus::query()->firstOrCreate(['id' => $orderStatus['id']], $orderStatus);
        }
    }
}
