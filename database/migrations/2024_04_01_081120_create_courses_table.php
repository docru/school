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
        Schema::create('courses', function (Blueprint $table) {
            $table->comment('Курсы');
            $table->id();
            $table->timestamps();
            $table->string('name')->comment('Название курса');
            $table->text('description')->nullable()->comment('Описание курса');
            $table->json('calendar_plan')->default('{}')->comment('Календарный план');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
