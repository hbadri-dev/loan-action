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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('authority'); // Zarinpal authority
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('ref_id')->nullable();
            $table->json('gateway_response')->nullable(); // Full gateway response
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'authority']);
            $table->index('status');
            $table->index('authority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
