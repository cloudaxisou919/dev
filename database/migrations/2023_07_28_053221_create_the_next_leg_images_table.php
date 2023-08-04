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
        Schema::create('the_next_leg_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thenextleg_id')->nullable();
            $table->foreign('thenextleg_id')->references('id')->on('the_next_legs')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->unsignedBigInteger('upscale_parent_id')->nullable();
            $table->foreign('upscale_parent_id')->references('id')->on('the_next_leg_images')->onDelete('cascade');
            $table->integer('image_index')->nullable();
            $table->string('upscale_image')->nullable();
            $table->string('image_local_path')->nullable();
            $table->string('upscale_local_path')->nullable();
            $table->dateTime('upscaling_at')->nullable();
            $table->integer('variation_of')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('the_next_leg_images');
    }
};
