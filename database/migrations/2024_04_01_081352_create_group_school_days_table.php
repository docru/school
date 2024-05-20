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
        Schema::create('group_school_days', function (Blueprint $table) {
            $table->comment('Учебный день группы');
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('course_school_day_id')->comment('Учебный день');
            $table->bigInteger('group_id')->unsigned()->comment('Группа');
            $table->date('date')->index()->comment('Дата');
            $table->integer('order')->comment('Сортировка');


            $table->foreign('course_school_day_id')->references('id')->on('course_school_days')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('groups')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_school_days');
    }
};
