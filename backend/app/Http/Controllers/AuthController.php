<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * 登録処理
     * POST /api/register
     */
    public function register(Request $request)
    {
        // 詳細なバリデーション
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        ], [
            'name.required' => '名前は必須です',
            'name.min' => '名前は2文字以上です',
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールアドレスの形式が正しくありません',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'password.required' => 'パスワードは必須です',
            'password.min' => 'パスワードは8文字以上です',
            'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => null, // 登録時は未確認
            ]);

            // メール確認リンクを送信（本番環境の場合）
            if (env('APP_ENV') === 'production') {
                // Mail verification sẽ được呼び出す
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // セキュリティログ
            Log::info("User registered successfully", [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => '登録成功！'
            ], 201);

        } catch (\Exception $e) {
            Log::error("Registration failed", [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => '登録に失敗しました',
                'errors' => ['server' => ['サーバーエラーが発生しました']],
            ], 500);
        }
    }

    /**
     * ログイン処理
     * POST /api/login
     */
    public function login(Request $request)
    {
        // バリデーション
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールアドレスの形式が正しくありません',
            'password.required' => 'パスワードは必須です',
        ]);

        // ログイン試行回数のチェック
        $attemptKey = 'login_attempts:' . $request->ip();
        $attempts = Cache::get($attemptKey, 0);

        if ($attempts >= 5) {
            Log::warning("Login blocked due to too many attempts", [
                'email' => $request->email,
                'ip' => $request->ip(),
                'attempts' => $attempts,
            ]);

            return response()->json([
                'message' => 'ログイン試行回数が上限に達しました。10分後に再試行してください。',
                'errors' => ['login' => ['アカウントが一時的にロックされています']],
            ], 429);
        }

        // 認証試行
        if (!Auth::attempt($request->only('email', 'password'))) {
            // 試行回数をインクリメント
            Cache::put($attemptKey, $attempts + 1, now()->addMinutes(10));

            // セキュリティログ
            Log::warning("Login failed", [
                'email' => $request->email,
                'ip' => $request->ip(),
                'attempts' => $attempts + 1,
            ]);

            return response()->json([
                'message' => 'メールアドレスまたはパスワードが正しくありません',
                'errors' => ['credentials' => ['認証情報が正しくありません']],
            ], 401);
        }

        // 成功時: 試行回数をリセット
        Cache::forget($attemptKey);

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // セキュリティログ
        Log::info("User logged in successfully", [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'ログイン成功！'
        ], 200);
    }

    /**
     * ログアウト処理
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // セキュリティログ
        Log::info("User logged out", [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        // 現在のトークンを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'ログアウト成功！'
        ], 200);
    }

    /**
     * トークンをリフレッシュ
     * POST /api/refresh-token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        // 現在のトークンを削除
        $request->user()->currentAccessToken()->delete();

        // 新しいトークンを発行
        $token = $user->createToken('auth_token')->plainTextToken;

        // セキュリティログ
        Log::info("Token refreshed", [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'トークンが更新されました'
        ], 200);
    }

    /**
     * 現在のユーザー情報を取得
     * GET /api/user
     */
    public function getUser(Request $request)
    {
        $user = $request->user();

        Log::info("getUser called", [
            'user' => $user ? $user->toArray() : null
        ]);

        if (!$user) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'ユーザーが見つかりません',
                'error' => 'User not found'
            ];
            Log::info("getUser response (not found)", ['response' => $response]);
            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'data' => $user->toArray(),
            'message' => 'ユーザー情報を取得しました',
            'error' => null
        ];

        Log::info("getUser response (success)", ['response' => $response]);
        return response()->json($response, 200);
    }
}
