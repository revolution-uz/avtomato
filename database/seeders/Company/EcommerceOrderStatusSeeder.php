<?php

namespace Database\Seeders\Company;

use App\Models\Company\Ecommerce\OrderStatus;
use Illuminate\Database\Seeder;

class EcommerceOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            [
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
            ] as $orderStatus) {
            OrderStatus::query()->firstOrCreate([
                'id' => $orderStatus['id'],
            ], [
                'name_uz' => $orderStatus['name_uz'],
                'name_ru' => $orderStatus['name_ru'],
                'name_en' => $orderStatus['name_en'],
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
