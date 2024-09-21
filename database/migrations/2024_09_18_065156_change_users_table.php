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

        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->after('phone')->nullable()->index()->comment('Фамилия');
            $table->renameColumn('nickname', 'patronymic');
            $table->dropColumn('nickname_description');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('patronymic')->nullable()->comment('Отчество')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('patronymic')->nullable()->comment('Псевдоним')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->renameColumn('patronymic', 'nickname');
            $table->text('nickname_description')->nullable()->comment('Описание псевдонима');
        });
    }
};
