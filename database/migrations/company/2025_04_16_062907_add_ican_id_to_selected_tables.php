<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->unsignedBigInteger('ican_id')->nullable()->index();
        });

        Schema::table('tariffs', function (Blueprint $table) {
            $table->unsignedBigInteger('ican_id')->nullable()->index();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('ican_id')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropIndex(['ican_id']);
            $table->dropColumn('ican_id');
        });

        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropIndex(['ican_id']);
            $table->dropColumn('ican_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['ican_id']);
            $table->dropColumn('ican_id');
        });
    }
};
