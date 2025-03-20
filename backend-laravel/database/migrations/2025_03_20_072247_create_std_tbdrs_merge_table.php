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
        Schema::create('std_tbdrs_merge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uusers_id')->constrained('u_users')->onDelete('cascade');
            $table->foreignId('tbdrs_id')->constrained('tbdrs_merge')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('std_tbdrs_merge');
    }
};