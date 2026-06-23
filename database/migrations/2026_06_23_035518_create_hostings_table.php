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
        Schema::create('hostings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_id')->constrained('customer_services')->onDelete('cascade');
            $table->foreignId('server_id')->nullable()->constrained('servers')->onDelete('set null');
            $table->string('hosting_type'); // Shared, VPS, Dedicated, Cloud
            $table->string('control_panel')->nullable(); // cPanel, Plesk, CyberPanel, None
            $table->string('username')->nullable();
            $table->string('disk_limit')->nullable();
            $table->string('bandwidth_limit')->nullable();
            $table->string('status')->default('Active'); // Active, Suspended, Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostings');
    }
};
