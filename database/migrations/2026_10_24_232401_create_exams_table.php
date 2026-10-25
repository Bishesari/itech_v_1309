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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            $table->foreignId('standard_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->string('title', 200);
            $table->string('type', 30)->index();

            $table->text('description')->nullable();

            $table->unsignedSmallInteger('question_count');
            $table->string('selection_mode', 20)->default('different');
            $table->json('questions_snapshot')->nullable();
            $table->unsignedSmallInteger('seconds_per_question')
                ->default(90);

            $table->unsignedTinyInteger('pass_percentage')->nullable();

            // برای آزمون‌های رایگان؛ در آزمون پولی محدودیت از خرید می‌آید.
            $table->unsignedSmallInteger('max_attempts')->nullable();

            // بازهٔ مجاز شرکت در آزمون، نه مدت خود آزمون.
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(false);

            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
