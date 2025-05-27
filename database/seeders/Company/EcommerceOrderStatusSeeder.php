<?php

namespace Database\Seeders\Company;

use App\Models\Company\Ecommerce\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            [
                'id' => OrderStatus::WAITING_FOR_PAYMENT,
                'name_uz' => 'To\'lov kutilmoqda',
                'name_ru' => 'Ожидание оплаты',
                'name_en' => 'Waiting for payment',
                'name_oz' => 'Тўлов кутилмоқда',
                'is_active' => true,
            ],
        ];

        try {
            foreach ($orderStatuses as $orderStatus) {
                OrderStatus::query()->firstOrCreate(['id' => $orderStatus['id']], $orderStatus);
            }

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
