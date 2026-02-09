<?php

use App\Models\Order;
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
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->string('gateway', 32); // dana, midtrans
            $table->string('gateway_reference')->comment('Gateway order/reference id for idempotency');
            $table->string('gateway_transaction_id')->nullable()->comment('Gateway transaction id after payment');
            $table->string('status', 32)->default('pending'); // pending, completed, failed, expired, cancelled
            $table->decimal('amount', 12, 2);
            $table->json('metadata')->nullable();
            $table->timestamp('initiated_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('redirect_url')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'gateway_reference']);
            $table->index(['order_id', 'gateway']);
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
