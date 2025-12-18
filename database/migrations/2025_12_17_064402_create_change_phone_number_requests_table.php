<?php

use App\Models\MobileUser;
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
        Schema::create('change_phone_number_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MobileUser::class)->constrained()->cascadeOnDelete();
            $table->string('new_phone_number');
            $table->string('otp');
            $table->timestamp('otp_expires_at');
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_phone_number_requests');
    }
};
