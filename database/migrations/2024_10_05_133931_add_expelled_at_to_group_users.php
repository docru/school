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
        try {
            Schema::table('group_users', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        } catch (\Illuminate\Database\QueryException $th) {
        }
        Schema::table('group_users', function (Blueprint $table) {
            $table->timestamp('expelled_at')->after('updated_at')->nullable()->comment('Дата отчисления');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_users', function (Blueprint $table) {
            $table->dropColumn('expelled_at');
        });
    }
};
