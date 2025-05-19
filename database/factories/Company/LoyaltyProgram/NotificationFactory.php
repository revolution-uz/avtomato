<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'title_uz' => $this->faker->text(255),
            'title_ru' => $this->faker->text(255),
            'body_uz' => $this->faker->text(1000),
            'body_ru' => $this->faker->text(1000),
            'sent_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
