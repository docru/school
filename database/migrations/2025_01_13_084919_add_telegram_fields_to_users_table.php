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
        Schema::table('users', function (Blueprint $table) {
            $table->BigInteger('tg_id')->unique()->nullable()->after('patronymic')->index()->comment('ID Telegram пользователя');
            $table->json('dialog_state')->after('tg_id')->nullable()->comment('Состояние диалога');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tg_id');
            $table->dropColumn('dialog_state');
        });
    }
};
