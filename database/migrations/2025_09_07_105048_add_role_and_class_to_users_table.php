<?php
// database/migrations/xxxx_xx_xx_add_role_and_class_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kepala_sekolah', 'guru'])->default('guru');
            $table->boolean('is_wali_kelas')->default(false);
            $table->string('nip')->nullable()->unique();
            $table->string('phone')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_wali_kelas', 'nip', 'phone']);
        });
    }
};