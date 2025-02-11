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
        Schema::create('component_checkouts', function (Blueprint $table) {
            $table->id();
            $table->integer('asset_id')->nullable();
            $table->integer('component_id')->nullable();
            $table->integer('assigned_qty')->nullable();
            $table->text('note')->nullable();
            $table->string('ticketnum')->nullable();
            $table->integer('assigned_to_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. 
     */
    public function down(): void
    {
        Schema::dropIfExists('component_checkouts');
    }
};
