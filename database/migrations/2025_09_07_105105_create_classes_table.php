<?php
// database/migrations/xxxx_xx_xx_create_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 10A, 10B, 11IPA1, dll
            $table->string('grade'); // 10, 11, 12
            $table->string('major')->nullable(); // IPA, IPS, BAHASA
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users');
            $table->integer('capacity')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};