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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_reference')->unique(); // e.g., TKT-1001
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The customer
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null'); // The assigned admin
            $table->string('subject');
            $table->text('description');
            $table->string('category');
            $table->enum('status', ['open', 'in-progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamp('due_date')->nullable(); // For SLA Tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
