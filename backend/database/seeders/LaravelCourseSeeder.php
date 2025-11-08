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
                        'title' => 'UPDATE操作',
                        'content' => "<?php\n// 方法1: update()メソッド\nArticle::where('id', 1)->update([\n    'title' => '更新されたタイトル',\n    'content' => '更新された内容'\n]);\n\n// 方法2: fill()とsave()\n\$article = Article::find(1);\n\$article->fill([\n    'title' => '更新されたタイトル',\n    'content' => '更新された内容'\n]);\n\$article->save();\n\n// 方法3: 直接プロパティに代入\n\$article = Article::find(1);\n\$article->title = '更新されたタイトル';\n\$article->content = '更新された内容';\n\$article->save();",
                        'code_language' => 'php',
                        'sort_order' => 1
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
                        'title' => 'DELETE操作',
                        'content' => "<?php\n// 方法1: delete()メソッド\n\$article = Article::find(1);\n\$article->delete();\n\n// 方法2: destroy()メソッド（ID指定）\nArticle::destroy(1);\nArticle::destroy([1, 2, 3]);\n\n// 方法3: 条件付き削除\nArticle::where('status', 'draft')->delete();\n\n// ソフトデリート（論理削除）\n// Modelに SoftDeletesトレイトを追加\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;\n\nclass Article extends Model\n{\n    use SoftDeletes;\n    protected \$dates = ['deleted_at'];\n}\n\n// ソフトデリートされたデータも含めて取得\n\$articles = Article::withTrashed()->get();\n\n// ソフトデリートされたデータのみ取得\n\$articles = Article::onlyTrashed()->get();",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
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
                        'type' => 'code_snippet',
                        'title' => 'RESTful APIの実装',
                        'content' => "<?php\n// routes/api.php\n\nuse App\\Http\\Controllers\\Api\\ArticleController;\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::apiResource('articles', ArticleController::class);\n\n// app/Http/Controllers/Api/ArticleController.php\n\nnamespace App\\Http\\Controllers\\Api;\n\nuse App\\Http\\Controllers\\Controller;\nuse App\\Models\\Article;\nuse Illuminate\\Http\\Request;\n\nclass ArticleController extends Controller\n{\n    public function index()\n    {\n        \$articles = Article::all();\n        return response()->json(\$articles);\n    }\n    \n    public function store(Request \$request)\n    {\n        \$article = Article::create(\$request->validated());\n        return response()->json(\$article, 201);\n    }\n    \n    public function show(\$id)\n    {\n        \$article = Article::findOrFail(\$id);\n        return response()->json(\$article);\n    }\n}",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
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
                        'type' => 'code_snippet',
                        'title' => 'ファイルアップロード',
                        'content' => "<?php\n// app/Http/Controllers/ArticleController.php\n\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Storage;\n\npublic function store(Request \$request)\n{\n    \$validated = \$request->validate([\n        'title' => 'required|string|max:255',\n        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',\n    ]);\n    \n    // ファイルを保存\n    \$imagePath = \$request->file('image')->store('images', 'public');\n    \n    // データベースに保存\n    \$article = Article::create([\n        'title' => \$validated['title'],\n        'image_path' => \$imagePath,\n    ]);\n    \n    return redirect()->route('articles.index');\n}\n\n// Bladeで画像表示\n<img src=\"{{ Storage::url(\$article->image_path) }}\" alt=\"{{ \$article->title }}\">",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
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
                        'title' => '総合問題のポイント',
                        'content' => "# 総合問題のポイント\n\n## 実装すべき機能\n- CRUD操作（作成、読み取り、更新、削除）\n- バリデーション\n- ファイルアップロード\n- ページネーション\n- 検索機能\n- 認証（オプション）\n\n## チェックリスト\n- [ ] ルーティングが適切に設定されているか\n- [ ] コントローラの処理が適切か\n- [ ] バリデーションが実装されているか\n- [ ] エラーハンドリングが適切か\n- [ ] ビューがレスポンシブか\n- [ ] セキュリティ対策が施されているか",
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);
    }
}

