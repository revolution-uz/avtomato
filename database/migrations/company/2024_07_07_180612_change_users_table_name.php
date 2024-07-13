<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('users', 'admins');

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'admin_id');
        });

        $tables = ['installment_comments', 'installments', 'action_events'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->renameColumn('user_id', 'admin_id');
            });
        }
    }

    public function down(): void
    {
        Schema::rename('admins', 'users');

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('admin_id', 'employee_id');
        });

        $tables = ['installment_comments', 'installments', 'action_events'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->renameColumn('admin_id', 'user_id');
            });
        }
    }
};
