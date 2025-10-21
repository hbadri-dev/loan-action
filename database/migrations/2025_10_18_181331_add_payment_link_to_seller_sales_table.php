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
        Schema::table('seller_sales', function (Blueprint $table) {
            $table->string('payment_link')->nullable();
            $table->boolean('payment_link_used')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_sales', function (Blueprint $table) {
            $table->dropColumn(['payment_link', 'payment_link_used']);
        });
    }
};
