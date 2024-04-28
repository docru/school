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
        Schema::create('group_users', function (Blueprint $table) {
            $table->comment('Участие пользователя в группе');
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('user_id')->unsigned()->comment('Пользователь');
            $table->bigInteger('group_id')->unsigned()->comment('Группа');
            $table->text('role')->comment('Роль в группе');

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('group_users');
    }
};
