<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ученик прикрепляется к лидеру домашней группы — пользователю с ролью
     * home_group_leader. Лидер у ученика один, поэтому ссылка лежит в users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('home_group_leader_id')
                ->after('authorized_at')
                ->nullable()
                ->comment('Лидер домашней группы')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_group_leader_id');
        });
    }
};
