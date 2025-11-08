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
                        'type' => 'code_snippet',
                        'title' => 'Hello Worldの基本',
                        'content' => "public class JKad01S1 {\n    public static void main(String[] args) {\n        System.out.println(\"Hello World!\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッドを使った出力',
                        'content' => "public class JSample01X {\n    public static void printHello() {\n        System.out.println(\"Hello\");\n    }\n    public static void printWorld() {\n        System.out.println(\"World!\");\n    }\n    public static void main(String[] args) {\n        printHello();\n        printWorld();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'System.out.println()の使い方',
                        'content' => "# System.out.println()の使い方\n\n- `System.out.println()`: 改行付きで出力\n- `System.out.print()`: 改行なしで出力\n- 文字列はダブルクォートで囲む\n- 数値や変数も出力可能",
                        'sort_order' => 3
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
                        'type' => 'code_snippet',
                        'title' => '変数と式の例',
                        'content' => "public class JKad02A {\n    public static void main(String[] args) {\n        int apple = 20;\n        int person = 3;\n        int eat = apple / person;  // 6\n        int rest = apple % person;  // 2\n        System.out.println(\"食べた数: \" + eat);\n        System.out.println(\"残り: \" + rest);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'int型の基本',
                        'content' => "# int型の基本\n\n- 整数を格納する型\n- 範囲: -2,147,483,648 ～ 2,147,483,647\n- 演算子: +, -, *, /, %\n- 整数同士の割り算は整数になる（小数点以下切り捨て）",
                        'sort_order' => 2
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
                        'type' => 'code_snippet',
                        'title' => 'Scannerの基本',
                        'content' => "import java.util.Scanner;\n\npublic class Example {\n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        System.out.print(\"名前を入力してください＞\");\n        String name = in.nextLine();\n        System.out.print(\"年齢を入力してください＞\");\n        int age = in.nextInt();\n        System.out.println(\"こんにちは、\" + name + \"さん（\" + age + \"歳）\");\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Scannerクラスのメソッド',
                        'content' => "# Scannerクラスのメソッド\n\n- `nextInt()`: 整数を読み込む\n- `nextDouble()`: 実数を読み込む\n- `nextLine()`: 1行の文字列を読み込む\n- `next()`: 単語を読み込む\n- `close()`: Scannerを閉じる（リソース解放）",
                        'sort_order' => 2
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
            ],
            [
                'title' => '第9回：クラス替えテスト①（練習問題）',
                'description' => '第1回～第8回までの理解度を確認するテスト練習',
                'sort_order' => 9,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第10回：チャレンジ課題②',
                'description' => 'より高度な総合課題',
                'sort_order' => 10,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
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
            ],
            [
                'title' => '第12回：if文②（if～else if）',
                'description' => 'if-else if-else構文、複数の条件分岐',
                'sort_order' => 12,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第13回：if文③（論理演算子、boolean）',
                'description' => '&&（AND）、||（OR）、!（NOT）、boolean型',
                'sort_order' => 13,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第14回：while文②（do～while、break、continue）',
                'description' => 'do-while文、break文、continue文',
                'sort_order' => 14,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第15回：配列',
                'description' => '配列の宣言、初期化、要素へのアクセス',
                'sort_order' => 15,
                'estimated_minutes' => 150,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第16回：for文①（for文の基本と配列）',
                'description' => 'for文の基本構文、配列との組み合わせ',
                'sort_order' => 16,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第17回：for文②（ループ処理の流れ、スコープ）',
                'description' => 'for文の実行フロー、変数のスコープ',
                'sort_order' => 17,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
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
            ],
            [
                'title' => '第19回：クラス替えテスト②（練習問題）',
                'description' => '第11回～第18回までの理解度を確認するテスト練習',
                'sort_order' => 19,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第20回：チャレンジ課題④',
                'description' => 'より高度な総合課題',
                'sort_order' => 20,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
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
            ],
            [
                'title' => '第22回：メソッド①',
                'description' => 'メソッドの定義、引数、戻り値',
                'sort_order' => 22,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第23回：メソッド②',
                'description' => 'メソッドのオーバーロード、再帰呼び出し',
                'sort_order' => 23,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第24回：switch文',
                'description' => 'switch-case文を使った分岐処理',
                'sort_order' => 24,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第25回：文字と文字列',
                'description' => 'char型、String型の詳細な操作、文字列メソッド',
                'sort_order' => 25,
                'estimated_minutes' => 150,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第26回：ビット演算とシフト演算',
                'description' => '&（AND）、|（OR）、^（XOR）、~（NOT）、<<（左シフト）、>>（右シフト）',
                'sort_order' => 26,
                'estimated_minutes' => 150,
                'priority' => 3,
                'resources' => [],
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
            ],
            [
                'title' => '第29回：プログラミング演習（総合練習）',
                'description' => 'これまで学習した内容の総合的な練習',
                'sort_order' => 29,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
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
            ],
        ]);
    }
}

