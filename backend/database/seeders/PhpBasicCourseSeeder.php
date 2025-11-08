<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class PhpBasicCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * PHP基礎演習 - Webサーバー開発の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'PHP基礎演習',
            'description' => '初心者向けPHPプログラミング基礎コース。Webサーバー開発の基礎からデータベース接続、API開発まで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['php', 'web', 'サーバー', '基礎', 'データベース', 'api'],
            'icon' => 'ic_php',
            'color' => '#777BB4',
            'is_featured' => true,
        ]);

        // Milestone 1: 環境構築とPHPの基本 (第0回～第1回)
        $milestone1 = $template->milestones()->create([
            'title' => '環境構築とPHPの基本',
            'description' => '開発環境のセットアップからPHPの基本構文まで学習',
            'sort_order' => 1,
            'estimated_hours' => 8,
            'deliverables' => [
                '開発環境をセットアップ完了',
                'PHPの基本構文を理解',
                '変数と出力をマスター'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第0回：環境構築',
                'description' => 'PHP開発環境のセットアップ（XAMPP、MAMP、またはDocker）',
                'sort_order' => 1,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [
                    '環境構築資料',
                    'XAMPP/MAMP/Dockerのインストール'
                ],
                'subtasks' => [
                    ['title' => '開発環境をセットアップ', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'Webサーバーを起動', 'estimated_minutes' => 20, 'sort_order' => 2],
                    ['title' => 'Hello Worldプログラムを作成', 'estimated_minutes' => 40, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Hello Worldの基本',
                        'content' => "<?php\necho \"Hello World!\";\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'PHPの基本構文',
                        'content' => "# PHPの基本構文\n\n- `<?php` でPHPコードの開始\n- `?>` でPHPコードの終了（ファイル末尾では省略可能）\n- `echo` で出力\n- セミコロン（;）で文を終了",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第1回：PHPの基本',
                'description' => '変数、データ型、演算子、文字列操作の基本',
                'sort_order' => 2,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    'PHPの基本資料',
                    '課題01'
                ],
                'subtasks' => [
                    ['title' => '変数の宣言と代入', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'データ型の理解（string, int, float, bool）', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '演算子（+, -, *, /, %, .）', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '文字列の連結と出力', 'estimated_minutes' => 30, 'sort_order' => 4],
                    ['title' => '課題01を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '変数と演算子の例',
                        'content' => "<?php\n\$name = \"田中\";\n\$age = 25;\n\$price = 1000;\n\$tax = \$price * 0.1;\n\$total = \$price + \$tax;\necho \"名前: \" . \$name . \"<br>\";\necho \"年齢: \" . \$age . \"<br>\";\necho \"合計: \" . \$total . \"円\";\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'PHPの変数',
                        'content' => "# PHPの変数\n\n- 変数は `$` で始まる\n- 型の宣言は不要（動的型付け）\n- 文字列の連結は `.` 演算子を使用\n- `echo` または `print` で出力",
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 2: 配列とフォーム処理 (第2回～第4回)
        $milestone2 = $template->milestones()->create([
            'title' => '配列とフォーム処理',
            'description' => '配列、連想配列、定数、ファイル読み込み、フォーム処理',
            'sort_order' => 2,
            'estimated_hours' => 18,
            'deliverables' => [
                '配列と連想配列を操作できる',
                '定数とファイル読み込みを理解',
                'フォームからデータを受け取れる'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第2回：配列・連想配列',
                'description' => '配列と連想配列の宣言、操作、ループ処理',
                'sort_order' => 3,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    '配列・連想配列資料',
                    '課題02'
                ],
                'subtasks' => [
                    ['title' => '配列の基本（array(), []）', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '配列の要素へのアクセス', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '連想配列の基本', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => 'foreach文で配列をループ', 'estimated_minutes' => 30, 'sort_order' => 4],
                    ['title' => '課題02を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '配列と連想配列の例',
                        'content' => "<?php\n// 通常の配列\n\$fruits = ['りんご', 'バナナ', 'オレンジ'];\necho \$fruits[0]; // りんご\n\n// 連想配列\n\$person = [\n    'name' => '田中',\n    'age' => 25,\n    'city' => '東京'\n];\necho \$person['name']; // 田中\n\n// foreach文\nforeach (\$fruits as \$fruit) {\n    echo \$fruit . \"<br>\";\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第3回：定数・別ファイル読み込み・フォーム処理①',
                'description' => '定数の定義、require/include、$_GET/$_POSTの基本',
                'sort_order' => 4,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    '定数・別ファイル読み込み・フォーム処理①資料',
                    '課題03'
                ],
                'subtasks' => [
                    ['title' => '定数の定義（define, const）', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'require/includeでファイル読み込み', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '$_GETでデータを受け取る', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '$_POSTでデータを受け取る', 'estimated_minutes' => 40, 'sort_order' => 4],
                    ['title' => '課題03を完了', 'estimated_minutes' => 30, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'フォーム処理の例',
                        'content' => "<?php\n// GETパラメータ\n\$name = \$_GET['name'] ?? '';\n\$age = \$_GET['age'] ?? 0;\n\n// POSTパラメータ\n\$email = \$_POST['email'] ?? '';\n\n// 定数\ndefine('SITE_NAME', 'My Site');\nconst MAX_USERS = 100;\n\n// ファイル読み込み\nrequire 'config.php';\ninclude 'header.php';\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第4回：フォーム処理②',
                'description' => 'フォームのバリデーション、エラーハンドリング、セキュリティ対策',
                'sort_order' => 5,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    'フォーム処理②資料',
                    '課題04',
                    'kadai04_1.php',
                    'kadai04_2.php',
                    'kadai04_utils.php'
                ],
                'subtasks' => [
                    ['title' => 'フォームのバリデーション', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'エラーメッセージの表示', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'XSS対策（htmlspecialchars）', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '複数ページのフォーム処理', 'estimated_minutes' => 30, 'sort_order' => 4],
                    ['title' => '課題04を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'セキュアなフォーム処理',
                        'content' => "<?php\n// XSS対策\n\$name = htmlspecialchars(\$_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');\n\n// バリデーション\n\$errors = [];\nif (empty(\$name)) {\n    \$errors[] = '名前を入力してください';\n}\nif (!filter_var(\$_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {\n    \$errors[] = '正しいメールアドレスを入力してください';\n}\n\n// エラーがない場合の処理\nif (empty(\$errors)) {\n    // データ処理\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 3: ファイルアップロードとセッション管理 (第5回～第6回)
        $milestone3 = $template->milestones()->create([
            'title' => 'ファイルアップロードとセッション管理',
            'description' => 'ファイルアップロード、クッキー、セッション管理',
            'sort_order' => 3,
            'estimated_hours' => 16,
            'deliverables' => [
                'ファイルをアップロードできる',
                'クッキーとセッションを管理できる',
                'ログイン機能を実装できる'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '第5回：ファイルアップロード',
                'description' => '$_FILESを使ったファイルアップロード処理、画像のアップロードと表示',
                'sort_order' => 6,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'ファイルアップロード資料',
                    '課題05',
                    'kadai05_1.php',
                    'kadai05_2.php'
                ],
                'subtasks' => [
                    ['title' => '$_FILESの基本', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'ファイルのアップロード処理', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'ファイルサイズとタイプのチェック', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => '画像の表示', 'estimated_minutes' => 50, 'sort_order' => 4],
                    ['title' => '課題05を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイルアップロードの例',
                        'content' => "<?php\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    if (isset(\$_FILES['image'])) {\n        \$file = \$_FILES['image'];\n        \$uploadDir = 'uploads/';\n        \$fileName = uniqid() . '_' . \$file['name'];\n        \$targetPath = \$uploadDir . \$fileName;\n        \n        if (move_uploaded_file(\$file['tmp_name'], \$targetPath)) {\n            echo 'アップロード成功';\n        }\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第6回：クッキー・セッション',
                'description' => 'クッキーとセッションの設定、取得、削除、ログイン機能の実装',
                'sort_order' => 7,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'クッキー・セッション資料',
                    '課題06',
                    'kadai06_1.php',
                    'kadai06_2.php',
                    'kadai06_3.php',
                    'kadai06_4.php',
                    'kadai06_resource.php'
                ],
                'subtasks' => [
                    ['title' => 'クッキーの設定と取得', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'セッションの開始と管理', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => 'ログイン機能の実装', 'estimated_minutes' => 70, 'sort_order' => 3],
                    ['title' => 'セッションを使った認証', 'estimated_minutes' => 40, 'sort_order' => 4],
                    ['title' => '課題06を完了', 'estimated_minutes' => 30, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'セッション管理の例',
                        'content' => "<?php\nsession_start();\n\n// ログイン処理\nif (\$_POST['username'] === 'admin' && \$_POST['password'] === 'pass') {\n    \$_SESSION['user_id'] = 1;\n    \$_SESSION['username'] = 'admin';\n    header('Location: index.php');\n    exit;\n}\n\n// 認証チェック\nif (!isset(\$_SESSION['user_id'])) {\n    header('Location: login.php');\n    exit;\n}\n\n// ログアウト\nsession_destroy();\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 4: データベース接続基礎 (第8回～第10回)
        $milestone4 = $template->milestones()->create([
            'title' => 'データベース接続基礎',
            'description' => 'MySQLへの接続、SELECT、INSERT操作',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                'データベースに接続できる',
                'SELECT文でデータを取得できる',
                'INSERT文でデータを追加できる'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第8回：DB接続（SELECT）',
                'description' => 'PDOを使ったデータベース接続、SELECT文でデータ取得',
                'sort_order' => 8,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'DB接続（SELECT）資料',
                    '課題08',
                    'kadai08_1.php'
                ],
                'subtasks' => [
                    ['title' => 'PDOの基本接続', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'SELECT文の実行', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'fetchAll()でデータ取得', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => 'テーブルにデータを表示', 'estimated_minutes' => 50, 'sort_order' => 4],
                    ['title' => '課題08を完了', 'estimated_minutes' => 30, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'PDO接続とSELECTの例',
                        'content' => "<?php\ntry {\n    \$pdo = new PDO(\n        'mysql:host=localhost;dbname=mydb;charset=utf8mb4',\n        'username',\n        'password',\n        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\n    );\n    \n    \$stmt = \$pdo->query('SELECT * FROM users');\n    \$users = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n    \n    foreach (\$users as \$user) {\n        echo \$user['name'] . '<br>';\n    }\n} catch (PDOException \$e) {\n    echo 'エラー: ' . \$e->getMessage();\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第9回：DB接続（SELECT②条件付き）',
                'description' => 'WHERE句、プレースホルダー、プリペアドステートメント',
                'sort_order' => 9,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    '課題09'
                ],
                'subtasks' => [
                    ['title' => 'WHERE句の使い方', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'プレースホルダー（:name）', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => 'プリペアドステートメント', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => 'SQLインジェクション対策', 'estimated_minutes' => 20, 'sort_order' => 4],
                    ['title' => '課題09を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'プリペアドステートメントの例',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// プリペアドステートメント\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE id = :id');\n\$stmt->execute(['id' => \$userId]);\n\$user = \$stmt->fetch(PDO::FETCH_ASSOC);\n\n// 複数条件\n\$stmt = \$pdo->prepare('SELECT * FROM products WHERE price > :min AND category = :cat');\n\$stmt->execute(['min' => 1000, 'cat' => 'electronics']);\n\$products = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第10回：DB接続（INSERT）',
                'description' => 'INSERT文でデータを追加、フォームからデータベースへの登録',
                'sort_order' => 10,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    'DB接続（INSERT）資料',
                    '課題10',
                    'kadai10_1.php',
                    'kadai10_2.php'
                ],
                'subtasks' => [
                    ['title' => 'INSERT文の基本', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'フォームデータをINSERT', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'バリデーションとエラーハンドリング', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '登録後のリダイレクト', 'estimated_minutes' => 40, 'sort_order' => 4],
                    ['title' => '課題10を完了', 'estimated_minutes' => 40, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'INSERT文の例',
                        'content' => "<?php\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$name = \$_POST['name'] ?? '';\n    \$email = \$_POST['email'] ?? '';\n    \n    try {\n        \$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n        \$stmt = \$pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');\n        \$stmt->execute(['name' => \$name, 'email' => \$email]);\n        \n        header('Location: success.php');\n        exit;\n    } catch (PDOException \$e) {\n        \$error = '登録に失敗しました';\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 5: データベース操作応用 (第11回～第12回)
        $milestone5 = $template->milestones()->create([
            'title' => 'データベース操作応用',
            'description' => 'UPDATE、DELETE、JSON処理',
            'sort_order' => 5,
            'estimated_hours' => 18,
            'deliverables' => [
                'UPDATE文でデータを更新できる',
                'DELETE文でデータを削除できる',
                'JSONデータを処理できる'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => '第11回：UPDATE・DELETE・Extra',
                'description' => 'UPDATE文、DELETE文、トランザクション処理',
                'sort_order' => 11,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [
                    '課題11',
                    'kadai11_1.php',
                    'kadai11_2.php',
                    'kadai11_3.php',
                    'kadai11_4.php'
                ],
                'subtasks' => [
                    ['title' => 'UPDATE文の基本', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'フォームからデータを更新', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'DELETE文の基本', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => 'トランザクション処理', 'estimated_minutes' => 50, 'sort_order' => 4],
                    ['title' => '課題11を完了', 'estimated_minutes' => 30, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'UPDATEとDELETEの例',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// UPDATE\n\$stmt = \$pdo->prepare('UPDATE users SET name = :name WHERE id = :id');\n\$stmt->execute(['name' => '新しい名前', 'id' => 1]);\n\n// DELETE\n\$stmt = \$pdo->prepare('DELETE FROM users WHERE id = :id');\n\$stmt->execute(['id' => 1]);\n\n// トランザクション\n\$pdo->beginTransaction();\ntry {\n    \$pdo->exec('UPDATE accounts SET balance = balance - 1000 WHERE id = 1');\n    \$pdo->exec('UPDATE accounts SET balance = balance + 1000 WHERE id = 2');\n    \$pdo->commit();\n} catch (Exception \$e) {\n    \$pdo->rollBack();\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第12回：JSON',
                'description' => 'JSONのエンコード・デコード、JSON APIの作成',
                'sort_order' => 12,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [
                    'JSON資料',
                    '課題12',
                    'kadai12_1.php'
                ],
                'subtasks' => [
                    ['title' => 'json_encode()とjson_decode()', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => '配列をJSONに変換', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'JSON APIエンドポイントの作成', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => 'Content-Typeヘッダーの設定', 'estimated_minutes' => 20, 'sort_order' => 4],
                    ['title' => '課題12を完了', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'JSON処理の例',
                        'content' => "<?php\n// 配列をJSONに変換\n\$data = [\n    'name' => '田中',\n    'age' => 25,\n    'city' => '東京'\n];\n\$json = json_encode(\$data, JSON_UNESCAPED_UNICODE);\necho \$json;\n\n// JSONを配列に変換\n\$jsonString = '{\"name\":\"田中\",\"age\":25}';\n\$array = json_decode(\$jsonString, true);\necho \$array['name'];\n\n// APIエンドポイント\nheader('Content-Type: application/json; charset=utf-8');\n\$users = [/* データベースから取得 */];\necho json_encode(\$users, JSON_UNESCAPED_UNICODE);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 6: API開発 (第13回～第14回)
        $milestone6 = $template->milestones()->create([
            'title' => 'API開発',
            'description' => 'RESTful APIの作成、フロントエンドとの連携',
            'sort_order' => 6,
            'estimated_hours' => 16,
            'deliverables' => [
                'RESTful APIを実装できる',
                'フロントエンドとAPIを連携できる',
                'CRUD操作のAPIを作成できる'
            ],
        ]);

        $milestone6->tasks()->createMany([
            [
                'title' => '第13回～第14回：自作API',
                'description' => 'RESTful APIの設計と実装、フロントエンド（JavaScript）との連携',
                'sort_order' => 13,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [
                    '自作API資料',
                    'kadai13_resource.php',
                    'PHP1_Front（デスクトップに配置）'
                ],
                'subtasks' => [
                    ['title' => 'RESTful APIの設計', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'GET APIの実装', 'estimated_minutes' => 80, 'sort_order' => 2],
                    ['title' => 'POST APIの実装', 'estimated_minutes' => 80, 'sort_order' => 3],
                    ['title' => 'PUT/PATCH APIの実装', 'estimated_minutes' => 80, 'sort_order' => 4],
                    ['title' => 'DELETE APIの実装', 'estimated_minutes' => 60, 'sort_order' => 5],
                    ['title' => 'フロントエンドとの連携', 'estimated_minutes' => 80, 'sort_order' => 6],
                    ['title' => 'エラーハンドリングとバリデーション', 'estimated_minutes' => 40, 'sort_order' => 7],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'RESTful APIの例',
                        'content' => "<?php\nheader('Content-Type: application/json; charset=utf-8');\nheader('Access-Control-Allow-Origin: *');\n\n\$method = \$_SERVER['REQUEST_METHOD'];\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\nswitch (\$method) {\n    case 'GET':\n        \$stmt = \$pdo->query('SELECT * FROM products');\n        \$products = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n        echo json_encode(\$products, JSON_UNESCAPED_UNICODE);\n        break;\n    \n    case 'POST':\n        \$data = json_decode(file_get_contents('php://input'), true);\n        \$stmt = \$pdo->prepare('INSERT INTO products (name, price) VALUES (:name, :price)');\n        \$stmt->execute(['name' => \$data['name'], 'price' => \$data['price']]);\n        echo json_encode(['success' => true, 'id' => \$pdo->lastInsertId()]);\n        break;\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'RESTful APIのベストプラクティス',
                        'content' => "# RESTful APIのベストプラクティス\n\n- HTTPメソッドを適切に使用（GET, POST, PUT, DELETE）\n- 適切なHTTPステータスコードを返す（200, 201, 400, 404, 500）\n- JSON形式でデータを返す\n- CORSヘッダーを設定（必要に応じて）\n- エラーハンドリングを実装\n- バリデーションを必ず行う",
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);
    }
}

