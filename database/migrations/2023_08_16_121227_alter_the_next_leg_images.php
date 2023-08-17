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
        Schema::table('the_next_leg_images', function (Blueprint $table) {
            $table->integer('variation_of')->nullable();
            $table->string('aws_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('the_next_leg_images', function (Blueprint $table) {
            $table->dropColumn('variation_of');
            $table->dropColumn('aws_path');
        });
    }
};
