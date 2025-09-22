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
        Schema::create('buyer_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_step')->default(1); // 1-9 steps
            $table->string('step_name'); // details, contract, payment, bid, waiting-seller, purchase-payment, awaiting-seller-transfer, confirm-transfer, complete
            $table->boolean('is_completed')->default(false);
            $table->json('step_data')->nullable(); // Store additional step-specific data
            $table->timestamp('last_activity_at')->useCurrent();
            $table->timestamps();

            // Unique constraint - one progress per user per auction
            $table->unique(['auction_id', 'user_id']);

            // Indexes
            $table->index(['user_id', 'is_completed']);
            $table->index('current_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_progress');
    }
};
