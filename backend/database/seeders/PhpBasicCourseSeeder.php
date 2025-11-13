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
                    [
                        'type' => 'note',
                        'title' => '開発環境の選択肢',
                        'content' => "# 開発環境の選択肢\n\n## XAMPP（クロスプラットフォーム）\n- Apache、MySQL、PHP、Perlを含む統合パッケージ\n- Windows、Mac、Linux対応\n- 初心者に最適\n\n## MAMP（Mac、Windows）\n- Mac/Windows向けのローカルサーバー環境\n- GUI操作が簡単\n- Pro版（有料）もあり\n\n## Docker（推奨）\n- コンテナ化された環境\n- 本番環境に近い構成が可能\n- チーム開発に最適\n- ポータビリティが高い",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'phpinfo()で環境確認',
                        'content' => "<?php\n// PHP環境の確認\nphpinfo();\n\n// 特定の情報のみ表示\nphpinfo(INFO_GENERAL);\n\n// PHPバージョンのみ表示\necho phpversion();\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'よくあるトラブルシューティング',
                        'content' => "# よくあるトラブルシューティング\n\n## Apacheが起動しない\n- ポート80が他のアプリケーションで使用されていないか確認\n- Skype、IIS、他のWebサーバーを停止してみる\n\n## PHPファイルがダウンロードされる\n- Apache設定でPHPモジュールが有効化されているか確認\n- .phpファイルがdocumentRoot配下にあるか確認\n\n## 文字化け\n- ファイルのエンコーディングをUTF-8に設定\n- `header('Content-Type: text/html; charset=utf-8');` を追加",
                        'sort_order' => 5
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
                    [
                        'type' => 'note',
                        'title' => 'PHPのデータ型',
                        'content' => "# PHPのデータ型\n\n## スカラー型\n- **string**: 文字列 `\"Hello\"` または `'Hello'`\n- **int**: 整数 `123`, `-456`\n- **float**: 浮動小数点数 `3.14`, `2.5`\n- **bool**: 真偽値 `true`, `false`\n\n## 複合型\n- **array**: 配列 `[1, 2, 3]`\n- **object**: オブジェクト\n- **callable**: 呼び出し可能\n- **iterable**: 反復可能\n\n## 特殊型\n- **null**: NULL値\n- **resource**: リソース",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'データ型の確認と変換',
                        'content' => "<?php\n// 型の確認\n\$value = 123;\nvar_dump(\$value); // int(123)\necho gettype(\$value); // integer\n\n// 型チェック関数\nis_string(\$value);  // false\nis_int(\$value);     // true\nis_float(\$value);   // false\nis_bool(\$value);    // false\nis_array(\$value);   // false\nis_null(\$value);    // false\n\n// 型変換（キャスト）\n\$str = \"123\";\n\$num = (int)\$str;      // 文字列を整数に\n\$float = (float)\$str;  // 文字列を浮動小数点数に\n\$bool = (bool)\$str;    // 文字列を真偽値に\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => '演算子の優先順位',
                        'content' => "# 演算子の優先順位\n\n## 算術演算子（高→低）\n1. `**` べき乗\n2. `*`, `/`, `%` 乗算、除算、剰余\n3. `+`, `-` 加算、減算\n4. `.` 文字列連結\n\n## 比較演算子\n- `==` 等しい（型変換あり）\n- `===` 厳密に等しい（型も一致）\n- `!=`, `<>` 等しくない\n- `!==` 厳密に等しくない\n- `<`, `>`, `<=`, `>=`\n\n## 論理演算子\n- `&&`, `and` 論理積\n- `||`, `or` 論理和\n- `!` 否定\n- `xor` 排他的論理和",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '文字列操作の詳細',
                        'content' => "<?php\n// 文字列の連結\n\$firstName = \"太郎\";\n\$lastName = \"山田\";\n\$fullName = \$lastName . \" \" . \$firstName; // 山田 太郎\n\n// 文字列補間（ダブルクォート）\n\$age = 25;\necho \"私は{\$age}歳です\"; // 私は25歳です\necho \"私は\$age歳です\";     // 私は25歳です\n\n// シングルクォートでは変数展開されない\necho '私は\$age歳です';      // 私は\$age歳です\n\n// ヒアドキュメント（複数行文字列）\n\$html = <<<HTML\n<div>\n    <h1>こんにちは</h1>\n    <p>年齢: \$age</p>\n</div>\nHTML;\n\n// 文字列関数\nstrlen(\$fullName);           // 文字列の長さ\nmb_strlen(\$fullName);        // マルチバイト対応\nstrtoupper(\"hello\");         // HELLO\nstrtolower(\"HELLO\");         // hello\nstr_replace(\"太郎\", \"花子\", \$fullName);\nsubstr(\"Hello\", 0, 3);       // Hel\nmb_substr(\"こんにちは\", 0, 2); // こん\n?>",
                        'code_language' => 'php',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => '変数のスコープ',
                        'content' => "# 変数のスコープ\n\n## ローカルスコープ\n- 関数内で定義された変数は関数内でのみ有効\n\n## グローバルスコープ\n- 関数外で定義された変数\n- 関数内で使用するには `global` キーワードが必要\n\n## スーパーグローバル変数\n- `\$_GET`, `\$_POST`, `\$_REQUEST`\n- `\$_SERVER`, `\$_SESSION`, `\$_COOKIE`\n- `\$_FILES`, `\$_ENV`\n- `\$GLOBALS`\n\nこれらはどこからでもアクセス可能",
                        'sort_order' => 7
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
                    [
                        'type' => 'note',
                        'title' => '配列の宣言方法',
                        'content' => "# 配列の宣言方法\n\n## 短縮構文（推奨）\n```php\n\$array = [1, 2, 3];\n\$assoc = ['key' => 'value'];\n```\n\n## 従来の構文\n```php\n\$array = array(1, 2, 3);\n\$assoc = array('key' => 'value');\n```\n\n## 空配列\n```php\n\$empty = [];\n\$empty = array();\n```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '配列の操作',
                        'content' => "<?php\n// 要素の追加\n\$fruits = ['りんご', 'バナナ'];\n\$fruits[] = 'オレンジ';           // 末尾に追加\narray_push(\$fruits, 'ぶどう');    // 末尾に追加\narray_unshift(\$fruits, 'いちご'); // 先頭に追加\n\n// 要素の削除\n\$last = array_pop(\$fruits);       // 末尾を削除して返す\n\$first = array_shift(\$fruits);    // 先頭を削除して返す\nunset(\$fruits[1]);                // インデックス指定で削除\n\n// 要素の検索\nin_array('りんご', \$fruits);      // 存在チェック\narray_search('バナナ', \$fruits);  // インデックスを返す\narray_key_exists('name', \$person); // キーの存在チェック\n\n// 配列の結合\n\$array1 = [1, 2];\n\$array2 = [3, 4];\n\$merged = array_merge(\$array1, \$array2); // [1, 2, 3, 4]\n\n// 配列の分割\n\$chunk = array_slice(\$fruits, 0, 2); // 最初の2要素\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '多次元配列',
                        'content' => "<?php\n// 2次元配列\n\$users = [\n    ['name' => '田中', 'age' => 25, 'city' => '東京'],\n    ['name' => '佐藤', 'age' => 30, 'city' => '大阪'],\n    ['name' => '鈴木', 'age' => 28, 'city' => '名古屋']\n];\n\n// アクセス\necho \$users[0]['name'];  // 田中\necho \$users[1]['age'];   // 30\n\n// foreachでループ\nforeach (\$users as \$user) {\n    echo \$user['name'] . \" (\" . \$user['age'] . \"歳)<br>\";\n}\n\n// キーと値を両方取得\nforeach (\$users as \$index => \$user) {\n    echo \"\$index: \" . \$user['name'] . \"<br>\";\n}\n\n// 連想配列のforeachでキーと値を取得\n\$person = ['name' => '田中', 'age' => 25];\nforeach (\$person as \$key => \$value) {\n    echo \"\$key: \$value<br>\";\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => '便利な配列関数',
                        'content' => "# 便利な配列関数\n\n## 情報取得\n- `count(\$array)`: 要素数を取得\n- `sizeof(\$array)`: count()のエイリアス\n- `empty(\$array)`: 空かチェック\n\n## 並べ替え\n- `sort(\$array)`: 昇順ソート（インデックス再割り当て）\n- `rsort(\$array)`: 降順ソート\n- `asort(\$array)`: 値で昇順ソート（キー保持）\n- `ksort(\$array)`: キーで昇順ソート\n- `usort(\$array, callable)`: カスタムソート\n\n## 変換\n- `array_keys(\$array)`: キーの配列を取得\n- `array_values(\$array)`: 値の配列を取得\n- `array_reverse(\$array)`: 逆順にする\n- `implode(', ', \$array)`: 配列を文字列に\n- `explode(', ', \$string)`: 文字列を配列に",
                        'sort_order' => 5
                    },
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
                    [
                        'type' => 'note',
                        'title' => '定数の定義方法',
                        'content' => "# 定数の定義方法\n\n## define()関数\n```php\ndefine('CONSTANT_NAME', 'value');\ndefine('DB_HOST', 'localhost');\ndefine('MAX_SIZE', 1000);\n```\n- 関数なので条件分岐内でも使用可能\n- 大文字小文字を区別しないオプションあり（非推奨）\n\n## constキーワード\n```php\nconst CONSTANT_NAME = 'value';\nconst DB_HOST = 'localhost';\nconst MAX_SIZE = 1000;\n```\n- コンパイル時に評価される\n- クラス内でも使用可能\n- 読みやすい（推奨）\n\n## 定数の使用\n```php\necho CONSTANT_NAME;\necho DB_HOST;\n```\n\n## マジック定数\n- `__FILE__`: 現在のファイルパス\n- `__DIR__`: 現在のディレクトリ\n- `__LINE__`: 現在の行番号\n- `__FUNCTION__`: 現在の関数名\n- `__CLASS__`: 現在のクラス名",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => 'require vs include',
                        'content' => "# require vs include\n\n## require\n- ファイルが見つからない場合、致命的エラー（Fatal Error）\n- スクリプトが停止する\n- 必須ファイルに使用（設定ファイルなど）\n\n## include\n- ファイルが見つからない場合、警告（Warning）のみ\n- スクリプトは続行される\n- オプショナルなファイルに使用\n\n## require_once / include_once\n- 同じファイルを複数回読み込まない\n- クラス定義や関数定義の重複を防ぐ\n\n```php\nrequire 'config.php';        // 必須\nrequire_once 'functions.php'; // 1回のみ\ninclude 'header.php';        // オプショナル\ninclude_once 'utils.php';    // 1回のみ\n```",
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '$_GETと$_POSTの詳細',
                        'content' => "<?php\n// GETリクエスト（URL: page.php?name=田中&age=25）\n\$name = \$_GET['name'] ?? 'ゲスト';  // Null合体演算子\n\$age = \$_GET['age'] ?? 0;\n\n// POSTリクエスト（フォーム送信）\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$username = \$_POST['username'] ?? '';\n    \$password = \$_POST['password'] ?? '';\n}\n\n// REQUESTスーパーグローバル（GET + POST + COOKIE）\n\$value = \$_REQUEST['key'] ?? '';\n\n// isset()で存在チェック\nif (isset(\$_GET['id'])) {\n    \$id = \$_GET['id'];\n}\n\n// empty()で空チェック\nif (!empty(\$_POST['email'])) {\n    \$email = \$_POST['email'];\n}\n\n// filter_input()でサニタイズ（推奨）\n\$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);\n\$age = filter_input(INPUT_GET, 'age', FILTER_VALIDATE_INT);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'HTMLフォームの例',
                        'content' => "<!-- GETメソッド -->\n<form action=\"process.php\" method=\"GET\">\n    <input type=\"text\" name=\"search\" placeholder=\"検索\">\n    <button type=\"submit\">検索</button>\n</form>\n\n<!-- POSTメソッド -->\n<form action=\"login.php\" method=\"POST\">\n    <input type=\"text\" name=\"username\" required>\n    <input type=\"password\" name=\"password\" required>\n    <button type=\"submit\">ログイン</button>\n</form>\n\n<!-- 複数の値を送信 -->\n<form action=\"submit.php\" method=\"POST\">\n    <input type=\"checkbox\" name=\"hobbies[]\" value=\"reading\"> 読書<br>\n    <input type=\"checkbox\" name=\"hobbies[]\" value=\"sports\"> スポーツ<br>\n    <select name=\"country\">\n        <option value=\"jp\">日本</option>\n        <option value=\"us\">アメリカ</option>\n    </select>\n    <button type=\"submit\">送信</button>\n</form>",
                        'code_language' => 'html',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'note',
                        'title' => 'GETとPOSTの使い分け',
                        'content' => "# GETとPOSTの使い分け\n\n## GETメソッド\n**使用場面：**\n- 検索フォーム\n- フィルタリング\n- ページネーション\n- ブックマーク可能なURL\n\n**特徴：**\n- URLにパラメータが表示される\n- ブラウザ履歴に残る\n- データサイズに制限あり（約2KB）\n- キャッシュされる\n- べき等（何度実行しても同じ結果）\n\n## POSTメソッド\n**使用場面：**\n- ログインフォーム\n- ユーザー登録\n- データの作成・更新・削除\n- ファイルアップロード\n\n**特徴：**\n- URLにパラメータが表示されない\n- セキュアな情報の送信に適している\n- データサイズ制限なし（php.iniで設定可能）\n- キャッシュされない\n- べき等ではない（実行ごとに状態が変わる）",
                        'sort_order' => 6
                    },
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
                    [
                        'type' => 'note',
                        'title' => 'バリデーションのパターン',
                        'content' => "# バリデーションのパターン\n\n## 必須チェック\n```php\nif (empty(\$value)) {\n    \$errors[] = 'この項目は必須です';\n}\n```\n\n## 文字数チェック\n```php\nif (mb_strlen(\$name) < 2 || mb_strlen(\$name) > 50) {\n    \$errors[] = '名前は2〜50文字で入力してください';\n}\n```\n\n## メールアドレス\n```php\nif (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {\n    \$errors[] = '正しいメールアドレスを入力してください';\n}\n```\n\n## 数値チェック\n```php\nif (!is_numeric(\$age) || \$age < 0 || \$age > 150) {\n    \$errors[] = '正しい年齢を入力してください';\n}\n```\n\n## 正規表現\n```php\n// 電話番号（ハイフンなし）\nif (!preg_match('/^0\\d{9,10}\$/', \$phone)) {\n    \$errors[] = '正しい電話番号を入力してください';\n}\n\n// パスワード（8文字以上、英数字含む）\nif (!preg_match('/^(?=.*[A-Za-z])(?=.*\\d)[A-Za-z\\d]{8,}\$/', \$password)) {\n    \$errors[] = 'パスワードは8文字以上の英数字で入力してください';\n}\n```",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => 'XSS（クロスサイトスクリプティング）対策',
                        'content' => "# XSS対策\n\n## htmlspecialchars()の使用\n```php\n\$safe = htmlspecialchars(\$input, ENT_QUOTES, 'UTF-8');\n```\n\n**変換される文字：**\n- `&` → `&amp;`\n- `\"` → `&quot;`\n- `'` → `&#039;`\n- `<` → `&lt;`\n- `>` → `&gt;`\n\n## ENT_QUOTESフラグ\n- ダブルクォートとシングルクォートの両方をエスケープ\n- 必ず指定すること\n\n## UTF-8エンコーディング\n- 第3引数で文字エンコーディングを指定\n- 省略すると予期しない動作の可能性\n\n## 使用タイミング\n- **入力時ではなく出力時**にエスケープ\n- データベースには元の値を保存\n- HTMLに出力する直前にエスケープ",
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '完全なフォーム処理の例',
                        'content' => "<?php\n// フォーム処理\n\$errors = [];\n\$name = '';\n\$email = '';\n\$age = '';\n\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    // 入力値の取得\n    \$name = trim(\$_POST['name'] ?? '');\n    \$email = trim(\$_POST['email'] ?? '');\n    \$age = trim(\$_POST['age'] ?? '');\n    \n    // バリデーション\n    if (empty(\$name)) {\n        \$errors['name'] = '名前を入力してください';\n    } elseif (mb_strlen(\$name) > 50) {\n        \$errors['name'] = '名前は50文字以内で入力してください';\n    }\n    \n    if (empty(\$email)) {\n        \$errors['email'] = 'メールアドレスを入力してください';\n    } elseif (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {\n        \$errors['email'] = '正しいメールアドレスを入力してください';\n    }\n    \n    if (empty(\$age)) {\n        \$errors['age'] = '年齢を入力してください';\n    } elseif (!is_numeric(\$age) || \$age < 0 || \$age > 150) {\n        \$errors['age'] = '正しい年齢を入力してください';\n    }\n    \n    // エラーがなければ処理実行\n    if (empty(\$errors)) {\n        // データベースへの保存など\n        header('Location: success.php');\n        exit;\n    }\n}\n?>\n\n<!-- HTMLフォーム -->\n<form method=\"POST\">\n    <div>\n        <label>名前:</label>\n        <input type=\"text\" name=\"name\" value=\"<?= htmlspecialchars(\$name, ENT_QUOTES, 'UTF-8') ?>\">\n        <?php if (isset(\$errors['name'])): ?>\n            <span class=\"error\"><?= htmlspecialchars(\$errors['name'], ENT_QUOTES, 'UTF-8') ?></span>\n        <?php endif; ?>\n    </div>\n    \n    <div>\n        <label>メールアドレス:</label>\n        <input type=\"email\" name=\"email\" value=\"<?= htmlspecialchars(\$email, ENT_QUOTES, 'UTF-8') ?>\">\n        <?php if (isset(\$errors['email'])): ?>\n            <span class=\"error\"><?= htmlspecialchars(\$errors['email'], ENT_QUOTES, 'UTF-8') ?></span>\n        <?php endif; ?>\n    </div>\n    \n    <button type=\"submit\">送信</button>\n</form>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'note',
                        'title' => 'セキュリティのベストプラクティス',
                        'content' => "# セキュリティのベストプラクティス\n\n## 1. 入力値の検証\n- **ホワイトリスト方式**を採用\n- 期待される値のみを受け入れる\n- サーバー側で必ず検証（クライアント側だけでは不十分）\n\n## 2. XSS対策\n- 出力時に `htmlspecialchars()` を使用\n- `ENT_QUOTES` フラグを指定\n- UTF-8エンコーディングを明示\n\n## 3. CSRF対策\n- トークンを生成してフォームに埋め込む\n- 送信時にトークンを検証\n\n## 4. SQLインジェクション対策\n- プリペアドステートメントを使用\n- 直接SQLに値を埋め込まない\n\n## 5. パスワードのハッシュ化\n- `password_hash()` を使用\n- `password_verify()` で検証\n\n## 6. HTTPSの使用\n- 機密情報の送信時は必須\n- Cookie に secure フラグを設定",
                        'sort_order' => 5
                    },
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
                    [
                        'type' => 'note',
                        'title' => '$_FILES配列の構造',
                        'content' => "# \$_FILES配列の構造\n\nアップロードされたファイルは `\$_FILES` スーパーグローバル変数に格納されます。\n\n## \$_FILES['fieldname']の要素\n- **name**: 元のファイル名\n- **type**: MIMEタイプ（例: image/jpeg）\n- **tmp_name**: サーバー上の一時ファイルパス\n- **error**: エラーコード（0なら成功）\n- **size**: ファイルサイズ（バイト単位）\n\n## エラーコード\n- `UPLOAD_ERR_OK (0)`: 成功\n- `UPLOAD_ERR_INI_SIZE (1)`: php.iniのupload_max_filesizeを超過\n- `UPLOAD_ERR_FORM_SIZE (2)`: HTMLフォームのMAX_FILE_SIZEを超過\n- `UPLOAD_ERR_PARTIAL (3)`: 一部のみアップロード\n- `UPLOAD_ERR_NO_FILE (4)`: ファイルが選択されていない\n- `UPLOAD_ERR_NO_TMP_DIR (6)`: 一時フォルダがない\n- `UPLOAD_ERR_CANT_WRITE (7)`: ディスクへの書き込み失敗",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'セキュアなファイルアップロード',
                        'content' => "<?php\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$errors = [];\n    \n    // ファイルが送信されているか確認\n    if (!isset(\$_FILES['image']) || \$_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {\n        \$errors[] = 'ファイルを選択してください';\n    } else {\n        \$file = \$_FILES['image'];\n        \n        // エラーチェック\n        if (\$file['error'] !== UPLOAD_ERR_OK) {\n            \$errors[] = 'ファイルのアップロードに失敗しました';\n        }\n        \n        // ファイルサイズチェック（5MB以下）\n        \$maxSize = 5 * 1024 * 1024;\n        if (\$file['size'] > \$maxSize) {\n            \$errors[] = 'ファイルサイズは5MB以下にしてください';\n        }\n        \n        // MIMEタイプチェック（画像のみ）\n        \$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];\n        \$finfo = finfo_open(FILEINFO_MIME_TYPE);\n        \$mimeType = finfo_file(\$finfo, \$file['tmp_name']);\n        finfo_close(\$finfo);\n        \n        if (!in_array(\$mimeType, \$allowedTypes)) {\n            \$errors[] = 'JPEG、PNG、GIF形式の画像のみアップロード可能です';\n        }\n        \n        // 拡張子チェック\n        \$ext = strtolower(pathinfo(\$file['name'], PATHINFO_EXTENSION));\n        \$allowedExts = ['jpg', 'jpeg', 'png', 'gif'];\n        if (!in_array(\$ext, \$allowedExts)) {\n            \$errors[] = '許可されていない拡張子です';\n        }\n        \n        // エラーがなければアップロード\n        if (empty(\$errors)) {\n            \$uploadDir = 'uploads/';\n            if (!is_dir(\$uploadDir)) {\n                mkdir(\$uploadDir, 0755, true);\n            }\n            \n            // ユニークなファイル名生成\n            \$newFileName = uniqid() . '_' . time() . '.' . \$ext;\n            \$targetPath = \$uploadDir . \$newFileName;\n            \n            if (move_uploaded_file(\$file['tmp_name'], \$targetPath)) {\n                echo 'アップロード成功: ' . htmlspecialchars(\$newFileName, ENT_QUOTES, 'UTF-8');\n            } else {\n                \$errors[] = 'ファイルの保存に失敗しました';\n            }\n        }\n    }\n    \n    // エラー表示\n    foreach (\$errors as \$error) {\n        echo '<p class=\"error\">' . htmlspecialchars(\$error, ENT_QUOTES, 'UTF-8') . '</p>';\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'HTMLフォーム（ファイルアップロード）',
                        'content' => "<!-- enctype=\"multipart/form-data\" が必須 -->\n<form action=\"upload.php\" method=\"POST\" enctype=\"multipart/form-data\">\n    <!-- 最大ファイルサイズ（バイト単位）をHTMLで指定（任意） -->\n    <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5242880\">\n    \n    <label for=\"image\">画像を選択:</label>\n    <input type=\"file\" name=\"image\" id=\"image\" accept=\"image/*\" required>\n    \n    <button type=\"submit\">アップロード</button>\n</form>\n\n<!-- 複数ファイルのアップロード -->\n<form action=\"upload_multi.php\" method=\"POST\" enctype=\"multipart/form-data\">\n    <input type=\"file\" name=\"images[]\" multiple accept=\"image/*\">\n    <button type=\"submit\">アップロード</button>\n</form>",
                        'code_language' => 'html',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'note',
                        'title' => 'ファイルアップロードのセキュリティ',
                        'content' => "# ファイルアップロードのセキュリティ\n\n## 1. ファイルタイプの検証\n- **拡張子だけでなくMIMEタイプも確認**\n- `finfo_file()` を使用して実際のファイルタイプを取得\n- ホワイトリスト方式で許可する形式を限定\n\n## 2. ファイルサイズの制限\n- `\$_FILES['file']['size']` でサイズチェック\n- php.iniの `upload_max_filesize` と `post_max_size` も設定\n\n## 3. ファイル名の処理\n- **元のファイル名をそのまま使わない**\n- ユニークな名前を生成（`uniqid()`, `time()` など）\n- パストラバーサル攻撃を防ぐ\n\n## 4. アップロード先の設定\n- Webルート外に保存（推奨）\n- または `.htaccess` でPHP実行を無効化\n- ディレクトリのパーミッション設定（0755など）\n\n## 5. 実行可能ファイルの防止\n- `.php`, `.exe`, `.sh` などの実行可能ファイルを拒否\n- ダブル拡張子（`.php.jpg`）に注意\n\n## php.ini設定\n```ini\nfile_uploads = On\nupload_max_filesize = 5M\npost_max_size = 8M\nmax_file_uploads = 20\n```",
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '画像のリサイズと表示',
                        'content' => "<?php\n// 画像のリサイズ（GDライブラリ使用）\nfunction resizeImage(\$sourcePath, \$targetPath, \$maxWidth, \$maxHeight) {\n    list(\$width, \$height, \$type) = getimagesize(\$sourcePath);\n    \n    // 元画像の読み込み\n    switch (\$type) {\n        case IMAGETYPE_JPEG:\n            \$source = imagecreatefromjpeg(\$sourcePath);\n            break;\n        case IMAGETYPE_PNG:\n            \$source = imagecreatefrompng(\$sourcePath);\n            break;\n        case IMAGETYPE_GIF:\n            \$source = imagecreatefromgif(\$sourcePath);\n            break;\n        default:\n            return false;\n    }\n    \n    // アスペクト比を保持してリサイズ\n    \$ratio = min(\$maxWidth / \$width, \$maxHeight / \$height);\n    \$newWidth = (int)(\$width * \$ratio);\n    \$newHeight = (int)(\$height * \$ratio);\n    \n    // 新しい画像を作成\n    \$resized = imagecreatetruecolor(\$newWidth, \$newHeight);\n    imagecopyresampled(\$resized, \$source, 0, 0, 0, 0, \$newWidth, \$newHeight, \$width, \$height);\n    \n    // 保存\n    imagejpeg(\$resized, \$targetPath, 90);\n    \n    // メモリ解放\n    imagedestroy(\$source);\n    imagedestroy(\$resized);\n    \n    return true;\n}\n\n// 使用例\n\$uploadedFile = \$_FILES['image']['tmp_name'];\n\$targetPath = 'uploads/resized_' . uniqid() . '.jpg';\nresizeImage(\$uploadedFile, \$targetPath, 800, 600);\n?>\n\n<!-- 画像の表示 -->\n<img src=\"<?= htmlspecialchars(\$targetPath, ENT_QUOTES, 'UTF-8') ?>\" alt=\"アップロードされた画像\">",
                        'code_language' => 'php',
                        'sort_order' => 6
                    },
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
                    [
                        'type' => 'note',
                        'title' => 'クッキーとセッションの違い',
                        'content' => "# クッキーとセッションの違い\n\n## クッキー（Cookie）\n**保存場所:** クライアント側（ブラウザ）\n**容量制限:** 約4KB\n**有効期限:** 設定可能（長期保存可能）\n**セキュリティ:** 低い（ユーザーが閲覧・改ざん可能）\n**用途例:**\n- ログイン状態の記憶\n- ユーザー設定の保存\n- トラッキング\n\n## セッション（Session）\n**保存場所:** サーバー側\n**容量制限:** なし（サーバーのメモリ次第）\n**有効期限:** ブラウザを閉じるまで（デフォルト）\n**セキュリティ:** 高い（サーバー側で管理）\n**用途例:**\n- ログイン認証\n- ショッピングカート\n- フォームの一時データ\n\n## 組み合わせ\nセッションIDはクッキーに保存され、実際のデータはサーバーに保存される仕組み",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'クッキーの詳細な使い方',
                        'content' => "<?php\n// クッキーの設定（基本）\nsetcookie('username', '田中', time() + 86400); // 24時間有効\n\n// クッキーの設定（詳細）\nsetcookie(\n    'user_pref',           // 名前\n    'dark_mode',           // 値\n    time() + 86400 * 30,   // 有効期限（30日）\n    '/',                   // パス（サイト全体）\n    '',                    // ドメイン\n    true,                  // HTTPS接続のみ（secure）\n    true                   // JavaScriptからアクセス不可（httponly）\n);\n\n// PHP 7.3以降の配列構文（推奨）\nsetcookie('token', 'abc123', [\n    'expires' => time() + 3600,\n    'path' => '/',\n    'domain' => '',\n    'secure' => true,\n    'httponly' => true,\n    'samesite' => 'Strict'  // CSRF対策\n]);\n\n// クッキーの取得\nif (isset(\$_COOKIE['username'])) {\n    \$username = \$_COOKIE['username'];\n}\n\n// クッキーの削除（有効期限を過去に設定）\nsetcookie('username', '', time() - 3600);\n\n// 全クッキーの確認\nprint_r(\$_COOKIE);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'セッションの完全な使い方',
                        'content' => "<?php\n// セッション開始（すべてのページの最初に記述）\nsession_start();\n\n// セッションに値を保存\n\$_SESSION['user_id'] = 123;\n\$_SESSION['username'] = '田中';\n\$_SESSION['role'] = 'admin';\n\$_SESSION['cart'] = ['item1', 'item2'];\n\n// セッションから値を取得\nif (isset(\$_SESSION['user_id'])) {\n    \$userId = \$_SESSION['user_id'];\n}\n\n// セッション変数の削除（個別）\nunset(\$_SESSION['cart']);\n\n// セッションIDの再生成（セキュリティ対策）\nsession_regenerate_id(true);\n\n// セッションの完全削除（ログアウト時）\n\$_SESSION = [];  // すべてのセッション変数を削除\nif (isset(\$_COOKIE[session_name()])) {\n    setcookie(session_name(), '', time() - 3600, '/');\n}\nsession_destroy();  // セッションファイルを削除\n\n// セッション設定のカスタマイズ\nini_set('session.cookie_lifetime', 0);        // ブラウザを閉じるまで\nini_set('session.cookie_httponly', 1);        // JavaScriptからアクセス不可\nini_set('session.cookie_secure', 1);          // HTTPS接続のみ\nini_set('session.use_strict_mode', 1);        // 厳格モード\nini_set('session.cookie_samesite', 'Strict'); // CSRF対策\n\n// セッションタイムアウトの実装\nif (isset(\$_SESSION['last_activity'])) {\n    \$timeout = 1800; // 30分\n    if (time() - \$_SESSION['last_activity'] > \$timeout) {\n        session_unset();\n        session_destroy();\n        header('Location: login.php?timeout=1');\n        exit;\n    }\n}\n\$_SESSION['last_activity'] = time();\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '完全なログインシステムの実装',
                        'content' => "<?php\n// login.php\nsession_start();\n\n\$error = '';\n\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$username = \$_POST['username'] ?? '';\n    \$password = \$_POST['password'] ?? '';\n    \n    // データベースからユーザー情報を取得（例）\n    \$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n    \$stmt = \$pdo->prepare('SELECT id, username, password FROM users WHERE username = :username');\n    \$stmt->execute(['username' => \$username]);\n    \$user = \$stmt->fetch(PDO::FETCH_ASSOC);\n    \n    // パスワード検証\n    if (\$user && password_verify(\$password, \$user['password'])) {\n        // セッション固定攻撃対策\n        session_regenerate_id(true);\n        \n        // セッションに保存\n        \$_SESSION['user_id'] = \$user['id'];\n        \$_SESSION['username'] = \$user['username'];\n        \$_SESSION['logged_in'] = true;\n        \$_SESSION['last_activity'] = time();\n        \n        // 「ログイン状態を保持」オプション\n        if (isset(\$_POST['remember'])) {\n            \$token = bin2hex(random_bytes(32));\n            setcookie('remember_token', \$token, time() + 86400 * 30, '/', '', true, true);\n            // トークンをDBに保存\n        }\n        \n        header('Location: dashboard.php');\n        exit;\n    } else {\n        \$error = 'ユーザー名またはパスワードが正しくありません';\n    }\n}\n?>\n\n<!-- ログインフォーム -->\n<form method=\"POST\">\n    <input type=\"text\" name=\"username\" required>\n    <input type=\"password\" name=\"password\" required>\n    <label>\n        <input type=\"checkbox\" name=\"remember\"> ログイン状態を保持\n    </label>\n    <button type=\"submit\">ログイン</button>\n    <?php if (\$error): ?>\n        <p class=\"error\"><?= htmlspecialchars(\$error, ENT_QUOTES, 'UTF-8') ?></p>\n    <?php endif; ?>\n</form>\n\n<?php\n// logout.php\nsession_start();\n\$_SESSION = [];\nif (isset(\$_COOKIE[session_name()])) {\n    setcookie(session_name(), '', time() - 3600, '/');\n}\nif (isset(\$_COOKIE['remember_token'])) {\n    setcookie('remember_token', '', time() - 3600, '/');\n}\nsession_destroy();\nheader('Location: login.php');\nexit;\n\n// auth_check.php（インクルードファイル）\nsession_start();\nif (!isset(\$_SESSION['logged_in']) || \$_SESSION['logged_in'] !== true) {\n    header('Location: login.php');\n    exit;\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'note',
                        'title' => 'セッションのセキュリティ対策',
                        'content' => "# セッションのセキュリティ対策\n\n## 1. セッション固定攻撃対策\n```php\n// ログイン成功時にセッションIDを再生成\nsession_regenerate_id(true);\n```\n\n## 2. セッションハイジャック対策\n- **HTTPSを使用**（必須）\n- `session.cookie_secure = 1` を設定\n- `session.cookie_httponly = 1` を設定\n- IPアドレスやUser-Agentのチェック\n\n## 3. CSRF対策\n```php\n// トークン生成\n\$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n\n// フォームに埋め込み\necho '<input type=\"hidden\" name=\"csrf_token\" value=\"' . \$_SESSION['csrf_token'] . '\">';\n\n// 検証\nif (\$_POST['csrf_token'] !== \$_SESSION['csrf_token']) {\n    die('不正なリクエストです');\n}\n```\n\n## 4. タイムアウト設定\n- 一定時間操作がない場合は自動ログアウト\n- `\$_SESSION['last_activity']` で最終アクセス時刻を記録\n\n## 5. パスワードのハッシュ化\n```php\n// ハッシュ化\n\$hash = password_hash(\$password, PASSWORD_DEFAULT);\n\n// 検証\npassword_verify(\$inputPassword, \$hash);\n```\n\n## 6. セッション設定（php.ini）\n```ini\nsession.cookie_httponly = 1\nsession.cookie_secure = 1\nsession.use_strict_mode = 1\nsession.cookie_samesite = Strict\nsession.gc_maxlifetime = 1800\n```",
                        'sort_order' => 6
                    },
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
                    [
                        'type' => 'note',
                        'title' => 'PDOとは？',
                        'content' => "# PDO（PHP Data Objects）\n\n## 概要\nPDOは、PHPでデータベースにアクセスするための統一されたインターフェースを提供する拡張機能です。\n\n## 利点\n1. **データベース非依存**: MySQL、PostgreSQL、SQLiteなど様々なDBに対応\n2. **セキュリティ**: プリペアドステートメントでSQLインジェクション対策\n3. **エラーハンドリング**: 例外処理が可能\n4. **オブジェクト指向**: 直感的なAPI\n\n## 従来のmysqli_*関数との比較\n- PDO: 複数のデータベースに対応、オブジェクト指向\n- mysqli: MySQL専用、手続き型とOOPの両方対応\n\n推奨: **PDOを使用**",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'PDOの接続オプション',
                        'content' => "<?php\n// 基本的な接続\n\$dsn = 'mysql:host=localhost;dbname=mydb;charset=utf8mb4';\n\$username = 'root';\n\$password = '';\n\ntry {\n    \$pdo = new PDO(\$dsn, \$username, \$password, [\n        // エラーモード: 例外をスロー\n        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n        \n        // デフォルトのフェッチモード: 連想配列\n        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n        \n        // プリペアドステートメントのエミュレーションを無効化\n        PDO::ATTR_EMULATE_PREPARES => false,\n        \n        // 持続的接続（使用は慎重に）\n        // PDO::ATTR_PERSISTENT => true,\n    ]);\n    \n    echo '接続成功';\n} catch (PDOException \$e) {\n    // 本番環境では詳細なエラーを表示しない\n    error_log(\$e->getMessage());\n    die('データベース接続エラー');\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'フェッチモードの種類',
                        'content' => "# フェッチモードの種類\n\n## PDO::FETCH_ASSOC\n連想配列として取得（カラム名がキー）\n```php\n['id' => 1, 'name' => '田中']\n```\n\n## PDO::FETCH_NUM\n数値添字配列として取得\n```php\n[0 => 1, 1 => '田中']\n```\n\n## PDO::FETCH_BOTH（デフォルト）\n連想配列と数値添字配列の両方\n```php\n['id' => 1, 0 => 1, 'name' => '田中', 1 => '田中']\n```\n\n## PDO::FETCH_OBJ\nオブジェクトとして取得\n```php\nobject { id = 1, name = '田中' }\n```\n\n## PDO::FETCH_CLASS\n指定したクラスのインスタンスとして取得\n\n推奨: **PDO::FETCH_ASSOC**（わかりやすく、メモリ効率が良い）",
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '様々なSELECT文',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// 全件取得\n\$stmt = \$pdo->query('SELECT * FROM users');\n\$users = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n\n// 1件のみ取得\n\$stmt = \$pdo->query('SELECT * FROM users WHERE id = 1');\n\$user = \$stmt->fetch(PDO::FETCH_ASSOC);\n\n// 特定のカラムのみ取得\n\$stmt = \$pdo->query('SELECT id, name FROM users');\n\$users = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n\n// 件数を取得\n\$stmt = \$pdo->query('SELECT COUNT(*) as count FROM users');\n\$count = \$stmt->fetch(PDO::FETCH_ASSOC)['count'];\n\n// ループで1行ずつ処理（メモリ効率が良い）\n\$stmt = \$pdo->query('SELECT * FROM users');\nwhile (\$user = \$stmt->fetch(PDO::FETCH_ASSOC)) {\n    echo \$user['name'] . '<br>';\n}\n\n// ORDER BY, LIMIT\n\$stmt = \$pdo->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 10');\n\$latestUsers = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n\n// JOIN\n\$sql = 'SELECT u.name, p.title FROM users u \n        INNER JOIN posts p ON u.id = p.user_id';\n\$stmt = \$pdo->query(\$sql);\n\$results = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'HTMLテーブルで表示',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass', [\n    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC\n]);\n\n\$stmt = \$pdo->query('SELECT * FROM users');\n\$users = \$stmt->fetchAll();\n?>\n\n<table border=\"1\">\n    <thead>\n        <tr>\n            <th>ID</th>\n            <th>名前</th>\n            <th>メール</th>\n            <th>登録日</th>\n        </tr>\n    </thead>\n    <tbody>\n        <?php foreach (\$users as \$user): ?>\n            <tr>\n                <td><?= htmlspecialchars(\$user['id'], ENT_QUOTES, 'UTF-8') ?></td>\n                <td><?= htmlspecialchars(\$user['name'], ENT_QUOTES, 'UTF-8') ?></td>\n                <td><?= htmlspecialchars(\$user['email'], ENT_QUOTES, 'UTF-8') ?></td>\n                <td><?= htmlspecialchars(\$user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>\n            </tr>\n        <?php endforeach; ?>\n    </tbody>\n</table>\n\n<?php if (empty(\$users)): ?>\n    <p>データがありません</p>\n<?php endif; ?>",
                        'code_language' => 'php',
                        'sort_order' => 6
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
                    [
                        'type' => 'note',
                        'title' => 'SQLインジェクションとは？',
                        'content' => "# SQLインジェクション攻撃\n\n## 危険なコード例\n```php\n// 絶対にやってはいけない！\n\$id = \$_GET['id'];\n\$sql = \"SELECT * FROM users WHERE id = '\$id'\";\n\$result = \$pdo->query(\$sql);\n```\n\n攻撃例: `?id=1' OR '1'='1`\n実行されるSQL: `SELECT * FROM users WHERE id = '1' OR '1'='1'`\n→ すべてのユーザー情報が取得される\n\n## 安全なコード（プリペアドステートメント）\n```php\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE id = :id');\n\$stmt->execute(['id' => \$_GET['id']]);\n```\n\nプレースホルダー（:id）を使用することで、値とSQLが分離され、SQLインジェクションを防げる。",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'プレースホルダーの種類',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// 名前付きプレースホルダー（推奨）\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE name = :name AND age > :age');\n\$stmt->execute([\n    'name' => '田中',\n    'age' => 20\n]);\n\n// 疑問符プレースホルダー\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE name = ? AND age > ?');\n\$stmt->execute(['田中', 20]);\n\n// bindParam（参照渡し）\n\$name = '田中';\n\$age = 20;\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE name = :name AND age > :age');\n\$stmt->bindParam(':name', \$name);\n\$stmt->bindParam(':age', \$age, PDO::PARAM_INT);\n\$stmt->execute();\n\n// bindValue（値渡し）\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE name = :name');\n\$stmt->bindValue(':name', '田中');\n\$stmt->execute();\n\n// LIKE検索\n\$keyword = '%' . \$_GET['keyword'] . '%';\n\$stmt = \$pdo->prepare('SELECT * FROM products WHERE name LIKE :keyword');\n\$stmt->execute(['keyword' => \$keyword]);\n\n// IN句（複数の値）\n\$ids = [1, 2, 3, 4, 5];\n\$placeholders = implode(',', array_fill(0, count(\$ids), '?'));\n\$stmt = \$pdo->prepare(\"SELECT * FROM users WHERE id IN (\$placeholders)\");\n\$stmt->execute(\$ids);\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'プレースホルダーの制限',
                        'content' => "# プレースホルダーで使えないもの\n\nプレースホルダーは**値**のみに使用可能です。以下は使用できません：\n\n## 使用できない例\n\n### テーブル名\n```php\n// NG\n\$stmt = \$pdo->prepare('SELECT * FROM :table');\n```\n\n### カラム名\n```php\n// NG\n\$stmt = \$pdo->prepare('SELECT :column FROM users');\n```\n\n### SQL構文\n```php\n// NG\n\$stmt = \$pdo->prepare('SELECT * FROM users :order');\n```\n\n## 対処方法\nテーブル名やカラム名を動的にする必要がある場合は、**ホワイトリスト方式**で検証：\n\n```php\n\$allowedColumns = ['name', 'email', 'created_at'];\n\$column = \$_GET['sort'] ?? 'id';\n\nif (!in_array(\$column, \$allowedColumns)) {\n    \$column = 'id';  // デフォルト値\n}\n\n\$stmt = \$pdo->query(\"SELECT * FROM users ORDER BY \$column\");\n```",
                        'sort_order' => 4
                    },
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
                    [
                        'type' => 'code_snippet',
                        'title' => '完全なINSERT処理',
                        'content' => "<?php\n\$errors = [];\n\$success = '';\n\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$name = trim(\$_POST['name'] ?? '');\n    \$email = trim(\$_POST['email'] ?? '');\n    \$age = trim(\$_POST['age'] ?? '');\n    \n    // バリデーション\n    if (empty(\$name)) {\n        \$errors[] = '名前を入力してください';\n    }\n    if (empty(\$email) || !filter_var(\$email, FILTER_VALIDATE_EMAIL)) {\n        \$errors[] = '正しいメールアドレスを入力してください';\n    }\n    if (!is_numeric(\$age) || \$age < 0) {\n        \$errors[] = '正しい年齢を入力してください';\n    }\n    \n    if (empty(\$errors)) {\n        try {\n            \$pdo = new PDO(\n                'mysql:host=localhost;dbname=mydb;charset=utf8mb4',\n                'user',\n                'pass',\n                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\n            );\n            \n            \$sql = 'INSERT INTO users (name, email, age, created_at) \n                    VALUES (:name, :email, :age, NOW())';\n            \$stmt = \$pdo->prepare(\$sql);\n            \$stmt->execute([\n                'name' => \$name,\n                'email' => \$email,\n                'age' => \$age\n            ]);\n            \n            // 挿入されたIDを取得\n            \$lastId = \$pdo->lastInsertId();\n            \n            \$success = \"登録が完了しました（ID: \$lastId）\";\n            \n            // リダイレクト\n            // header('Location: success.php?id=' . \$lastId);\n            // exit;\n        } catch (PDOException \$e) {\n            // 重複キーエラーの処理\n            if (\$e->getCode() == 23000) {\n                \$errors[] = 'このメールアドレスは既に登録されています';\n            } else {\n                \$errors[] = '登録に失敗しました';\n                error_log(\$e->getMessage());\n            }\n        }\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '複数データのINSERT',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// 複数行を一度にINSERT（効率的）\n\$sql = 'INSERT INTO users (name, email) VALUES \n        (:name1, :email1),\n        (:name2, :email2),\n        (:name3, :email3)';\n\$stmt = \$pdo->prepare(\$sql);\n\$stmt->execute([\n    'name1' => '田中', 'email1' => 'tanaka@example.com',\n    'name2' => '佐藤', 'email2' => 'sato@example.com',\n    'name3' => '鈴木', 'email3' => 'suzuki@example.com'\n]);\n\n// ループでINSERT（トランザクション使用）\n\$users = [\n    ['name' => '田中', 'email' => 'tanaka@example.com'],\n    ['name' => '佐藤', 'email' => 'sato@example.com'],\n    ['name' => '鈴木', 'email' => 'suzuki@example.com']\n];\n\ntry {\n    \$pdo->beginTransaction();\n    \n    \$stmt = \$pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');\n    \n    foreach (\$users as \$user) {\n        \$stmt->execute(\$user);\n    }\n    \n    \$pdo->commit();\n    echo '全データの登録が完了しました';\n} catch (PDOException \$e) {\n    \$pdo->rollBack();\n    echo '登録に失敗しました: ' . \$e->getMessage();\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'note',
                        'title' => 'lastInsertId()とrowCount()',
                        'content' => "# 便利なPDOメソッド\n\n## lastInsertId()\nAUTO_INCREMENTで生成された最後のIDを取得\n\n```php\n\$stmt->execute(['name' => '田中']);\n\$id = \$pdo->lastInsertId();\necho \"挿入されたID: \$id\";\n```\n\n## rowCount()\n影響を受けた行数を取得（INSERT, UPDATE, DELETE）\n\n```php\n\$stmt = \$pdo->prepare('INSERT INTO users (name) VALUES (:name)');\n\$stmt->execute(['name' => '田中']);\n\$count = \$stmt->rowCount();\necho \"\$count 件挿入されました\";\n```\n\n**注意:** SELECT文でのrowCount()は推奨されません。COUNT(*)を使用してください。",
                        'sort_order' => 4
                    },
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
                    [
                        'type' => 'code_snippet',
                        'title' => 'UPDATE文の実践例',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// 単一カラムの更新\n\$stmt = \$pdo->prepare('UPDATE users SET name = :name WHERE id = :id');\n\$stmt->execute(['name' => '新田中', 'id' => 1]);\necho \$stmt->rowCount() . '件更新しました';\n\n// 複数カラムの更新\n\$stmt = \$pdo->prepare('UPDATE users SET name = :name, email = :email, updated_at = NOW() WHERE id = :id');\n\$stmt->execute([\n    'name' => '田中太郎',\n    'email' => 'tanaka@example.com',\n    'id' => 1\n]);\n\n// 条件付き更新\n\$stmt = \$pdo->prepare('UPDATE products SET price = price * 0.9 WHERE category = :category');\n\$stmt->execute(['category' => 'electronics']);\n\n// フォームからの更新処理\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$id = \$_POST['id'] ?? 0;\n    \$name = \$_POST['name'] ?? '';\n    \$email = \$_POST['email'] ?? '';\n    \n    if (!empty(\$name) && !empty(\$email)) {\n        \$stmt = \$pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');\n        \$result = \$stmt->execute([\n            'name' => \$name,\n            'email' => \$email,\n            'id' => \$id\n        ]);\n        \n        if (\$stmt->rowCount() > 0) {\n            echo '更新しました';\n        } else {\n            echo '更新するデータがありませんでした';\n        }\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'トランザクションとは？',
                        'content' => "# トランザクション処理\n\n## 概要\nトランザクションは、複数のSQL文を1つの処理単位としてまとめる仕組みです。\n\n## ACID特性\n- **Atomicity（原子性）**: 全て成功 or 全て失敗\n- **Consistency（一貫性）**: データの整合性を保つ\n- **Isolation（独立性）**: 他の処理と独立\n- **Durability（永続性）**: 完了後はデータが永続\n\n## 使用例\n- 銀行の送金処理（引き落としと入金は両方成功する必要がある）\n- ECサイトの注文処理（在庫減少と注文登録）\n- ユーザー登録（usersテーブルとprofilesテーブル）\n\n## 基本構文\n```php\n\$pdo->beginTransaction();  // 開始\ntry {\n    // SQL処理\n    \$pdo->commit();  // 確定\n} catch (Exception \$e) {\n    \$pdo->rollBack();  // 取り消し\n}\n```",
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'DELETE文の実践例',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// 単一レコードの削除\n\$stmt = \$pdo->prepare('DELETE FROM users WHERE id = :id');\n\$stmt->execute(['id' => 1]);\necho \$stmt->rowCount() . '件削除しました';\n\n// 複数レコードの削除\n\$stmt = \$pdo->prepare('DELETE FROM users WHERE age < :age');\n\$stmt->execute(['age' => 18]);\n\n// 論理削除（実際には削除せず、フラグを立てる）\n\$stmt = \$pdo->prepare('UPDATE users SET deleted_at = NOW() WHERE id = :id');\n\$stmt->execute(['id' => 1]);\n\n// 削除確認付き\nif (\$_SERVER['REQUEST_METHOD'] === 'POST') {\n    \$id = \$_POST['id'] ?? 0;\n    \$confirm = \$_POST['confirm'] ?? '';\n    \n    if (\$confirm === 'yes') {\n        try {\n            \$pdo->beginTransaction();\n            \n            // 関連データも削除（外部キー制約がない場合）\n            \$stmt = \$pdo->prepare('DELETE FROM user_profiles WHERE user_id = :id');\n            \$stmt->execute(['id' => \$id]);\n            \n            \$stmt = \$pdo->prepare('DELETE FROM users WHERE id = :id');\n            \$stmt->execute(['id' => \$id]);\n            \n            \$pdo->commit();\n            echo '削除しました';\n        } catch (PDOException \$e) {\n            \$pdo->rollBack();\n            echo '削除に失敗しました';\n        }\n    }\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    },
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
                    [
                        'type' => 'code_snippet',
                        'title' => 'json_encodeの詳細',
                        'content' => "<?php\n// 基本的なエンコード\n\$data = ['name' => '田中', 'age' => 25];\n\$json = json_encode(\$data);\necho \$json;  // {\"name\":\"\\u7530\\u4e2d\",\"age\":25}\n\n// 日本語をエスケープしない\n\$json = json_encode(\$data, JSON_UNESCAPED_UNICODE);\necho \$json;  // {\"name\":\"田中\",\"age\":25}\n\n// 整形して出力\n\$json = json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);\necho \$json;\n/* 出力:\n{\n    \"name\": \"田中\",\n    \"age\": 25\n}\n*/\n\n// スラッシュをエスケープしない\n\$url = ['url' => 'https://example.com/path'];\n\$json = json_encode(\$url, JSON_UNESCAPED_SLASHES);\necho \$json;  // {\"url\":\"https://example.com/path\"}\n\n// 数値文字列を数値として出力\n\$data = ['id' => '123', 'price' => '1000'];\n\$json = json_encode(\$data, JSON_NUMERIC_CHECK);\necho \$json;  // {\"id\":123,\"price\":1000}\n\n// 複数オプションの組み合わせ\n\$json = json_encode(\$data, \n    JSON_UNESCAPED_UNICODE | \n    JSON_UNESCAPED_SLASHES | \n    JSON_PRETTY_PRINT\n);\n\n// エラーチェック\nif (\$json === false) {\n    echo 'JSONエンコードエラー: ' . json_last_error_msg();\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'json_decodeの詳細',
                        'content' => "<?php\n\$jsonString = '{\"name\":\"田中\",\"age\":25,\"hobbies\":[\"読書\",\"スポーツ\"]}';\n\n// 連想配列として取得（推奨）\n\$data = json_decode(\$jsonString, true);\necho \$data['name'];      // 田中\necho \$data['age'];       // 25\necho \$data['hobbies'][0]; // 読書\n\n// オブジェクトとして取得\n\$data = json_decode(\$jsonString);\necho \$data->name;        // 田中\necho \$data->age;         // 25\necho \$data->hobbies[0];  // 読書\n\n// エラーハンドリング\n\$invalidJson = '{name: \"田中\"}';\n\$data = json_decode(\$invalidJson, true);\n\nif (json_last_error() !== JSON_ERROR_NONE) {\n    echo 'JSONデコードエラー: ' . json_last_error_msg();\n    // Syntax error など\n}\n\n// POSTリクエストのJSON解析\n\$input = file_get_contents('php://input');\n\$data = json_decode(\$input, true);\n\nif (json_last_error() === JSON_ERROR_NONE) {\n    // JSONが正常に解析された\n    \$name = \$data['name'] ?? '';\n    \$email = \$data['email'] ?? '';\n} else {\n    // エラー処理\n    http_response_code(400);\n    echo json_encode(['error' => 'Invalid JSON']);\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'データベースとJSON',
                        'content' => "<?php\n\$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');\n\n// データベースからJSONレスポンスを作成\nheader('Content-Type: application/json; charset=utf-8');\n\ntry {\n    \$stmt = \$pdo->query('SELECT id, name, email, created_at FROM users');\n    \$users = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n    \n    echo json_encode([\n        'success' => true,\n        'data' => \$users,\n        'count' => count(\$users)\n    ], JSON_UNESCAPED_UNICODE);\n    \n} catch (PDOException \$e) {\n    http_response_code(500);\n    echo json_encode([\n        'success' => false,\n        'error' => 'Database error'\n    ]);\n}\n\n// 1件のデータを返す\n\$id = \$_GET['id'] ?? 0;\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE id = :id');\n\$stmt->execute(['id' => \$id]);\n\$user = \$stmt->fetch(PDO::FETCH_ASSOC);\n\nif (\$user) {\n    echo json_encode(['success' => true, 'data' => \$user], JSON_UNESCAPED_UNICODE);\n} else {\n    http_response_code(404);\n    echo json_encode(['success' => false, 'error' => 'User not found']);\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'JSONのベストプラクティス',
                        'content' => "# JSONのベストプラクティス\n\n## 1. Content-Typeヘッダーを設定\n```php\nheader('Content-Type: application/json; charset=utf-8');\n```\n\n## 2. 日本語をエスケープしない\n```php\njson_encode(\$data, JSON_UNESCAPED_UNICODE);\n```\n\n## 3. エラーハンドリング\n```php\nif (json_last_error() !== JSON_ERROR_NONE) {\n    // エラー処理\n}\n```\n\n## 4. HTTPステータスコードを適切に設定\n- 200: 成功\n- 201: 作成成功\n- 400: リクエストエラー\n- 404: 見つからない\n- 500: サーバーエラー\n\n## 5. 統一されたレスポンス形式\n```php\n[\n    'success' => true/false,\n    'data' => [...],\n    'error' => 'エラーメッセージ'\n]\n```\n\n## 6. CORSヘッダー（必要に応じて）\n```php\nheader('Access-Control-Allow-Origin: *');\n```",
                        'sort_order' => 5
                    },
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
                    [
                        'type' => 'code_snippet',
                        'title' => '完全なCRUD API',
                        'content' => "<?php\n// api/users.php\nheader('Content-Type: application/json; charset=utf-8');\nheader('Access-Control-Allow-Origin: *');\nheader('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');\nheader('Access-Control-Allow-Headers: Content-Type');\n\n// OPTIONSリクエストへの対応（CORS preflight）\nif (\$_SERVER['REQUEST_METHOD'] === 'OPTIONS') {\n    http_response_code(200);\n    exit;\n}\n\ntry {\n    \$pdo = new PDO(\n        'mysql:host=localhost;dbname=mydb;charset=utf8mb4',\n        'user',\n        'pass',\n        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\n    );\n    \n    \$method = \$_SERVER['REQUEST_METHOD'];\n    \$path = \$_SERVER['PATH_INFO'] ?? '/';\n    \$segments = explode('/', trim(\$path, '/'));\n    \$id = \$segments[0] ?? null;\n    \n    switch (\$method) {\n        case 'GET':\n            if (\$id) {\n                // 1件取得\n                \$stmt = \$pdo->prepare('SELECT * FROM users WHERE id = :id');\n                \$stmt->execute(['id' => \$id]);\n                \$user = \$stmt->fetch(PDO::FETCH_ASSOC);\n                \n                if (\$user) {\n                    echo json_encode(['success' => true, 'data' => \$user], JSON_UNESCAPED_UNICODE);\n                } else {\n                    http_response_code(404);\n                    echo json_encode(['success' => false, 'error' => 'User not found']);\n                }\n            } else {\n                // 全件取得\n                \$stmt = \$pdo->query('SELECT * FROM users ORDER BY id DESC');\n                \$users = \$stmt->fetchAll(PDO::FETCH_ASSOC);\n                echo json_encode(['success' => true, 'data' => \$users], JSON_UNESCAPED_UNICODE);\n            }\n            break;\n        \n        case 'POST':\n            // 新規作成\n            \$input = json_decode(file_get_contents('php://input'), true);\n            \n            if (!isset(\$input['name']) || !isset(\$input['email'])) {\n                http_response_code(400);\n                echo json_encode(['success' => false, 'error' => 'Name and email are required']);\n                exit;\n            }\n            \n            \$stmt = \$pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');\n            \$stmt->execute([\n                'name' => \$input['name'],\n                'email' => \$input['email']\n            ]);\n            \n            http_response_code(201);\n            echo json_encode([\n                'success' => true,\n                'data' => ['id' => \$pdo->lastInsertId()]\n            ]);\n            break;\n        \n        case 'PUT':\n        case 'PATCH':\n            // 更新\n            if (!\$id) {\n                http_response_code(400);\n                echo json_encode(['success' => false, 'error' => 'ID is required']);\n                exit;\n            }\n            \n            \$input = json_decode(file_get_contents('php://input'), true);\n            \n            \$stmt = \$pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');\n            \$stmt->execute([\n                'name' => \$input['name'],\n                'email' => \$input['email'],\n                'id' => \$id\n            ]);\n            \n            echo json_encode(['success' => true, 'message' => 'User updated']);\n            break;\n        \n        case 'DELETE':\n            // 削除\n            if (!\$id) {\n                http_response_code(400);\n                echo json_encode(['success' => false, 'error' => 'ID is required']);\n                exit;\n            }\n            \n            \$stmt = \$pdo->prepare('DELETE FROM users WHERE id = :id');\n            \$stmt->execute(['id' => \$id]);\n            \n            echo json_encode(['success' => true, 'message' => 'User deleted']);\n            break;\n        \n        default:\n            http_response_code(405);\n            echo json_encode(['success' => false, 'error' => 'Method not allowed']);\n    }\n    \n} catch (PDOException \$e) {\n    http_response_code(500);\n    echo json_encode(['success' => false, 'error' => 'Database error']);\n    error_log(\$e->getMessage());\n}\n?>",
                        'code_language' => 'php',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'note',
                        'title' => 'HTTPメソッドとステータスコード',
                        'content' => "# HTTPメソッド\n\n## GET\n- リソースの取得\n- `/api/users` - 全ユーザー取得\n- `/api/users/1` - ID=1のユーザー取得\n\n## POST\n- 新規リソースの作成\n- `/api/users` - 新規ユーザー作成\n\n## PUT / PATCH\n- リソースの更新\n- PUT: 完全な更新\n- PATCH: 部分的な更新\n- `/api/users/1` - ID=1のユーザー更新\n\n## DELETE\n- リソースの削除\n- `/api/users/1` - ID=1のユーザー削除\n\n# HTTPステータスコード\n\n## 2xx 成功\n- **200 OK**: 成功\n- **201 Created**: 作成成功\n- **204 No Content**: 成功（レスポンスなし）\n\n## 4xx クライアントエラー\n- **400 Bad Request**: リクエストエラー\n- **401 Unauthorized**: 認証エラー\n- **403 Forbidden**: 権限エラー\n- **404 Not Found**: リソースが見つからない\n- **405 Method Not Allowed**: メソッドが許可されていない\n\n## 5xx サーバーエラー\n- **500 Internal Server Error**: サーバーエラー\n- **503 Service Unavailable**: サービス利用不可",
                        'sort_order' => 4
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'JavaScriptからAPIを呼び出す',
                        'content' => "// GET - 全件取得\nfetch('http://localhost/api/users.php')\n    .then(response => response.json())\n    .then(data => {\n        console.log(data);\n        if (data.success) {\n            displayUsers(data.data);\n        }\n    })\n    .catch(error => console.error('Error:', error));\n\n// GET - 1件取得\nfetch('http://localhost/api/users.php/1')\n    .then(response => response.json())\n    .then(data => console.log(data));\n\n// POST - 新規作成\nfetch('http://localhost/api/users.php', {\n    method: 'POST',\n    headers: {\n        'Content-Type': 'application/json',\n    },\n    body: JSON.stringify({\n        name: '田中太郎',\n        email: 'tanaka@example.com'\n    })\n})\n.then(response => response.json())\n.then(data => {\n    if (data.success) {\n        console.log('ユーザー作成成功:', data.data.id);\n    }\n});\n\n// PUT - 更新\nfetch('http://localhost/api/users.php/1', {\n    method: 'PUT',\n    headers: {\n        'Content-Type': 'application/json',\n    },\n    body: JSON.stringify({\n        name: '新田中',\n        email: 'newtanaka@example.com'\n    })\n})\n.then(response => response.json())\n.then(data => console.log(data));\n\n// DELETE - 削除\nfetch('http://localhost/api/users.php/1', {\n    method: 'DELETE'\n})\n.then(response => response.json())\n.then(data => console.log(data));\n\n// async/await版\nasync function getUsers() {\n    try {\n        const response = await fetch('http://localhost/api/users.php');\n        const data = await response.json();\n        \n        if (data.success) {\n            return data.data;\n        } else {\n            throw new Error(data.error);\n        }\n    } catch (error) {\n        console.error('エラー:', error);\n    }\n}",
                        'code_language' => 'javascript',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'note',
                        'title' => 'API開発のセキュリティ',
                        'content' => "# API開発のセキュリティ\n\n## 1. 認証・認可\n```php\n// JWTトークン検証\n\$headers = getallheaders();\n\$token = \$headers['Authorization'] ?? '';\n\nif (!\$token || !validateToken(\$token)) {\n    http_response_code(401);\n    echo json_encode(['error' => 'Unauthorized']);\n    exit;\n}\n```\n\n## 2. レート制限\n- 同一IPからのリクエスト数を制限\n- DoS攻撃の防止\n\n## 3. 入力値の検証\n```php\n\$input = json_decode(file_get_contents('php://input'), true);\n\nif (!isset(\$input['email']) || !filter_var(\$input['email'], FILTER_VALIDATE_EMAIL)) {\n    http_response_code(400);\n    echo json_encode(['error' => 'Invalid email']);\n    exit;\n}\n```\n\n## 4. SQLインジェクション対策\n- プリペアドステートメントを必ず使用\n\n## 5. XSS対策\n- APIはJSONを返すので基本的に不要\n- HTMLを返す場合は htmlspecialchars() を使用\n\n## 6. CORS設定\n```php\n// 特定のオリジンのみ許可\n\$allowedOrigins = ['https://example.com'];\n\$origin = \$_SERVER['HTTP_ORIGIN'] ?? '';\n\nif (in_array(\$origin, \$allowedOrigins)) {\n    header('Access-Control-Allow-Origin: ' . \$origin);\n}\n```\n\n## 7. HTTPSの使用\n- 本番環境では必ずHTTPSを使用",
                        'sort_order' => 6
                    },
                ],
            ],
        ]);
    }
}

