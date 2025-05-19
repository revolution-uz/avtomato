<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_delivery_points', function (Blueprint $table) {
            $table->dropColumn('address_id');
            $table->string('phone', 16)->nullable();
            $table->string('address_uz')->nullable();
            $table->string('address_ru')->nullable();
            $table->string('address_en')->nullable();
            $table->string('address_oz')->nullable();
            $table->string('landmark_uz')->nullable();
            $table->string('landmark_ru')->nullable();
            $table->string('landmark_en')->nullable();
            $table->string('landmark_oz')->nullable();
            $table->string('location_url')->nullable();
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('mahalla_id')->nullable()->constrained('mahallas');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_delivery_points', function (Blueprint $table) {
            $table->bigInteger('address_id')->nullable();
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['mahalla_id']);
            $table->dropColumn([
                'phone',
                'address_uz',
                'address_ru',
                'address_en',
                'address_oz',
                'landmark_uz',
                'landmark_ru',
                'landmark_en',
                'landmark_oz',
                'location_url',
                'lat',
                'lng',
                'region_id',
                'district_id',
                'mahalla_id',
            ]);
        });
    }
};
