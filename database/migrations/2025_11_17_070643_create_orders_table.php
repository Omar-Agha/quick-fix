<?php

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
            $table->foreignIdFor(Service::class)->constrained();
            $table->foreignIdFor(LocationAddress::class)->constrained();
            $table->double('cost');
            $table->double('fees');
            $table->string('payment_method');
            $table->string('status');
            $table->dateTime('reserve_datetime');
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
