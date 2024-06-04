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
        Schema::create('roles_and_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->integer('created_by');
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('permission_id', 'roles_and_permissions_permission_idx');
            $table->index('role_id', 'roles_and_permissions_role_idx');

            $table->foreign('permission_id', 'roles_and_permissions_permission_fk')->on('permissions')->references('id');
            $table->foreign('role_id', 'roles_and_permissions_role_fk')->on('roles')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_and_permissions');
    }
};
