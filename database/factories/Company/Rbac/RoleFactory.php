<?php

namespace Database\Factories\Company\Rbac;

use App\Models\Company\Rbac\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }
}
