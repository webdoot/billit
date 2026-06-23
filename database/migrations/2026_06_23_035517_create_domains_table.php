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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_id')->constrained('customer_services')->onDelete('cascade');
            $table->string('domain_name');
            $table->string('registrar');
            $table->string('registrar_account')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expiry_date');
            $table->boolean('auto_renew')->default(false);
            $table->string('dns_provider')->nullable();
            $table->string('nameserver_1')->nullable();
            $table->string('nameserver_2')->nullable();
            $table->string('nameserver_3')->nullable();
            $table->string('nameserver_4')->nullable();
            $table->string('status')->default('Active'); // Active, Expired, Transferred
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
