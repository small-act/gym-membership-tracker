<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('membership_plans', 'duration_unit')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->string('duration_unit', 10)
                    ->default('days')
                    ->after('duration_days');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('membership_plans', 'duration_unit')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->dropColumn('duration_unit');
            });
        }
    }
};
