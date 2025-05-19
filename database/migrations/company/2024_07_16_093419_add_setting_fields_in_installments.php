<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            if (! Schema::hasColumn('installments', 'card_after_due_days')) {
                $table->integer('card_after_due_days')->nullable();
            }
            if (! Schema::hasColumn('installments', 'pinfl_after_due_days')) {
                $table->integer('pinfl_after_due_days')->nullable();
            }
            if (! Schema::hasColumn('installments', 'guarantor_after_due_days')) {
                $table->integer('guarantor_after_due_days')->nullable();
            }
            if (! Schema::hasColumn('installments', 'guarantor_pinfl_after_due_days')) {
                $table->integer('guarantor_pinfl_after_due_days')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            if (Schema::hasColumn('installments', 'card_after_due_days')) {
                $table->dropColumn('card_after_due_days');
            }
            if (Schema::hasColumn('installments', 'pinfl_after_due_days')) {
                $table->dropColumn('pinfl_after_due_days');
            }
            if (Schema::hasColumn('installments', 'guarantor_after_due_days')) {
                $table->dropColumn('guarantor_after_due_days');
            }
            if (Schema::hasColumn('installments', 'guarantor_pinfl_after_due_days')) {
                $table->dropColumn('guarantor_pinfl_after_due_days');
            }
        });
    }
};
