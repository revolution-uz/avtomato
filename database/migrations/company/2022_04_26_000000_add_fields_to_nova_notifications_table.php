<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToNovaNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('nova_notifications', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropColumns('nova_notifications', ['deleted_at']);
    }
}
