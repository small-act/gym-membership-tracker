<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'processed_by')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('processed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'processed_by')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('processed_by');
            });
        }
    }
};