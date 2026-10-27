<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Задания урока и ответы учеников.
     *
     * Старые таблицы были заготовкой: задание привязывалось к курсу, а не к уроку,
     * не было ни заголовка, ни порядка, ни полей под видео. Обе пустые, поэтому
     * пересобираем, а не правим по полю.
     *
     * Домашнее задание и задания на уроке устроены одинаково, различает их kind.
     */
    public function up(): void
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('tasks');

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete()->comment('Урок');
            $table->string('kind', 20)->comment('homework — домашнее задание, classwork — задание на уроке');
            $table->string('type', 20)->comment('memorize, question, video, text');
            $table->unsignedInteger('order')->default(0)->comment('Порядок в списке');
            $table->string('title')->comment('Название блока');
            $table->text('body')->nullable()->comment('Текст: что выучить, описание вопроса, пояснение');
            $table->string('url')->nullable()->comment('Ссылка на видео');
            $table->timestamps();

            $table->index(['lesson_id', 'kind', 'order']);
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete()->comment('Задание');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('Ученик');
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete()->comment('Группа');
            $table->text('answer')->nullable()->comment('Ответ ученика');
            $table->string('status', 20)->default('draft')->comment('draft — черновик, submitted — сдан, checked — проверен');
            $table->timestamp('submitted_at')->nullable()->comment('Когда сдан');
            $table->timestamp('checked_at')->nullable()->comment('Когда проверен');
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete()->comment('Кто проверил');
            $table->text('comment')->nullable()->comment('Комментарий учителя');
            $table->timestamps();

            // Ответ у ученика на задание один
            $table->unique(['task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('tasks');

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('course_id')->constrained()->comment('Курс');
            $table->string('type')->comment('Тип задания');
            $table->text('description')->comment('Описание');
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->comment('Пользователь');
            $table->foreignId('group_id')->constrained()->comment('Группа');
            $table->foreignId('task_id')->constrained()->comment('Задание');
            $table->text('answer')->comment('Ответ');
        });
    }
};
