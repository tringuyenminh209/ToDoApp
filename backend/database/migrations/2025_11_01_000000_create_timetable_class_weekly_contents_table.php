<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 週別授業内容テーブル作成
     * Timetable Class Weekly Contents: 週ごとの授業内容管理
     */
    public function up(): void
    {
        Schema::create('timetable_class_weekly_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_class_id')
                  ->constrained('timetable_classes')
                  ->onDelete('cascade')
                  ->comment('授業ID');

            // 週情報
            $table->integer('year')->comment('年');
            $table->integer('week_number')->comment('週番号（1-53）');
            $table->date('week_start_date')->comment('週の開始日（月曜日）');

            // 週別内容
            $table->string('title')->nullable()->comment('週別タイトル');
            $table->text('content')->nullable()->comment('週別内容・トピック');
            $table->text('homework')->nullable()->comment('宿題');
            $table->text('notes')->nullable()->comment('週別メモ');

            // 進捗
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])
                  ->default('scheduled')
                  ->comment('ステータス');

            $table->timestamps();

            // Indexes
            $table->index(['timetable_class_id', 'year', 'week_number'], 'idx_class_year_week');
            $table->index(['week_start_date'], 'idx_week_start');

            // Unique constraint: 1つの授業に対して1週間に1つのコンテンツのみ
            $table->unique(['timetable_class_id', 'year', 'week_number'], 'unique_class_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_class_weekly_contents');
    }
};

