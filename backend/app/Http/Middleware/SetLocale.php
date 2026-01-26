<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware SetLocale
 * 
 * Xác định ngôn ngữ cho request
 * Ưu tiên: query param > header > session > default
 */
class SetLocale
{
    /**
     * Các ngôn ngữ được hỗ trợ
     */
    protected array $supportedLocales = ['ja', 'en', 'vi'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        if ($this->isValidLocale($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Xác định ngôn ngữ từ request
     */
    protected function determineLocale(Request $request): string
    {
        // 1. Query parameter ?lang=en
        if ($request->has('lang')) {
            return $request->query('lang');
        }

        // 2. Custom header X-Locale
        if ($request->hasHeader('X-Locale')) {
            return $request->header('X-Locale');
        }

        // 3. Accept-Language header (lấy phần đầu tiên)
        if ($request->hasHeader('Accept-Language')) {
            $acceptLanguage = $request->header('Accept-Language');
            // Parse Accept-Language: ja,en-US;q=0.9,en;q=0.8
            $locale = explode(',', $acceptLanguage)[0];
            $locale = explode('-', $locale)[0]; // en-US -> en
            return trim($locale);
        }

        // 4. Session
        if (session()->has('locale')) {
            return session('locale');
        }

        // 5. Default
        return config('app.locale', 'ja');
    }

    /**
     * Kiểm tra ngôn ngữ có hợp lệ không
     */
    protected function isValidLocale(string $locale): bool
    {
        return in_array($locale, $this->supportedLocales);
    }
}
