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
        Schema::create('mac_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->macAddress('mac_address')->unique()->nullable(false);
            $table->uuid('dhcp_entry_id');
            $table->foreign('dhcp_entry_id')->references('id')->on('dhcp_entries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mac_addresses');
    }
};
