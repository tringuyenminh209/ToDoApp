<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AIService;
use App\Models\User;

$user = User::find(1);

$aiService = app(AIService::class);

$message = "Tạo folder JavaScript và thêm 3 code snippets về array methods";

echo "Testing parseKnowledgeCreationIntent...\n";
echo "Message: {$message}\n\n";

$result = $aiService->parseKnowledgeCreationIntent($message, [], $user);

echo "Result:\n";
print_r($result);
echo "\n";
