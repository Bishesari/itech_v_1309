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
        Schema::create('chapter_exam', function (Blueprint $table) {
            $table->foreignId('chapter_id')
                ->constrained()
                ->restrictOnDelete();

            $table->id();
            $table->foreignId('exam_id')
                ->constrained()
                ->cascadeOnDelete();



            $table->timestamps();

            $table->primary(['chapter_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_exam');
    }
};
