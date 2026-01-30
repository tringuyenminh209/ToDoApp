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
        // リモート Ollama (docker-compose.ollama.yml 別サーバー) の場合は OLLAMA_BASE_URL を指定
        'base_url' => env('OPENAI_BASE_URL') ?: env('OLLAMA_BASE_URL', 'http://ollama:11434/v1'),
        // 推奨: qwen2.5:3b / qwen2.5:1.5b (軽量), gemma2:2b
        'model' => env('OPENAI_MODEL', 'qwen2.5:3b'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 300),
        'temperature' => env('OPENAI_TEMPERATURE', 0.5),
        // リモート Ollama は初回ロードが遅いため 300 など長めを推奨
        'timeout' => (int) env('OPENAI_TIMEOUT', 120),
        'fallback_model' => env('OPENAI_FALLBACK_MODEL', 'qwen2.5:3b'),
        'enable_fallback' => env('OPENAI_ENABLE_FALLBACK', false),
        // キャッシュ設定
        'cache_ttl' => env('OPENAI_CACHE_TTL', 3600),
    ],

];
