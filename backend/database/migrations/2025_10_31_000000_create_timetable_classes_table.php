<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 時間割テーブル作成
     * Timetable Classes: 授業スケジュール管理
     */
    public function up(): void
    {
        Schema::create('timetable_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Class情報
            $table->string('name', 255)->comment('授業名');
            $table->text('description')->nullable()->comment('詳細説明');
            $table->string('room', 100)->nullable()->comment('教室');
            $table->string('instructor', 255)->nullable()->comment('講師名');

            // スケジュール
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                  ->comment('曜日');
            $table->integer('period')->comment('時限（1-10）');
            $table->time('start_time')->comment('開始時刻');
            $table->time('end_time')->comment('終了時刻');

            // 色・アイコン
            $table->string('color', 7)->default('#4F46E5')->comment('色（HEX）');
            $table->string('icon', 50)->nullable()->comment('アイコン名');

            // メモ
            $table->text('notes')->nullable()->comment('メモ');

            // 関連
            $table->foreignId('learning_path_id')
                  ->nullable()
                  ->constrained('learning_paths')
                  ->onDelete('set null')
                  ->comment('関連Learning Path');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'day', 'period'], 'idx_user_day_period');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_classes');
    }
};

