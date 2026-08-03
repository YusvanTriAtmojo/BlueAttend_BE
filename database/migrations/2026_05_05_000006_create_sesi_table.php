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
         Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_event')->nullable();;
            $table->foreign('id_event')->references('id')->on('event')->onDelete('cascade');
            $table->unsignedBigInteger('id_ble')->nullable();;
            $table->foreign('id_ble')->references('id')->on('ble')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi');
    }
};
