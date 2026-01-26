<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'http://ollama:11434/v1'),
        // 推奨: qwen2.5:3b, gemma2:2b (軽量)
        'model' => env('OPENAI_MODEL', 'qwen2.5:3b'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
        'temperature' => env('OPENAI_TEMPERATURE', 0.5),
        // Local AI用に長めのタイムアウト設定
        'timeout' => env('OPENAI_TIMEOUT', 120),
        'fallback_model' => env('OPENAI_FALLBACK_MODEL', 'qwen2.5:3b'),
        'enable_fallback' => env('OPENAI_ENABLE_FALLBACK', false),
        // キャッシュ設定
        'cache_ttl' => env('OPENAI_CACHE_TTL', 3600),
    ],

];
