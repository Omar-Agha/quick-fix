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
        Schema::create('location_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(MobileUser::class)->constrained()->cascadeOnDelete();
            $table->integer('address_type');
            $table->string('address');
            $table->string('full_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_addresses');
    }
};
