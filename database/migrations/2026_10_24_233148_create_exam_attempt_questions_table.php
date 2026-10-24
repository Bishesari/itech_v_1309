<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempt_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_attempt_id')
                ->constrained()
                ->cascadeOnDelete();

            // برای ردیابی منبع سؤال؛ snapshot مستقل از این رکورد است.
            $table->foreignId('question_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedSmallInteger('position');

            $table->json('question_snapshot');

            // شناسهٔ گزینه‌های snapshot‌شده، ترتیب و پاسخ صحیح در سمت سرور.
            $table->json('options_snapshot');

            // شناسهٔ گزینه‌ای که کاربر انتخاب کرده است.
            $table->string('selected_option_key', 64)->nullable();

            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();

            $table->timestamps();

            $table->unique(['exam_attempt_id', 'position']);
            $table->index(['exam_attempt_id', 'is_correct']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_questions');
    }
};
