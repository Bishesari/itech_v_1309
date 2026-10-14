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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            // Amount in Iranian Rials
            $table->unsignedBigInteger('amount');

            // pending, paid, failed
            $table->string('status', 20)->default('pending');

            $table->string('gateway', 20)->default('sep');

            // Unique merchant request number sent to SEP
            $table->string('res_num', 50)->unique();

            // SEP response data
            $table->string('gateway_token')->nullable()->unique();
            $table->string('gateway_reference')->nullable()->unique();

            $table->uuid('token_request_key')
                ->nullable()
                ->unique();

            // Set only after successful server-side verification
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
