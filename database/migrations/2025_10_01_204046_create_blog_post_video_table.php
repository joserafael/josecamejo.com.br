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
        Schema::create('blog_post_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_video_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->string('caption')->nullable();
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['blog_post_id', 'blog_video_id']);
            $table->index(['blog_post_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_video');
    }
};
