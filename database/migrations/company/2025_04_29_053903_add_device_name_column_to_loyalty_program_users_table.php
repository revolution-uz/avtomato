<?php

use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_users', function (Blueprint $table) {
            $table->string('main_device')->nullable();
        });

        User::query()->chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->update([
                    'main_device' => $user->tokens()->withTrashed()->orderBy('id')->first()?->name,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_users', function (Blueprint $table) {
            $table->dropColumn('main_device');
        });
    }
};
