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
        Schema::create('comments', function (Blueprint $table) {
            $table->comment('Комментарии');
            $table->id();
            $table->timestamps();
            $table->bigInteger('entity_id')->unsigned()->comment('Сущность');
            $table->string('type_entity')->comment('Тип сущности');
            $table->text('comment')->comment('Комментарий');
            $table->json('score')->comment('Оценка');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
