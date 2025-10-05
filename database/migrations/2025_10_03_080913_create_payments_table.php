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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('auction_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // PaymentType enum
            $table->unsignedBigInteger('amount'); // Amount in Toman
            $table->text('description');
            $table->string('status')->default('pending'); // PaymentStatus enum
            $table->string('authority')->nullable(); // Zarinpal authority
            $table->string('ref_id')->nullable(); // Zarinpal ref_id
            $table->string('gateway_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->index(['user_id', 'auction_id']);
            $table->index('status');
            $table->index('type');
            $table->index('authority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
