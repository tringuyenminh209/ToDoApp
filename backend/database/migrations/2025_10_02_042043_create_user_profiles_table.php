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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Onboarding data
            $table->enum('goal_type', ['learning', 'work', 'health'])->nullable()->comment('ユーザーの主な目標');
            $table->enum('preferred_time', ['morning', 'morning_late', 'afternoon', 'evening'])->nullable()->comment('好みの作業時間帯');

            //settings
            $table->boolean('notification_enabled')->default(true);
            $table->boolean('onboarding_completed')->default(false);

            $table->timestamps();

            //indexes
            $table->unique('user_id');
            $table->index('onboarding_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
