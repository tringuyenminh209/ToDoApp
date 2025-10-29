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
        Schema::create('knowledge_item_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_item_id')
                ->constrained('knowledge_items')
                ->onDelete('cascade');

            $table->string('tag_name', 100)->comment('タグ名');

            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->unique(['knowledge_item_id', 'tag_name']);
            $table->index('tag_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_item_tags');
    }
};
