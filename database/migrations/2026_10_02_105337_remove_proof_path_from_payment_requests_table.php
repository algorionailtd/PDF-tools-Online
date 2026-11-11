<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('payment_requests', 'proof_path')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->dropColumn('proof_path');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('payment_requests', 'proof_path')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->string('proof_path')->nullable()->after('transaction_id');
            });
        }
    }
};