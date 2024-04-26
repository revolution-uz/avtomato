<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->renameColumn('plum_id', 'external_id');
            $table->string('external_id')->change();
        });
    }

    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->renameColumn('external_id', 'plum_id');
            $table->bigInteger('plum_id')->change();
        });
    }
};
