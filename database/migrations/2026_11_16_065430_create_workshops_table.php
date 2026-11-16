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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('instructor_name');
            $table->string('instructor_title')->nullable();
            $table->text('description');
            $table->dateTime('schedule_date');
            $table->unsignedInteger('duration_hours')->default(3);
            $table->string('location')->default('Lab RPL SMK PESAT & Live Streaming');
            $table->unsignedInteger('price')->default(50000);
            $table->unsignedInteger('quota')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
