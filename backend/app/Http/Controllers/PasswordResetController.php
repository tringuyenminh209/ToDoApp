<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * パスワードリセットトークンを送信
     * POST /api/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールアドレスの形式が正しくありません',
        ]);

        // ユーザーが存在するかチェック
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // セキュリティのため、ユーザーが存在しない場合でも同じメッセージを返す
            return response()->json([
                'message' => 'パスワードリセットリンクを送信しました'
            ], 200);
        }

        // 既存のトークンを削除
        DB::table('password_resets')->where('email', $request->email)->delete();

        // 6桁のOTPトークンを生成
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // トークンをデータベースに保存（60分間有効）
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // セキュリティログ
        Log::info("Password reset token requested", [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        // Development mode: トークンを直接返す
        if (env('APP_ENV') === 'local' || env('APP_DEBUG')) {
            return response()->json([
                'message' => 'パスワードリセットトークンが発行されました',
                'token' => $token, // 開発環境でのみ
                'expires_in' => 60,
            ], 200);
        }

        // Production: メール送信
        try {
            Mail::send('emails.password-reset', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('パスワードリセット');
            });
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email", [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'パスワードリセットリンクを送信しました',
            'expires_in' => 60,
        ], 200);
    }

    /**
     * パスワードをリセット
     * POST /api/reset-password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'token' => 'required|string|min:6|max:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールアドレスの形式が正しくありません',
            'token.required' => 'トークンは必須です',
            'token.min' => 'トークンは6桁です',
            'password.required' => 'パスワードは必須です',
            'password.min' => 'パスワードは8文字以上です',
            'password.confirmed' => 'パスワード確認が一致しません',
        ]);

        // トークンを検証
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'トークンが無効です',
                'errors' => ['token' => ['トークンが見つかりません']],
            ], 422);
        }

        // トークンが60分以内かチェック
        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();

            return response()->json([
                'message' => 'トークンの有効期限が切れています',
                'errors' => ['token' => ['トークンの有効期限が切れています']],
            ], 422);
        }

        // トークンを検証
        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'トークンが無効です',
                'errors' => ['token' => ['トークンが正しくありません']],
            ], 422);
        }

        // パスワードを更新
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'ユーザーが見つかりません',
                'errors' => ['email' => ['ユーザーが見つかりません']],
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // トークンを削除
        DB::table('password_resets')->where('email', $request->email)->delete();

        // セキュリティログ
        Log::info("Password reset successful", [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'パスワードがリセットされました',
        ], 200);
    }
}

