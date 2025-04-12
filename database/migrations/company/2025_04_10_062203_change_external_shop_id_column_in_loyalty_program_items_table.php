<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loyalty_program_items', function (Blueprint $table) {
            $table->renameColumn('external_shop_id', 'external_shop_code');
        });

        Schema::table('loyalty_program_items', function (Blueprint $table) {
            $table->string('external_shop_code')->nullable()->index()->change();
            $table->index('external_code');
        });
    }

    public function down()
    {
        Schema::table('loyalty_program_items', function (Blueprint $table) {
            $table->dropIndex(['external_shop_code', 'external_code']);
            $table->unsignedBigInteger('external_shop_code')->nullable()->change();
        });

        Schema::table('loyalty_program_items', function (Blueprint $table) {
            $table->renameColumn('external_shop_code', 'external_shop_id');
        });
    }
};
