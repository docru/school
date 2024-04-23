<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->comment('Уроки');
            $table->id();
            $table->timestamps();
            $table->bigInteger('course_id')->unsigned()->comment('Курс');
            $table->unsignedBigInteger('module_id')->nullable()->comment('Модуль');
            $table->string('name', 255)->default('Урок')->comment('Название урока');
            $table->float('hours')->nullable()->default(1)->comment('Количество учебных часов');
            $table->integer('order')->default(0)->comment('Порядок');
            $table->text('methodical_description')->nullable()->comment('Методическое описание');
            $table->text('abstract')->nullable()->comment('Конспект');
            $table->integer('school_day')->nullable()->comment('Учебный день');
            $table->integer('school_day_order')->nullable()->comment('Сортировка в учебном дне');

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('module_id')->references('id')->on('modules')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
