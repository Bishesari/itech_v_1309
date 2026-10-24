<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_blueprint_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('chapter_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('difficulty', 20);

            $table->unsignedSmallInteger('question_count');

            $table->timestamps();

            $table->unique([
                'exam_id',
                'chapter_id',
                'difficulty',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_blueprint_rules');
    }
};
