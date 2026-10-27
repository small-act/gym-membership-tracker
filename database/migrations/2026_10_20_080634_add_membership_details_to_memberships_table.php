<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->foreignId('member_id')
                ->constrained('members')
                ->restrictOnDelete();

            $table->foreignId('membership_plan_id')
                ->constrained('membership_plans')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_id');
            $table->dropConstrainedForeignId('membership_plan_id');

            $table->dropColumn([
                'start_date',
                'end_date',
                'price',
                'status',
                'notes',
            ]);
        });
    }
};
