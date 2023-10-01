<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'personal_access_tokens';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('device_ip')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropColumns(self::TABLE, 'device_ip');
        if (Schema::hasColumn(self::TABLE, 'deleted_at')) {
            Schema::dropColumns(self::TABLE, 'deleted_at');
        }
    }
};
