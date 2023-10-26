<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->renameColumn('myuzkard_id', 'plum_id');
        });

        Schema::table('installments', function (Blueprint $table) {
            $table->renameColumn('myuzkard_id', 'plum_id');
        });

        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->renameColumn('myuzkard_id', 'plum_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('myuzkard_id', 'plum_id');
        });
    }
};
