<?php

use App\Models\Coupon;
use App\Models\MobileUser;
use App\Models\LocationAddress;
use App\Models\Service;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(MobileUser::class)->constrained();
            $table->foreignIdFor(LocationAddress::class)->constrained();


            $table->boolean('is_direct_service');
            $table->double('total_cost');
            $table->double('fees');
            $table->double('pay_at_cashier');
            $table->string('payment_method')->nullable();
            $table->integer('status');
            $table->dateTime('reserve_datetime')->nullable();
            $table->text('description')->nullable();
            $table->string('coupon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
