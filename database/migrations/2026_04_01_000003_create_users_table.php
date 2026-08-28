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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_role')->nullable();
            $table->foreign('id_role')->references('id')->on('role')->onDelete('set null');
            $table->string('nama', 50);
            $table->string('nip', 18)->unique();
            $table->string('email')->unique();
            $table->string('alamat', 100);
            $table->string('notlp', 13);
            $table->string('password');
            $table->string('foto_profile')->nullable();
            $table->longText('face_embedding')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
