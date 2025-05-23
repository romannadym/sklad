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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name');
            $table->string('asset_serial');
            $table->unsignedBigInteger('asset_id');
            $table->string('requester_email');
            $table->string('requester_name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('component_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('sd_ticket_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
