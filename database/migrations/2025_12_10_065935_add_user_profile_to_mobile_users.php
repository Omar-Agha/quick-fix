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
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->string('full_name')->after('phone_number')->default('');
            $table->string('avatar')->nullable()->after('full_name');
            $table->string('home_phone')->nullable()->after('phone_number');
            $table->string(column: 'email')->nullable()->after('home_phone');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->dropColumn('full_name');
            $table->dropColumn('avatar');
            $table->dropColumn('home_phone');
            $table->dropColumn('email');
            $table->dropSoftDeletes();
        });
    }
};
