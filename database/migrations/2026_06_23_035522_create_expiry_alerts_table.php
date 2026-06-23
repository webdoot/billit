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
        Schema::create('expiry_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_id')->constrained('customer_services')->onDelete('cascade');
            $table->integer('days_before'); // 60, 30, 15, 7, 1
            $table->date('alert_date');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expiry_alerts');
    }
};
