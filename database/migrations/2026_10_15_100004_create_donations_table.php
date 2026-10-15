<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fundraiser_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('FCFA');
            $table->string('message', 500)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status', 20)->default('paid')->index();
            $table->string('reference', 40)->nullable()->unique();
            $table->timestamps();

            $table->index(['fundraiser_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
