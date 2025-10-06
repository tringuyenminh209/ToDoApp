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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Project Information
            $table->string('name_en', 255)->comment('プロジェクト名（英語）');
            $table->string('name_ja', 255)->comment('プロジェクト名（日本語）');
            $table->text('description_en')->nullable()->comment('説明（英語）');
            $table->text('description_ja')->nullable()->comment('説明（日本語）');

            // Status & Timeline
            $table->string('status')->default('active')->comment('ステータス');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('end_date')->nullable()->comment('終了日');

            // Progress & Appearance
            $table->decimal('progress_percentage', 5, 2)->default(0)->comment('進捗率');
            $table->string('color', 7)->default('#6366f1')->comment('カラーコード');
            $table->boolean('is_active')->default(true)->comment('アクティブかどうか');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
