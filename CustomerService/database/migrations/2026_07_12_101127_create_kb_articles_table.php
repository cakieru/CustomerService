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
    Schema::create('kb_articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('category');
        $table->string('cat_id'); 
        $table->string('tags')->nullable();
        $table->text('desc');
        $table->integer('views')->default(0);
        $table->integer('yes_votes')->default(0);
        $table->integer('no_votes')->default(0);
        $table->string('visibility')->default('public');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_articles');
    }
};
