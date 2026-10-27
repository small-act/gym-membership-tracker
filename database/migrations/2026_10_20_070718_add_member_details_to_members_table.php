<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('member_number')->nullable()->unique();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('status')->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['member_number']);
            $table->dropUnique(['email']);
            $table->dropColumn([
                'member_number',
                'full_name',
                'email',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'emergency_contact',
                'status',
            ]);
        });
    }
};
