<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class JavaBasicCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Java基礎演習 - 30回の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Java基礎演習',
            'description' => '初心者向けJavaプログラミング基礎コース。30回の実践的な課題を通じて、Javaの基本構文からアルゴリズムまで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 180,
            'tags' => ['java', '基礎', '演習', '初心者', 'プログラミング'],
            'icon' => 'ic_java',
            'color' => '#ED8B00',
            'is_featured' => true,
        ]);

        // Milestone 1: 環境設定と基本構文 (第1回～第4回)
        $milestone1 = $template->milestones()->create([
            'title' => '環境設定と基本構文',
            'description' => '開発環境のセットアップから、変数、式、文字列入力まで学習',
            'sort_order' => 1,
            'estimated_hours' => 12,
            'deliverables' => [
                '開発環境をセットアップ完了',
                'Hello Worldプログラムを作成',
                '変数と式を使った計算プログラム',
                'キーボード入力を受け取るプログラム'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第1回：環境設定と画面出力',
                'description' => 'Java開発環境のセットアップとSystem.out.println()を使った画面出力',
                'sort_order' => 1,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [
                    '環境設定①（JCPad）',
                    '環境設定②（IntelliJ）'
                ],
                'subtasks' => [
                    ['title' => '開発環境をセットアップ', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'Hello Worldプログラムを作成', 'estimated_minutes' => 20, 'sort_order' => 2],
                    ['title' => 'ASCIIアートで文字を出力（E, Cなど）', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => 'メソッドを使った出力プログラム', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Javaとは？',
                        'content' => "# Javaとは？\n\n**Java**は、1995年にSun Microsystems（現Oracle）が開発したプログラミング言語です。\n\n## Javaの特徴\n\n1. **プラットフォーム独立性**: 「一度書けば、どこでも動く（Write Once, Run Anywhere）」\n2. **オブジェクト指向**: クラスとオブジェクトの概念\n3. **ガベージコレクション**: 自動メモリ管理\n4. **豊富なライブラリ**: 標準ライブラリが充実\n5. **堅牢性**: 強い型チェック、例外処理\n\n## Javaの用途\n- Androidアプリ開発\n- Webアプリケーション（Spring Framework）\n- 企業向けシステム\n- デスクトップアプリケーション\n- ビッグデータ処理（Hadoop）",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Java開発環境の準備',
                        'content' => "# Java開発環境の準備\n\n## 必要なもの\n\n### 1. JDK (Java Development Kit)\n- Javaプログラムを開発・実行するためのツール\n- Oracle JDK または OpenJDK\n- インストール確認: `java -version` および `javac -version`\n\n### 2. 統合開発環境（IDE）\n\n**IntelliJ IDEA** (推奨)\n- 高機能なJava IDE\n- コード補完、デバッグ機能が優秀\n- Community Edition（無料版）で十分\n\n**JCPad** (初心者向け)\n- シンプルで軽量\n- 日本語対応\n- すぐに使い始められる\n\n**Eclipse**\n- 無料でオープンソース\n- プラグインが豊富\n\n## インストール手順\n1. JDKをダウンロードしてインストール\n2. IDEをダウンロードしてインストール\n3. 環境変数の設定（JAVA_HOME, PATH）\n4. 動作確認（Hello Worldの実行）",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Hello Worldの基本',
                        'content' => "public class JKad01S1 {\n    public static void main(String[] args) {\n        System.out.println(\"Hello World!\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Javaプログラムの構造',
                        'content' => "# Javaプログラムの構造\n\n```java\npublic class ClassName {\n    public static void main(String[] args) {\n        // プログラムの処理\n    }\n}\n```\n\n## 構成要素の説明\n\n### 1. `public class ClassName`\n- **public**: アクセス修飾子（どこからでもアクセス可能）\n- **class**: クラスの宣言\n- **ClassName**: クラス名（ファイル名と一致する必要がある）\n\n### 2. `public static void main(String[] args)`\n- **public**: どこからでも呼び出し可能\n- **static**: インスタンス化せずに呼び出せる\n- **void**: 戻り値がない\n- **main**: プログラムのエントリーポイント\n- **String[] args**: コマンドライン引数\n\n## 重要なルール\n1. ファイル名とクラス名は一致させる（例: `JKad01S1.java` → `class JKad01S1`）\n2. 大文字小文字を区別する\n3. 文の最後にセミコロン（;）を付ける\n4. ブロックは波括弧（{}）で囲む",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ASCIIアートの出力例',
                        'content' => "public class JKad01S2 {\n    public static void main(String[] args) {\n        // 「E」の文字をASCIIアートで出力\n        System.out.println(\"EEEEEEEE\");\n        System.out.println(\"EE\");\n        System.out.println(\"EE\");\n        System.out.println(\"EEEEEEEE\");\n        System.out.println(\"EE\");\n        System.out.println(\"EE\");\n        System.out.println(\"EEEEEEEE\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッドを使った出力',
                        'content' => "public class JSample01X {\n    // メソッドの定義\n    public static void printHello() {\n        System.out.println(\"Hello\");\n    }\n    \n    public static void printWorld() {\n        System.out.println(\"World!\");\n    }\n    \n    // メインメソッド\n    public static void main(String[] args) {\n        printHello();  // メソッドの呼び出し\n        printWorld();  // メソッドの呼び出し\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => 'System.out.println()とSystem.out.print()の違い',
                        'content' => "# System.out.println()とSystem.out.print()の違い\n\n## println()メソッド\n```java\nSystem.out.println(\"Hello\");\nSystem.out.println(\"World\");\n```\n**出力結果:**\n```\nHello\nWorld\n```\n- 出力後に改行される\n\n## print()メソッド\n```java\nSystem.out.print(\"Hello\");\nSystem.out.print(\"World\");\n```\n**出力結果:**\n```\nHelloWorld\n```\n- 改行されない\n\n## 使い分け\n- **println()**: 1行ずつ出力したい場合\n- **print()**: 同じ行に複数の内容を出力したい場合\n\n## エスケープシーケンス\n- `\\n`: 改行\n- `\\t`: タブ\n- `\\\"`: ダブルクォート\n- `\\\\`: バックスラッシュ\n\n```java\nSystem.out.println(\"Hello\\nWorld\");  // 改行を含む\nSystem.out.println(\"Name:\\tTaro\");   // タブを含む\n```",
                        'sort_order' => 7
                    ],
                    [
                        'type' => 'note',
                        'title' => 'コンパイルと実行',
                        'content' => "# Javaプログラムのコンパイルと実行\n\n## コマンドラインでの実行\n\n### 1. コンパイル\n```bash\njavac JKad01S1.java\n```\n- `.java`ファイルから`.class`ファイル（バイトコード）を生成\n- エラーがあればコンパイルエラーが表示される\n\n### 2. 実行\n```bash\njava JKad01S1\n```\n- `.class`を指定する（拡張子は不要）\n- JVM（Java Virtual Machine）がバイトコードを実行\n\n## IDEでの実行\n- **IntelliJ IDEA**: 緑の再生ボタン または Shift+F10\n- **Eclipse**: Ctrl+F11\n- **JCPad**: F5 または 実行ボタン\n\n## よくあるエラー\n\n### コンパイルエラー\n```\nerror: class JKad01S1 is public, should be declared in a file named JKad01S1.java\n```\n→ ファイル名とクラス名が一致していない\n\n### 実行エラー\n```\nError: Could not find or load main class JKad01S1\n```\n→ mainメソッドが見つからない、またはクラス名が間違っている",
                        'sort_order' => 8
                    ],
                ],
            ],
            [
                'title' => '第2回：変数と式①（int型①）',
                'description' => 'int型の変数宣言、代入、四則演算、剰余演算',
                'sort_order' => 2,
                'estimated_minutes' => 90,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '変数の宣言と代入', 'estimated_minutes' => 20, 'sort_order' => 1],
                    ['title' => '四則演算（+, -, *, /）', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '剰余演算（%）', 'estimated_minutes' => 20, 'sort_order' => 3],
                    ['title' => '実践問題：リンゴの分配', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '変数とは？',
                        'content' => "# 変数とは？\n\n**変数**は、データを格納する箱のようなものです。\n\n## 変数の3つの要素\n1. **型（Type）**: どんな種類のデータを入れるか\n2. **名前（Name）**: 変数を識別するための名前\n3. **値（Value）**: 実際に格納されているデータ\n\n## 変数の宣言と代入\n```java\nint age;        // 宣言（箱を用意）\nage = 20;       // 代入（値を入れる）\n\nint score = 100;  // 宣言と同時に代入（初期化）\n```\n\n## 変数の命名規則\n- 英数字とアンダースコア（_）が使える\n- 最初の文字は英字またはアンダースコア\n- 予約語（int, class, publicなど）は使えない\n- キャメルケースを使う（myScore, studentName）\n- 意味のある名前を付ける",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'int型の詳細',
                        'content' => "# int型の詳細\n\n**int型**は、整数を格納するデータ型です。\n\n## 基本情報\n- **サイズ**: 32ビット（4バイト）\n- **範囲**: -2,147,483,648 ～ 2,147,483,647\n- **デフォルト値**: 0\n\n## 使用例\n```java\nint count = 10;\nint temperature = -5;\nint maxValue = 2147483647;\n```\n\n## 注意点\n- 範囲を超えるとオーバーフローが発生\n- 小数点以下は格納できない（double型を使用）\n\n```java\nint x = 2147483647;\nx = x + 1;  // オーバーフロー: -2147483648になる\n```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '変数の宣言と代入',
                        'content' => "public class JKad02_Variables {\n    public static void main(String[] args) {\n        // 宣言のみ\n        int x;\n        \n        // 値を代入\n        x = 10;\n        System.out.println(\"x = \" + x);  // x = 10\n        \n        // 宣言と同時に初期化\n        int y = 20;\n        System.out.println(\"y = \" + y);  // y = 20\n        \n        // 値の更新\n        x = 30;\n        System.out.println(\"x = \" + x);  // x = 30\n        \n        // 複数の変数を一度に宣言\n        int a = 1, b = 2, c = 3;\n        System.out.println(\"a=\" + a + \", b=\" + b + \", c=\" + c);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => '四則演算',
                        'content' => "# 四則演算\n\nJavaでは、数値に対して以下の演算が可能です。\n\n## 基本演算子\n\n| 演算子 | 意味 | 例 | 結果 |\n|-------|------|-----|------|\n| + | 加算 | 10 + 3 | 13 |\n| - | 減算 | 10 - 3 | 7 |\n| * | 乗算 | 10 * 3 | 30 |\n| / | 除算 | 10 / 3 | 3 |\n| % | 剰余 | 10 % 3 | 1 |\n\n## 整数除算の注意点\n```java\nint a = 10;\nint b = 3;\nint result = a / b;  // 結果は3（小数点以下切り捨て）\n```\n\n整数同士の割り算は、結果も整数になります。\n\n## 演算の優先順位\n1. **括弧**: `( )`\n2. **乗除剰**: `*`, `/`, `%`（左から右へ）\n3. **加減**: `+`, `-`（左から右へ）\n\n```java\nint x = 2 + 3 * 4;      // 14 (3*4が先に計算される)\nint y = (2 + 3) * 4;    // 20 (括弧内が先に計算される)\n```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '四則演算の例',
                        'content' => "public class JKad02_Arithmetic {\n    public static void main(String[] args) {\n        int a = 10;\n        int b = 3;\n        \n        System.out.println(\"a + b = \" + (a + b));  // 13\n        System.out.println(\"a - b = \" + (a - b));  // 7\n        System.out.println(\"a * b = \" + (a * b));  // 30\n        System.out.println(\"a / b = \" + (a / b));  // 3（整数除算）\n        System.out.println(\"a % b = \" + (a % b));  // 1（剰余）\n        \n        // 括弧を使った計算\n        int result1 = (a + b) * 2;  // (10 + 3) * 2 = 26\n        int result2 = a + b * 2;    // 10 + (3 * 2) = 16\n        \n        System.out.println(\"(a + b) * 2 = \" + result1);\n        System.out.println(\"a + b * 2 = \" + result2);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => '剰余演算（%）の使い道',
                        'content' => "# 剰余演算（%）の使い道\n\n**剰余演算**は、割り算の余りを求める演算です。\n\n## 使用例\n\n### 1. 偶数・奇数の判定\n```java\nif (number % 2 == 0) {\n    System.out.println(\"偶数\");\n} else {\n    System.out.println(\"奇数\");\n}\n```\n\n### 2. 倍数の判定\n```java\nif (number % 3 == 0) {\n    System.out.println(\"3の倍数\");\n}\n```\n\n### 3. 分配問題\n```java\nint apples = 20;\nint people = 3;\nint perPerson = apples / people;  // 6個ずつ\nint remaining = apples % people;  // 2個余る\n```\n\n### 4. 桁の取得\n```java\nint number = 1234;\nint lastDigit = number % 10;  // 4（1の位）\n```\n\n### 5. 循環処理\n```java\n// 0, 1, 2, 0, 1, 2, ... と繰り返す\nint index = count % 3;\n```",
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'リンゴの分配問題',
                        'content' => "import java.util.Scanner;\n\npublic class JKad02A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        \n        System.out.print(\"リンゴの個数を入力してください＞\");\n        int apple = in.nextInt();\n        \n        System.out.print(\"人数を入力してください＞\");\n        int person = in.nextInt();\n        \n        // 1人あたりの個数\n        int eat = apple / person;\n        \n        // 余りの個数\n        int rest = apple % person;\n        \n        System.out.println(\"1人\" + eat + \"個ずつ食べられます。\");\n        System.out.println(\"残りは\" + rest + \"個です。\");\n        \n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 7
                    ],
                ],
            ],
            [
                'title' => '第3回：変数と式②（int型②）',
                'description' => 'int型の応用、複雑な式の計算',
                'sort_order' => 3,
                'estimated_minutes' => 90,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => '複雑な式の計算', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => '演算子の優先順位', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 30, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '複雑な式の計算',
                        'content' => "public class JKad03A {\n    public static void main(String[] args) {\n        int a = 10;\n        int b = 3;\n        int c = 5;\n        \n        // 演算子の優先順位: *, /, % は +, - より優先\n        int result1 = a + b * c;  // 10 + 15 = 25\n        int result2 = (a + b) * c;  // 13 * 5 = 65\n        int result3 = a * b + c / 2;  // 30 + 2 = 32\n        \n        System.out.println(\"a + b * c = \" + result1);\n        System.out.println(\"(a + b) * c = \" + result2);\n        System.out.println(\"a * b + c / 2 = \" + result3);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'インクリメントとデクリメント',
                        'content' => "public class JKad03B {\n    public static void main(String[] args) {\n        int x = 5;\n        \n        x++;  // x = x + 1; と同じ (x は 6)\n        System.out.println(\"x++ 後: \" + x);\n        \n        x--;  // x = x - 1; と同じ (x は 5)\n        System.out.println(\"x-- 後: \" + x);\n        \n        x += 3;  // x = x + 3; と同じ (x は 8)\n        System.out.println(\"x += 3 後: \" + x);\n        \n        x *= 2;  // x = x * 2; と同じ (x は 16)\n        System.out.println(\"x *= 2 後: \" + x);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => '演算子の優先順位',
                        'content' => "# 演算子の優先順位\n\n1. **括弧**: `( )`\n2. **単項演算子**: `++`, `--`, `+`, `-`\n3. **乗除剰**: `*`, `/`, `%`\n4. **加減**: `+`, `-`\n5. **代入**: `=`, `+=`, `-=`, `*=`, `/=`, `%=`\n\n## 複合代入演算子\n- `x += 5` は `x = x + 5` と同じ\n- `x -= 3` は `x = x - 3` と同じ\n- `x *= 2` は `x = x * 2` と同じ\n- `x /= 4` は `x = x / 4` と同じ",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第4回：String型とキーボード入力',
                'description' => 'String型の使い方とScannerクラスを使ったキーボード入力',
                'sort_order' => 4,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'String型の基本', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'Scannerクラスの使い方', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'nextInt()で整数入力', 'estimated_minutes' => 20, 'sort_order' => 3],
                    ['title' => 'nextLine()で文字列入力', 'estimated_minutes' => 20, 'sort_order' => 4],
                    ['title' => '実践問題：入力と出力', 'estimated_minutes' => 20, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'String型とは？',
                        'content' => "# String型とは？\n\n**String型**は、文字列（テキスト）を格納するデータ型です。\n\n## 特徴\n- 文字列はダブルクォート（\"\"）で囲む\n- 文字列は変更できない（イミュータブル）\n- +演算子で文字列を連結できる\n\n## 使用例\n```java\nString name = \"太郎\";\nString message = \"こんにちは\";\nString empty = \"\";  // 空文字列\n```\n\n## 文字列の連結\n```java\nString firstName = \"山田\";\nString lastName = \"太郎\";\nString fullName = firstName + lastName;  // \"山田太郎\"\n\nint age = 20;\nString text = \"年齢は\" + age + \"歳です\";  // \"年齢は20歳です\"\n```\n\n## エスケープシーケンス\n- `\\n`: 改行\n- `\\t`: タブ\n- `\\\"`: ダブルクォート\n- `\\\\`: バックスラッシュ\n\n```java\nString text = \"Hello\\nWorld\";  // 2行に分かれる\nString quote = \"彼は\\\"こんにちは\\\"と言った\";\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Scannerクラスとは？',
                        'content' => "# Scannerクラスとは？\n\n**Scanner**は、キーボードから入力を受け取るためのクラスです。\n\n## 基本的な使い方\n\n### 1. インポート\n```java\nimport java.util.Scanner;\n```\n\n### 2. Scannerオブジェクトの作成\n```java\nScanner in = new Scanner(System.in);\n```\n\n### 3. 入力を受け取る\n```java\nint number = in.nextInt();      // 整数\nString text = in.nextLine();    // 文字列\n```\n\n### 4. Scannerを閉じる\n```java\nin.close();\n```\n\n## 注意点\n- `nextInt()`の後に`nextLine()`を使う場合は、余分な改行を読み飛ばす必要がある\n- プログラムの最後で必ず`close()`を呼ぶ",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Scannerの基本的な使い方',
                        'content' => "import java.util.Scanner;\n\npublic class JKad04_Scanner {\n    public static void main(String[] args) {\n        // Scannerオブジェクトの作成\n        Scanner in = new Scanner(System.in);\n        \n        // 名前を入力\n        System.out.print(\"名前を入力してください＞\");\n        String name = in.nextLine();\n        \n        // 年齢を入力\n        System.out.print(\"年齢を入力してください＞\");\n        int age = in.nextInt();\n        \n        // 結果を表示\n        System.out.println(\"こんにちは、\" + name + \"さん（\" + age + \"歳）\");\n        \n        // Scannerを閉じる\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Scannerクラスの主要メソッド',
                        'content' => "# Scannerクラスの主要メソッド\n\n## 数値の入力\n```java\nint i = in.nextInt();        // int型\nlong l = in.nextLong();      // long型\ndouble d = in.nextDouble();  // double型\nfloat f = in.nextFloat();    // float型\n```\n\n## 文字列の入力\n```java\nString line = in.nextLine();  // 1行全体\nString word = in.next();      // 次の単語（空白まで）\n```\n\n## その他\n```java\nboolean b = in.nextBoolean();  // true/false\nboolean hasNext = in.hasNext();  // 次の入力があるか確認\n```\n\n## メソッドの違い\n\n| メソッド | 読み取り範囲 | 改行の扱い |\n|---------|------------|----------|\n| `next()` | 次の単語 | 空白で区切る |\n| `nextLine()` | 1行全体 | 改行まで |\n| `nextInt()` | 次の整数 | 数字以外で終了 |",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '複数の値を入力する',
                        'content' => "import java.util.Scanner;\n\npublic class JKad04_MultipleInputs {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        \n        System.out.print(\"1つ目の数値を入力＞\");\n        int num1 = in.nextInt();\n        \n        System.out.print(\"2つ目の数値を入力＞\");\n        int num2 = in.nextInt();\n        \n        System.out.print(\"3つ目の数値を入力＞\");\n        int num3 = in.nextInt();\n        \n        int sum = num1 + num2 + num3;\n        double average = sum / 3.0;  // 3.0で割ると小数点以下も計算される\n        \n        System.out.println(\"合計: \" + sum);\n        System.out.println(\"平均: \" + average);\n        \n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'nextInt()とnextLine()の組み合わせ',
                        'content' => "# nextInt()とnextLine()の組み合わせ\n\n## 問題点\n`nextInt()`の後に`nextLine()`を使うと、改行文字が残ってしまう問題があります。\n\n```java\nScanner in = new Scanner(System.in);\n\nSystem.out.print(\"年齢＞\");\nint age = in.nextInt();  // 「20」と入力してEnter\n\nSystem.out.print(\"名前＞\");\nString name = in.nextLine();  // 改行文字を読み取ってしまい、空文字列になる\n```\n\n## 解決方法1: 余分な改行を読み飛ばす\n```java\nint age = in.nextInt();\nin.nextLine();  // 改行文字を読み飛ばす\n\nString name = in.nextLine();  // 正しく名前を入力できる\n```\n\n## 解決方法2: すべてnextLine()で読み取る\n```java\nSystem.out.print(\"年齢＞\");\nString ageStr = in.nextLine();\nint age = Integer.parseInt(ageStr);  // 文字列をintに変換\n\nSystem.out.print(\"名前＞\");\nString name = in.nextLine();\n```\n\n## 推奨方法\n基本的には**解決方法1**を使うことが多いです。",
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '計算機プログラム',
                        'content' => "import java.util.Scanner;\n\npublic class JKad04_Calculator {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        \n        System.out.println(\"簡単な計算機\");\n        System.out.println(\"==================\");\n        \n        System.out.print(\"1つ目の数を入力＞\");\n        int a = in.nextInt();\n        \n        System.out.print(\"2つ目の数を入力＞\");\n        int b = in.nextInt();\n        \n        System.out.println();\n        System.out.println(\"計算結果:\");\n        System.out.println(a + \" + \" + b + \" = \" + (a + b));\n        System.out.println(a + \" - \" + b + \" = \" + (a - b));\n        System.out.println(a + \" * \" + b + \" = \" + (a * b));\n        \n        if (b != 0) {\n            System.out.println(a + \" / \" + b + \" = \" + (a / b));\n            System.out.println(a + \" % \" + b + \" = \" + (a % b));\n        } else {\n            System.out.println(\"0で割ることはできません\");\n        }\n        \n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 7
                    ],
                ],
            ],
        ]);

        // Milestone 2: 条件分岐 (第5回～第7回)
        $milestone2 = $template->milestones()->create([
            'title' => '条件分岐とループ基礎',
            'description' => 'if文、while文、ネストとインデント',
            'sort_order' => 2,
            'estimated_hours' => 15,
            'deliverables' => [
                'if文を使った条件分岐プログラム',
                'while文を使ったループプログラム',
                'ネストしたループの理解'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第5回：if文①',
                'description' => 'if文を使った条件分岐、比較演算子',
                'sort_order' => 5,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'if文の基本構文', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => '比較演算子（>, <, ==, !=）', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '最大値・最小値を求める', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題：5人の点数から最高点を求める', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'if文で最大値を求める',
                        'content' => "import java.util.Scanner;\n\npublic class JKad05A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"のび太の点数を入力してください＞\");\n        int score1 = in.nextInt();\n        System.out.print(\"しずかちゃんの点数を入力してください＞\");\n        int score2 = in.nextInt();\n        System.out.print(\"ジャイアンの点数を入力してください＞\");\n        int score3 = in.nextInt();\n        System.out.print(\"スネ夫の点数を入力してください＞\");\n        int score4 = in.nextInt();\n        System.out.print(\"出木杉くんの点数を入力してください＞\");\n        int score5 = in.nextInt();\n\n        int max = score1;\n        if (max < score2) max = score2;\n        if (max < score3) max = score3;\n        if (max < score4) max = score4;\n        if (max < score5) max = score5;\n\n        System.out.println(\"一番高い点数は\" + max + \"点です！\");\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'if文の構文',
                        'content' => "# if文の構文\n\n```java\nif (条件) {\n    // 条件がtrueの時に実行される処理\n}\n```\n\n## 比較演算子\n- `>` より大きい\n- `<` より小さい\n- `>=` 以上\n- `<=` 以下\n- `==` 等しい\n- `!=` 等しくない",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第6回：while文①',
                'description' => 'while文を使ったループ処理',
                'sort_order' => 6,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'while文の基本構文', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'カウンタ変数を使ったループ', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '条件によるループ終了', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'while文の構文',
                        'content' => "# while文の構文\n\n```java\nwhile (条件) {\n    // 条件がtrueの間、繰り返し実行される処理\n}\n```\n\n- 条件がfalseになるまで繰り返す\n- 無限ループに注意（条件が常にtrueの場合）",
                        'sort_order' => 1
                    ],
                ],
            ],
            [
                'title' => '第7回：ネストとインデント',
                'description' => 'ネストしたif文、ネストしたループ、インデントの重要性',
                'sort_order' => 7,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ネストしたif文', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'ネストしたループ', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'インデントの正しい使い方', 'estimated_minutes' => 20, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ネストしたif文の例',
                        'content' => "import java.util.Scanner;\n\npublic class JKad07A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"点数を入力してください＞\");\n        int score = in.nextInt();\n        \n        if (score >= 0 && score <= 100) {\n            if (score >= 90) {\n                System.out.println(\"評価：A（優秀）\");\n            } else if (score >= 70) {\n                System.out.println(\"評価：B（良好）\");\n            } else if (score >= 50) {\n                System.out.println(\"評価：C（合格）\");\n            } else {\n                System.out.println(\"評価：D（不合格）\");\n            }\n        } else {\n            System.out.println(\"エラー：0～100の範囲で入力してください\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ネストしたループの例（九九表）',
                        'content' => "public class JKad07B {\n    public static void main(String[] args) {\n        System.out.println(\"九九表を出力します\");\n        \n        int i = 1;\n        while (i <= 9) {\n            int j = 1;\n            while (j <= 9) {\n                System.out.print(i * j + \"\\t\");\n                j++;\n            }\n            System.out.println();  // 改行\n            i++;\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'インデントの重要性',
                        'content' => "# インデントの重要性\n\n## インデントとは\n- コードの階層構造を視覚的に表現する字下げ\n- 通常、スペース4つまたはタブ1つを使用\n\n## なぜ重要か\n1. **可読性**: コードの構造が一目でわかる\n2. **デバッグ**: エラーを見つけやすい\n3. **保守性**: 他人が読みやすいコード\n\n## インデントのルール\n- `{` の後は1段階深く\n- `}` の前は1段階浅く\n- 同じレベルのコードは同じインデント\n- if, while, for の中身は必ずインデント",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'インデントの良い例と悪い例',
                        'content' => "// 悪い例（インデントなし）\nif (x > 0) {\nSystem.out.println(\"正の数\");\nif (x > 10) {\nSystem.out.println(\"10より大きい\");\n}\n}\n\n// 良い例（正しいインデント）\nif (x > 0) {\n    System.out.println(\"正の数\");\n    if (x > 10) {\n        System.out.println(\"10より大きい\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 3: チャレンジ課題とテスト (第8回～第10回)
        $milestone3 = $template->milestones()->create([
            'title' => 'チャレンジ課題と復習',
            'description' => 'これまで学習した内容の総合的な復習と応用',
            'sort_order' => 3,
            'estimated_hours' => 12,
            'deliverables' => [
                'チャレンジ課題①を完了',
                'クラス替えテスト①の練習',
                'チャレンジ課題②を完了'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '第8回：チャレンジ課題①',
                'description' => '第1回～第7回までの総合的な復習課題',
                'sort_order' => 8,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '基本構文の復習', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '変数と演算の総合問題', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => 'if文とwhile文の組み合わせ', 'estimated_minutes' => 50, 'sort_order' => 3],
                    ['title' => '応用課題', 'estimated_minutes' => 40, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '復習すべき重要ポイント',
                        'content' => "# 第1回～第7回の復習ポイント\n\n## 1. 基本構文\n- System.out.println()で出力\n- メソッドの定義と呼び出し\n\n## 2. 変数と式\n- int型の宣言と代入\n- 四則演算、剰余演算\n- 複合代入演算子（+=, -=など）\n\n## 3. キーボード入力\n- Scannerクラスの使い方\n- nextInt(), nextLine()\n\n## 4. 条件分岐\n- if文の基本構文\n- 比較演算子\n- ネストしたif文\n\n## 5. ループ\n- while文の基本\n- ループカウンタ\n- ネストしたループ",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '練習問題：数当てゲーム',
                        'content' => "import java.util.Scanner;\n\npublic class Challenge01 {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        int answer = 42;  // 正解の数\n        int guess = 0;\n        int count = 0;\n        \n        System.out.println(\"数当てゲーム（1～100）\");\n        \n        while (guess != answer) {\n            System.out.print(\"予想する数を入力＞\");\n            guess = in.nextInt();\n            count++;\n            \n            if (guess < answer) {\n                System.out.println(\"もっと大きいです\");\n            } else if (guess > answer) {\n                System.out.println(\"もっと小さいです\");\n            } else {\n                System.out.println(\"正解！ \" + count + \"回で当たりました！\");\n            }\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第9回：クラス替えテスト①（練習問題）',
                'description' => '第1回～第8回までの理解度を確認するテスト練習',
                'sort_order' => 9,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => '基本問題（変数と式）', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => '応用問題（if文）', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '応用問題（while文）', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '総合問題', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'テスト対策のチェックリスト',
                        'content' => "# テスト対策チェックリスト\n\n## 確認すべき内容\n- [ ] 変数の宣言と初期化ができる\n- [ ] 四則演算、剰余演算の計算ができる\n- [ ] Scannerでキーボード入力を受け取れる\n- [ ] if文で条件分岐ができる\n- [ ] while文でループ処理ができる\n- [ ] ネストした構造を正しく書ける\n- [ ] インデントを正しく使える\n- [ ] エラーメッセージを読んでデバッグできる",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'テスト練習問題：合計と平均',
                        'content' => "import java.util.Scanner;\n\npublic class TestPractice {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        int sum = 0;\n        int count = 0;\n        \n        System.out.println(\"整数を入力してください（0で終了）\");\n        \n        while (true) {\n            System.out.print(\"数値＞\");\n            int num = in.nextInt();\n            \n            if (num == 0) {\n                break;\n            }\n            \n            sum += num;\n            count++;\n        }\n        \n        if (count > 0) {\n            double average = (double)sum / count;\n            System.out.println(\"合計: \" + sum);\n            System.out.println(\"平均: \" + average);\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第10回：チャレンジ課題②',
                'description' => 'より高度な総合課題',
                'sort_order' => 10,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '複雑なループ処理', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'パターン出力', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '総合応用課題', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャレンジ：素数判定',
                        'content' => "import java.util.Scanner;\n\npublic class Challenge02 {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"整数を入力してください＞\");\n        int n = in.nextInt();\n        \n        boolean isPrime = true;\n        \n        if (n <= 1) {\n            isPrime = false;\n        } else {\n            int i = 2;\n            while (i * i <= n) {\n                if (n % i == 0) {\n                    isPrime = false;\n                    break;\n                }\n                i++;\n            }\n        }\n        \n        if (isPrime) {\n            System.out.println(n + \"は素数です\");\n        } else {\n            System.out.println(n + \"は素数ではありません\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャレンジ：三角形のパターン',
                        'content' => "import java.util.Scanner;\n\npublic class Pattern {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"段数を入力してください＞\");\n        int n = in.nextInt();\n        \n        int i = 1;\n        while (i <= n) {\n            int j = 1;\n            while (j <= i) {\n                System.out.print(\"*\");\n                j++;\n            }\n            System.out.println();\n            i++;\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 4: 高度な条件分岐とループ (第11回～第17回)
        $milestone4 = $template->milestones()->create([
            'title' => '高度な条件分岐とループ',
            'description' => 'if-else if、論理演算子、do-while、for文、配列',
            'sort_order' => 4,
            'estimated_hours' => 30,
            'deliverables' => [
                'if-else if文をマスター',
                '論理演算子とboolean型を理解',
                'do-while、break、continueを使える',
                '配列を操作できる',
                'for文を使ったループ処理'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第11回：変数と式③（整数型と実数型）',
                'description' => 'long型、double型、float型の使い方',
                'sort_order' => 11,
                'estimated_minutes' => 90,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'long型の使い方', 'estimated_minutes' => 20, 'sort_order' => 1],
                    ['title' => 'double型とfloat型', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '型変換（キャスト）', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 10, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '整数型と実数型の基本',
                        'content' => "public class JKad11A {\n    public static void main(String[] args) {\n        // 整数型\n        int a = 2147483647;  // int型の最大値\n        long b = 9223372036854775807L;  // long型（末尾にL）\n        \n        // 実数型\n        double c = 3.14159265359;\n        float d = 3.14f;  // float型（末尾にf）\n        \n        System.out.println(\"int: \" + a);\n        System.out.println(\"long: \" + b);\n        System.out.println(\"double: \" + c);\n        System.out.println(\"float: \" + d);\n        \n        // 整数の割り算と実数の割り算\n        int x = 7;\n        int y = 2;\n        System.out.println(\"7 / 2 (int) = \" + (x / y));  // 3\n        System.out.println(\"7 / 2 (double) = \" + ((double)x / y));  // 3.5\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'データ型の種類と範囲',
                        'content' => "# データ型の種類\n\n## 整数型\n- `byte`: -128 ～ 127 (8ビット)\n- `short`: -32,768 ～ 32,767 (16ビット)\n- `int`: -2,147,483,648 ～ 2,147,483,647 (32ビット)\n- `long`: -9,223,372,036,854,775,808 ～ 9,223,372,036,854,775,807 (64ビット)\n\n## 実数型\n- `float`: 単精度浮動小数点数 (32ビット)\n- `double`: 倍精度浮動小数点数 (64ビット)\n\n## 型変換（キャスト）\n```java\nint x = 10;\ndouble y = (double)x;  // 明示的キャスト\nint z = (int)3.14;     // 3（小数点以下切り捨て）\n```",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第12回：if文②（if～else if）',
                'description' => 'if-else if-else構文、複数の条件分岐',
                'sort_order' => 12,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'if-else文の基本', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'if-else if-else構文', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '複数条件の判定', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'if-else if-else構文の例',
                        'content' => "import java.util.Scanner;\n\npublic class JKad12A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"点数を入力してください＞\");\n        int score = in.nextInt();\n        \n        if (score >= 90) {\n            System.out.println(\"評価：A（優秀）\");\n        } else if (score >= 80) {\n            System.out.println(\"評価：B（良好）\");\n        } else if (score >= 70) {\n            System.out.println(\"評価：C（普通）\");\n        } else if (score >= 60) {\n            System.out.println(\"評価：D（要努力）\");\n        } else {\n            System.out.println(\"評価：F（不合格）\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'if-else if-else構文のポイント',
                        'content' => "# if-else if-else構文\n\n## 構文\n```java\nif (条件1) {\n    // 条件1がtrueの時\n} else if (条件2) {\n    // 条件1がfalseで条件2がtrueの時\n} else if (条件3) {\n    // 条件1, 2がfalseで条件3がtrueの時\n} else {\n    // すべての条件がfalseの時\n}\n```\n\n## ポイント\n- 上から順に条件を評価\n- 最初にtrueになった条件のブロックを実行\n- それ以降の条件は評価されない\n- else は省略可能",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第13回：if文③（論理演算子、boolean）',
                'description' => '&&（AND）、||（OR）、!（NOT）、boolean型',
                'sort_order' => 13,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'boolean型の基本', 'estimated_minutes' => 20, 'sort_order' => 1],
                    ['title' => '論理演算子（&&, ||, !）', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '複雑な条件式', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '論理演算子の例',
                        'content' => "import java.util.Scanner;\n\npublic class JKad13A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"年齢を入力してください＞\");\n        int age = in.nextInt();\n        System.out.print(\"身長を入力してください（cm）＞\");\n        int height = in.nextInt();\n        \n        // && (AND): 両方trueの時にtrue\n        if (age >= 12 && height >= 140) {\n            System.out.println(\"ジェットコースターに乗れます\");\n        } else {\n            System.out.println(\"ジェットコースターに乗れません\");\n        }\n        \n        // || (OR): どちらかがtrueの時にtrue\n        if (age < 6 || age >= 65) {\n            System.out.println(\"入場料：無料\");\n        } else {\n            System.out.println(\"入場料：1000円\");\n        }\n        \n        // ! (NOT): trueとfalseを反転\n        boolean isRaining = false;\n        if (!isRaining) {\n            System.out.println(\"傘は不要です\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '論理演算子の真理値表',
                        'content' => "# 論理演算子\n\n## AND演算子 (&&)\n| A | B | A && B |\n|---|---|--------|\n| true | true | true |\n| true | false | false |\n| false | true | false |\n| false | false | false |\n\n## OR演算子 (||)\n| A | B | A \\|\\| B |\n|---|---|--------|\n| true | true | true |\n| true | false | true |\n| false | true | true |\n| false | false | false |\n\n## NOT演算子 (!)\n| A | !A |\n|---|----|\n| true | false |\n| false | true |\n\n## 短絡評価\n- `A && B`: Aがfalseなら、Bは評価されない\n- `A || B`: Aがtrueなら、Bは評価されない",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第14回：while文②（do～while、break、continue）',
                'description' => 'do-while文、break文、continue文',
                'sort_order' => 14,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'do-while文の基本', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'break文でループ脱出', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'continue文でスキップ', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'do-while文の例',
                        'content' => "import java.util.Scanner;\n\npublic class JKad14A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        int num;\n        \n        // do-while: 最低1回は実行される\n        do {\n            System.out.print(\"1～10の数を入力してください＞\");\n            num = in.nextInt();\n            \n            if (num < 1 || num > 10) {\n                System.out.println(\"エラー：1～10の範囲で入力してください\");\n            }\n        } while (num < 1 || num > 10);\n        \n        System.out.println(\"入力された数：\" + num);\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'break文とcontinue文の例',
                        'content' => "public class JKad14B {\n    public static void main(String[] args) {\n        // break: ループを抜ける\n        System.out.println(\"breakの例：\");\n        int i = 1;\n        while (i <= 10) {\n            if (i == 5) {\n                break;  // i が 5 になったらループ終了\n            }\n            System.out.print(i + \" \");\n            i++;\n        }\n        System.out.println();\n        \n        // continue: 次の繰り返しへ\n        System.out.println(\"continueの例：\");\n        int j = 0;\n        while (j < 10) {\n            j++;\n            if (j % 2 == 0) {\n                continue;  // 偶数はスキップ\n            }\n            System.out.print(j + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'while文とdo-while文の違い',
                        'content' => "# while文 vs do-while文\n\n## while文\n```java\nwhile (条件) {\n    // 処理\n}\n```\n- 条件を先にチェック\n- 条件がfalseなら1回も実行されない\n\n## do-while文\n```java\ndo {\n    // 処理\n} while (条件);\n```\n- 処理を先に実行\n- **最低1回は必ず実行される**\n- 入力チェックなどに便利\n\n## break文とcontinue文\n- `break`: ループを完全に抜ける\n- `continue`: 現在の繰り返しをスキップして次へ",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第15回：配列',
                'description' => '配列の宣言、初期化、要素へのアクセス',
                'sort_order' => 15,
                'estimated_minutes' => 150,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '配列の宣言と初期化', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '配列要素へのアクセス', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => '配列の長さ（length）', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '配列の応用問題', 'estimated_minutes' => 50, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '配列の基本',
                        'content' => "public class JKad15A {\n    public static void main(String[] args) {\n        // 配列の宣言と初期化（方法1）\n        int[] scores = new int[5];\n        scores[0] = 85;\n        scores[1] = 92;\n        scores[2] = 78;\n        scores[3] = 95;\n        scores[4] = 88;\n        \n        // 配列の宣言と初期化（方法2）\n        int[] numbers = {10, 20, 30, 40, 50};\n        \n        // 配列要素へのアクセス\n        System.out.println(\"1人目の点数: \" + scores[0]);\n        System.out.println(\"3人目の点数: \" + scores[2]);\n        \n        // 配列の長さ\n        System.out.println(\"配列の長さ: \" + scores.length);\n        \n        // 配列の全要素を表示\n        int i = 0;\n        while (i < scores.length) {\n            System.out.println((i+1) + \"人目: \" + scores[i]);\n            i++;\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '配列を使った合計と平均',
                        'content' => "public class JKad15B {\n    public static void main(String[] args) {\n        int[] scores = {85, 92, 78, 95, 88};\n        int sum = 0;\n        \n        // 合計を計算\n        int i = 0;\n        while (i < scores.length) {\n            sum += scores[i];\n            i++;\n        }\n        \n        // 平均を計算\n        double average = (double)sum / scores.length;\n        \n        System.out.println(\"合計: \" + sum);\n        System.out.println(\"平均: \" + average);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => '配列の重要ポイント',
                        'content' => "# 配列の基本\n\n## 配列の宣言\n```java\nデータ型[] 配列名 = new データ型[要素数];\n```\n\n## 配列の初期化\n```java\n// 方法1: 後から値を代入\nint[] arr = new int[5];\narr[0] = 10;\n\n// 方法2: 宣言時に初期化\nint[] arr = {10, 20, 30, 40, 50};\n```\n\n## 重要な点\n- インデックスは0から始まる\n- 配列の長さは `配列名.length`\n- 範囲外アクセスはエラー（ArrayIndexOutOfBoundsException）\n- 配列の長さは後から変更できない",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第16回：for文①（for文の基本と配列）',
                'description' => 'for文の基本構文、配列との組み合わせ',
                'sort_order' => 16,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'for文の基本構文', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'for文と配列の組み合わせ', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '拡張for文（for-each）', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'for文の基本',
                        'content' => "public class JKad16A {\n    public static void main(String[] args) {\n        // for文の基本構文\n        // for (初期化; 条件; 更新) { 処理 }\n        \n        // 1から10まで表示\n        for (int i = 1; i <= 10; i++) {\n            System.out.print(i + \" \");\n        }\n        System.out.println();\n        \n        // 配列の全要素を表示\n        int[] scores = {85, 92, 78, 95, 88};\n        for (int i = 0; i < scores.length; i++) {\n            System.out.println((i+1) + \"人目: \" + scores[i]);\n        }\n        \n        // 拡張for文（for-each）\n        System.out.println(\"拡張for文:\");\n        for (int score : scores) {\n            System.out.println(\"点数: \" + score);\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'for文を使った配列操作',
                        'content' => "public class JKad16B {\n    public static void main(String[] args) {\n        int[] numbers = {3, 7, 2, 9, 5, 1, 8};\n        \n        // 最大値を求める\n        int max = numbers[0];\n        for (int i = 1; i < numbers.length; i++) {\n            if (numbers[i] > max) {\n                max = numbers[i];\n            }\n        }\n        System.out.println(\"最大値: \" + max);\n        \n        // 偶数の個数を数える\n        int count = 0;\n        for (int num : numbers) {\n            if (num % 2 == 0) {\n                count++;\n            }\n        }\n        System.out.println(\"偶数の個数: \" + count);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'for文の構文',
                        'content' => "# for文の構文\n\n## 基本形\n```java\nfor (初期化; 条件; 更新) {\n    // 繰り返す処理\n}\n```\n\n## 実行順序\n1. 初期化（最初に1回だけ）\n2. 条件チェック\n3. 条件がtrueなら処理実行\n4. 更新\n5. 2に戻る\n\n## while文との比較\n```java\n// for文\nfor (int i = 0; i < 10; i++) {\n    System.out.println(i);\n}\n\n// 同じ処理をwhile文で\nint i = 0;\nwhile (i < 10) {\n    System.out.println(i);\n    i++;\n}\n```\n\n## 拡張for文（for-each）\n```java\nfor (データ型 変数名 : 配列名) {\n    // 処理\n}\n```\n- インデックス不要\n- 読み取り専用に便利",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第17回：for文②（ループ処理の流れ、スコープ）',
                'description' => 'for文の実行フロー、変数のスコープ',
                'sort_order' => 17,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'for文の実行フロー詳細', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => '変数のスコープ', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'ネストしたfor文', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '変数のスコープ',
                        'content' => "public class JKad17A {\n    public static void main(String[] args) {\n        int x = 10;  // mainメソッド内で有効\n        \n        for (int i = 0; i < 3; i++) {\n            int y = 20;  // forブロック内でのみ有効\n            System.out.println(\"x=\" + x + \", y=\" + y + \", i=\" + i);\n        }\n        \n        System.out.println(\"x=\" + x);  // OK\n        // System.out.println(\"y=\" + y);  // エラー: yはスコープ外\n        // System.out.println(\"i=\" + i);  // エラー: iはスコープ外\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ネストしたfor文の例',
                        'content' => "public class JKad17B {\n    public static void main(String[] args) {\n        // 九九表を出力\n        for (int i = 1; i <= 9; i++) {\n            for (int j = 1; j <= 9; j++) {\n                System.out.print(i * j + \"\\t\");\n            }\n            System.out.println();  // 改行\n        }\n        \n        // 三角形のパターン\n        System.out.println(\"\\n三角形パターン:\");\n        for (int i = 1; i <= 5; i++) {\n            for (int j = 1; j <= i; j++) {\n                System.out.print(\"*\");\n            }\n            System.out.println();\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => '変数のスコープとは',
                        'content' => "# 変数のスコープ\n\n## スコープとは\n変数が有効な範囲のこと。変数は宣言されたブロック `{ }` 内でのみ使用可能。\n\n## スコープの種類\n\n### 1. メソッドスコープ\n```java\npublic static void main(String[] args) {\n    int x = 10;  // メソッド全体で有効\n}\n```\n\n### 2. ブロックスコープ\n```java\nif (条件) {\n    int y = 20;  // このブロック内でのみ有効\n}\n// ここではyは使えない\n```\n\n### 3. ループスコープ\n```java\nfor (int i = 0; i < 10; i++) {\n    // iはこのループ内でのみ有効\n}\n// ここではiは使えない\n```\n\n## 重要なルール\n- 内側のスコープから外側の変数にアクセス可能\n- 外側のスコープから内側の変数にアクセス不可\n- 同じスコープ内で同じ名前の変数は宣言できない",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 5: チャレンジとテスト (第18回～第20回)
        $milestone5 = $template->milestones()->create([
            'title' => 'チャレンジ課題と中間テスト',
            'description' => '中間的な復習とテスト',
            'sort_order' => 5,
            'estimated_hours' => 12,
            'deliverables' => [
                'チャレンジ課題③を完了',
                'クラス替えテスト②の練習',
                'チャレンジ課題④を完了'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => '第18回：チャレンジ課題③',
                'description' => '第11回～第17回までの総合的な復習課題',
                'sort_order' => 18,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '配列の応用問題', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'for文の応用問題', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '総合課題', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '第11回～第17回の復習ポイント',
                        'content' => "# 復習ポイント\n\n## 1. データ型\n- long型、double型、float型\n- 型変換（キャスト）\n\n## 2. 条件分岐の応用\n- if-else if-else構文\n- 論理演算子（&&, ||, !）\n- boolean型\n\n## 3. ループの応用\n- do-while文\n- break文、continue文\n\n## 4. 配列\n- 配列の宣言と初期化\n- 配列要素へのアクセス\n- length プロパティ\n\n## 5. for文\n- for文の基本構文\n- 拡張for文（for-each）\n- ネストしたfor文\n- 変数のスコープ",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャレンジ：配列のソート（選択ソート）',
                        'content' => "public class Challenge03 {\n    public static void main(String[] args) {\n        int[] numbers = {64, 34, 25, 12, 22, 11, 90};\n        \n        System.out.println(\"ソート前:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n        System.out.println();\n        \n        // 選択ソート\n        for (int i = 0; i < numbers.length - 1; i++) {\n            int minIndex = i;\n            for (int j = i + 1; j < numbers.length; j++) {\n                if (numbers[j] < numbers[minIndex]) {\n                    minIndex = j;\n                }\n            }\n            // 要素の入れ替え\n            int temp = numbers[i];\n            numbers[i] = numbers[minIndex];\n            numbers[minIndex] = temp;\n        }\n        \n        System.out.println(\"ソート後:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第19回：クラス替えテスト②（練習問題）',
                'description' => '第11回～第18回までの理解度を確認するテスト練習',
                'sort_order' => 19,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => '基本問題（配列とfor文）', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '応用問題（論理演算）', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '総合問題', 'estimated_minutes' => 40, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'テスト対策チェックリスト',
                        'content' => "# テスト対策チェックリスト\n\n## 確認すべき内容\n- [ ] long型、double型の使い分けができる\n- [ ] 型変換（キャスト）ができる\n- [ ] if-else if-else構文を書ける\n- [ ] 論理演算子（&&, ||, !）を使える\n- [ ] do-while文を書ける\n- [ ] break、continueの違いを理解している\n- [ ] 配列を宣言・初期化できる\n- [ ] for文を使って配列を操作できる\n- [ ] ネストしたfor文を書ける\n- [ ] 変数のスコープを理解している",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'テスト練習問題：配列の統計',
                        'content' => "public class TestPractice2 {\n    public static void main(String[] args) {\n        int[] scores = {85, 92, 78, 95, 88, 76, 90, 82};\n        \n        // 合計、平均、最大値、最小値を求める\n        int sum = 0;\n        int max = scores[0];\n        int min = scores[0];\n        \n        for (int score : scores) {\n            sum += score;\n            if (score > max) max = score;\n            if (score < min) min = score;\n        }\n        \n        double average = (double)sum / scores.length;\n        \n        System.out.println(\"合計: \" + sum);\n        System.out.println(\"平均: \" + average);\n        System.out.println(\"最高点: \" + max);\n        System.out.println(\"最低点: \" + min);\n        \n        // 平均以上の人数を数える\n        int count = 0;\n        for (int score : scores) {\n            if (score >= average) {\n                count++;\n            }\n        }\n        System.out.println(\"平均以上の人数: \" + count + \"人\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第20回：チャレンジ課題④',
                'description' => 'より高度な総合課題',
                'sort_order' => 20,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '2次元配列の準備', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '探索アルゴリズム', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '総合応用課題', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャレンジ：線形探索',
                        'content' => "import java.util.Scanner;\n\npublic class Challenge04A {\n    public static void main(String[] args) {\n        int[] numbers = {15, 42, 8, 23, 67, 91, 34, 56, 12, 78};\n        Scanner in = new Scanner(System.in);\n        \n        System.out.print(\"探す数を入力してください＞\");\n        int target = in.nextInt();\n        \n        // 線形探索\n        int index = -1;\n        for (int i = 0; i < numbers.length; i++) {\n            if (numbers[i] == target) {\n                index = i;\n                break;\n            }\n        }\n        \n        if (index != -1) {\n            System.out.println(target + \"は\" + index + \"番目に見つかりました\");\n        } else {\n            System.out.println(target + \"は見つかりませんでした\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャレンジ：2次元配列の基本',
                        'content' => "public class Challenge04B {\n    public static void main(String[] args) {\n        // 2次元配列（3人の5教科の点数）\n        int[][] scores = {\n            {80, 75, 90, 85, 88},  // 1人目\n            {92, 88, 95, 90, 93},  // 2人目\n            {70, 65, 78, 72, 75}   // 3人目\n        };\n        \n        // 各人の合計と平均\n        for (int i = 0; i < scores.length; i++) {\n            int sum = 0;\n            for (int j = 0; j < scores[i].length; j++) {\n                sum += scores[i][j];\n            }\n            double average = (double)sum / scores[i].length;\n            System.out.println((i+1) + \"人目 - 合計: \" + sum + \", 平均: \" + average);\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 6: 高度なプログラミング (第21回～第26回)
        $milestone6 = $template->milestones()->create([
            'title' => '高度なプログラミング技法',
            'description' => '多重ループ、多次元配列、メソッド、switch文、文字列操作、ビット演算',
            'sort_order' => 6,
            'estimated_hours' => 36,
            'deliverables' => [
                '多重ループと多次元配列をマスター',
                'メソッドを定義して使える',
                'switch文を使った分岐処理',
                '文字列操作を理解',
                'ビット演算とシフト演算を理解'
            ],
        ]);

        $milestone6->tasks()->createMany([
            [
                'title' => '第21回：多重ループと多次元配列',
                'description' => 'ネストしたfor文、2次元配列、3次元配列',
                'sort_order' => 21,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '2次元配列の宣言と初期化', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '多重ループでの配列操作', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '3次元配列の基本', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '2次元配列の基本',
                        'content' => "public class JKad21A {\n    public static void main(String[] args) {\n        // 2次元配列の宣言（3行4列）\n        int[][] matrix = {\n            {1, 2, 3, 4},\n            {5, 6, 7, 8},\n            {9, 10, 11, 12}\n        };\n        \n        // 多重ループで全要素を表示\n        for (int i = 0; i < matrix.length; i++) {\n            for (int j = 0; j < matrix[i].length; j++) {\n                System.out.print(matrix[i][j] + \"\\t\");\n            }\n            System.out.println();\n        }\n        \n        // 拡張for文を使った表示\n        System.out.println(\"\\n拡張for文:\");\n        for (int[] row : matrix) {\n            for (int value : row) {\n                System.out.print(value + \"\\t\");\n            }\n            System.out.println();\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '2次元配列の応用：行列の転置',
                        'content' => "public class JKad21B {\n    public static void main(String[] args) {\n        int[][] original = {\n            {1, 2, 3},\n            {4, 5, 6}\n        };\n        \n        // 転置行列（行と列を入れ替え）\n        int rows = original.length;\n        int cols = original[0].length;\n        int[][] transposed = new int[cols][rows];\n        \n        for (int i = 0; i < rows; i++) {\n            for (int j = 0; j < cols; j++) {\n                transposed[j][i] = original[i][j];\n            }\n        }\n        \n        System.out.println(\"元の行列:\");\n        for (int[] row : original) {\n            for (int val : row) {\n                System.out.print(val + \" \");\n            }\n            System.out.println();\n        }\n        \n        System.out.println(\"\\n転置行列:\");\n        for (int[] row : transposed) {\n            for (int val : row) {\n                System.out.print(val + \" \");\n            }\n            System.out.println();\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => '多次元配列の重要ポイント',
                        'content' => "# 多次元配列\n\n## 2次元配列の宣言\n```java\n// 方法1: 後から代入\nint[][] arr = new int[3][4];  // 3行4列\n\n// 方法2: 初期化\nint[][] arr = {\n    {1, 2, 3},\n    {4, 5, 6}\n};\n```\n\n## アクセス方法\n```java\nint value = arr[行インデックス][列インデックス];\n```\n\n## 長さの取得\n- `arr.length`: 行数\n- `arr[i].length`: i行目の列数\n\n## 3次元配列\n```java\nint[][][] cube = new int[2][3][4];\n// 2つの3×4行列\n```\n\n## 用途\n- 表・グリッドデータ\n- ゲームボード（チェス、碁など）\n- 画像データ",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第22回：メソッド①',
                'description' => 'メソッドの定義、引数、戻り値',
                'sort_order' => 22,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'メソッドの基本構文', 'estimated_minutes' => 45, 'sort_order' => 1],
                    ['title' => '引数と戻り値', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'メソッドの実践', 'estimated_minutes' => 75, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッドの基本',
                        'content' => "public class JKad22A {\n    // 戻り値なし、引数なし\n    public static void sayHello() {\n        System.out.println(\"こんにちは！\");\n    }\n    \n    // 戻り値あり、引数なし\n    public static int getRandomNumber() {\n        return 42;\n    }\n    \n    // 戻り値なし、引数あり\n    public static void printMessage(String message) {\n        System.out.println(\"メッセージ: \" + message);\n    }\n    \n    // 戻り値あり、引数あり\n    public static int add(int a, int b) {\n        return a + b;\n    }\n    \n    public static void main(String[] args) {\n        sayHello();\n        \n        int num = getRandomNumber();\n        System.out.println(\"数値: \" + num);\n        \n        printMessage(\"テスト\");\n        \n        int sum = add(10, 20);\n        System.out.println(\"合計: \" + sum);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッドを使った配列操作',
                        'content' => "public class JKad22B {\n    // 配列の合計を返すメソッド\n    public static int sumArray(int[] arr) {\n        int sum = 0;\n        for (int num : arr) {\n            sum += num;\n        }\n        return sum;\n    }\n    \n    // 配列の平均を返すメソッド\n    public static double averageArray(int[] arr) {\n        return (double)sumArray(arr) / arr.length;\n    }\n    \n    // 配列の最大値を返すメソッド\n    public static int maxArray(int[] arr) {\n        int max = arr[0];\n        for (int num : arr) {\n            if (num > max) max = num;\n        }\n        return max;\n    }\n    \n    public static void main(String[] args) {\n        int[] scores = {85, 92, 78, 95, 88};\n        \n        System.out.println(\"合計: \" + sumArray(scores));\n        System.out.println(\"平均: \" + averageArray(scores));\n        System.out.println(\"最大値: \" + maxArray(scores));\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'メソッドの構文',
                        'content' => "# メソッドの構文\n\n## 基本形\n```java\nアクセス修飾子 static 戻り値の型 メソッド名(引数リスト) {\n    // 処理\n    return 戻り値;  // 戻り値がある場合\n}\n```\n\n## 例\n```java\npublic static int add(int a, int b) {\n    return a + b;\n}\n```\n\n## 重要なポイント\n- **戻り値の型**: メソッドが返す値の型（void=返さない）\n- **引数**: メソッドに渡す値\n- **return文**: 値を返してメソッドを終了\n- **メソッド名**: 動詞で始める（add, get, calculate など）\n\n## メリット\n1. コードの再利用\n2. 可読性の向上\n3. デバッグが容易\n4. 保守性の向上",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第23回：メソッド②',
                'description' => 'メソッドのオーバーロード、再帰呼び出し',
                'sort_order' => 23,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'メソッドのオーバーロード', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '再帰呼び出しの基本', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '再帰の応用', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッドのオーバーロード',
                        'content' => "public class JKad23A {\n    // 同じ名前のメソッドを複数定義（引数の型や数が異なる）\n    \n    // int型2つの足し算\n    public static int add(int a, int b) {\n        return a + b;\n    }\n    \n    // int型3つの足し算\n    public static int add(int a, int b, int c) {\n        return a + b + c;\n    }\n    \n    // double型2つの足し算\n    public static double add(double a, double b) {\n        return a + b;\n    }\n    \n    public static void main(String[] args) {\n        System.out.println(add(10, 20));          // 30\n        System.out.println(add(10, 20, 30));      // 60\n        System.out.println(add(1.5, 2.3));        // 3.8\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '再帰呼び出しの例',
                        'content' => "public class JKad23B {\n    // 階乗を計算する再帰メソッド\n    // 5! = 5 × 4 × 3 × 2 × 1 = 120\n    public static int factorial(int n) {\n        if (n <= 1) {\n            return 1;  // 基底ケース\n        }\n        return n * factorial(n - 1);  // 再帰呼び出し\n    }\n    \n    // フィボナッチ数列\n    // 0, 1, 1, 2, 3, 5, 8, 13, 21, ...\n    public static int fibonacci(int n) {\n        if (n <= 1) {\n            return n;  // 基底ケース\n        }\n        return fibonacci(n - 1) + fibonacci(n - 2);\n    }\n    \n    public static void main(String[] args) {\n        System.out.println(\"5! = \" + factorial(5));  // 120\n        \n        System.out.print(\"フィボナッチ数列: \");\n        for (int i = 0; i < 10; i++) {\n            System.out.print(fibonacci(i) + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'オーバーロードと再帰',
                        'content' => "# メソッドのオーバーロード\n\n同じ名前で引数の型や数が異なるメソッドを複数定義すること\n\n## 条件\n- メソッド名が同じ\n- 引数の数、型、順序が異なる\n- 戻り値の型だけ異なるのはNG\n\n---\n\n# 再帰呼び出し\n\nメソッドが自分自身を呼び出すこと\n\n## 必須要素\n1. **基底ケース**: 再帰を終了する条件\n2. **再帰ケース**: 自分自身を呼び出す部分\n\n## 注意点\n- 必ず終了条件を設定\n- 深い再帰はスタックオーバーフローの危険\n- ループで書ける場合はループの方が効率的なことも\n\n## 再帰が有効な場面\n- 木構造の探索\n- 分割統治法\n- 数学的な定義（階乗、フィボナッチなど）",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第24回：switch文',
                'description' => 'switch-case文を使った分岐処理',
                'sort_order' => 24,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'switch文の基本構文', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'break文とdefault', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 40, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'switch文の基本',
                        'content' => "import java.util.Scanner;\n\npublic class JKad24A {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"曜日を数字で入力してください（1-7）＞\");\n        int day = in.nextInt();\n        \n        switch (day) {\n            case 1:\n                System.out.println(\"月曜日\");\n                break;\n            case 2:\n                System.out.println(\"火曜日\");\n                break;\n            case 3:\n                System.out.println(\"水曜日\");\n                break;\n            case 4:\n                System.out.println(\"木曜日\");\n                break;\n            case 5:\n                System.out.println(\"金曜日\");\n                break;\n            case 6:\n                System.out.println(\"土曜日\");\n                break;\n            case 7:\n                System.out.println(\"日曜日\");\n                break;\n            default:\n                System.out.println(\"エラー：1～7を入力してください\");\n        }\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'breakを省略した例（フォールスルー）',
                        'content' => "import java.util.Scanner;\n\npublic class JKad24B {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"月を入力してください（1-12）＞\");\n        int month = in.nextInt();\n        \n        String season;\n        switch (month) {\n            case 12:\n            case 1:\n            case 2:\n                season = \"冬\";\n                break;\n            case 3:\n            case 4:\n            case 5:\n                season = \"春\";\n                break;\n            case 6:\n            case 7:\n            case 8:\n                season = \"夏\";\n                break;\n            case 9:\n            case 10:\n            case 11:\n                season = \"秋\";\n                break;\n            default:\n                season = \"不明\";\n        }\n        \n        System.out.println(season + \"です\");\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'switch文の構文',
                        'content' => "# switch文の構文\n\n```java\nswitch (式) {\n    case 値1:\n        // 処理1\n        break;\n    case 値2:\n        // 処理2\n        break;\n    default:\n        // どのcaseにも該当しない時の処理\n}\n```\n\n## 重要なポイント\n- 式の値は int, char, String, enum のいずれか\n- **break**: 次のcaseに進まない（省略すると次へ続く）\n- **default**: 省略可能（どのcaseにも該当しない時）\n\n## if文との使い分け\n- **switch**: 1つの変数の値で分岐\n- **if-else if**: 複雑な条件式や範囲判定",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第25回：文字と文字列',
                'description' => 'char型、String型の詳細な操作、文字列メソッド',
                'sort_order' => 25,
                'estimated_minutes' => 150,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'char型の基本', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'String型のメソッド', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '文字列の比較と検索', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 20, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'char型と文字列の基本',
                        'content' => "public class JKad25A {\n    public static void main(String[] args) {\n        // char型（1文字）\n        char c1 = 'A';\n        char c2 = '漢';\n        char c3 = '\\n';  // エスケープシーケンス（改行）\n        \n        System.out.println(\"文字: \" + c1);\n        System.out.println(\"文字コード: \" + (int)c1);  // 65\n        \n        // String型（文字列）\n        String str = \"Hello, World!\";\n        System.out.println(\"文字列: \" + str);\n        System.out.println(\"長さ: \" + str.length());\n        System.out.println(\"3番目の文字: \" + str.charAt(2));  // 'l'\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Stringクラスの主要メソッド',
                        'content' => "public class JKad25B {\n    public static void main(String[] args) {\n        String str = \"Hello, Java Programming!\";\n        \n        // 長さ\n        System.out.println(\"length: \" + str.length());\n        \n        // 部分文字列\n        System.out.println(\"substring(7, 11): \" + str.substring(7, 11));  // \"Java\"\n        \n        // 大文字・小文字変換\n        System.out.println(\"toUpperCase: \" + str.toUpperCase());\n        System.out.println(\"toLowerCase: \" + str.toLowerCase());\n        \n        // 検索\n        System.out.println(\"indexOf('Java'): \" + str.indexOf(\"Java\"));  // 7\n        System.out.println(\"contains('Java'): \" + str.contains(\"Java\"));  // true\n        \n        // 置換\n        System.out.println(\"replace: \" + str.replace(\"Java\", \"Python\"));\n        \n        // 分割\n        String[] words = str.split(\" \");\n        for (String word : words) {\n            System.out.println(word);\n        }\n        \n        // 文字列比較（重要！）\n        String s1 = \"hello\";\n        String s2 = \"hello\";\n        System.out.println(\"equals: \" + s1.equals(s2));  // true\n        System.out.println(\"==: \" + (s1 == s2));  // 参照比較（使わない）\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Stringクラスの主要メソッド一覧',
                        'content' => "# Stringクラスの主要メソッド\n\n## 情報取得\n- `length()`: 文字列の長さ\n- `charAt(int index)`: 指定位置の文字\n- `isEmpty()`: 空文字列かどうか\n\n## 検索\n- `indexOf(String str)`: 文字列の位置（見つからない場合-1）\n- `contains(String str)`: 含まれるかどうか\n- `startsWith(String prefix)`: 指定文字列で始まるか\n- `endsWith(String suffix)`: 指定文字列で終わるか\n\n## 変換\n- `toUpperCase()`: 大文字に変換\n- `toLowerCase()`: 小文字に変換\n- `trim()`: 前後の空白を削除\n- `replace(char old, char new)`: 文字を置換\n\n## 分割・結合\n- `split(String regex)`: 文字列を分割\n- `substring(int begin, int end)`: 部分文字列\n\n## 比較\n- `equals(String str)`: 内容を比較（必ずこれを使う）\n- `equalsIgnoreCase(String str)`: 大文字小文字を無視して比較\n- `compareTo(String str)`: 辞書順比較\n\n## 重要：文字列の比較は必ず equals() を使う！",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第26回：ビット演算とシフト演算',
                'description' => '&（AND）、|（OR）、^（XOR）、~（NOT）、<<（左シフト）、>>（右シフト）',
                'sort_order' => 26,
                'estimated_minutes' => 150,
                'priority' => 3,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ビット演算の基本', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'シフト演算', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => '実践応用', 'estimated_minutes' => 40, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ビット演算の基本',
                        'content' => "public class JKad26A {\n    public static void main(String[] args) {\n        int a = 12;  // 1100 (2進数)\n        int b = 10;  // 1010 (2進数)\n        \n        // ビットAND\n        System.out.println(\"a & b = \" + (a & b));  // 8 (1000)\n        \n        // ビットOR\n        System.out.println(\"a | b = \" + (a | b));  // 14 (1110)\n        \n        // ビットXOR\n        System.out.println(\"a ^ b = \" + (a ^ b));  // 6 (0110)\n        \n        // ビットNOT\n        System.out.println(\"~a = \" + (~a));  // -13\n        \n        // 2進数表示\n        System.out.println(\"\\n2進数表示:\");\n        System.out.println(\"a = \" + Integer.toBinaryString(a));\n        System.out.println(\"b = \" + Integer.toBinaryString(b));\n        System.out.println(\"a & b = \" + Integer.toBinaryString(a & b));\n        System.out.println(\"a | b = \" + Integer.toBinaryString(a | b));\n        System.out.println(\"a ^ b = \" + Integer.toBinaryString(a ^ b));\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'シフト演算',
                        'content' => "public class JKad26B {\n    public static void main(String[] args) {\n        int x = 8;  // 1000 (2進数)\n        \n        // 左シフト（2倍になる）\n        System.out.println(\"x << 1 = \" + (x << 1));  // 16 (10000)\n        System.out.println(\"x << 2 = \" + (x << 2));  // 32 (100000)\n        \n        // 右シフト（2で割る）\n        System.out.println(\"x >> 1 = \" + (x >> 1));  // 4 (100)\n        System.out.println(\"x >> 2 = \" + (x >> 2));  // 2 (10)\n        \n        // 論理右シフト（符号なし）\n        int y = -8;\n        System.out.println(\"y >> 1 = \" + (y >> 1));   // 算術右シフト\n        System.out.println(\"y >>> 1 = \" + (y >>> 1)); // 論理右シフト\n        \n        // 応用：2のべき乗判定\n        int n = 16;\n        boolean isPowerOfTwo = (n > 0) && ((n & (n - 1)) == 0);\n        System.out.println(\"\\n\" + n + \"は2のべき乗？ \" + isPowerOfTwo);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ビット演算とシフト演算',
                        'content' => "# ビット演算\n\n## ビット論理演算\n- `&` (AND): 両方が1なら1\n- `|` (OR): どちらかが1なら1\n- `^` (XOR): 異なれば1\n- `~` (NOT): ビット反転\n\n## 真理値表（AND）\n```\n1100 (12)\n1010 (10)\n----\n1000 (8)\n```\n\n## シフト演算\n- `<<` (左シフト): ビットを左へ移動（×2）\n- `>>` (算術右シフト): ビットを右へ移動（÷2、符号保持）\n- `>>>` (論理右シフト): ビットを右へ移動（符号なし）\n\n## 例\n```\n8 << 1 = 16   (1000 → 10000)\n8 >> 1 = 4    (1000 → 100)\n```\n\n## 応用例\n- フラグ管理\n- 高速な乗除算\n- 2のべき乗判定: `(n & (n-1)) == 0`\n- ビットマスク\n\n注：通常の開発では使用頻度は低いが、低レベルプログラミングやパフォーマンス最適化で重要",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 7: アルゴリズムと実践 (第27回～第30回)
        $milestone7 = $template->milestones()->create([
            'title' => 'アルゴリズムと実践プロジェクト',
            'description' => 'ソートアルゴリズム、実践的なプログラミング演習',
            'sort_order' => 7,
            'estimated_hours' => 24,
            'deliverables' => [
                'バブルソート、マージソートを実装',
                'クイックソートを実装',
                'リバーシゲームを完成'
            ],
        ]);

        $milestone7->tasks()->createMany([
            [
                'title' => '第27回：プログラミング演習①（バブルソート・マージソート）',
                'description' => 'バブルソートとマージソートのアルゴリズムを理解し実装',
                'sort_order' => 27,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'バブルソートのアルゴリズムを理解', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'バブルソートを実装', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'マージソートのアルゴリズムを理解', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'マージソートを実装', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'バブルソート',
                        'content' => "public class BubbleSort {\n    public static void bubbleSort(int[] arr) {\n        int n = arr.length;\n        \n        // 外側のループ：パスの回数\n        for (int i = 0; i < n - 1; i++) {\n            // 内側のループ：隣接要素の比較\n            for (int j = 0; j < n - 1 - i; j++) {\n                // 隣接要素を比較して交換\n                if (arr[j] > arr[j + 1]) {\n                    int temp = arr[j];\n                    arr[j] = arr[j + 1];\n                    arr[j + 1] = temp;\n                }\n            }\n        }\n    }\n    \n    public static void main(String[] args) {\n        int[] numbers = {64, 34, 25, 12, 22, 11, 90};\n        \n        System.out.println(\"ソート前:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n        \n        bubbleSort(numbers);\n        \n        System.out.println(\"\\nソート後:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'マージソート',
                        'content' => "public class MergeSort {\n    public static void mergeSort(int[] arr, int left, int right) {\n        if (left < right) {\n            int mid = (left + right) / 2;\n            \n            // 左半分をソート\n            mergeSort(arr, left, mid);\n            // 右半分をソート\n            mergeSort(arr, mid + 1, right);\n            // マージ\n            merge(arr, left, mid, right);\n        }\n    }\n    \n    public static void merge(int[] arr, int left, int mid, int right) {\n        int n1 = mid - left + 1;\n        int n2 = right - mid;\n        \n        int[] L = new int[n1];\n        int[] R = new int[n2];\n        \n        for (int i = 0; i < n1; i++)\n            L[i] = arr[left + i];\n        for (int j = 0; j < n2; j++)\n            R[j] = arr[mid + 1 + j];\n        \n        int i = 0, j = 0, k = left;\n        while (i < n1 && j < n2) {\n            if (L[i] <= R[j]) {\n                arr[k++] = L[i++];\n            } else {\n                arr[k++] = R[j++];\n            }\n        }\n        \n        while (i < n1) arr[k++] = L[i++];\n        while (j < n2) arr[k++] = R[j++];\n    }\n    \n    public static void main(String[] args) {\n        int[] numbers = {64, 34, 25, 12, 22, 11, 90};\n        mergeSort(numbers, 0, numbers.length - 1);\n        \n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ソートアルゴリズムの比較',
                        'content' => "# ソートアルゴリズムの比較\n\n## バブルソート\n- **時間計算量**: O(n²)\n- **特徴**: シンプルで理解しやすい\n- **動作**: 隣接要素を比較して交換\n- **用途**: 小規模データ、学習用\n\n## マージソート\n- **時間計算量**: O(n log n)\n- **特徴**: 安定ソート、分割統治法\n- **動作**: 分割してソート後にマージ\n- **用途**: 大規模データ、外部ソート\n\n## 性能比較（要素数1000の場合）\n- バブルソート: 約500,000回の比較\n- マージソート: 約10,000回の比較\n\n## アルゴリズムの選び方\n1. データ量が少ない → バブルソート\n2. データ量が多い → マージソート、クイックソート\n3. 安定性が必要 → マージソート\n4. メモリ効率重視 → クイックソート",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第28回：プログラミング演習②（クイックソートに挑戦！）',
                'description' => 'クイックソートのアルゴリズムを理解し実装',
                'sort_order' => 28,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'クイックソートのアルゴリズムを理解', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'クイックソートを実装', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'パフォーマンス比較', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'クイックソート',
                        'content' => "public class QuickSort {\n    public static void quickSort(int[] arr, int low, int high) {\n        if (low < high) {\n            // パーティション\n            int pi = partition(arr, low, high);\n            \n            // ピボットの左側をソート\n            quickSort(arr, low, pi - 1);\n            // ピボットの右側をソート\n            quickSort(arr, pi + 1, high);\n        }\n    }\n    \n    public static int partition(int[] arr, int low, int high) {\n        int pivot = arr[high];  // ピボット（基準値）\n        int i = low - 1;\n        \n        for (int j = low; j < high; j++) {\n            // ピボットより小さい要素を左側に移動\n            if (arr[j] < pivot) {\n                i++;\n                // 要素を交換\n                int temp = arr[i];\n                arr[i] = arr[j];\n                arr[j] = temp;\n            }\n        }\n        \n        // ピボットを正しい位置に配置\n        int temp = arr[i + 1];\n        arr[i + 1] = arr[high];\n        arr[high] = temp;\n        \n        return i + 1;\n    }\n    \n    public static void main(String[] args) {\n        int[] numbers = {64, 34, 25, 12, 22, 11, 90};\n        \n        System.out.println(\"ソート前:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n        \n        quickSort(numbers, 0, numbers.length - 1);\n        \n        System.out.println(\"\\nソート後:\");\n        for (int num : numbers) {\n            System.out.print(num + \" \");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'クイックソートの仕組み',
                        'content' => "# クイックソートの仕組み\n\n## アルゴリズム\n1. ピボット（基準値）を選ぶ\n2. ピボットより小さい要素を左、大きい要素を右に分割\n3. 分割された部分を再帰的にソート\n\n## 時間計算量\n- **平均**: O(n log n)\n- **最悪**: O(n²) ※ピボット選択が悪い場合\n- **最良**: O(n log n)\n\n## 特徴\n- **高速**: 実用的に最も高速なソートアルゴリズムの1つ\n- **分割統治法**: 問題を小さく分割して解決\n- **不安定**: 同じ値の順序が保証されない\n- **in-place**: 追加メモリがほぼ不要\n\n## ピボットの選び方\n1. 最後の要素（シンプル）\n2. 最初の要素\n3. 中央の要素\n4. ランダムに選択\n5. 3つの中央値（median-of-three）",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第29回：プログラミング演習（総合練習）',
                'description' => 'これまで学習した内容の総合的な練習',
                'sort_order' => 29,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'アルゴリズムの復習', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '総合問題①', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '総合問題②', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Java基礎演習の総まとめ',
                        'content' => "# Java基礎演習の総まとめ\n\n## 学習した主要トピック\n\n### 1. 基本構文\n- 変数と型（int, double, String, boolean, char）\n- 演算子（算術、比較、論理、ビット）\n- 入出力（Scanner, System.out）\n\n### 2. 制御構造\n- 条件分岐（if, if-else, if-else if, switch）\n- ループ（while, do-while, for, 拡張for）\n- break, continue\n\n### 3. データ構造\n- 配列（1次元、多次元）\n- 文字列操作（Stringクラス）\n\n### 4. メソッド\n- メソッド定義と呼び出し\n- 引数と戻り値\n- オーバーロード\n- 再帰呼び出し\n\n### 5. アルゴリズム\n- 探索（線形探索、二分探索）\n- ソート（バブル、選択、マージ、クイック）\n\n## 次のステップ\n1. オブジェクト指向プログラミング\n2. 例外処理\n3. コレクションフレームワーク\n4. ファイル入出力\n5. GUI プログラミング",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '総合問題例：二分探索',
                        'content' => "public class BinarySearch {\n    // ソート済み配列から要素を探索\n    public static int binarySearch(int[] arr, int target) {\n        int left = 0;\n        int right = arr.length - 1;\n        \n        while (left <= right) {\n            int mid = (left + right) / 2;\n            \n            if (arr[mid] == target) {\n                return mid;  // 見つかった\n            } else if (arr[mid] < target) {\n                left = mid + 1;  // 右半分を探索\n            } else {\n                right = mid - 1;  // 左半分を探索\n            }\n        }\n        \n        return -1;  // 見つからない\n    }\n    \n    public static void main(String[] args) {\n        int[] numbers = {1, 3, 5, 7, 9, 11, 13, 15, 17, 19};\n        int target = 11;\n        \n        int index = binarySearch(numbers, target);\n        \n        if (index != -1) {\n            System.out.println(target + \"は\" + index + \"番目にあります\");\n        } else {\n            System.out.println(target + \"は見つかりませんでした\");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第30回：プログラミング演習③（リバーシ！）',
                'description' => 'リバーシ（オセロ）ゲームの実装',
                'sort_order' => 30,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ゲーム盤面の表示', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '石を置く処理', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '石をひっくり返す処理', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => '勝敗判定', 'estimated_minutes' => 60, 'sort_order' => 4],
                    ['title' => 'ゲームループの完成', 'estimated_minutes' => 90, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'リバーシの基本構造',
                        'content' => "import java.util.Scanner;\n\npublic class Reversi {\n    static final int BOARD_SIZE = 8;\n    static final int EMPTY = 0;\n    static final int BLACK = 1;\n    static final int WHITE = 2;\n    static int[][] board = new int[BOARD_SIZE][BOARD_SIZE];\n    \n    // ボードを初期化\n    public static void initBoard() {\n        for (int i = 0; i < BOARD_SIZE; i++) {\n            for (int j = 0; j < BOARD_SIZE; j++) {\n                board[i][j] = EMPTY;\n            }\n        }\n        // 初期配置\n        board[3][3] = WHITE;\n        board[3][4] = BLACK;\n        board[4][3] = BLACK;\n        board[4][4] = WHITE;\n    }\n    \n    // ボードを表示\n    public static void printBoard() {\n        System.out.println(\"  0 1 2 3 4 5 6 7\");\n        for (int i = 0; i < BOARD_SIZE; i++) {\n            System.out.print(i + \" \");\n            for (int j = 0; j < BOARD_SIZE; j++) {\n                if (board[i][j] == EMPTY) {\n                    System.out.print(\". \");\n                } else if (board[i][j] == BLACK) {\n                    System.out.print(\"● \");\n                } else {\n                    System.out.print(\"○ \");\n                }\n            }\n            System.out.println();\n        }\n    }\n    \n    public static void main(String[] args) {\n        initBoard();\n        printBoard();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '石をひっくり返す処理の例',
                        'content' => "// 指定方向に石をひっくり返せるか確認\npublic static boolean canFlip(int row, int col, int player, int dr, int dc) {\n    int opponent = (player == BLACK) ? WHITE : BLACK;\n    int r = row + dr;\n    int c = col + dc;\n    int count = 0;\n    \n    // 隣接マスが相手の石かチェック\n    while (r >= 0 && r < BOARD_SIZE && c >= 0 && c < BOARD_SIZE) {\n        if (board[r][c] == opponent) {\n            count++;\n            r += dr;\n            c += dc;\n        } else if (board[r][c] == player && count > 0) {\n            return true;  // ひっくり返せる\n        } else {\n            break;\n        }\n    }\n    return false;\n}\n\n// 実際に石をひっくり返す\npublic static void flip(int row, int col, int player, int dr, int dc) {\n    int opponent = (player == BLACK) ? WHITE : BLACK;\n    int r = row + dr;\n    int c = col + dc;\n    \n    while (board[r][c] == opponent) {\n        board[r][c] = player;\n        r += dr;\n        c += dc;\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'リバーシゲーム実装のポイント',
                        'content' => "# リバーシゲーム実装のポイント\n\n## 必要な機能\n\n### 1. ボード管理\n- 2次元配列で8×8のボードを表現\n- 0: 空、1: 黒、2: 白\n\n### 2. 石を置く処理\n- 空いているマスか確認\n- 8方向（上下左右、斜め4方向）をチェック\n- ひっくり返せる石があるか確認\n\n### 3. ひっくり返す処理\n- 8方向それぞれについて\n- 相手の石が連続している\n- その先に自分の石がある\n→ その間の石をすべてひっくり返す\n\n### 4. 勝敗判定\n- 両者とも置けるマスがない\n- 石の数を数えて多い方が勝ち\n\n### 5. ゲームループ\n1. ボードを表示\n2. 現在のプレイヤーの手番\n3. 座標を入力\n4. 有効な手か確認\n5. 石を置く\n6. 石をひっくり返す\n7. プレイヤー交代\n8. 終了判定\n\n## 8方向の定義\n```java\nint[] dr = {-1, -1, -1, 0, 0, 1, 1, 1};\nint[] dc = {-1, 0, 1, -1, 1, -1, 0, 1};\n```\n\n## チャレンジ課題\n- コンピュータ対戦機能\n- 置けるマスのヒント表示\n- ゲームの保存・読み込み",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);
    }
}

