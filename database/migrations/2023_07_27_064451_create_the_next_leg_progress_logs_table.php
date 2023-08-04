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
        Schema::create('the_next_leg_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nextleg_id')->nullable();
            $table->foreign('nextleg_id')->references('id')->on('the_next_legs')->onDelete('cascade');
            $table->text('progress_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('the_next_leg_progress_logs');
    }
};
