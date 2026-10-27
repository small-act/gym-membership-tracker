<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique();
            $table->text('face_descriptor')->nullable();
            $table->timestamp('face_enrolled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn([
                'qr_token',
                'face_descriptor',
                'face_enrolled_at',
            ]);
        });
    }
};
