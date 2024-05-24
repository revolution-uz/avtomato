<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('installment_cards', function (Blueprint $table) {
            $table->dropUnique('installment_cards_installment_id_card_id_unique');
            $table->unique(['installment_id', 'card_id', 'deleted_at']);
        });
        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->dropUnique('installment_guarantors_installment_id_customer_id_unique');
            $table->unique(['installment_id', 'customer_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installment_cards', function (Blueprint $table) {
            $table->dropUnique('installment_cards_installment_id_card_id_deleted_at_unique');
            $table->unique(['installment_id', 'card_id']);
        });
        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->dropUnique('installment_guarantors_installment_id_customer_id_deleted_at_unique');
            $table->unique(['installment_id', 'customer_id']);
        });
    }
};
