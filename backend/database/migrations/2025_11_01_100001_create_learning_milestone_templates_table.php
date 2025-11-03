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
        Schema::create('learning_milestone_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')
                  ->constrained('learning_path_templates')
                  ->onDelete('cascade')
                  ->comment('テンプレートID');

            // Milestone Information
            $table->string('title', 255)->comment('マイルストーンタイトル');
            $table->text('description')->nullable()->comment('説明');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // Estimation
            $table->integer('estimated_hours')->nullable()->comment('見積もり時間（時間）');

            // Deliverables
            $table->json('deliverables')->nullable()->comment('成果物リスト');

            $table->timestamps();

            // Indexes
            $table->index(['template_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_milestone_templates');
    }
};

