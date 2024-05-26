<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'auth_attempts',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('phone');
                $table->string('code');

                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('auth_attempts');
    }
};
