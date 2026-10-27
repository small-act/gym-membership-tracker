<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'paymongo_checkout_session_id')) {
                $table->string('paymongo_checkout_session_id')
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('payments', 'paymongo_payment_intent_id')) {
                $table->string('paymongo_payment_intent_id')
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('payments', 'paymongo_webhook_event_id')) {
                $table->string('paymongo_webhook_event_id')
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'paid_at')) {
                $table->dropColumn('paid_at');
            }

            if (Schema::hasColumn('payments', 'paymongo_webhook_event_id')) {
                $table->dropUnique(['paymongo_webhook_event_id']);
                $table->dropColumn('paymongo_webhook_event_id');
            }

            if (Schema::hasColumn('payments', 'paymongo_payment_intent_id')) {
                $table->dropUnique(['paymongo_payment_intent_id']);
                $table->dropColumn('paymongo_payment_intent_id');
            }

            if (Schema::hasColumn('payments', 'paymongo_checkout_session_id')) {
                $table->dropUnique(['paymongo_checkout_session_id']);
                $table->dropColumn('paymongo_checkout_session_id');
            }
        });
    }
};
