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
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('hostname')->nullable();
            $table->string('mac');
            $table->string('ip')->nullable();
            $table->string('added_by');
            $table->string('owner');
            $table->date('added_date');
            $table->string('wireless')->default('Yes');
            $table->string('status')->default('Enabled');
            $table->text('notes')->nullable();
            $table->string('ssd')->default('No');
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->index('mac');
            $table->index('ip');
            $table->index('hostname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
