<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeLaravelSeeder extends Seeder
{
    /**
     * Seed Laravel cheat code data from quickref.me
     * Reference: https://quickref.me/laravel
     */
    public function run(): void
    {
        // Create Laravel Language
        $laravelLanguage = CheatCodeLanguage::create([
            'name' => 'laravel',
            'display_name' => 'Laravel',
            'slug' => 'laravel',
            'icon' => 'ic_laravel',
            'color' => '#FF2D20',
            'description' => 'Laravelは表現力豊かで進歩的なPHP Webアプリケーションフレームワークです。Laravel 8の一般的なコマンドと機能のリファレンス。',
            'category' => 'framework',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 11,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($laravelLanguage, 'はじめに', 1, 'Laravelのインストールと要件', 'getting-started');

        $this->createExample($section1, $laravelLanguage, '要件', 1,
            "# PHP version >= 7.3\n# BCMath PHP Extension\n# Ctype PHP Extension\n# Fileinfo PHP Extension\n# JSON PHP Extension\n# Mbstring PHP Extension\n# OpenSSL PHP Extension\n# PDO PHP Extension\n# Tokenizer PHP Extension\n# XML PHP Extension",
            'Laravelのシステム要件',
            null,
            'easy'
        );

        $this->createExample($section1, $laravelLanguage, 'Composerでインストール', 2,
            "$ composer create-project laravel/laravel example-app\n$ cd example-app\n$ php artisan serve",
            'Composerを使用したLaravelのインストール',
            null,
            'easy'
        );

        $this->createExample($section1, $laravelLanguage, 'Dockerでインストール', 3,
            "$ curl -s https://laravel.build/example-app | bash\n$ cd example-app\n$ ./vendor/bin/sail up",
            'Laravel Sail（Docker）を使用したインストール',
            null,
            'easy'
        );

        // Section 2: Configuration
        $section2 = $this->createSection($laravelLanguage, '設定', 2, '.envファイルと設定値の取得', 'configuration');

        $this->createExample($section2, $laravelLanguage, '.env値の取得', 1,
            "env('APP_DEBUG');\n\n// with default value\nenv('APP_DEBUG', false);",
            '.envファイルから値を取得',
            null,
            'easy'
        );

        $this->createExample($section2, $laravelLanguage, '環境の判定', 2,
            "use Illuminate\\Support\\Facades\\App;\n\n\$environment = App::environment();",
            '現在の環境を判定',
            null,
            'easy'
        );

        $this->createExample($section2, $laravelLanguage, '設定値の取得', 3,
            "// config/app.php --> ['timezone' => '']\n\$value = config('app.timezone');\n\n// Retrieve a default value if the configuration value does not exist...\n\$value = config('app.timezone', 'Asia/Seoul');",
            'ドット記法で設定値を取得',
            null,
            'easy'
        );

        $this->createExample($section2, $laravelLanguage, '設定値の設定', 4,
            "config(['app.timezone' => 'America/Chicago']);",
            '実行時に設定値を設定',
            null,
            'easy'
        );

        $this->createExample($section2, $laravelLanguage, 'デバッグモード', 5,
            "// .env file\nAPP_ENV=local\nAPP_DEBUG=true\n\n// Production\nAPP_ENV=production\nAPP_DEBUG=false",
            'デバッグモードのオン/オフ',
            null,
            'easy'
        );

        $this->createExample($section2, $laravelLanguage, 'メンテナンスモード', 6,
            "php artisan down\n\n// Disable maintenance mode\nphp artisan up\n\n// Bypass Maintenance Mode\nphp artisan down --secret=\"1630542a-246b-4b66-afa1-dd72a4c43515\"",
            'メンテナンスモードの設定',
            null,
            'easy'
        );

        // Section 3: Routing
        $section3 = $this->createSection($laravelLanguage, 'ルーティング', 3, 'HTTPルートとルートパラメータ', 'routing');

        $this->createExample($section3, $laravelLanguage, 'HTTPメソッド', 1,
            "Route::get(\$uri, \$callback);\nRoute::post(\$uri, \$callback);\nRoute::put(\$uri, \$callback);\nRoute::patch(\$uri, \$callback);\nRoute::delete(\$uri, \$callback);\nRoute::options(\$uri, \$callback);",
            '基本的なHTTPルートメソッド',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, '複数HTTPメソッド', 2,
            "Route::match(['get', 'post'], '/', function () {\n    //\n});\n\nRoute::any('/', function () {\n    //\n});",
            '複数のHTTPメソッドに対応するルート',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, '基本的な定義', 3,
            "use Illuminate\\Support\\Facades\\Route;\n\n// closure\nRoute::get('/greeting', function () {\n    return 'Hello World';\n});\n\n// controller action\nRoute::get(\n    '/user/profile',\n    [UserProfileController::class, 'show']\n);",
            'クロージャとコントローラーアクション',
            "Hello World",
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, '依存性注入', 4,
            "use Illuminate\\Http\\Request;\n\nRoute::get('/users', function (Request \$request) {\n    // ...\n});",
            'ルートでの依存性注入',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'ビュールート', 5,
            "// Argument 1: URI, Argument 2: view name\nRoute::view('/welcome', 'welcome');\n\n// with data\nRoute::view('/welcome', 'welcome', ['name' => 'Taylor']);",
            'ビューを返すルート',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'ルートモデルバインディング', 6,
            "use App\\Models\\User;\n\nRoute::get('/users/{user}', function (User \$user) {\n    return \$user->email;\n});\n\n// /user/1 --> User::where('id', '=', 1);",
            '暗黙的なルートモデルバインディング',
            null,
            'medium'
        );

        $this->createExample($section3, $laravelLanguage, 'カスタム解決カラム', 7,
            "use App\\Models\\Post;\n\nRoute::get('/posts/{post:slug}', function (Post \$post) {\n    return \$post;\n});\n\n// /posts/my-post --> Post::where('slug', '=', 'my-post');",
            'スラッグを使用したモデル解決',
            null,
            'medium'
        );

        $this->createExample($section3, $laravelLanguage, 'ルートパラメータ', 8,
            "Route::get('/user/{id}', function (\$id) {\n    return 'User '.\$id;\n});",
            '必須パラメータ',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'オプショナルパラメータ', 9,
            "Route::get('/user/{name?}', function (\$name = null) {\n    return \$name;\n});\n\nRoute::get('/user/{name?}', function (\$name = 'John') {\n    return \$name;\n});",
            'オプショナルなルートパラメータ',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'リダイレクトルート', 10,
            "Route::redirect('/here', '/there');\n\n// Set the status code\nRoute::redirect('/here', '/there', 301);\n\n// Permanent 301 redirect\nRoute::permanentRedirect('/here', '/there');",
            'HTTPリダイレクトルート',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, '正規表現制約', 11,
            "Route::get('/user/{name}', function (\$name) {\n    //\n})->where('name', '[A-Za-z]+');\n\nRoute::get('/user/{id}', function (\$id) {\n    //\n})->where('id', '[0-9]+');",
            'ルートパラメータの正規表現制約',
            null,
            'medium'
        );

        $this->createExample($section3, $laravelLanguage, '名前付きルート', 12,
            "Route::get('/user/profile', function () {\n    //\n})->name('profile');",
            'ルートに名前を付ける',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'フォールバックルート', 13,
            "Route::fallback(function () {\n    //\n});",
            '他のルートが一致しない場合に実行されるルート',
            null,
            'easy'
        );

        $this->createExample($section3, $laravelLanguage, 'ルートグループ - ミドルウェア', 14,
            "Route::middleware(['first', 'second'])->group(function () {\n    Route::get('/', function () {\n        // Uses first & second middleware...\n    });\n});",
            'ミドルウェアを共有するルートグループ',
            null,
            'medium'
        );

        $this->createExample($section3, $laravelLanguage, 'ルートグループ - URIプレフィックス', 15,
            "Route::prefix('admin')->group(function () {\n    Route::get('/users', function () {\n        // Matches The \"/admin/users\" URL\n    });\n});",
            'URIプレフィックスを共有するルートグループ',
            null,
            'medium'
        );

        $this->createExample($section3, $laravelLanguage, '現在のルートへのアクセス', 16,
            "use Illuminate\\Support\\Facades\\Route;\n\n// Illuminate\\Routing\\Route\n\$route = Route::current();\n\n// string\n\$name = Route::currentRouteName();\n\n// string\n\$action = Route::currentRouteAction();",
            '現在のルート情報の取得',
            null,
            'medium'
        );

        // Section 4: Helpers
        $section4 = $this->createSection($laravelLanguage, 'ヘルパー', 4, '便利なヘルパー関数', 'helpers');

        $this->createExample($section4, $laravelLanguage, 'route() - 名前付きルート', 1,
            "\$url = route('profile');",
            '名前付きルートのURLを生成',
            null,
            'easy'
        );

        $this->createExample($section4, $laravelLanguage, 'route() - パラメータ付き', 2,
            "// Route::get('/user/{id}/profile', /*...*/ )->name('profile);\n\n\$url = route('profile', ['id' => 1]);\n\n// /user/1/profile/",
            'パラメータ付きルートURLの生成',
            "/user/1/profile/",
            'easy'
        );

        $this->createExample($section4, $laravelLanguage, 'route() - クエリ文字列', 3,
            "\$url = route('profile', ['id' => 1, 'photos' => 'yes']);\n\n// /user/1/profile?photos=yes",
            'クエリ文字列を含むルートURL',
            "/user/1/profile?photos=yes",
            'easy'
        );

        $this->createExample($section4, $laravelLanguage, 'url() - URL生成', 4,
            "\$url = url('/user/profile');\n\n// https://example.com/user/profile",
            '絶対URLの生成',
            null,
            'easy'
        );

        $this->createExample($section4, $laravelLanguage, 'asset() - アセットURL', 5,
            "\$url = asset('img/photo.jpg');\n\n// https://example.com/img/photo.jpg",
            'アセットファイルのURLを生成',
            null,
            'easy'
        );

        $this->createExample($section4, $laravelLanguage, 'redirect() - リダイレクト', 6,
            "return redirect('/home');\n\nreturn redirect()->route('profile');\n\nreturn redirect()->back();",
            'リダイレクトレスポンスの生成',
            null,
            'easy'
        );

        // Section 5: Validation
        $section5 = $this->createSection($laravelLanguage, 'バリデーション', 5, 'フォームバリデーション', 'validation');

        $this->createExample($section5, $laravelLanguage, '基本的なバリデーション', 1,
            "\$validatedData = \$request->validate([\n    'title' => 'required|max:255',\n    'body' => 'required',\n]);",
            'リクエストデータのバリデーション',
            null,
            'easy'
        );

        $this->createExample($section5, $laravelLanguage, 'バリデーションルール', 2,
            "'email' => 'required|email|unique:users'\n'age' => 'required|integer|min:18|max:65'\n'password' => 'required|confirmed|min:8'",
            '一般的なバリデーションルール',
            null,
            'easy'
        );

        $this->createExample($section5, $laravelLanguage, 'パスワードバリデーション', 3,
            "\$validatedData = \$request->validate([\n    'password' => ['required', 'confirmed', Password::min(8)],\n]);\n\n// Require at least 8 characters...\nPassword::min(8)\n\n// Require at least one letter...\nPassword::min(8)->letters()\n\n// Require at least one uppercase and one lowercase letter...\nPassword::min(8)->mixedCase()\n\n// Require at least one number...\nPassword::min(8)->numbers()\n\n// Require at least one symbol...\nPassword::min(8)->symbols()",
            'パスワードの複雑さ要件',
            null,
            'medium'
        );

        $this->createExample($section5, $laravelLanguage, 'バリデーションエラーの表示', 4,
            "@if (\$errors->any())\n    <div class=\"alert alert-danger\">\n        <ul>\n            @foreach (\$errors->all() as \$error)\n                <li>{{ \$error }}</li>\n            @endforeach\n        </ul>\n    </div>\n@endif",
            'Bladeテンプレートでのエラー表示',
            null,
            'easy'
        );

        $this->createExample($section5, $laravelLanguage, 'オプショナルフィールド', 5,
            "\$request->validate([\n    'title' => 'required|unique:posts|max:255',\n    'body' => 'required',\n    'publish_at' => 'nullable|date',\n]);",
            'nullableフィールドのバリデーション',
            null,
            'easy'
        );

        $this->createExample($section5, $laravelLanguage, 'バリデーション済みデータの取得', 6,
            "\$validated = \$request->validated();\n\n// Or with safe()\n\$validated = \$request->safe()->only(['name', 'email']);\n\n\$validated = \$request->safe()->except(['name', 'email']);\n\n\$validated = \$request->safe()->all();",
            'バリデーション済みデータの取得',
            null,
            'easy'
        );

        // Section 6: Session
        $section6 = $this->createSection($laravelLanguage, 'セッション', 6, 'セッション管理', 'session');

        $this->createExample($section6, $laravelLanguage, 'セッションの存在確認', 1,
            "if (\$request->session()->has('users')) {\n    //\n}\n\nif (\$request->session()->exists('users')) {\n    //\n}\n\nif (\$request->session()->missing('users')) {\n    //\n}",
            'セッション値の存在確認',
            null,
            'easy'
        );

        $this->createExample($section6, $laravelLanguage, 'セッション値の取得', 2,
            "\$value = \$request->session()->get('key');\n\n// Pass a default value\n\$value = \$request->session()->get('key', 'default');",
            'セッションから値を取得',
            null,
            'easy'
        );

        $this->createExample($section6, $laravelLanguage, 'session()ヘルパー', 3,
            "Route::get('/home', function () {\n    // Retrieve a piece of data from the session...\n    \$value = session('key');\n\n    // Specifying a default value...\n    \$value = session('key', 'default');\n\n    // Store a piece of data in the session...\n    session(['key' => 'value']);\n});",
            'グローバルsession()ヘルパーの使用',
            null,
            'easy'
        );

        $this->createExample($section6, $laravelLanguage, '全セッションデータの取得', 4,
            "\$data = \$request->session()->all();",
            'すべてのセッションデータを取得',
            null,
            'easy'
        );

        $this->createExample($section6, $laravelLanguage, '取得と削除', 5,
            "\$value = \$request->session()->pull('key', 'default');",
            'セッションから値を取得して削除',
            null,
            'easy'
        );

        $this->createExample($section6, $laravelLanguage, 'セッション値の保存', 6,
            "\$request->session()->put('key', 'value');\n\n// Via the global \"session\" helper\nsession(['key' => 'value']);\n\n// Push a new value onto a session value that is an array\n\$request->session()->push('user.teams', 'developers');",
            'セッションに値を保存',
            null,
            'easy'
        );

        // Section 7: Logging
        $section7 = $this->createSection($laravelLanguage, 'ロギング', 7, 'ログレベルの使用', 'logging');

        $this->createExample($section7, $laravelLanguage, 'ログレベル', 1,
            "use Illuminate\\Support\\Facades\\Log;\n\nLog::emergency(\$message);\nLog::alert(\$message);\nLog::critical(\$message);\nLog::error(\$message);\nLog::warning(\$message);\nLog::notice(\$message);\nLog::info(\$message);\nLog::debug(\$message);",
            'すべてのログレベルの使用',
            null,
            'easy'
        );

        $this->createExample($section7, $laravelLanguage, 'コンテキスト情報', 2,
            "use Illuminate\\Support\\Facades\\Log;\n\nLog::info('User failed to login.', ['id' => \$user->id]);",
            'コンテキスト情報を含むログ',
            null,
            'easy'
        );

        // Section 8: Deployment
        $section8 = $this->createSection($laravelLanguage, 'デプロイ', 8, '本番環境へのデプロイ', 'deployment');

        $this->createExample($section8, $laravelLanguage, 'Composerオートローダーの最適化', 1,
            "composer install --optimize-autoloader --no-dev",
            'Composerのオートローダーマップを最適化',
            null,
            'easy'
        );

        $this->createExample($section8, $laravelLanguage, '設定のキャッシュ', 2,
            "php artisan config:cache",
            '設定ファイルのキャッシュ',
            null,
            'easy'
        );

        $this->createExample($section8, $laravelLanguage, 'ルートのキャッシュ', 3,
            "php artisan route:cache",
            'ルートファイルのキャッシュ',
            null,
            'easy'
        );

        $this->createExample($section8, $laravelLanguage, 'ビューのキャッシュ', 4,
            "php artisan view:cache",
            'ビューファイルのキャッシュ',
            null,
            'easy'
        );

        // Update language counts
        $this->updateLanguageCounts($laravelLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null, ?string $slug = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug ?? Str::slug($title),
            'description' => $description,
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function createExample(
        CheatCodeSection $section,
        CheatCodeLanguage $language,
        string $title,
        int $sortOrder,
        string $code,
        ?string $description = null,
        ?string $output = null,
        string $difficulty = 'easy'
    ): CodeExample {
        return CodeExample::create([
            'section_id' => $section->id,
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'code' => $code,
            'description' => $description,
            'output' => $output,
            'difficulty' => $difficulty,
            'tags' => $this->generateTags($title, $description),
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function generateTags(string $title, ?string $description): array
    {
        $tags = [];
        $titleLower = strtolower($title);
        $descLower = $description ? strtolower($description) : '';

        // Add tags based on title and description
        if (str_contains($titleLower, 'route') || str_contains($descLower, 'ルート') || str_contains($descLower, 'routing')) {
            $tags[] = 'routing';
        }
        if (str_contains($titleLower, 'validation') || str_contains($titleLower, 'validate') || str_contains($descLower, 'バリデーション')) {
            $tags[] = 'validation';
        }
        if (str_contains($titleLower, 'session') || str_contains($descLower, 'セッション')) {
            $tags[] = 'session';
        }
        if (str_contains($titleLower, 'log') || str_contains($descLower, 'ログ')) {
            $tags[] = 'logging';
        }
        if (str_contains($titleLower, 'middleware') || str_contains($descLower, 'ミドルウェア')) {
            $tags[] = 'middleware';
        }
        if (str_contains($titleLower, 'model') || str_contains($titleLower, 'binding') || str_contains($descLower, 'モデル')) {
            $tags[] = 'model-binding';
        }
        if (str_contains($titleLower, 'controller') || str_contains($descLower, 'コントローラー')) {
            $tags[] = 'controller';
        }
        if (str_contains($titleLower, 'helper') || str_contains($titleLower, 'ヘルパー')) {
            $tags[] = 'helper';
        }
        if (str_contains($titleLower, 'config') || str_contains($titleLower, '設定') || str_contains($descLower, 'configuration')) {
            $tags[] = 'configuration';
        }
        if (str_contains($titleLower, 'deploy') || str_contains($titleLower, 'デプロイ') || str_contains($descLower, 'deployment')) {
            $tags[] = 'deployment';
        }
        if (str_contains($titleLower, 'password') || str_contains($descLower, 'パスワード')) {
            $tags[] = 'password';
        }
        if (str_contains($titleLower, 'redirect') || str_contains($descLower, 'リダイレクト')) {
            $tags[] = 'redirect';
        }

        // Add basic tags
        $tags[] = 'laravel';
        $tags[] = 'php';
        $tags[] = 'framework';
        $tags[] = 'basics';

        return array_unique($tags);
    }

    private function updateLanguageCounts(CheatCodeLanguage $language): void
    {
        $language->update([
            'sections_count' => $language->sections()->count(),
            'examples_count' => $language->codeExamples()->count(),
            'exercises_count' => $language->exercises()->count(),
        ]);

        // Update section counts
        foreach ($language->sections as $section) {
            $section->update([
                'examples_count' => $section->examples()->count(),
            ]);
        }
    }
}

