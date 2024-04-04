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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique()->comment('Телефон');
            $table->string('name')->nullable()->comment('Имя');
            $table->string('nickname')->nullable()->comment('Псевдоним');
            $table->text('nickname_description')->nullable()->comment('Описание псевдонима');
//            $table->text('about_me')->nullable()->comment('О себе');
            $table->string('entry_code', 12)->nullable()->comment('Код входа');
            $table->timestamp('entry_code_generated_at')->nullable()->comment('Время генерации кода');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
