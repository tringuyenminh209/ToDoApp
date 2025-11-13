<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get user settings
     * GET /api/settings
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get or create user settings
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                UserSetting::getDefaultSettings()
            );

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => '設定を取得しました'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '設定の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user settings
     * PUT /api/settings
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validation rules
            $validator = Validator::make($request->all(), [
                // Appearance
                'theme' => 'nullable|in:light,dark,auto',

                // Pomodoro Settings
                'default_focus_minutes' => 'nullable|integer|min:1|max:120',
                'pomodoro_duration' => 'nullable|integer|min:1|max:120',
                'break_minutes' => 'nullable|integer|min:1|max:60',
                'long_break_minutes' => 'nullable|integer|min:1|max:60',
                'auto_start_break' => 'nullable|boolean',
                'block_notifications' => 'nullable|boolean',
                'background_sound' => 'nullable|boolean',

                // Daily Goals
                'daily_target_tasks' => 'nullable|integer|min:1|max:100',

                // Notifications
                'notification_enabled' => 'nullable|boolean',
                'push_notifications' => 'nullable|boolean',
                'daily_reminders' => 'nullable|boolean',
                'goal_reminders' => 'nullable|boolean',
                'reminder_times' => 'nullable|array',
                'reminder_times.*' => 'string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',

                // Localization
                'language' => 'nullable|in:vi,en,ja',
                'timezone' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'バリデーションエラー',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get or create user settings
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                UserSetting::getDefaultSettings()
            );

            // Update only provided fields
            $settings->fill($request->only([
                'theme',
                'default_focus_minutes',
                'pomodoro_duration',
                'break_minutes',
                'long_break_minutes',
                'auto_start_break',
                'block_notifications',
                'background_sound',
                'daily_target_tasks',
                'notification_enabled',
                'push_notifications',
                'daily_reminders',
                'goal_reminders',
                'reminder_times',
                'language',
                'timezone',
            ]));

            $settings->save();

            Log::info('User settings updated', [
                'user_id' => $user->id,
                'settings' => $settings->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => '設定を更新しました'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '設定の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user settings to default
     * POST /api/settings/reset
     */
    public function reset(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get or create user settings
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                UserSetting::getDefaultSettings()
            );

            // Reset to default values
            $settings->fill(UserSetting::getDefaultSettings());
            $settings->save();

            Log::info('User settings reset to default', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => '設定をデフォルトに戻しました'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to reset settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '設定のリセットに失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update specific setting
     * PATCH /api/settings/{key}
     */
    public function updateSetting(Request $request, string $key): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate key
            $allowedKeys = [
                'theme', 'default_focus_minutes', 'pomodoro_duration',
                'break_minutes', 'long_break_minutes', 'auto_start_break',
                'block_notifications', 'background_sound', 'daily_target_tasks',
                'notification_enabled', 'push_notifications', 'daily_reminders',
                'goal_reminders', 'reminder_times', 'language', 'timezone',
            ];

            if (!in_array($key, $allowedKeys)) {
                return response()->json([
                    'success' => false,
                    'message' => '無効な設定キーです'
                ], 400);
            }

            // Get or create user settings
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                UserSetting::getDefaultSettings()
            );

            // Update the specific setting
            $settings->{$key} = $request->input('value');
            $settings->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'value' => $settings->{$key}
                ],
                'message' => '設定を更新しました'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to update setting {$key}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '設定の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
