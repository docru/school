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
        Schema::create('school_days', function (Blueprint $table) {
            $table->comment('Учебный день группы');
            $table->id();
            $table->timestamps();
            $table->bigInteger('group_id')->unsigned()->comment('Группа');
            $table->date('date')->index()->comment('Дата');


            $table->foreign('group_id')->references('id')->on('groups')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_days');
    }
};
