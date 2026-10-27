<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('membership_id')
                ->constrained('memberships')
                ->restrictOnDelete();

            $table->decimal('amount', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->string('reference_number')->nullable();
            $table->string('status')->default('paid');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('membership_id');

            $table->dropColumn([
                'amount',
                'payment_date',
                'payment_method',
                'reference_number',
                'status',
                'notes',
            ]);
        });
    }
};
