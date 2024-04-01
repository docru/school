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
        Schema::create('answers', function (Blueprint $table) {
            $table->comment('Курсы');
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned()->comment('Пользователь');
            $table->bigInteger('group_id')->unsigned()->comment('Группа');
            $table->bigInteger('task_id')->unsigned()->comment('Задание');
            $table->text('answer')->comment('Ответ');

            $table->foreign('user_id')->references('id')->on('users')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('groups')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('task_id')->references('id')->on('tasks')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
