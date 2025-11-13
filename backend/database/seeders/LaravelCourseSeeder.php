<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class LaravelCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Laravel基礎演習 - Webサイト制作（サーバサイドⅡ）の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Laravel基礎演習',
            'description' => '初心者向けLaravelフレームワーク基礎コース。ルーティング、Bladeテンプレート、Eloquent ORM、API開発まで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 140,
            'tags' => ['laravel', 'php', 'framework', 'web', 'eloquent', 'api'],
            'icon' => 'ic_laravel',
            'color' => '#FF2D20',
            'is_featured' => true,
        ]);

        // Milestone 1: 環境構築とLaravel基礎 (第0回～第1回)
        $milestone1 = $template->milestones()->create([
            'title' => '環境構築とLaravel基礎',
            'description' => '開発環境のセットアップからLaravelの基本構造、ルーティング、コントローラまで学習',
            'sort_order' => 1,
            'estimated_hours' => 10,
            'deliverables' => [
                '開発環境をセットアップ完了',
                'Laravelプロジェクトを作成',
                'ルーティングとコントローラを理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第0回：環境構築',
                'description' => 'Composer、Node.js、Laravelのインストールとプロジェクト作成',
                'sort_order' => 1,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    '環境構築資料',
                    'Composer-Setup.exe',
                    'node-v22.14.0-x64.msi'
                ],
                'subtasks' => [
                    ['title' => 'Composerをインストール', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'Node.jsをインストール', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'Laravelプロジェクトを作成', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '開発サーバーを起動', 'estimated_minutes' => 20, 'sort_order' => 4],
                    ['title' => 'プロジェクト構造を理解', 'estimated_minutes' => 60, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Laravelプロジェクトの作成',
                        'content' => "# ComposerでLaravelをインストール\ncomposer create-project laravel/laravel my-app\n\n# プロジェクトディレクトリに移動\ncd my-app\n\n# 開発サーバーを起動\nphp artisan serve\n\n# ブラウザで http://localhost:8000 にアクセス",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Laravelのディレクトリ構造',
                        'content' => "# Laravelのディレクトリ構造\n\n- `app/`: アプリケーションのコアコード\n- `routes/`: ルート定義\n- `resources/views/`: Bladeテンプレート\n- `database/`: マイグレーション、シーダー\n- `config/`: 設定ファイル\n- `public/`: 公開ディレクトリ（エントリーポイント）",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Composerとは？',
                        'content' => "# Composer\n\n**Composer**は、PHPの依存関係管理ツールです。Node.jsのnpmやPythonのpipに相当します。\n\n## 主な機能\n- パッケージのインストール\n- 依存関係の解決\n- オートローディング\n- プロジェクトの初期化\n\n## 重要なファイル\n- **composer.json**: プロジェクトの依存関係を定義\n- **composer.lock**: インストールされた正確なバージョンを記録\n- **vendor/**: インストールされたパッケージが格納\n\n## よく使うコマンド\n```bash\ncomposer install      # composer.lockに基づいてインストール\ncomposer update       # 最新バージョンに更新\ncomposer require xxx  # パッケージを追加\ncomposer dump-autoload # オートローダーを再生成\n```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => '詳細なディレクトリ構造',
                        'content' => "# Laravelの詳細なディレクトリ構造\n\n## app/ - アプリケーションコア\n- **Http/Controllers/**: コントローラ\n- **Http/Middleware/**: ミドルウェア\n- **Http/Requests/**: フォームリクエスト\n- **Models/**: Eloquentモデル\n- **Providers/**: サービスプロバイダー\n\n## routes/ - ルーティング\n- **web.php**: Webルート（セッション、CSRF保護あり）\n- **api.php**: APIルート（ステートレス）\n- **console.php**: Artisanコマンド\n- **channels.php**: ブロードキャストチャンネル\n\n## resources/ - リソース\n- **views/**: Bladeテンプレート\n- **css/**: CSSファイル\n- **js/**: JavaScriptファイル\n\n## database/ - データベース\n- **migrations/**: マイグレーションファイル\n- **seeders/**: シーダー\n- **factories/**: モデルファクトリ\n\n## config/ - 設定\n各種設定ファイル（app.php, database.php, など）\n\n## storage/ - ストレージ\n- **app/**: アプリケーションが生成するファイル\n- **framework/**: フレームワークが使用\n- **logs/**: ログファイル\n\n## public/ - 公開ディレクトリ\n- **index.php**: エントリーポイント\n- CSS、JavaScript、画像などの公開ファイル",
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '環境設定（.env）',
                        'content' => "# .env ファイルの設定例\n\nAPP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=base64:xxx... # php artisan key:generate で生成\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\n# データベース設定\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=your_database\nDB_USERNAME=your_username\nDB_PASSWORD=your_password\n\n# メール設定\nMAIL_MAILER=smtp\nMAIL_HOST=smtp.mailtrap.io\nMAIL_PORT=2525\n\n# セッション設定\nSESSION_DRIVER=file\n\n# キャッシュ設定\nCACHE_DRIVER=file",
                        'code_language' => 'bash',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'よく使うArtisanコマンド',
                        'content' => "# プロジェクト管理\nphp artisan serve              # 開発サーバー起動\nphp artisan key:generate       # アプリケーションキー生成\nphp artisan config:cache       # 設定をキャッシュ\nphp artisan config:clear       # 設定キャッシュをクリア\nphp artisan cache:clear        # キャッシュをクリア\n\n# データベース\nphp artisan migrate            # マイグレーション実行\nphp artisan migrate:rollback   # ロールバック\nphp artisan migrate:fresh      # すべて削除して再マイグレーション\nphp artisan db:seed            # シーダー実行\n\n# コード生成\nphp artisan make:controller UserController  # コントローラ作成\nphp artisan make:model User                  # モデル作成\nphp artisan make:migration create_users_table # マイグレーション作成\nphp artisan make:seeder UserSeeder          # シーダー作成\nphp artisan make:request StoreUserRequest   # リクエスト作成\nphp artisan make:middleware CheckAge        # ミドルウェア作成\n\n# その他\nphp artisan route:list         # ルート一覧表示\nphp artisan tinker             # REPLを起動",
                        'code_language' => 'bash',
                        'sort_order' => 6
                    },
                    [
                        'type' => 'note',
                        'title' => 'トラブルシューティング',
                        'content' => "# よくある問題と解決方法\n\n## 1. Composerのインストールエラー\n- PHPのバージョンを確認（Laravel 10は PHP 8.1以上が必要）\n- `php -v` でバージョン確認\n\n## 2. パーミッションエラー\n```bash\nchmod -R 775 storage\nchmod -R 775 bootstrap/cache\n```\n\n## 3. APP_KEYが設定されていない\n```bash\nphp artisan key:generate\n```\n\n## 4. データベース接続エラー\n- .envファイルのDB設定を確認\n- データベースが起動しているか確認\n- 接続情報が正しいか確認\n\n## 5. キャッシュの問題\n```bash\nphp artisan cache:clear\nphp artisan config:clear\nphp artisan route:clear\nphp artisan view:clear\ncomposer dump-autoload\n```\n\n## 6. ポート8000が使用中\n```bash\nphp artisan serve --port=8001\n```",
                        'sort_order' => 7
                    },
                ],
            ],
            [
                'title' => '第1回：Laravelの説明・ルーティングとコントローラ',
                'description' => 'Laravelの基本概念、ルーティング、コントローラの作成と使い方',
                'sort_order' => 2,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'Laravel・ルーティングとコントローラ資料',
                    'kadai01は講義のハンズオンです（別資料なし）'
                ],
                'subtasks' => [
                    ['title' => 'Laravelの基本概念を理解', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'ルーティングの基本（routes/web.php）', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'コントローラの作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'コントローラとルートの連携', 'estimated_minutes' => 60, 'sort_order' => 4],
                    ['title' => 'ハンズオン課題を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '基本的なルーティング',
                        'content' => "<?php\n// routes/web.php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// 基本的なルート\nRoute::get('/', function () {\n    return view('welcome');\n});\n\n// パラメータ付きルート\nRoute::get('/user/{id}', function (\$id) {\n    return 'User ID: ' . \$id;\n});\n\n// コントローラを使ったルート\nRoute::get('/posts', [PostController::class, 'index']);\nRoute::get('/posts/{id}', [PostController::class, 'show']);",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'コントローラの作成',
                        'content' => "<?php\n// app/Http/Controllers/PostController.php\n\nnamespace App\\Http\\Controllers;\n\nuse Illuminate\\Http\\Request;\n\nclass PostController extends Controller\n{\n    public function index()\n    {\n        return view('posts.index');\n    }\n    \n    public function show(\$id)\n    {\n        return view('posts.show', ['id' => \$id]);\n    }\n}\n\n// コントローラ作成コマンド\n// php artisan make:controller PostController",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'MVCアーキテクチャ',
                        'content' => "# MVCアーキテクチャ（Model-View-Controller）\n\nLaravelはMVCアーキテクチャを採用しています。\n\n## Model（モデル）\n- データベースとのやり取りを担当\n- ビジネスロジックを含む\n- Eloquent ORMを使用\n- 場所: `app/Models/`\n\n## View（ビュー）\n- ユーザーに表示される画面\n- HTMLを生成\n- Bladeテンプレートエンジンを使用\n- 場所: `resources/views/`\n\n## Controller（コントローラ）\n- リクエストを処理\n- ModelとViewを橋渡し\n- ビジネスロジックを調整\n- 場所: `app/Http/Controllers/`\n\n## フロー\n1. **リクエスト** → ルーティング\n2. **ルーティング** → コントローラ\n3. **コントローラ** → モデルでデータ取得\n4. **コントローラ** → ビューにデータを渡す\n5. **ビュー** → HTMLを生成\n6. **レスポンス** → ユーザーに返す",
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'ルーティングの詳細',
                        'content' => "<?php\n// routes/web.php\n\n// HTTPメソッド\nRoute::get('/posts', [PostController::class, 'index']);\nRoute::post('/posts', [PostController::class, 'store']);\nRoute::put('/posts/{id}', [PostController::class, 'update']);\nRoute::delete('/posts/{id}', [PostController::class, 'destroy']);\n\n// Resourceルート（RESTful）\nRoute::resource('posts', PostController::class);\n// 上記は以下と同等：\n// GET    /posts          index\n// GET    /posts/create   create\n// POST   /posts          store\n// GET    /posts/{id}     show\n// GET    /posts/{id}/edit edit\n// PUT    /posts/{id}     update\n// DELETE /posts/{id}     destroy\n\n// ルートグループ\nRoute::prefix('admin')->group(function () {\n    Route::get('/users', [AdminController::class, 'users']);\n    Route::get('/posts', [AdminController::class, 'posts']);\n});\n\n// ミドルウェア\nRoute::middleware(['auth'])->group(function () {\n    Route::get('/dashboard', [DashboardController::class, 'index']);\n});\n\n// ルート名\nRoute::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');\n// Bladeで使用: route('posts.show', ['id' => 1])  // /posts/1",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'コントローラの詳細パターン',
                        'content' => "<?php\n// app/Http/Controllers/PostController.php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Models\\Post;\nuse Illuminate\\Http\\Request;\n\nclass PostController extends Controller\n{\n    // 一覧表示\n    public function index()\n    {\n        \$posts = Post::all();\n        return view('posts.index', compact('posts'));\n    }\n    \n    // 作成フォーム表示\n    public function create()\n    {\n        return view('posts.create');\n    }\n    \n    // データ保存\n    public function store(Request \$request)\n    {\n        \$validated = \$request->validate([\n            'title' => 'required|max:255',\n            'content' => 'required',\n        ]);\n        \n        Post::create(\$validated);\n        \n        return redirect()->route('posts.index')\n            ->with('success', '投稿を作成しました');\n    }\n    \n    // 詳細表示\n    public function show(\$id)\n    {\n        \$post = Post::findOrFail(\$id);\n        return view('posts.show', compact('post'));\n    }\n    \n    // 編集フォーム表示\n    public function edit(\$id)\n    {\n        \$post = Post::findOrFail(\$id);\n        return view('posts.edit', compact('post'));\n    }\n    \n    // データ更新\n    public function update(Request \$request, \$id)\n    {\n        \$validated = \$request->validate([\n            'title' => 'required|max:255',\n            'content' => 'required',\n        ]);\n        \n        \$post = Post::findOrFail(\$id);\n        \$post->update(\$validated);\n        \n        return redirect()->route('posts.show', \$id)\n            ->with('success', '投稿を更新しました');\n    }\n    \n    // データ削除\n    public function destroy(\$id)\n    {\n        \$post = Post::findOrFail(\$id);\n        \$post->delete();\n        \n        return redirect()->route('posts.index')\n            ->with('success', '投稿を削除しました');\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'note',
                        'title' => 'コントローラ作成のオプション',
                        'content' => "# コントローラ作成コマンド\n\n## 基本的なコントローラ\n```bash\nphp artisan make:controller PostController\n```\n\n## リソースコントローラ（CRUD メソッド付き）\n```bash\nphp artisan make:controller PostController --resource\n```\n以下のメソッドが自動生成されます：\n- index()\n- create()\n- store()\n- show()\n- edit()\n- update()\n- destroy()\n\n## モデルと同時に作成\n```bash\nphp artisan make:controller PostController --resource --model=Post\n```\n\n## APIリソースコントローラ（create, edit なし）\n```bash\nphp artisan make:controller Api/PostController --api\n```\n\n## 単一アクションコントローラ\n```bash\nphp artisan make:controller ShowProfileController --invokable\n```\n`__invoke()` メソッドのみを持つコントローラ",
                        'sort_order' => 6
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'ルートパラメータの詳細',
                        'content' => "<?php\n// routes/web.php\n\n// 必須パラメータ\nRoute::get('/user/{id}', function (\$id) {\n    return \"User ID: \$id\";\n});\n\n// オプショナルパラメータ\nRoute::get('/user/{name?}', function (\$name = 'Guest') {\n    return \"Hello, \$name\";\n});\n\n// 正規表現制約\nRoute::get('/user/{id}', function (\$id) {\n    return \"User ID: \$id\";\n})->where('id', '[0-9]+');\n\nRoute::get('/user/{name}', function (\$name) {\n    return \"User: \$name\";\n})->where('name', '[A-Za-z]+');\n\n// 複数の制約\nRoute::get('/post/{id}/{slug}', function (\$id, \$slug) {\n    return \"Post \$id: \$slug\";\n})->where(['id' => '[0-9]+', 'slug' => '[a-z-]+']);\n\n// グローバル制約（RouteServiceProvider）\nRoute::pattern('id', '[0-9]+');\n\n// コントローラでの受け取り\npublic function show(\$id, \$slug)\n{\n    return \"ID: \$id, Slug: \$slug\";\n}",
                        'code_language' => 'php',
                        'sort_order' => 7
                    },
                ],
            ],
        ]);

        // Milestone 2: Bladeテンプレートとビュー (第2回～第3回)
        $milestone2 = $template->milestones()->create([
            'title' => 'Bladeテンプレートとビュー',
            'description' => 'Bladeテンプレートエンジンの使い方、レイアウト、コンポーネント',
            'sort_order' => 2,
            'estimated_hours' => 16,
            'deliverables' => [
                'Bladeテンプレートをマスター',
                'レイアウトとコンポーネントを使える',
                '動的なビューを作成できる'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第2回：Bladeテンプレート',
                'description' => 'Bladeの基本構文、ディレクティブ、変数の表示',
                'sort_order' => 3,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'Bladeテンプレート資料',
                    '課題02'
                ],
                'subtasks' => [
                    ['title' => 'Bladeの基本構文', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '変数の表示とエスケープ', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'ディレクティブ（@if, @foreach, @while）', 'estimated_minutes' => 80, 'sort_order' => 3],
                    ['title' => 'レイアウトと継承', 'estimated_minutes' => 40, 'sort_order' => 4],
                    ['title' => '課題02を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Bladeの基本構文',
                        'content' => "{{-- resources/views/welcome.blade.php --}}\n\n{{-- 変数の表示 --}}\n<h1>{{ \$title }}</h1>\n\n{{-- エスケープなし（HTMLを表示） --}}\n{!! \$htmlContent !!}\n\n{{-- 条件分岐 --}}\n@if (\$user->isAdmin())\n    <p>管理者です</p>\n@else\n    <p>一般ユーザーです</p>\n@endif\n\n{{-- ループ --}}\n@foreach (\$posts as \$post)\n    <div>{{ \$post->title }}</div>\n@endforeach",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Bladeテンプレートエンジンとは',
                        'content' => "# Bladeテンプレートエンジン\n\n**Blade**は、Laravelの強力なテンプレートエンジンです。\n\n## 特徴\n- **シンプルな構文**: 直感的で書きやすい\n- **PHPコード使用可能**: BladeとPHPを混在できる\n- **自動エスケープ**: XSS攻撃を防ぐ\n- **レイアウト継承**: コードの再利用が簡単\n- **コンポーネント**: 再利用可能なUI部品\n- **高速**: キャッシュされてPHPコードに変換\n\n## ファイル命名規則\n- ファイル名: `xxx.blade.php`\n- 場所: `resources/views/`\n- 例: `resources/views/posts/index.blade.php`",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Blade ディレクティブ詳細',
                        'content' => "{{-- 条件分岐 --}}\n@if (\$count > 0)\n    <p>データがあります</p>\n@elseif (\$count === 0)\n    <p>データがありません</p>\n@else\n    <p>エラー</p>\n@endif\n\n@unless (\$user->isAdmin())\n    <p>管理者ではありません</p>\n@endunless\n\n@isset(\$variable)\n    <p>変数が設定されています</p>\n@endisset\n\n@empty(\$array)\n    <p>配列が空です</p>\n@endempty\n\n{{-- ループ --}}\n@foreach (\$users as \$user)\n    <p>{{ \$user->name }}</p>\n@endforeach\n\n@forelse (\$posts as \$post)\n    <p>{{ \$post->title }}</p>\n@empty\n    <p>投稿がありません</p>\n@endforelse\n\n@for (\$i = 0; \$i < 10; \$i++)\n    <p>{{ \$i }}</p>\n@endfor\n\n@while (true)\n    <p>無限ループ</p>\n    @break\n@endwhile\n\n{{-- ループ変数 --}}\n@foreach (\$users as \$user)\n    @if (\$loop->first)\n        <p>最初の要素</p>\n    @endif\n    \n    <p>{{ \$loop->index }}: {{ \$user->name }}</p>\n    \n    @if (\$loop->last)\n        <p>最後の要素</p>\n    @endif\n@endforeach",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'レイアウトと継承',
                        'content' => "{{-- resources/views/layouts/app.blade.php --}}\n<!DOCTYPE html>\n<html>\n<head>\n    <title>@yield('title', 'デフォルトタイトル')</title>\n    @stack('styles')\n</head>\n<body>\n    <header>\n        @include('partials.header')\n    </header>\n    \n    <main>\n        @yield('content')\n    </main>\n    \n    <footer>\n        @include('partials.footer')\n    </footer>\n    \n    @stack('scripts')\n</body>\n</html>\n\n{{-- resources/views/posts/index.blade.php --}}\n@extends('layouts.app')\n\n@section('title', '投稿一覧')\n\n@push('styles')\n    <link rel=\"stylesheet\" href=\"/css/posts.css\">\n@endpush\n\n@section('content')\n    <h1>投稿一覧</h1>\n    @foreach (\$posts as \$post)\n        <article>\n            <h2>{{ \$post->title }}</h2>\n            <p>{{ \$post->content }}</p>\n        </article>\n    @endforeach\n@endsection\n\n@push('scripts')\n    <script src=\"/js/posts.js\"></script>\n@endpush",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'セキュリティとエスケープ',
                        'content' => "{{-- 自動エスケープ（推奨） --}}\n<p>{{ \$userInput }}</p>\n{{-- <script>alert('XSS')</script> → &lt;script&gt;alert('XSS')&lt;/script&gt; --}}\n\n{{-- エスケープなし（信頼できるHTMLのみ） --}}\n<div>{!! \$trustedHtml !!}</div>\n\n{{-- 古い構文（Laravel 5.2以前） --}}\n<?php echo htmlspecialchars(\$variable); ?>\n\n{{-- 生のPHPコード --}}\n@php\n    \$myVariable = 'test';\n    echo \$myVariable;\n@endphp\n\n{{-- コメント（HTML出力されない） --}}\n{{-- これはBladeコメントです --}}\n\n<!-- これはHTMLコメント（出力される） -->",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                ],
            ],
            [
                'title' => '第3回：ビュー作成・ルーティング',
                'description' => '複数のビュー作成、ルーティングの応用、データの受け渡し',
                'sort_order' => 4,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    '課題03',
                    'blade用テキスト（articleDetail.txt, articleEditing.txt, articleList.txt, articleRegistration.txt）'
                ],
                'subtasks' => [
                    ['title' => '複数のビューを作成', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'ルーティングの応用', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'コントローラからビューへデータを渡す', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '記事一覧・詳細・登録・編集画面を作成', 'estimated_minutes' => 60, 'sort_order' => 4],
                    ['title' => '課題03を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'コントローラからビューへデータを渡す',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\npublic function index()\n{\n    \$articles = [\n        ['id' => 1, 'title' => '記事1', 'content' => '内容1'],\n        ['id' => 2, 'title' => '記事2', 'content' => '内容2'],\n    ];\n    \n    return view('articles.index', ['articles' => \$articles]);\n    // または\n    return view('articles.index', compact('articles'));\n}\n\npublic function show(\$id)\n{\n    \$article = ['id' => \$id, 'title' => '記事タイトル', 'content' => '記事内容'];\n    return view('articles.show', compact('article'));\n}",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'データ渡しの様々な方法',
                        'content' => "<?php\n// 方法1: 配列で渡す\nreturn view('posts.index', ['posts' => \$posts, 'title' => 'Posts']);\n\n// 方法2: compact()を使う\n\$posts = Post::all();\n\$title = 'Posts';\nreturn view('posts.index', compact('posts', 'title'));\n\n// 方法3: with()メソッド\nreturn view('posts.index')\n    ->with('posts', \$posts)\n    ->with('title', 'Posts');\n\n// 方法4: withメソッドチェーン\nreturn view('posts.index')\n    ->withPosts(\$posts)\n    ->withTitle('Posts');\n\n// すべてのビューでデータを共有\nView::share('siteName', 'My Blog');\n\n// または AppServiceProvider の boot() で\nView::composer('*', function (\$view) {\n    \$view->with('siteName', 'My Blog');\n});",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Bladeでのデータ表示パターン',
                        'content' => "{{-- resources/views/articles/index.blade.php --}}\n\n@extends('layouts.app')\n\n@section('title', '記事一覧')\n\n@section('content')\n<div class=\"container\">\n    <h1>記事一覧</h1>\n    \n    @if(count(\$articles) > 0)\n        <div class=\"article-list\">\n            @foreach(\$articles as \$article)\n                <article class=\"article-item\">\n                    <h2>\n                        <a href=\"{{ route('articles.show', \$article->id) }}\">\n                            {{ \$article->title }}\n                        </a>\n                    </h2>\n                    <p>{{ Str::limit(\$article->content, 100) }}</p>\n                    <div class=\"meta\">\n                        <span>作成日: {{ \$article->created_at->format('Y-m-d') }}</span>\n                        <span>著者: {{ \$article->user->name }}</span>\n                    </div>\n                </article>\n            @endforeach\n        </div>\n        \n        {{-- ページネーション --}}\n        {{ \$articles->links() }}\n    @else\n        <p class=\"alert alert-info\">記事がありません</p>\n    @endif\n    \n    <a href=\"{{ route('articles.create') }}\" class=\"btn btn-primary\">\n        新規作成\n    </a>\n</div>\n@endsection",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 3: リクエストとバリデーション (第4回)
        $milestone3 = $template->milestones()->create([
            'title' => 'リクエストとバリデーション',
            'description' => 'フォームリクエストの処理、バリデーションルール、エラーメッセージ',
            'sort_order' => 3,
            'estimated_hours' => 12,
            'deliverables' => [
                'フォームデータを受け取れる',
                'バリデーションを実装できる',
                'エラーメッセージを表示できる'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '第4回：リクエスト・バリデーション',
                'description' => 'Requestオブジェクト、バリデーションルール、カスタムバリデーション',
                'sort_order' => 5,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [
                    'リクエストとバリデーション資料',
                    '課題04',
                    'blade用テキスト（kadai04_1.txt, kadai04_2.txt, kadai04_3.txt）'
                ],
                'subtasks' => [
                    ['title' => 'Requestオブジェクトの使い方', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '基本的なバリデーションルール', 'estimated_minutes' => 80, 'sort_order' => 2],
                    ['title' => 'バリデーションエラーの表示', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'FormRequestクラスの作成', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => '課題04を完了', 'estimated_minutes' => 80, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'バリデーションの基本',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\nuse Illuminate\\Http\\Request;\n\npublic function store(Request \$request)\n{\n    \$validated = \$request->validate([\n        'title' => 'required|string|max:255',\n        'content' => 'required|string|min:10',\n        'email' => 'required|email',\n    ]);\n    \n    // バリデーション通過後の処理\n    // \$validated['title'], \$validated['content'] を使用\n}\n\n// Bladeでエラー表示\n@if (\$errors->any())\n    <div class=\"alert alert-danger\">\n        <ul>\n            @foreach (\$errors->all() as \$error)\n                <li>{{ \$error }}</li>\n            @endforeach\n        </ul>\n    </div>\n@endif",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'よく使うバリデーションルール',
                        'content' => "# よく使うバリデーションルール\n\n## 基本\n- `required`: 必須\n- `nullable`: NULL可能\n- `string`: 文字列\n- `integer`: 整数\n- `numeric`: 数値\n- `boolean`: 真偽値\n- `array`: 配列\n\n## 文字列\n- `min:3`: 最小文字数\n- `max:255`: 最大文字数\n- `between:3,10`: 3〜10文字\n- `alpha`: 英字のみ\n- `alpha_num`: 英数字のみ\n- `alpha_dash`: 英数字とダッシュ、アンダースコア\n\n## 数値\n- `min:18`: 最小値\n- `max:100`: 最大値\n- `between:18,100`: 18〜100\n- `digits:4`: 4桁\n- `digits_between:3,5`: 3〜5桁\n\n## 形式\n- `email`: メールアドレス\n- `url`: URL形式\n- `ip`: IPアドレス\n- `date`: 日付形式\n- `date_format:Y-m-d`: 指定形式\n- `before:tomorrow`: 明日より前\n- `after:yesterday`: 昨日より後\n\n## ファイル\n- `file`: ファイル\n- `image`: 画像ファイル\n- `mimes:jpeg,png`: MIME type\n- `max:2048`: 最大サイズ(KB)\n\n## データベース\n- `unique:users,email`: テーブル内で一意\n- `exists:users,id`: テーブルに存在\n\n## その他\n- `confirmed`: xxx_confirmationフィールドと一致\n- `same:field`: 指定フィールドと同じ\n- `different:field`: 指定フィールドと異なる\n- `in:foo,bar`: 指定値のいずれか\n- `not_in:foo,bar`: 指定値以外\n- `regex:/pattern/`: 正規表現",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'FormRequestクラス',
                        'content' => "<?php\n// php artisan make:request StoreArticleRequest\n\n// app/Http/Requests/StoreArticleRequest.php\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass StoreArticleRequest extends FormRequest\n{\n    public function authorize(): bool\n    {\n        // 認可ロジック\n        return true;\n    }\n    \n    public function rules(): array\n    {\n        return [\n            'title' => 'required|string|max:255',\n            'content' => 'required|string|min:10',\n            'category_id' => 'required|exists:categories,id',\n            'tags' => 'nullable|array',\n            'tags.*' => 'string|max:50',\n            'image' => 'nullable|image|mimes:jpeg,png|max:2048',\n        ];\n    }\n    \n    public function messages(): array\n    {\n        return [\n            'title.required' => 'タイトルは必須です',\n            'title.max' => 'タイトルは255文字以内で入力してください',\n            'content.required' => '内容は必須です',\n            'content.min' => '内容は10文字以上で入力してください',\n        ];\n    }\n    \n    public function attributes(): array\n    {\n        return [\n            'title' => 'タイトル',\n            'content' => '内容',\n        ];\n    }\n}\n\n// コントローラで使用\npublic function store(StoreArticleRequest \$request)\n{\n    // 自動的にバリデーション済み\n    \$validated = \$request->validated();\n    Article::create(\$validated);\n    return redirect()->route('articles.index');\n}",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Bladeでのエラー表示パターン',
                        'content' => "{{-- 全エラーを一括表示 --}}\n@if (\$errors->any())\n    <div class=\"alert alert-danger\">\n        <ul>\n            @foreach (\$errors->all() as \$error)\n                <li>{{ \$error }}</li>\n            @endforeach\n        </ul>\n    </div>\n@endif\n\n{{-- 各フィールドごとにエラー表示 --}}\n<div class=\"form-group\">\n    <label for=\"title\">タイトル</label>\n    <input type=\"text\" \n           name=\"title\" \n           value=\"{{ old('title') }}\"\n           class=\"form-control @error('title') is-invalid @enderror\">\n    @error('title')\n        <div class=\"invalid-feedback\">{{ \$message }}</div>\n    @enderror\n</div>\n\n<div class=\"form-group\">\n    <label for=\"content\">内容</label>\n    <textarea name=\"content\" \n              class=\"form-control @error('content') is-invalid @enderror\">{{ old('content') }}</textarea>\n    @error('content')\n        <div class=\"invalid-feedback\">{{ \$message }}</div>\n    @enderror\n</div>\n\n{{-- エラーの有無をチェック --}}\n@if(\$errors->has('email'))\n    <p>{{ \$errors->first('email') }}</p>\n@endif",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 4: データベース操作基礎 (第5回～第6回)
        $milestone4 = $template->milestones()->create([
            'title' => 'データベース操作基礎',
            'description' => 'マイグレーション、Eloquentモデル、シーダー、SELECT操作',
            'sort_order' => 4,
            'estimated_hours' => 20,
            'deliverables' => [
                'マイグレーションを作成できる',
                'Eloquentモデルを使える',
                'データベースからデータを取得できる'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第5回：マイグレーション、モデル、シーダー',
                'description' => 'マイグレーションの作成と実行、Eloquentモデルの定義、シーダーでデータ投入',
                'sort_order' => 6,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [
                    'マイグレーション、モデル、シーダー資料',
                    '課題05',
                    'サンプルコード（Sample.php, migrations, seeders）',
                    'MySQLオートコミットONにする'
                ],
                'subtasks' => [
                    ['title' => 'マイグレーションの作成', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'テーブル構造を定義', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'Eloquentモデルの作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'シーダーの作成と実行', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => 'Factoryの使い方', 'estimated_minutes' => 60, 'sort_order' => 5],
                    ['title' => '課題05を完了', 'estimated_minutes' => 40, 'sort_order' => 6],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'マイグレーションとモデル',
                        'content' => "<?php\n// database/migrations/2024_04_24_234711_create_articles_table.php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('articles', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('title');\n            \$table->text('content');\n            \$table->timestamps();\n        });\n    }\n    \n    public function down(): void\n    {\n        Schema::dropIfExists('articles');\n    }\n};\n\n// app/Models/Article.php\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass Article extends Model\n{\n    protected \$fillable = ['title', 'content'];\n}",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'シーダーの作成',
                        'content' => "<?php\n// database/seeders/ArticlesTableSeeder.php\n\nuse Illuminate\\Database\\Seeder;\nuse App\\Models\\Article;\n\nclass ArticlesTableSeeder extends Seeder\n{\n    public function run(): void\n    {\n        Article::create([\n            'title' => '記事タイトル1',\n            'content' => '記事内容1'\n        ]);\n        \n        // Factoryを使う場合\n        Article::factory()->count(10)->create();\n    }\n}\n\n// 実行コマンド\n// php artisan db:seed --class=ArticlesTableSeeder",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'マイグレーションの基本コマンド',
                        'content' => "# マイグレーションコマンド\n\n## 作成\n```bash\nphp artisan make:migration create_articles_table\nphp artisan make:migration add_status_to_articles_table\n```\n\n## 実行\n```bash\nphp artisan migrate                # マイグレーション実行\nphp artisan migrate:status         # 状態確認\nphp artisan migrate:rollback       # 最後のバッチをロールバック\nphp artisan migrate:rollback --step=5  # 5ステップロールバック\nphp artisan migrate:reset          # すべてロールバック\nphp artisan migrate:refresh        # すべてロールバック後に再実行\nphp artisan migrate:fresh          # すべて削除して再実行\nphp artisan migrate:fresh --seed   # 再実行＋シーダー実行\n```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'マイグレーションの詳細なカラム定義',
                        'content' => "<?php\n\nSchema::create('users', function (Blueprint \$table) {\n    // 主キー\n    \$table->id();  // BIGINT UNSIGNED AUTO_INCREMENT\n    \$table->uuid('uuid');  // UUID\n    \n    // 文字列\n    \$table->string('name', 100);  // VARCHAR(100)\n    \$table->text('bio');  // TEXT\n    \$table->longText('content');  // LONGTEXT\n    \n    // 数値\n    \$table->integer('age');  // INT\n    \$table->bigInteger('views');  // BIGINT\n    \$table->decimal('price', 8, 2);  // DECIMAL(8,2)\n    \$table->double('rating', 8, 2);  // DOUBLE(8,2)\n    \$table->float('score', 8, 2);  // FLOAT(8,2)\n    \n    // 日時\n    \$table->date('birth_date');  // DATE\n    \$table->datetime('published_at');  // DATETIME\n    \$table->timestamp('verified_at');  // TIMESTAMP\n    \$table->time('opening_time');  // TIME\n    \$table->year('year');  // YEAR\n    \$table->timestamps();  // created_at, updated_at\n    \$table->softDeletes();  // deleted_at\n    \n    // 真偽値\n    \$table->boolean('is_active');  // TINYINT(1)\n    \n    // その他\n    \$table->json('metadata');  // JSON\n    \$table->enum('status', ['pending', 'approved', 'rejected']);\n    \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n    \n    // 修飾子\n    \$table->string('email')->unique();  // 一意制約\n    \$table->string('name')->nullable();  // NULL許可\n    \$table->string('status')->default('active');  // デフォルト値\n    \$table->string('name')->after('id');  // 指定カラムの後\n    \$table->index(['user_id', 'created_at']);  // インデックス\n});",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Eloquentモデルとファクトリ',
                        'content' => "<?php\n// php artisan make:model Article -mfs\n// -m: migration, -f: factory, -s: seeder\n\n// app/Models/Article.php\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;\n\nclass Article extends Model\n{\n    use SoftDeletes;\n    \n    protected \$fillable = ['title', 'content', 'user_id', 'status'];\n    protected \$guarded = ['id'];\n    protected \$hidden = ['password'];\n    protected \$casts = [\n        'published_at' => 'datetime',\n        'is_active' => 'boolean',\n        'metadata' => 'array',\n    ];\n    protected \$dates = ['published_at', 'deleted_at'];\n    \n    // リレーション\n    public function user()\n    {\n        return \$this->belongsTo(User::class);\n    }\n}\n\n// database/factories/ArticleFactory.php\nnamespace Database\\Factories;\n\nuse Illuminate\\Database\\Eloquent\\Factories\\Factory;\n\nclass ArticleFactory extends Factory\n{\n    public function definition(): array\n    {\n        return [\n            'title' => fake()->sentence(),\n            'content' => fake()->paragraph(),\n            'user_id' => 1,\n            'status' => 'published',\n        ];\n    }\n}\n\n// 使用例\nArticle::factory()->count(50)->create();",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                ],
            ],
            [
                'title' => '第6回：DB処理（SELECT）',
                'description' => 'Eloquentを使ったSELECT操作、データの取得と表示',
                'sort_order' => 7,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'DB接続（SELECT）資料',
                    '課題06'
                ],
                'subtasks' => [
                    ['title' => 'all()で全件取得', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'find()でID指定取得', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'where()で条件検索', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '取得したデータをビューで表示', 'estimated_minutes' => 60, 'sort_order' => 4],
                    ['title' => '課題06を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Eloquent SELECT操作',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\nuse App\\Models\\Article;\n\n// 全件取得\n\$articles = Article::all();\n\n// ID指定で取得\n\$article = Article::find(1);\n\n// 条件付き検索\n\$articles = Article::where('status', 'published')\n    ->orderBy('created_at', 'desc')\n    ->get();\n\n// 最初の1件\n\$article = Article::where('title', 'Laravel')->first();\n\n// 件数取得\n\$count = Article::count();\n\n// ページネーション\n\$articles = Article::paginate(10);",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Eloquent クエリビルダーの詳細',
                        'content' => "<?php\n// 基本的な検索\n\$articles = Article::where('user_id', 1)->get();\n\$articles = Article::where('views', '>', 100)->get();\n\$articles = Article::whereIn('status', ['published', 'featured'])->get();\n\$articles = Article::whereBetween('created_at', [now()->subDays(7), now()])->get();\n\n// 複数条件（AND）\n\$articles = Article::where('status', 'published')\n    ->where('views', '>', 100)\n    ->get();\n\n// 複数条件（OR）\n\$articles = Article::where('status', 'published')\n    ->orWhere('status', 'featured')\n    ->get();\n\n// ソート\n\$articles = Article::orderBy('created_at', 'desc')->get();\n\$articles = Article::latest()->get();  // created_at で降順\n\$articles = Article::oldest()->get();  // created_at で昇順\n\n// 制限\n\$articles = Article::take(5)->get();\n\$articles = Article::limit(5)->offset(10)->get();\n\n// 特定カラムのみ取得\n\$articles = Article::select('id', 'title', 'created_at')->get();\n\n// 集計\n\$count = Article::count();\n\$max = Article::max('views');\n\$avg = Article::avg('rating');\n\$sum = Article::sum('price');\n\n// Eager Loading（N+1問題回避）\n\$articles = Article::with('user', 'comments')->get();",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 5: データベース操作応用 (第7回～第9回)
        $milestone5 = $template->milestones()->create([
            'title' => 'データベース操作応用',
            'description' => '条件付きSELECT、INSERT、UPDATE、DELETE操作',
            'sort_order' => 5,
            'estimated_hours' => 24,
            'deliverables' => [
                '条件付きSELECTを実装できる',
                'INSERTでデータを追加できる',
                'UPDATEでデータを更新できる',
                'DELETEでデータを削除できる'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => '第7回：DB処理（条件付きSELECT、INSERT）',
                'description' => '複雑な条件検索、Eloquentを使ったINSERT操作',
                'sort_order' => 8,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => [
                    'DB処理（条件付きSELECT、INSERT）資料',
                    '課題07'
                ],
                'subtasks' => [
                    ['title' => '複数条件での検索', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'リレーションを使った検索', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'create()でデータ追加', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'フォームからデータを登録', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => '課題07を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '条件付きSELECTとINSERT',
                        'content' => "<?php\n// 複数条件での検索\n\$articles = Article::where('status', 'published')\n    ->where('created_at', '>', now()->subDays(7))\n    ->whereIn('category_id', [1, 2, 3])\n    ->get();\n\n// リレーションを使った検索\n\$articles = Article::whereHas('user', function (\$query) {\n    \$query->where('active', true);\n})->get();\n\n// INSERT操作\nArticle::create([\n    'title' => '新しい記事',\n    'content' => '記事内容',\n    'user_id' => 1\n]);\n\n// または\n\$article = new Article();\n\$article->title = '新しい記事';\n\$article->content = '記事内容';\n\$article->save();",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'INSERT操作の詳細パターン',
                        'content' => "<?php\n// 方法1: create() - マスアサインメント\nArticle::create([\n    'title' => 'Title',\n    'content' => 'Content',\n    'user_id' => auth()->id(),\n]);\n\n// 方法2: new + save()\n\$article = new Article();\n\$article->title = 'Title';\n\$article->content = 'Content';\n\$article->save();\n\n// 方法3: firstOrCreate() - 存在しなければ作成\n\$article = Article::firstOrCreate(\n    ['title' => 'Unique Title'],\n    ['content' => 'Content']\n);\n\n// 方法4: updateOrCreate() - 存在すれば更新、なければ作成\n\$article = Article::updateOrCreate(\n    ['title' => 'Title'],\n    ['content' => 'Updated Content']\n);\n\n// 一括INSERT\nArticle::insert([\n    ['title' => 'Title 1', 'content' => 'Content 1'],\n    ['title' => 'Title 2', 'content' => 'Content 2'],\n]);",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第8回：DB処理（UPDATE）',
                'description' => 'Eloquentを使ったUPDATE操作、データの更新',
                'sort_order' => 9,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'DB処理（UPDATE）資料',
                    '課題08'
                ],
                'subtasks' => [
                    ['title' => 'update()でデータ更新', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'fill()とsave()で更新', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'フォームからデータを更新', 'estimated_minutes' => 80, 'sort_order' => 3],
                    ['title' => '課題08を完了', 'estimated_minutes' => 40, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'UPDATE操作の基本',
                        'content' => "<?php\n// 方法1: update()メソッド\nArticle::where('id', 1)->update([\n    'title' => '更新されたタイトル',\n    'content' => '更新された内容'\n]);\n\n// 方法2: fill()とsave()\n\$article = Article::find(1);\n\$article->fill([\n    'title' => '更新されたタイトル',\n    'content' => '更新された内容'\n]);\n\$article->save();\n\n// 方法3: 直接プロパティに代入\n\$article = Article::find(1);\n\$article->title = '更新されたタイトル';\n\$article->content = '更新された内容';\n\$article->save();",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'コントローラでのUPDATE処理',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\npublic function update(Request \$request, \$id)\n{\n    // バリデーション\n    \$validated = \$request->validate([\n        'title' => 'required|string|max:255',\n        'content' => 'required|string|min:10',\n        'status' => 'in:draft,published',\n    ]);\n    \n    // レコードを取得して更新\n    \$article = Article::findOrFail(\$id);\n    \$article->update(\$validated);\n    \n    // リダイレクトとフラッシュメッセージ\n    return redirect()->route('articles.show', \$id)\n        ->with('success', '記事を更新しました');\n}\n\n// 編集フォーム表示\npublic function edit(\$id)\n{\n    \$article = Article::findOrFail(\$id);\n    return view('articles.edit', compact('article'));\n}",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '編集フォームのBlade',
                        'content' => "{{-- resources/views/articles/edit.blade.php --}}\n\n@extends('layouts.app')\n\n@section('title', '記事編集')\n\n@section('content')\n<div class=\"container\">\n    <h1>記事編集</h1>\n    \n    @if(session('success'))\n        <div class=\"alert alert-success\">{{ session('success') }}</div>\n    @endif\n    \n    <form action=\"{{ route('articles.update', \$article->id) }}\" method=\"POST\">\n        @csrf\n        @method('PUT')\n        \n        <div class=\"form-group\">\n            <label for=\"title\">タイトル</label>\n            <input type=\"text\" \n                   name=\"title\" \n                   value=\"{{ old('title', \$article->title) }}\"\n                   class=\"form-control @error('title') is-invalid @enderror\">\n            @error('title')\n                <div class=\"invalid-feedback\">{{ \$message }}</div>\n            @enderror\n        </div>\n        \n        <div class=\"form-group\">\n            <label for=\"content\">内容</label>\n            <textarea name=\"content\" \n                      rows=\"10\"\n                      class=\"form-control @error('content') is-invalid @enderror\">{{ old('content', \$article->content) }}</textarea>\n            @error('content')\n                <div class=\"invalid-feedback\">{{ \$message }}</div>\n            @enderror\n        </div>\n        \n        <button type=\"submit\" class=\"btn btn-primary\">更新</button>\n        <a href=\"{{ route('articles.show', \$article->id) }}\" class=\"btn btn-secondary\">キャンセル</a>\n    </form>\n</div>\n@endsection",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'UPDATE操作のベストプラクティス',
                        'content' => "# UPDATE操作のベストプラクティス\n\n## 1. 必ずバリデーションを実施\n```php\n\$validated = \$request->validate([\n    'title' => 'required|max:255',\n]);\n```\n\n## 2. findOrFail()を使用\n存在しないIDの場合は404エラーを返す\n```php\n\$article = Article::findOrFail(\$id);\n```\n\n## 3. @methodディレクティブ\nHTMLフォームはPUTメソッドをサポートしないため、@method('PUT')を使用\n\n## 4. old()ヘルパー\nバリデーションエラー時に入力値を保持\n```php\n{{ old('title', \$article->title) }}\n```\n\n## 5. フラッシュメッセージ\nユーザーに更新完了を通知\n```php\nreturn redirect()->with('success', '更新しました');\n```\n\n## 6. 条件付き更新\n```php\n// 条件に一致する全レコードを更新\nArticle::where('status', 'draft')\n    ->where('user_id', auth()->id())\n    ->update(['status' => 'published']);\n```\n\n## 7. タイムスタンプの制御\n```php\n// updated_atを更新しない\n\$article->timestamps = false;\n\$article->save();\n\n// 手動でタッチ\n\$article->touch();\n```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'インクリメント・デクリメント',
                        'content' => "<?php\n// ビュー数を増やす\n\$article = Article::find(1);\n\$article->increment('views');\n\$article->increment('views', 5);  // 5増やす\n\n// いいね数を減らす\n\$article->decrement('likes');\n\$article->decrement('likes', 2);  // 2減らす\n\n// 複数カラムを同時に更新\n\$article->increment('views', 1, ['last_viewed_at' => now()]);\n\n// または直接\nArticle::where('id', 1)->increment('views');\nArticle::where('status', 'published')->increment('priority', 5);",
                        'code_language' => 'php',
                        'sort_order' => 5
                    ],
                ],
            ],
            [
                'title' => '第9回：DB処理（DELETE）',
                'description' => 'Eloquentを使ったDELETE操作、データの削除',
                'sort_order' => 10,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    'DB処理（DELETE）資料',
                    '課題09'
                ],
                'subtasks' => [
                    ['title' => 'delete()でデータ削除', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'destroy()でID指定削除', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'ソフトデリートの理解', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => '課題09を完了', 'estimated_minutes' => 40, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'DELETE操作の基本',
                        'content' => "<?php\n// 方法1: delete()メソッド\n\$article = Article::find(1);\n\$article->delete();\n\n// 方法2: destroy()メソッド（ID指定）\nArticle::destroy(1);\nArticle::destroy([1, 2, 3]);  // 複数削除\n\n// 方法3: 条件付き削除\nArticle::where('status', 'draft')->delete();\n\n// 方法4: truncate（全データ削除）\nArticle::truncate();  // 注意：自動インクリメントもリセット",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'コントローラでのDELETE処理',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\npublic function destroy(\$id)\n{\n    \$article = Article::findOrFail(\$id);\n    \n    // 認可チェック（オプション）\n    // \$this->authorize('delete', \$article);\n    \n    \$article->delete();\n    \n    return redirect()->route('articles.index')\n        ->with('success', '記事を削除しました');\n}\n\n// Bladeの削除ボタン\n<form action=\"{{ route('articles.destroy', \$article->id) }}\" \n      method=\"POST\" \n      onsubmit=\"return confirm('本当に削除しますか？');\">\n    @csrf\n    @method('DELETE')\n    <button type=\"submit\" class=\"btn btn-danger\">削除</button>\n</form>",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ソフトデリート（論理削除）',
                        'content' => "# ソフトデリート（Soft Delete）\n\n**ソフトデリート**は、データを物理的に削除せず、`deleted_at`カラムにタイムスタンプを記録する方法です。\n\n## メリット\n- データを完全に削除しない（復元可能）\n- 監査ログとして使用可能\n- 誤削除からの復旧が可能\n\n## 実装方法\n\n### 1. マイグレーションにカラム追加\n```php\nSchema::table('articles', function (Blueprint \$table) {\n    \$table->softDeletes();  // deleted_at カラムを追加\n});\n```\n\n### 2. モデルにトレイト追加\n```php\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;\n\nclass Article extends Model\n{\n    use SoftDeletes;\n}\n```\n\n## 使用方法\n\n### 削除（論理削除）\n```php\n\$article->delete();  // deleted_at に現在時刻が記録される\n```\n\n### 取得時の挙動\n```php\nArticle::all();  // 削除されていないデータのみ\nArticle::withTrashed()->get();  // 削除されたデータも含む\nArticle::onlyTrashed()->get();  // 削除されたデータのみ\n```\n\n### 復元\n```php\n\$article = Article::withTrashed()->find(1);\n\$article->restore();  // deleted_at を NULL に戻す\n\n// 条件付き復元\nArticle::onlyTrashed()\n    ->where('user_id', 1)\n    ->restore();\n```\n\n### 完全削除\n```php\n\$article->forceDelete();  // 物理的に削除\n```",
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'ソフトデリートの実装例',
                        'content' => "<?php\n// app/Models/Article.php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;\n\nclass Article extends Model\n{\n    use SoftDeletes;\n    \n    protected \$fillable = ['title', 'content', 'user_id'];\n    protected \$dates = ['deleted_at'];\n}\n\n// app/Http/Controllers/ArticleController.php\n\n// 削除（論理削除）\npublic function destroy(\$id)\n{\n    \$article = Article::findOrFail(\$id);\n    \$article->delete();\n    \n    return redirect()->route('articles.index')\n        ->with('success', '記事を削除しました');\n}\n\n// ゴミ箱一覧\npublic function trash()\n{\n    \$articles = Article::onlyTrashed()->paginate(10);\n    return view('articles.trash', compact('articles'));\n}\n\n// 復元\npublic function restore(\$id)\n{\n    \$article = Article::withTrashed()->findOrFail(\$id);\n    \$article->restore();\n    \n    return redirect()->route('articles.index')\n        ->with('success', '記事を復元しました');\n}\n\n// 完全削除\npublic function forceDestroy(\$id)\n{\n    \$article = Article::withTrashed()->findOrFail(\$id);\n    \$article->forceDelete();\n    \n    return redirect()->route('articles.trash')\n        ->with('success', '記事を完全に削除しました');\n}",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'リレーションの削除',
                        'content' => "<?php\n// マイグレーションで外部キー制約を設定\nSchema::create('comments', function (Blueprint \$table) {\n    \$table->id();\n    \$table->foreignId('article_id')\n        ->constrained()\n        ->onDelete('cascade');  // 親が削除されたら子も削除\n    \$table->text('content');\n    \$table->timestamps();\n});\n\n// モデルでdeleting イベントを使用\nclass Article extends Model\n{\n    protected static function boot()\n    {\n        parent::boot();\n        \n        static::deleting(function (\$article) {\n            // 記事削除時にコメントも削除\n            \$article->comments()->delete();\n            \n            // または画像ファイルを削除\n            if (\$article->image_path) {\n                Storage::delete(\$article->image_path);\n            }\n        });\n    }\n    \n    public function comments()\n    {\n        return \$this->hasMany(Comment::class);\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'DELETE操作の注意点',
                        'content' => "# DELETE操作の注意点\n\n## 1. 削除前の確認\nJavaScriptで確認ダイアログを表示\n```html\n<form onsubmit=\"return confirm('本当に削除しますか？');\">\n```\n\n## 2. 認可チェック\n```php\n\$this->authorize('delete', \$article);\n```\n\n## 3. トランザクション\n複数テーブルを削除する場合\n```php\nDB::transaction(function () use (\$article) {\n    \$article->comments()->delete();\n    \$article->delete();\n});\n```\n\n## 4. 外部キー制約\n- `onDelete('cascade')`: 親削除時に子も削除\n- `onDelete('set null')`: 親削除時に子の外部キーをNULLに\n- `onDelete('restrict')`: 子が存在する場合は親を削除不可\n\n## 5. ファイルの削除\n```php\nStorage::delete(\$article->image_path);\n```\n\n## 6. ソフトデリートの判定\n```php\nif (\$article->trashed()) {\n    // 削除済み\n}\n```\n\n## 7. 削除とセキュリティ\n- CSRFトークンを必ず使用（@csrf）\n- DELETEメソッドを使用（@method('DELETE')）\n- 認証・認可を実装",
                        'sort_order' => 6
                    },
                ],
            ],
        ]);

        // Milestone 6: API開発とファイルアップロード (第10回～第11回)
        $milestone6 = $template->milestones()->create([
            'title' => 'API開発とファイルアップロード',
            'description' => 'RESTful APIの作成、ファイルアップロード処理',
            'sort_order' => 6,
            'estimated_hours' => 20,
            'deliverables' => [
                'RESTful APIを実装できる',
                'ファイルをアップロードできる',
                'アップロードしたファイルを管理できる'
            ],
        ]);

        $milestone6->tasks()->createMany([
            [
                'title' => '第10回：API',
                'description' => 'RESTful APIの設計と実装、JSONレスポンス',
                'sort_order' => 11,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => [
                    'API資料',
                    '課題10'
                ],
                'subtasks' => [
                    ['title' => 'APIルートの定義', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'JSONレスポンスの返却', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'CRUD操作のAPI実装', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'APIリソースクラスの使用', 'estimated_minutes' => 40, 'sort_order' => 4],
                    ['title' => '課題10を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'RESTful APIとは',
                        'content' => "# RESTful API\n\n**REST (Representational State Transfer)** は、Webサービスのアーキテクチャスタイルです。\n\n## HTTPメソッドとCRUD操作\n\n| HTTPメソッド | CRUD操作 | エンドポイント例 | 説明 |\n|------------|---------|----------------|------|\n| GET | Read | GET /api/articles | 一覧取得 |\n| GET | Read | GET /api/articles/1 | 詳細取得 |\n| POST | Create | POST /api/articles | 新規作成 |\n| PUT/PATCH | Update | PUT /api/articles/1 | 更新 |\n| DELETE | Delete | DELETE /api/articles/1 | 削除 |\n\n## RESTful API の原則\n\n1. **ステートレス**: サーバーはクライアントの状態を保持しない\n2. **統一インターフェース**: 一貫したURLとHTTPメソッド\n3. **リソース指向**: URLはリソースを表す\n4. **JSONフォーマット**: データはJSON形式で送受信\n\n## ステータスコード\n\n- **200 OK**: 成功\n- **201 Created**: 作成成功\n- **204 No Content**: 成功（レスポンスなし）\n- **400 Bad Request**: リクエストエラー\n- **401 Unauthorized**: 認証エラー\n- **403 Forbidden**: 権限エラー\n- **404 Not Found**: リソース未発見\n- **422 Unprocessable Entity**: バリデーションエラー\n- **500 Internal Server Error**: サーバーエラー",
                        'sort_order' => 1
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'RESTful APIの基本実装',
                        'content' => "<?php\n// routes/api.php\n\nuse App\\Http\\Controllers\\Api\\ArticleController;\nuse Illuminate\\Support\\Facades\\Route;\n\n// APIリソースルート（RESTful）\nRoute::apiResource('articles', ArticleController::class);\n\n// 上記は以下と同等：\n// GET    /api/articles          index    一覧取得\n// POST   /api/articles          store    作成\n// GET    /api/articles/{id}     show     詳細取得\n// PUT    /api/articles/{id}     update   更新\n// DELETE /api/articles/{id}     destroy  削除\n\n// カスタムルート追加\nRoute::get('articles/search', [ArticleController::class, 'search']);\nRoute::post('articles/{id}/publish', [ArticleController::class, 'publish']);",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'APIコントローラの完全実装',
                        'content' => "<?php\n// app/Http/Controllers/Api/ArticleController.php\n\nnamespace App\\Http\\Controllers\\Api;\n\nuse App\\Http\\Controllers\\Controller;\nuse App\\Models\\Article;\nuse App\\Http\\Requests\\StoreArticleRequest;\nuse Illuminate\\Http\\Request;\n\nclass ArticleController extends Controller\n{\n    // 一覧取得 GET /api/articles\n    public function index()\n    {\n        \$articles = Article::with('user')\n            ->latest()\n            ->paginate(10);\n        \n        return response()->json(\$articles);\n    }\n    \n    // 作成 POST /api/articles\n    public function store(StoreArticleRequest \$request)\n    {\n        \$article = Article::create(\$request->validated());\n        \n        return response()->json([\n            'message' => '記事を作成しました',\n            'data' => \$article\n        ], 201);\n    }\n    \n    // 詳細取得 GET /api/articles/{id}\n    public function show(\$id)\n    {\n        \$article = Article::with('user', 'comments')\n            ->findOrFail(\$id);\n        \n        return response()->json(\$article);\n    }\n    \n    // 更新 PUT /api/articles/{id}\n    public function update(StoreArticleRequest \$request, \$id)\n    {\n        \$article = Article::findOrFail(\$id);\n        \$article->update(\$request->validated());\n        \n        return response()->json([\n            'message' => '記事を更新しました',\n            'data' => \$article\n        ]);\n    }\n    \n    // 削除 DELETE /api/articles/{id}\n    public function destroy(\$id)\n    {\n        \$article = Article::findOrFail(\$id);\n        \$article->delete();\n        \n        return response()->json([\n            'message' => '記事を削除しました'\n        ], 204);\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'APIリソースクラス',
                        'content' => "<?php\n// php artisan make:resource ArticleResource\n\n// app/Http/Resources/ArticleResource.php\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass ArticleResource extends JsonResource\n{\n    public function toArray(Request \$request): array\n    {\n        return [\n            'id' => \$this->id,\n            'title' => \$this->title,\n            'content' => \$this->content,\n            'excerpt' => \$this->excerpt,\n            'author' => [\n                'id' => \$this->user->id,\n                'name' => \$this->user->name,\n            ],\n            'published_at' => \$this->published_at?->format('Y-m-d H:i:s'),\n            'created_at' => \$this->created_at->format('Y-m-d H:i:s'),\n        ];\n    }\n}\n\n// コントローラで使用\nuse App\\Http\\Resources\\ArticleResource;\n\npublic function index()\n{\n    \$articles = Article::paginate(10);\n    return ArticleResource::collection(\$articles);\n}\n\npublic function show(\$id)\n{\n    \$article = Article::findOrFail(\$id);\n    return new ArticleResource(\$article);\n}",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'エラーハンドリング',
                        'content' => "<?php\n// app/Http/Controllers/Api/ArticleController.php\n\nuse Illuminate\\Database\\Eloquent\\ModelNotFoundException;\nuse Illuminate\\Validation\\ValidationException;\n\npublic function show(\$id)\n{\n    try {\n        \$article = Article::findOrFail(\$id);\n        return response()->json(\$article);\n    } catch (ModelNotFoundException \$e) {\n        return response()->json([\n            'error' => '記事が見つかりません'\n        ], 404);\n    }\n}\n\n// app/Exceptions/Handler.php でグローバルエラーハンドリング\n\npublic function render(\$request, Throwable \$exception)\n{\n    if (\$request->is('api/*')) {\n        if (\$exception instanceof ModelNotFoundException) {\n            return response()->json([\n                'error' => 'リソースが見つかりません'\n            ], 404);\n        }\n        \n        if (\$exception instanceof ValidationException) {\n            return response()->json([\n                'error' => 'バリデーションエラー',\n                'errors' => \$exception->errors()\n            ], 422);\n        }\n        \n        return response()->json([\n            'error' => 'サーバーエラー'\n        ], 500);\n    }\n    \n    return parent::render(\$request, \$exception);\n}",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'API認証（Sanctum）',
                        'content' => "<?php\n// Sanctumのインストール\n// composer require laravel/sanctum\n// php artisan vendor:publish --provider=\"Laravel\\Sanctum\\SanctumServiceProvider\"\n// php artisan migrate\n\n// routes/api.php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// 認証不要\nRoute::post('register', [AuthController::class, 'register']);\nRoute::post('login', [AuthController::class, 'login']);\n\n// 認証が必要\nRoute::middleware('auth:sanctum')->group(function () {\n    Route::post('logout', [AuthController::class, 'logout']);\n    Route::apiResource('articles', ArticleController::class);\n});\n\n// app/Http/Controllers/Api/AuthController.php\n\npublic function login(Request \$request)\n{\n    \$credentials = \$request->validate([\n        'email' => 'required|email',\n        'password' => 'required',\n    ]);\n    \n    if (!Auth::attempt(\$credentials)) {\n        return response()->json([\n            'error' => '認証に失敗しました'\n        ], 401);\n    }\n    \n    \$user = Auth::user();\n    \$token = \$user->createToken('api-token')->plainTextToken;\n    \n    return response()->json([\n        'token' => \$token,\n        'user' => \$user\n    ]);\n}\n\npublic function logout(Request \$request)\n{\n    \$request->user()->currentAccessToken()->delete();\n    \n    return response()->json([\n        'message' => 'ログアウトしました'\n    ]);\n}",
                        'code_language' => 'php',
                        'sort_order' => 6
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'JavaScriptでAPIを呼び出す',
                        'content' => "// Fetch API を使用\n\n// GET リクエスト\nfetch('/api/articles')\n    .then(response => response.json())\n    .then(data => console.log(data))\n    .catch(error => console.error('Error:', error));\n\n// POST リクエスト\nfetch('/api/articles', {\n    method: 'POST',\n    headers: {\n        'Content-Type': 'application/json',\n        'Accept': 'application/json',\n        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content\n    },\n    body: JSON.stringify({\n        title: '新しい記事',\n        content: '記事の内容'\n    })\n})\n.then(response => response.json())\n.then(data => console.log(data));\n\n// 認証トークンを使用（Sanctum）\nconst token = 'your-api-token';\n\nfetch('/api/articles', {\n    method: 'GET',\n    headers: {\n        'Authorization': `Bearer \${token}`,\n        'Accept': 'application/json'\n    }\n})\n.then(response => response.json())\n.then(data => console.log(data));\n\n// Axios を使用（推奨）\nimport axios from 'axios';\n\n// GET\naxios.get('/api/articles')\n    .then(response => console.log(response.data))\n    .catch(error => console.error(error));\n\n// POST\naxios.post('/api/articles', {\n    title: '新しい記事',\n    content: '記事の内容'\n})\n.then(response => console.log(response.data));\n\n// 認証トークン設定\naxios.defaults.headers.common['Authorization'] = `Bearer \${token}`;",
                        'code_language' => 'javascript',
                        'sort_order' => 7
                    },
                ],
            ],
            [
                'title' => '第11回：ファイルアップロード',
                'description' => 'ファイルアップロード処理、ストレージ管理、画像の表示',
                'sort_order' => 12,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => [
                    'ファイルアップロード（総合課題①）資料',
                    '課題11',
                    'EX/js/kadai05.js（画像選択時のサムネイル）'
                ],
                'subtasks' => [
                    ['title' => 'ファイルアップロードの基本', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'ファイルのバリデーション', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'ストレージへの保存', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '画像の表示とサムネイル', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => '課題11を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Laravelストレージシステム',
                        'content' => "# Laravelストレージシステム\n\nLaravelは、ファイル操作のために統一されたAPIを提供しています。\n\n## ストレージディスク\n\n### 1. local（デフォルト）\n- 場所: `storage/app/`\n- 用途: 非公開ファイル\n\n### 2. public\n- 場所: `storage/app/public/`\n- 用途: 公開ファイル（画像など）\n- シンボリックリンク作成: `php artisan storage:link`\n- アクセス: `public/storage/` 経由\n\n### 3. s3（オプション）\n- Amazon S3\n- 本番環境で推奨\n\n## 設定ファイル\n`config/filesystems.php` で設定を管理\n\n```php\n'default' => env('FILESYSTEM_DISK', 'local'),\n\n'disks' => [\n    'local' => [\n        'driver' => 'local',\n        'root' => storage_path('app'),\n    ],\n    'public' => [\n        'driver' => 'local',\n        'root' => storage_path('app/public'),\n        'url' => env('APP_URL').'/storage',\n        'visibility' => 'public',\n    ],\n],\n```",
                        'sort_order' => 1
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイルアップロードの基本',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Storage;\n\npublic function store(Request \$request)\n{\n    // バリデーション\n    \$validated = \$request->validate([\n        'title' => 'required|string|max:255',\n        'content' => 'required',\n        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 最大2MB\n    ]);\n    \n    // ファイルを保存（自動的にユニークな名前を生成）\n    \$imagePath = \$request->file('image')->store('images', 'public');\n    \n    // 元のファイル名を保持して保存\n    \$originalName = \$request->file('image')->getClientOriginalName();\n    \$imagePath = \$request->file('image')->storeAs('images', \$originalName, 'public');\n    \n    // データベースに保存\n    \$article = Article::create([\n        'title' => \$validated['title'],\n        'content' => \$validated['content'],\n        'image_path' => \$imagePath,\n    ]);\n    \n    return redirect()->route('articles.index')\n        ->with('success', '記事を作成しました');\n}\n\n// ファイルの削除\npublic function destroy(\$id)\n{\n    \$article = Article::findOrFail(\$id);\n    \n    // ストレージからファイルを削除\n    if (\$article->image_path) {\n        Storage::disk('public')->delete(\$article->image_path);\n    }\n    \n    \$article->delete();\n    \n    return redirect()->route('articles.index');\n}",
                        'code_language' => 'php',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'アップロードフォーム',
                        'content' => "{{-- resources/views/articles/create.blade.php --}}\n\n@extends('layouts.app')\n\n@section('content')\n<div class=\"container\">\n    <h1>記事作成</h1>\n    \n    {{-- enctype=\"multipart/form-data\" が必須 --}}\n    <form action=\"{{ route('articles.store') }}\" method=\"POST\" enctype=\"multipart/form-data\">\n        @csrf\n        \n        <div class=\"form-group\">\n            <label for=\"title\">タイトル</label>\n            <input type=\"text\" \n                   name=\"title\" \n                   value=\"{{ old('title') }}\"\n                   class=\"form-control @error('title') is-invalid @enderror\">\n            @error('title')\n                <div class=\"invalid-feedback\">{{ \$message }}</div>\n            @enderror\n        </div>\n        \n        <div class=\"form-group\">\n            <label for=\"image\">画像</label>\n            <input type=\"file\" \n                   name=\"image\" \n                   accept=\"image/*\"\n                   class=\"form-control @error('image') is-invalid @enderror\"\n                   id=\"imageInput\">\n            @error('image')\n                <div class=\"invalid-feedback\">{{ \$message }}</div>\n            @enderror\n            \n            {{-- プレビュー --}}\n            <div id=\"imagePreview\" class=\"mt-2\"></div>\n        </div>\n        \n        <button type=\"submit\" class=\"btn btn-primary\">作成</button>\n    </form>\n</div>\n\n<script>\n// 画像プレビュー\ndocument.getElementById('imageInput').addEventListener('change', function(e) {\n    const file = e.target.files[0];\n    const preview = document.getElementById('imagePreview');\n    \n    if (file) {\n        const reader = new FileReader();\n        reader.onload = function(e) {\n            preview.innerHTML = `<img src=\"\${e.target.result}\" style=\"max-width: 300px;\" class=\"img-thumbnail\">`;\n        }\n        reader.readAsDataURL(file);\n    } else {\n        preview.innerHTML = '';\n    }\n});\n</script>\n@endsection",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '画像の表示',
                        'content' => "{{-- resources/views/articles/show.blade.php --}}\n\n@extends('layouts.app')\n\n@section('content')\n<div class=\"container\">\n    <h1>{{ \$article->title }}</h1>\n    \n    @if(\$article->image_path)\n        {{-- Storage::url() でURLを取得 --}}\n        <img src=\"{{ Storage::url(\$article->image_path) }}\" \n             alt=\"{{ \$article->title }}\"\n             class=\"img-fluid\">\n        \n        {{-- または asset() ヘルパー --}}\n        <img src=\"{{ asset('storage/' . \$article->image_path) }}\" \n             alt=\"{{ \$article->title }}\">\n    @endif\n    \n    <div class=\"content\">\n        {{ \$article->content }}\n    </div>\n</div>\n@endsection\n\n{{-- 一覧でサムネイル表示 --}}\n@foreach(\$articles as \$article)\n    <div class=\"article-card\">\n        @if(\$article->image_path)\n            <img src=\"{{ Storage::url(\$article->image_path) }}\" \n                 alt=\"{{ \$article->title }}\"\n                 style=\"width: 200px; height: 150px; object-fit: cover;\">\n        @else\n            <img src=\"{{ asset('images/no-image.png') }}\" \n                 alt=\"No Image\">\n        @endif\n        <h3>{{ \$article->title }}</h3>\n    </div>\n@endforeach",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'Storage ファサードの使い方',
                        'content' => "<?php\nuse Illuminate\\Support\\Facades\\Storage;\n\n// ファイル保存\nStorage::put('file.txt', 'Contents');  // storage/app/file.txt\nStorage::disk('public')->put('file.txt', 'Contents');  // storage/app/public/file.txt\n\n// ファイルアップロード\n\$path = Storage::putFile('images', \$request->file('image'));\n\$path = Storage::putFileAs('images', \$request->file('image'), 'filename.jpg');\n\n// ファイル読み取り\n\$contents = Storage::get('file.txt');\n\n// ファイル存在確認\nif (Storage::exists('file.txt')) {\n    // ファイルが存在する\n}\n\n// ファイル削除\nStorage::delete('file.txt');\nStorage::delete(['file1.txt', 'file2.txt']);  // 複数削除\n\n// ファイルコピー・移動\nStorage::copy('old.txt', 'new.txt');\nStorage::move('old.txt', 'new.txt');\n\n// ファイルサイズ取得\n\$size = Storage::size('file.txt');\n\n// 最終更新日時\n\$time = Storage::lastModified('file.txt');\n\n// ディレクトリ内のファイル一覧\n\$files = Storage::files('images');\n\$allFiles = Storage::allFiles('images');  // サブディレクトリも含む\n\n// ディレクトリ一覧\n\$directories = Storage::directories('images');\n\$allDirectories = Storage::allDirectories('images');\n\n// ディレクトリ作成・削除\nStorage::makeDirectory('new-folder');\nStorage::deleteDirectory('folder');\n\n// URLを取得（publicディスクのみ）\n\$url = Storage::url('images/photo.jpg');\n// 結果: /storage/images/photo.jpg\n\n// 一時的なURL（S3など）\n\$url = Storage::temporaryUrl(\n    'file.jpg', \n    now()->addMinutes(5)\n);",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '画像のリサイズ（Intervention Image）',
                        'content' => "<?php\n// Intervention Imageのインストール\n// composer require intervention/image\n\nuse Intervention\\Image\\Facades\\Image;\n\npublic function store(Request \$request)\n{\n    \$validated = \$request->validate([\n        'title' => 'required',\n        'image' => 'required|image|max:5120',  // 最大5MB\n    ]);\n    \n    if (\$request->hasFile('image')) {\n        \$image = \$request->file('image');\n        \$filename = time() . '.' . \$image->getClientOriginalExtension();\n        \n        // 画像をリサイズ\n        \$img = Image::make(\$image->path());\n        \n        // 幅を800pxにリサイズ（アスペクト比維持）\n        \$img->resize(800, null, function (\$constraint) {\n            \$constraint->aspectRatio();\n            \$constraint->upsize();  // 元画像より大きくしない\n        });\n        \n        // 保存\n        \$path = 'images/' . \$filename;\n        Storage::disk('public')->put(\$path, (string) \$img->encode());\n        \n        // サムネイルも作成\n        \$thumbnail = Image::make(\$image->path())\n            ->fit(300, 300)  // 300x300の正方形にトリミング\n            ->encode();\n        \n        \$thumbnailPath = 'thumbnails/' . \$filename;\n        Storage::disk('public')->put(\$thumbnailPath, (string) \$thumbnail);\n        \n        // データベースに保存\n        \$article = Article::create([\n            'title' => \$validated['title'],\n            'image_path' => \$path,\n            'thumbnail_path' => \$thumbnailPath,\n        ]);\n    }\n    \n    return redirect()->route('articles.index');\n}",
                        'code_language' => 'php',
                        'sort_order' => 6
                    },
                    [
                        'type' => 'note',
                        'title' => 'ファイルアップロードのベストプラクティス',
                        'content' => "# ファイルアップロードのベストプラクティス\n\n## 1. バリデーション\n```php\n'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',\n'document' => 'required|file|mimes:pdf,doc,docx|max:10240',\n```\n\n## 2. セキュリティ\n- **ファイルタイプをチェック**: MIMEタイプを検証\n- **ファイルサイズを制限**: max:2048 (KB)\n- **ファイル名をサニタイズ**: ユニークな名前を生成\n- **実行可能ファイルを拒否**: .php, .exe など\n\n## 3. ストレージリンク\n```bash\nphp artisan storage:link\n```\n`public/storage` → `storage/app/public` のシンボリックリンク作成\n\n## 4. ファイル名の処理\n```php\n// ユニークな名前を自動生成\n\$path = \$request->file('image')->store('images', 'public');\n\n// カスタム名\n\$filename = time() . '_' . \$request->file('image')->getClientOriginalName();\n\$path = \$request->file('image')->storeAs('images', \$filename, 'public');\n\n// UUIDを使用\nuse Illuminate\\Support\\Str;\n\$filename = Str::uuid() . '.' . \$request->file('image')->extension();\n```\n\n## 5. 古いファイルの削除\n更新時は古いファイルを削除\n```php\nif (\$article->image_path) {\n    Storage::disk('public')->delete(\$article->image_path);\n}\n```\n\n## 6. エラーハンドリング\n```php\ntry {\n    \$path = \$request->file('image')->store('images', 'public');\n} catch (\\Exception \$e) {\n    return back()->with('error', 'ファイルアップロードに失敗しました');\n}\n```\n\n## 7. プログレス表示\nJavaScriptで進捗を表示\n\n## 8. 本番環境\n- S3などのクラウドストレージを使用\n- CDNで配信\n- バックアップを設定",
                        'sort_order' => 7
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '複数ファイルのアップロード',
                        'content' => "<?php\n// コントローラ\npublic function store(Request \$request)\n{\n    \$validated = \$request->validate([\n        'title' => 'required',\n        'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',\n    ]);\n    \n    \$article = Article::create([\n        'title' => \$validated['title'],\n    ]);\n    \n    // 複数画像を処理\n    if (\$request->hasFile('images')) {\n        foreach (\$request->file('images') as \$image) {\n            \$path = \$image->store('images', 'public');\n            \n            // 中間テーブルに保存\n            \$article->images()->create([\n                'path' => \$path,\n                'filename' => \$image->getClientOriginalName(),\n                'size' => \$image->getSize(),\n            ]);\n        }\n    }\n    \n    return redirect()->route('articles.show', \$article);\n}\n\n// Blade\n<form action=\"{{ route('articles.store') }}\" method=\"POST\" enctype=\"multipart/form-data\">\n    @csrf\n    \n    <input type=\"text\" name=\"title\">\n    \n    {{-- multiple属性で複数選択可能 --}}\n    <input type=\"file\" name=\"images[]\" multiple accept=\"image/*\">\n    \n    <button type=\"submit\">送信</button>\n</form>\n\n// Model\nclass Article extends Model\n{\n    public function images()\n    {\n        return \$this->hasMany(ArticleImage::class);\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 8
                    },
                ],
            ],
        ]);

        // Milestone 7: 総合問題 (第12回)
        $milestone7 = $template->milestones()->create([
            'title' => '総合問題',
            'description' => 'これまで学習した内容の総合的な復習と応用',
            'sort_order' => 7,
            'estimated_hours' => 12,
            'deliverables' => [
                '総合課題を完了',
                'Laravelの基礎を完全にマスター'
            ],
        ]);

        $milestone7->tasks()->createMany([
            [
                'title' => '第12回：総合問題',
                'description' => 'Laravelの全機能を使った総合的なWebアプリケーションの構築',
                'sort_order' => 13,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [
                    '課題12',
                    'kadai12_1.blade.php',
                    'originalTheme.scss'
                ],
                'subtasks' => [
                    ['title' => '要件定義と設計', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'データベース設計とマイグレーション', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'ルーティングとコントローラ実装', 'estimated_minutes' => 80, 'sort_order' => 3],
                    ['title' => 'ビューとBladeテンプレート作成', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => 'バリデーションとエラーハンドリング', 'estimated_minutes' => 40, 'sort_order' => 5],
                    ['title' => 'テストとデバッグ', 'estimated_minutes' => 40, 'sort_order' => 6],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '総合問題の概要',
                        'content' => "# 総合問題の概要\n\nこの課題では、これまで学習した全ての技術を統合して、完全なWebアプリケーションを構築します。\n\n## 実装すべき機能\n\n### 1. 基本CRUD操作\n- **Create**: 記事の新規作成\n- **Read**: 一覧表示、詳細表示\n- **Update**: 記事の編集\n- **Delete**: 記事の削除\n\n### 2. データベース操作\n- マイグレーション\n- Eloquent ORM\n- リレーション（1対多、多対多など）\n- トランザクション\n\n### 3. バリデーション\n- フォームバリデーション\n- FormRequestクラス\n- カスタムバリデーションルール\n\n### 4. ファイルアップロード\n- 画像アップロード\n- ファイルバリデーション\n- ストレージ管理\n\n### 5. ユーザー体験\n- ページネーション\n- 検索機能\n- フィルタリング\n- ソート機能\n\n### 6. セキュリティ\n- CSRF保護\n- XSS対策\n- SQLインジェクション対策\n- 認証・認可（オプション）\n\n### 7. UI/UX\n- レスポンシブデザイン\n- エラーメッセージ表示\n- 成功メッセージ表示\n- ローディング表示",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '開発手順',
                        'content' => "# 開発手順\n\n## ステップ1: 要件定義（30分）\n- 作成するアプリケーションの仕様を決定\n- 必要な機能をリストアップ\n- データ構造を設計\n\n## ステップ2: データベース設計（30分）\n- ER図を作成\n- テーブル構造を決定\n- リレーションを定義\n\n## ステップ3: 環境構築（30分）\n```bash\n# プロジェクト作成\ncomposer create-project laravel/laravel project-name\ncd project-name\n\n# .env設定\nDB_DATABASE=your_database\nDB_USERNAME=your_username\nDB_PASSWORD=your_password\n\n# マイグレーション作成\nphp artisan make:model Article -mfs\nphp artisan make:model Category -mfs\n\n# マイグレーション実行\nphp artisan migrate\n\n# シーダー実行\nphp artisan db:seed\n```\n\n## ステップ4: ルーティング設計（30分）\n```php\n// routes/web.php\nRoute::resource('articles', ArticleController::class);\nRoute::get('articles/search', [ArticleController::class, 'search']);\n```\n\n## ステップ5: コントローラ実装（90分）\n- index(), create(), store(), show(), edit(), update(), destroy()\n- バリデーション\n- エラーハンドリング\n\n## ステップ6: ビュー作成（90分）\n- レイアウトファイル\n- 一覧画面\n- 詳細画面\n- 作成・編集フォーム\n\n## ステップ7: テスト（30分）\n- 各機能の動作確認\n- エラーケースの確認\n- レスポンシブ確認\n\n## ステップ8: リファクタリング（30分）\n- コードの整理\n- コメント追加\n- パフォーマンス最適化",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'サンプル：記事管理システムのマイグレーション',
                        'content' => "<?php\n// database/migrations/xxxx_create_categories_table.php\n\nSchema::create('categories', function (Blueprint \$table) {\n    \$table->id();\n    \$table->string('name');\n    \$table->string('slug')->unique();\n    \$table->text('description')->nullable();\n    \$table->timestamps();\n});\n\n// database/migrations/xxxx_create_articles_table.php\n\nSchema::create('articles', function (Blueprint \$table) {\n    \$table->id();\n    \$table->string('title');\n    \$table->string('slug')->unique();\n    \$table->text('content');\n    \$table->text('excerpt')->nullable();\n    \$table->string('image_path')->nullable();\n    \$table->foreignId('category_id')->constrained()->onDelete('cascade');\n    \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n    \$table->enum('status', ['draft', 'published', 'archived'])->default('draft');\n    \$table->integer('views')->default(0);\n    \$table->timestamp('published_at')->nullable();\n    \$table->timestamps();\n    \$table->softDeletes();\n    \n    \$table->index(['status', 'published_at']);\n    \$table->index('created_at');\n});\n\n// database/migrations/xxxx_create_tags_table.php\n\nSchema::create('tags', function (Blueprint \$table) {\n    \$table->id();\n    \$table->string('name');\n    \$table->string('slug')->unique();\n    \$table->timestamps();\n});\n\n// 多対多の中間テーブル\nSchema::create('article_tag', function (Blueprint \$table) {\n    \$table->foreignId('article_id')->constrained()->onDelete('cascade');\n    \$table->foreignId('tag_id')->constrained()->onDelete('cascade');\n    \$table->primary(['article_id', 'tag_id']);\n});",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'サンプル：モデルとリレーション',
                        'content' => "<?php\n// app/Models/Article.php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;\n\nclass Article extends Model\n{\n    use SoftDeletes;\n    \n    protected \$fillable = [\n        'title', 'slug', 'content', 'excerpt', \n        'image_path', 'category_id', 'user_id',\n        'status', 'published_at'\n    ];\n    \n    protected \$casts = [\n        'published_at' => 'datetime',\n    ];\n    \n    // リレーション\n    public function category()\n    {\n        return \$this->belongsTo(Category::class);\n    }\n    \n    public function user()\n    {\n        return \$this->belongsTo(User::class);\n    }\n    \n    public function tags()\n    {\n        return \$this->belongsToMany(Tag::class);\n    }\n    \n    // スコープ\n    public function scopePublished(\$query)\n    {\n        return \$query->where('status', 'published')\n            ->whereNotNull('published_at')\n            ->where('published_at', '<=', now());\n    }\n    \n    public function scopeLatest(\$query)\n    {\n        return \$query->orderBy('published_at', 'desc');\n    }\n    \n    // アクセサ\n    public function getExcerptAttribute(\$value)\n    {\n        return \$value ?: Str::limit(\$this->content, 150);\n    }\n}\n\n// app/Models/Category.php\n\nclass Category extends Model\n{\n    protected \$fillable = ['name', 'slug', 'description'];\n    \n    public function articles()\n    {\n        return \$this->hasMany(Article::class);\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'サンプル：完全なCRUDコントローラ',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Models\\Article;\nuse App\\Models\\Category;\nuse App\\Http\\Requests\\StoreArticleRequest;\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Storage;\nuse Illuminate\\Support\\Str;\n\nclass ArticleController extends Controller\n{\n    // 一覧表示（検索・フィルタ・ページネーション）\n    public function index(Request \$request)\n    {\n        \$query = Article::with('category', 'user')\n            ->published()\n            ->latest();\n        \n        // 検索\n        if (\$search = \$request->get('search')) {\n            \$query->where('title', 'like', \"%\$search%\")\n                ->orWhere('content', 'like', \"%\$search%\");\n        }\n        \n        // カテゴリフィルタ\n        if (\$categoryId = \$request->get('category')) {\n            \$query->where('category_id', \$categoryId);\n        }\n        \n        \$articles = \$query->paginate(12);\n        \$categories = Category::all();\n        \n        return view('articles.index', compact('articles', 'categories'));\n    }\n    \n    // 作成フォーム\n    public function create()\n    {\n        \$categories = Category::all();\n        return view('articles.create', compact('categories'));\n    }\n    \n    // 保存\n    public function store(StoreArticleRequest \$request)\n    {\n        \$data = \$request->validated();\n        \$data['slug'] = Str::slug(\$data['title']);\n        \$data['user_id'] = auth()->id();\n        \n        // 画像アップロード\n        if (\$request->hasFile('image')) {\n            \$data['image_path'] = \$request->file('image')\n                ->store('articles', 'public');\n        }\n        \n        \$article = Article::create(\$data);\n        \n        // タグを同期\n        if (\$request->has('tags')) {\n            \$article->tags()->sync(\$request->tags);\n        }\n        \n        return redirect()->route('articles.show', \$article)\n            ->with('success', '記事を作成しました');\n    }\n    \n    // 詳細表示\n    public function show(Article \$article)\n    {\n        \$article->load('category', 'user', 'tags');\n        \$article->increment('views');\n        \n        return view('articles.show', compact('article'));\n    }\n    \n    // 編集フォーム\n    public function edit(Article \$article)\n    {\n        \$categories = Category::all();\n        return view('articles.edit', compact('article', 'categories'));\n    }\n    \n    // 更新\n    public function update(StoreArticleRequest \$request, Article \$article)\n    {\n        \$data = \$request->validated();\n        \$data['slug'] = Str::slug(\$data['title']);\n        \n        // 画像アップロード\n        if (\$request->hasFile('image')) {\n            // 古い画像を削除\n            if (\$article->image_path) {\n                Storage::disk('public')->delete(\$article->image_path);\n            }\n            \n            \$data['image_path'] = \$request->file('image')\n                ->store('articles', 'public');\n        }\n        \n        \$article->update(\$data);\n        \n        if (\$request->has('tags')) {\n            \$article->tags()->sync(\$request->tags);\n        }\n        \n        return redirect()->route('articles.show', \$article)\n            ->with('success', '記事を更新しました');\n    }\n    \n    // 削除\n    public function destroy(Article \$article)\n    {\n        if (\$article->image_path) {\n            Storage::disk('public')->delete(\$article->image_path);\n        }\n        \n        \$article->delete();\n        \n        return redirect()->route('articles.index')\n            ->with('success', '記事を削除しました');\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'サンプル：FormRequest',
                        'content' => "<?php\n// app/Http/Requests/StoreArticleRequest.php\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass StoreArticleRequest extends FormRequest\n{\n    public function authorize(): bool\n    {\n        return true;\n    }\n    \n    public function rules(): array\n    {\n        \$articleId = \$this->route('article')?->id;\n        \n        return [\n            'title' => 'required|string|max:255',\n            'content' => 'required|string|min:100',\n            'excerpt' => 'nullable|string|max:500',\n            'category_id' => 'required|exists:categories,id',\n            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',\n            'status' => 'required|in:draft,published,archived',\n            'published_at' => 'nullable|date',\n            'tags' => 'nullable|array',\n            'tags.*' => 'exists:tags,id',\n        ];\n    }\n    \n    public function messages(): array\n    {\n        return [\n            'title.required' => 'タイトルは必須です',\n            'title.max' => 'タイトルは255文字以内で入力してください',\n            'content.required' => '本文は必須です',\n            'content.min' => '本文は100文字以上で入力してください',\n            'category_id.required' => 'カテゴリは必須です',\n            'category_id.exists' => '選択されたカテゴリが存在しません',\n            'image.image' => '画像ファイルを選択してください',\n            'image.max' => '画像は2MB以下にしてください',\n        ];\n    }\n    \n    public function attributes(): array\n    {\n        return [\n            'title' => 'タイトル',\n            'content' => '本文',\n            'excerpt' => '概要',\n            'category_id' => 'カテゴリ',\n            'image' => '画像',\n            'status' => 'ステータス',\n            'published_at' => '公開日時',\n            'tags' => 'タグ',\n        ];\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 6
                    },
                    [
                        'type' => 'note',
                        'title' => 'チェックリスト',
                        'content' => "# 総合問題チェックリスト\n\n## データベース\n- [ ] マイグレーションファイルが正しく作成されている\n- [ ] 外部キー制約が適切に設定されている\n- [ ] インデックスが必要な箇所に設定されている\n- [ ] シーダーでテストデータが作成できる\n\n## モデル\n- [ ] \$fillable または \$guarded が設定されている\n- [ ] リレーションが正しく定義されている\n- [ ] 日時のキャストが設定されている\n- [ ] スコープが適切に実装されている\n\n## ルーティング\n- [ ] RESTful なルート定義になっている\n- [ ] ルート名が設定されている\n- [ ] 必要に応じてミドルウェアが設定されている\n\n## コントローラ\n- [ ] 単一責任の原則に従っている\n- [ ] バリデーションが実装されている\n- [ ] エラーハンドリングが適切\n- [ ] リダイレクト時にフラッシュメッセージを設定\n\n## ビュー\n- [ ] レイアウトファイルで共通部分を管理\n- [ ] CSRF トークンが設定されている\n- [ ] old() ヘルパーで入力値を保持\n- [ ] エラーメッセージが表示される\n- [ ] レスポンシブデザインになっている\n\n## セキュリティ\n- [ ] CSRF 保護が有効\n- [ ] XSS 対策（{{ }} でエスケープ）\n- [ ] SQL インジェクション対策（Eloquent 使用）\n- [ ] ファイルアップロードのバリデーション\n- [ ] 認証・認可の実装（必要に応じて）\n\n## パフォーマンス\n- [ ] N+1 問題を回避（Eager Loading）\n- [ ] ページネーションを実装\n- [ ] 不要なクエリを削減\n\n## ユーザー体験\n- [ ] 検索機能が動作する\n- [ ] フィルタリングが動作する\n- [ ] ソート機能が動作する\n- [ ] ページネーションが動作する\n- [ ] 成功・エラーメッセージが表示される\n\n## コード品質\n- [ ] コードが読みやすい\n- [ ] 適切なコメントがある\n- [ ] 命名規則に従っている\n- [ ] DRY 原則に従っている\n\n## テスト\n- [ ] すべての機能が動作する\n- [ ] エラーケースも確認済み\n- [ ] 各ブラウザで動作確認\n- [ ] モバイルでの動作確認",
                        'sort_order' => 7
                    },
                    [
                        'type' => 'note',
                        'title' => 'よくあるエラーと解決方法',
                        'content' => "# よくあるエラーと解決方法\n\n## 1. 「Class not found」\n```bash\ncomposer dump-autoload\n```\n\n## 2. 「SQLSTATE[HY000] [2002] Connection refused」\n- .env のDB設定を確認\n- データベースが起動しているか確認\n\n## 3. 「The POST method is not supported for this route」\n- ルートとフォームのメソッドが一致しているか確認\n- @csrf トークンが設定されているか確認\n\n## 4. 「CSRF token mismatch」\n```blade\n@csrf\n```\nを忘れずに追加\n\n## 5. 「419 Page Expired」\n- セッションが切れた\n- ページをリロード\n\n## 6. 「Mass Assignment Exception」\n- \$fillable に追加\n```php\nprotected \$fillable = ['column_name'];\n```\n\n## 7. 「Column not found」\n- マイグレーションを確認\n```bash\nphp artisan migrate:fresh\n```\n\n## 8. 「Call to undefined relationship」\n- モデルにリレーションメソッドを定義\n\n## 9. 「File not found」\n```bash\nphp artisan storage:link\n```\n\n## 10. 「N+1 Query Problem」\n```php\n// 悪い例\n\$articles = Article::all();\nforeach (\$articles as \$article) {\n    echo \$article->user->name;  // N+1\n}\n\n// 良い例\n\$articles = Article::with('user')->get();\nforeach (\$articles as \$article) {\n    echo \$article->user->name;\n}\n```",
                        'sort_order' => 8
                    },
                ],
            ],
        ]);
    }
}

