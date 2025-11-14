<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class GoCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Go言語基礎コース - 15週間の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Go言語基礎コース',
            'description' => '初心者向けGo言語プログラミング基礎コース。15週間の実践的な課題を通じて、Goの基本構文からゴルーチンまで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['go', 'golang', '基礎', '初心者', 'プログラミング', '並行処理'],
            'icon' => 'ic_go',
            'color' => '#00ADD8',
            'is_featured' => true,
        ]);

        // Milestone 1: Go基礎 (第1週～第4週)
        $milestone1 = $template->milestones()->create([
            'title' => 'Go基礎',
            'description' => '開発環境のセットアップから、変数、データ型、演算子、入力処理まで学習',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'Go開発環境をセットアップ完了',
                'Hello Worldプログラムを作成',
                '変数と型を使ったプログラム',
                'ユーザー入力を受け取るプログラム'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：環境設定とHello World',
                'description' => 'Go開発環境のセットアップとfmt.Println()を使った画面出力',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go公式サイト', 'Visual Studio Code + Go拡張機能'],
                'subtasks' => [
                    ['title' => 'Goをインストール', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'VS Codeをセットアップ', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'Hello Worldプログラムを作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Goとは？',
                        'content' => "# Goとは？\n\n**Go**（Golang）は、2009年にGoogleが開発したプログラミング言語です。\n\n## Goの特徴\n1. **シンプルで読みやすい**: 構文が簡潔\n2. **高速なコンパイル**: ビルドが非常に速い\n3. **並行処理が得意**: ゴルーチンとチャネル\n4. **静的型付け**: コンパイル時に型チェック\n5. **ガベージコレクション**: 自動メモリ管理\n\n## Goの用途\n- Web APIサーバー\n- マイクロサービス\n- CLIツール\n- クラウドインフラ（Docker, Kubernetes）",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Hello World',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    fmt.Println(\"Hello, World!\")\n}",
                        'code_language' => 'go',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：変数とデータ型',
                'description' => 'Goの変数宣言、基本データ型、型推論について学習',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go公式ドキュメント - Variables'],
                'subtasks' => [
                    ['title' => '変数の宣言方法を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => '基本データ型を理解', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '変数の宣言方法',
                        'content' => "# 変数の宣言方法\n\n```go\n// var キーワード\nvar name string = \"Gopher\"\n\n// 型推論\nvar age = 25\n\n// 短縮宣言（最もよく使われる）\nmessage := \"Hello!\"\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '変数宣言の例',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    var message string = \"Hello\"\n    count := 100\n    pi := 3.14\n    \n    fmt.Println(message, count, pi)\n}",
                        'code_language' => 'go',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：演算子と式',
                'description' => '算術演算子、比較演算子、論理演算子について学習',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Go by Example - Operators'],
                'subtasks' => [
                    ['title' => '算術演算子を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '算術演算子',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    a, b := 10, 3\n    \n    fmt.Println(a + b)  // 13\n    fmt.Println(a - b)  // 7\n    fmt.Println(a * b)  // 30\n    fmt.Println(a / b)  // 3\n    fmt.Println(a % b)  // 1\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 4
            [
                'title' => '第4週：ユーザー入力とフォーマット',
                'description' => 'fmt.Scan()を使った入力とfmt.Printf()を使ったフォーマット出力',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Go fmt package'],
                'subtasks' => [
                    ['title' => 'ユーザー入力を受け取る', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ユーザー入力',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    var name string\n    var age int\n    \n    fmt.Print(\"名前を入力: \")\n    fmt.Scan(&name)\n    \n    fmt.Print(\"年齢を入力: \")\n    fmt.Scan(&age)\n    \n    fmt.Printf(\"%sさんは%d歳です\\n\", name, age)\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 2: 制御フローと関数 (第5週～第7週)
        $milestone2 = $template->milestones()->create([
            'title' => '制御フローと関数',
            'description' => 'if/else、switch、ループ、関数の定義と使用',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                '条件分岐を使ったプログラム',
                'ループを使ったプログラム',
                '関数を定義したプログラム'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 5
            [
                'title' => '第5週：If/ElseとSwitch',
                'description' => '条件分岐の基本とswitch文',
                'sort_order' => 5,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - If/Else'],
                'subtasks' => [
                    ['title' => 'if/else文を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'If/Else',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    age := 20\n    \n    if age >= 18 {\n        fmt.Println(\"成人です\")\n    } else {\n        fmt.Println(\"未成年です\")\n    }\n    \n    // 初期化付きif\n    if score := 85; score >= 80 {\n        fmt.Println(\"合格\")\n    }\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Switch文',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    day := \"Monday\"\n    \n    switch day {\n    case \"Monday\":\n        fmt.Println(\"月曜日\")\n    case \"Tuesday\":\n        fmt.Println(\"火曜日\")\n    default:\n        fmt.Println(\"その他\")\n    }\n}",
                        'code_language' => 'go',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：ループ（for）',
                'description' => 'for文とrangeを使った繰り返し処理',
                'sort_order' => 6,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - For'],
                'subtasks' => [
                    ['title' => 'for文を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'For文',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    // 基本的なfor\n    for i := 0; i < 5; i++ {\n        fmt.Println(i)\n    }\n    \n    // whileのように\n    j := 0\n    for j < 5 {\n        fmt.Println(j)\n        j++\n    }\n    \n    // 無限ループ\n    // for {\n    //     fmt.Println(\"infinite\")\n    // }\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 7
            [
                'title' => '第7週：関数',
                'description' => '関数の定義、引数、戻り値、複数戻り値',
                'sort_order' => 7,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Functions'],
                'subtasks' => [
                    ['title' => '関数を定義', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '関数の定義',
                        'content' => "package main\n\nimport \"fmt\"\n\n// 基本的な関数\nfunc greet(name string) {\n    fmt.Printf(\"Hello, %s!\\n\", name)\n}\n\n// 戻り値あり\nfunc add(a, b int) int {\n    return a + b\n}\n\n// 複数戻り値\nfunc divide(a, b float64) (float64, error) {\n    if b == 0 {\n        return 0, fmt.Errorf(\"division by zero\")\n    }\n    return a / b, nil\n}\n\nfunc main() {\n    greet(\"Gopher\")\n    sum := add(3, 5)\n    fmt.Println(\"Sum:\", sum)\n    \n    result, err := divide(10, 2)\n    if err != nil {\n        fmt.Println(\"Error:\", err)\n    } else {\n        fmt.Println(\"Result:\", result)\n    }\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 3: データ構造 (第8週～第10週)
        $milestone3 = $template->milestones()->create([
            'title' => 'データ構造',
            'description' => '配列、スライス、マップ、構造体の使い方',
            'sort_order' => 3,
            'estimated_hours' => 24,
            'deliverables' => [
                'スライスを使ったプログラム',
                'マップを使ったプログラム',
                '構造体を定義したプログラム'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 8
            [
                'title' => '第8週：配列とスライス',
                'description' => '配列とスライスの違い、操作方法',
                'sort_order' => 8,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Slices'],
                'subtasks' => [
                    ['title' => '配列を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'スライスを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '配列とスライス',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    // 配列（固定長）\n    var arr [3]int = [3]int{1, 2, 3}\n    fmt.Println(arr)\n    \n    // スライス（可変長）\n    slice := []int{1, 2, 3, 4, 5}\n    fmt.Println(slice)\n    \n    // スライス操作\n    slice = append(slice, 6)\n    fmt.Println(slice)\n    \n    // スライシング\n    sub := slice[1:4]  // [2, 3, 4]\n    fmt.Println(sub)\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 9
            [
                'title' => '第9週：マップ',
                'description' => 'マップの作成、追加、削除、検索',
                'sort_order' => 9,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Maps'],
                'subtasks' => [
                    ['title' => 'マップを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'マップ',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    // マップの作成\n    ages := make(map[string]int)\n    ages[\"Alice\"] = 25\n    ages[\"Bob\"] = 30\n    \n    // リテラル\n    scores := map[string]int{\n        \"Math\":    90,\n        \"English\": 85,\n    }\n    \n    // アクセス\n    fmt.Println(ages[\"Alice\"])  // 25\n    \n    // 存在チェック\n    value, exists := ages[\"Charlie\"]\n    if exists {\n        fmt.Println(value)\n    } else {\n        fmt.Println(\"Not found\")\n    }\n    \n    // 削除\n    delete(ages, \"Bob\")\n    \n    // ループ\n    for name, age := range ages {\n        fmt.Printf(\"%s: %d\\n\", name, age)\n    }\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 10
            [
                'title' => '第10週：構造体',
                'description' => '構造体の定義と使用',
                'sort_order' => 10,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Structs'],
                'subtasks' => [
                    ['title' => '構造体を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '構造体',
                        'content' => "package main\n\nimport \"fmt\"\n\n// 構造体の定義\ntype Person struct {\n    Name string\n    Age  int\n}\n\nfunc main() {\n    // 構造体の作成\n    p1 := Person{Name: \"Alice\", Age: 25}\n    \n    // フィールドアクセス\n    fmt.Println(p1.Name)  // Alice\n    fmt.Println(p1.Age)   // 25\n    \n    // フィールド更新\n    p1.Age = 26\n    \n    // ポインタ\n    p2 := &Person{Name: \"Bob\", Age: 30}\n    fmt.Println(p2.Name)\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 4: OOPとインターフェース (第11週～第13週)
        $milestone4 = $template->milestones()->create([
            'title' => 'OOPとインターフェース',
            'description' => 'メソッド、レシーバー、インターフェース、エラーハンドリング',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                'メソッドを定義したプログラム',
                'インターフェースを実装したプログラム',
                'エラーハンドリングを使ったプログラム'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 11
            [
                'title' => '第11週：メソッドとレシーバー',
                'description' => '構造体にメソッドを定義',
                'sort_order' => 11,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Methods'],
                'subtasks' => [
                    ['title' => 'メソッドを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'メソッド',
                        'content' => "package main\n\nimport \"fmt\"\n\ntype Rectangle struct {\n    Width  float64\n    Height float64\n}\n\n// 値レシーバー\nfunc (r Rectangle) Area() float64 {\n    return r.Width * r.Height\n}\n\n// ポインタレシーバー\nfunc (r *Rectangle) Scale(factor float64) {\n    r.Width *= factor\n    r.Height *= factor\n}\n\nfunc main() {\n    rect := Rectangle{Width: 10, Height: 5}\n    \n    fmt.Println(\"Area:\", rect.Area())  // 50\n    \n    rect.Scale(2)\n    fmt.Println(\"After scale:\", rect.Area())  // 200\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 12
            [
                'title' => '第12週：インターフェース',
                'description' => 'インターフェースの定義と実装',
                'sort_order' => 12,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Interfaces'],
                'subtasks' => [
                    ['title' => 'インターフェースを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'インターフェース',
                        'content' => "package main\n\nimport \"fmt\"\n\n// インターフェース定義\ntype Shape interface {\n    Area() float64\n}\n\ntype Circle struct {\n    Radius float64\n}\n\nfunc (c Circle) Area() float64 {\n    return 3.14 * c.Radius * c.Radius\n}\n\ntype Rectangle struct {\n    Width, Height float64\n}\n\nfunc (r Rectangle) Area() float64 {\n    return r.Width * r.Height\n}\n\n// インターフェースを受け取る関数\nfunc printArea(s Shape) {\n    fmt.Printf(\"Area: %.2f\\n\", s.Area())\n}\n\nfunc main() {\n    c := Circle{Radius: 5}\n    r := Rectangle{Width: 10, Height: 5}\n    \n    printArea(c)  // Area: 78.50\n    printArea(r)  // Area: 50.00\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 13
            [
                'title' => '第13週：エラーハンドリング',
                'description' => 'エラーの扱い方とdefer, panic, recover',
                'sort_order' => 13,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Errors'],
                'subtasks' => [
                    ['title' => 'エラーハンドリングを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'エラーハンドリング',
                        'content' => "package main\n\nimport (\n    \"errors\"\n    \"fmt\"\n)\n\nfunc divide(a, b float64) (float64, error) {\n    if b == 0 {\n        return 0, errors.New(\"division by zero\")\n    }\n    return a / b, nil\n}\n\nfunc main() {\n    result, err := divide(10, 2)\n    if err != nil {\n        fmt.Println(\"Error:\", err)\n        return\n    }\n    fmt.Println(\"Result:\", result)\n    \n    // defer（関数終了時に実行）\n    defer fmt.Println(\"This runs last\")\n    fmt.Println(\"This runs first\")\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                ],
            ],
        ]);

        // Milestone 5: 並行処理と高度なトピック (第14週～第15週)
        $milestone5 = $template->milestones()->create([
            'title' => '並行処理と高度なトピック',
            'description' => 'ゴルーチン、チャネル、並行処理パターン',
            'sort_order' => 5,
            'estimated_hours' => 24,
            'deliverables' => [
                'ゴルーチンを使ったプログラム',
                'チャネルを使った並行処理',
                '実践的なGoプログラム'
            ],
        ]);

        $milestone5->tasks()->createMany([
            // Week 14
            [
                'title' => '第14週：ゴルーチンとチャネル',
                'description' => 'Goの並行処理の基本',
                'sort_order' => 14,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Go by Example - Goroutines'],
                'subtasks' => [
                    ['title' => 'ゴルーチンを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'チャネルを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ゴルーチン',
                        'content' => "package main\n\nimport (\n    \"fmt\"\n    \"time\"\n)\n\nfunc sayHello(name string) {\n    for i := 0; i < 3; i++ {\n        fmt.Printf(\"Hello, %s!\\n\", name)\n        time.Sleep(100 * time.Millisecond)\n    }\n}\n\nfunc main() {\n    // 普通の関数呼び出し（同期）\n    // sayHello(\"Alice\")\n    \n    // ゴルーチン（並行実行）\n    go sayHello(\"Alice\")\n    go sayHello(\"Bob\")\n    \n    // メインゴルーチンが終わらないように待つ\n    time.Sleep(1 * time.Second)\n    fmt.Println(\"Done\")\n}",
                        'code_language' => 'go',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'チャネル',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc sum(numbers []int, ch chan int) {\n    total := 0\n    for _, num := range numbers {\n        total += num\n    }\n    ch <- total  // チャネルに送信\n}\n\nfunc main() {\n    numbers := []int{1, 2, 3, 4, 5, 6}\n    \n    ch := make(chan int)\n    \n    // 2つのゴルーチンで並行計算\n    go sum(numbers[:len(numbers)/2], ch)\n    go sum(numbers[len(numbers)/2:], ch)\n    \n    // 2つの結果を受信\n    result1, result2 := <-ch, <-ch\n    \n    fmt.Println(\"Total:\", result1+result2)  // 21\n}",
                        'code_language' => 'go',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 15
            [
                'title' => '第15週：高度なトピックとベストプラクティス',
                'description' => 'パッケージ管理、テスト、デプロイ',
                'sort_order' => 15,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Go公式ドキュメント'],
                'subtasks' => [
                    ['title' => 'パッケージを学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'テストを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '実践プロジェクトを作成', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Goのパッケージ',
                        'content' => "# Goのパッケージ\n\n## パッケージの作成\n\n```go\n// mypackage/mypackage.go\npackage mypackage\n\nfunc Hello(name string) string {\n    return \"Hello, \" + name\n}\n```\n\n## パッケージの使用\n\n```go\n// main.go\npackage main\n\nimport (\n    \"fmt\"\n    \"myproject/mypackage\"\n)\n\nfunc main() {\n    msg := mypackage.Hello(\"Gopher\")\n    fmt.Println(msg)\n}\n```\n\n## Go Modules\n```bash\n# モジュール初期化\ngo mod init example.com/myproject\n\n# 依存関係の追加\ngo get github.com/gin-gonic/gin\n\n# 依存関係の整理\ngo mod tidy\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'テストの書き方',
                        'content' => "// math.go\npackage math\n\nfunc Add(a, b int) int {\n    return a + b\n}\n\n// math_test.go\npackage math\n\nimport \"testing\"\n\nfunc TestAdd(t *testing.T) {\n    result := Add(2, 3)\n    expected := 5\n    \n    if result != expected {\n        t.Errorf(\"Add(2, 3) = %d; want %d\", result, expected)\n    }\n}\n\n// テスト実行\n// go test",
                        'code_language' => 'go',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Goのベストプラクティス',
                        'content' => "# Goのベストプラクティス\n\n## コーディングスタイル\n1. `gofmt`でフォーマット\n2. `golint`でリント\n3. エラーは必ずチェック\n4. 短い変数名を使う\n5. インターフェースは小さく保つ\n\n## プロジェクト構造\n```\nmyproject/\n├── cmd/\n│   └── myapp/\n│       └── main.go\n├── internal/\n│   └── package/\n├── pkg/\n│   └── public/\n├── go.mod\n└── go.sum\n```\n\n## エラーハンドリング\n- エラーは無視しない\n- 適切なエラーメッセージ\n- カスタムエラー型を使う\n\n## パフォーマンス\n- ベンチマークを書く\n- プロファイリングを使う\n- 不要なアロケーションを避ける",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "Go Course Seeder completed successfully!\n";
    }
}
