<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_receipts', function (Blueprint $table) {
            // Add fields for loan verification only if they don't exist
            if (!Schema::hasColumn('payment_receipts', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('payment_receipts', 'national_id')) {
                $table->string('national_id')->nullable()->after('full_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'national_id']);
        });
    }
};
