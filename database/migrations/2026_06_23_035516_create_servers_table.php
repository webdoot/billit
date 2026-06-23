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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->string('hostname')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->decimal('monthly_cost', 15, 2)->default(0.00);
            $table->date('renewal_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
