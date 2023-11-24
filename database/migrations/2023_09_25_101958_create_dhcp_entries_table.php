<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dhcp_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hostname')->unique()->nullable(false);
            $table->macAddress('mac_address')->unique()->nullable(false);
            $table->ipAddress('ip_address')->nullable();
            $table->string('owner')->nullable(false);
            $table->string('added_by')->nullable(false);
            $table->boolean('is_ssd')->nullable(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_imported')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dhcp_entries');
    }
};
