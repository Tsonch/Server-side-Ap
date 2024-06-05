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
        Schema::create('users_and_roles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->integer('created_by');
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('user_id', 'users_and_roles_user_idx');
            $table->index('role_id', 'users_and_roles_role_idx');

            $table->foreign('user_id', 'users_and_roles_user_fk')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('role_id', 'users_and_roles_role_fk')->on('roles')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_and_roles');
    }
};
