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
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('service_product_id')->constrained('service_products')->onDelete('cascade');
            $table->string('service_name');
            $table->date('start_date');
            $table->date('expiry_date');
            $table->string('billing_cycle'); // Monthly, Quarterly, Half Yearly, Yearly, One Time
            $table->decimal('amount', 15, 2);
            $table->boolean('auto_renew')->default(false);
            $table->string('status')->default('Pending'); // Active, Expired, Suspended, Cancelled, Pending
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_services');
    }
};
