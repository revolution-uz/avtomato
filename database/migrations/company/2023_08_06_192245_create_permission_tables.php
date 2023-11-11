<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionTables extends Migration
{
    public function up()
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->timestamps();

            $table->unique('slug');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->integer('permission_group_id');
            $table->integer('feature_id')->nullable();
            $table->boolean('is_branch_related');
            $table->timestamps();

            $table->unique('slug');
            $table->index('permission_group_id');
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->cascadeOnDelete();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // role id
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->integer('branch_id')->nullable();

            $table->index('role_id');
            $table->index('permission_id');
            $table->index('branch_id');
            $table->unique(['role_id', 'permission_id', 'branch_id']);

            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->restrictOnDelete();

        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::dropColumns('users', ['role_id']);
        Schema::drop('role_permissions');
        Schema::drop('roles');
        Schema::drop('permissions');
        Schema::drop('permission_groups');
    }
}
