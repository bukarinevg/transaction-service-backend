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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->uuid('payment_id')->unique();
            $table->foreignId('user_id')->constrained('user');
            $table->foreignId('project_id')->constrained('project');
            $table->string('details');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->enum('status', ['Оплачен', 'Не оплачен'])->default('Не оплачен');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
