<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    /**
     * メール確認リンクを再送信
     * POST /api/email/verification-notification
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'メールアドレスは既に確認済みです',
            ], 400);
        }

        // 確認リンクを生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $request->user()->id, 'hash' => Hash::make($request->user()->email)]
        );

        // Development mode: リンクを返す
        if (env('APP_ENV') === 'local' || env('APP_DEBUG')) {
            return response()->json([
                'message' => '確認リンクが生成されました',
                'verification_url' => $verificationUrl,
            ], 200);
        }

        // Production: メール送信
        try {
            Mail::send('emails.verify', ['verificationUrl' => $verificationUrl], function ($message) use ($request) {
                $message->to($request->user()->email)
                    ->subject('メールアドレス確認');
            });

            Log::info("Verification email sent", [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            return response()->json([
                'message' => '確認メールを送信しました',
            ], 200);

        } catch (\Exception $e) {
            Log::error("Failed to send verification email", [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'メール送信に失敗しました',
                'errors' => ['email' => ['後でもう一度お試しください']],
            ], 500);
        }
    }

    /**
     * メールアドレスを確認
     * GET /api/email/verify/{id}/{hash}
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // ハッシュの検証
        if (!Hash::check($user->email, $hash)) {
            return response()->json([
                'message' => '無効な検証リンクです',
                'errors' => ['verification' => ['リンクが無効です']],
            ], 400);
        }

        // 既に確認済みかチェック
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'メールアドレスは既に確認済みです',
            ], 200);
        }

        // メールアドレスを確認済みにマーク
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            Log::info("Email verified", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        return response()->json([
            'message' => 'メールアドレスが確認されました',
        ], 200);
    }
}
