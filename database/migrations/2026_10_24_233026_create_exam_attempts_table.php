<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            // برای آزمون رایگان null است؛ برای آزمون پولی به خرید متصل می‌شود.
            $table->foreignId('order_item_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->string('status', 20)->default('in_progress');

            // مقادیر زمان شروع، مدت و تعداد سؤال در شروع آزمون تثبیت می‌شوند.
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedSmallInteger('question_count');
            $table->unsignedInteger('duration_seconds');

            // نتیجه پس از پایان آزمون محاسبه و ذخیره می‌شود.
            $table->unsignedSmallInteger('correct_answers')->nullable();
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->boolean('passed')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'exam_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
