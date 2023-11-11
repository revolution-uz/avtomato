<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreign('role_id')->cascadeOnDelete();
            $table->foreign('permission_id')->cascadeOnDelete();
            $table->foreign('branch_id')->restrictOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->restrictOnDelete();
        });
    }
};
