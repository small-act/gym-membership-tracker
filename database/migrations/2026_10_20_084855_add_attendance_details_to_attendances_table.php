<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('member_id')
                ->constrained('members')
                ->restrictOnDelete();

            $table->date('attendance_date');
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->string('check_in_method')->default('manual');
            $table->text('notes')->nullable();

            // A member can have only one attendance record per day.
            $table->unique(
                ['member_id', 'attendance_date'],
                'attendances_member_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_member_date_unique');
            $table->dropConstrainedForeignId('member_id');
            $table->dropColumn([
                'attendance_date',
                'check_in_at',
                'check_out_at',
                'check_in_method',
                'notes',
            ]);
        });
    }
};
