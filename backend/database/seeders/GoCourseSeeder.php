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
                    [
                        'type' => 'exercise',
                        'title' => '演習：開発環境セットアップ',
                        'content' => "# 演習：Go開発環境セットアップ\n\n## 目標\nGoの開発環境を構築し、基本的なプログラムを実行する\n\n## 手順\n\n### 1. Goのインストール確認\n```bash\n# バージョン確認\ngo version\n# go version go1.22.0 windows/amd64\n\n# 環境変数確認\ngo env GOPATH\ngo env GOROOT\n```\n\n### 2. ワークスペースの作成\n```bash\nmkdir -p ~/go-projects/hello\ncd ~/go-projects/hello\n\n# Go Modulesの初期化\ngo mod init example.com/hello\n```\n\n### 3. Hello Worldプログラム\n```go\n// main.go\npackage main\n\nimport \"fmt\"\n\nfunc main() {\n    fmt.Println(\"Hello, Go!\")\n    fmt.Println(\"Go version:\", runtime.Version())\n}\n```\n\n### 4. ビルドと実行\n```bash\n# 実行（ビルド＋実行）\ngo run main.go\n\n# ビルドのみ\ngo build\n\n# 実行ファイルを実行\n./hello        # Linux/Mac\nhello.exe      # Windows\n\n# クロスコンパイル（Windows → Linux）\nGOOS=linux GOARCH=amd64 go build -o hello-linux\n```\n\n### 5. Goツールの使用\n```bash\n# フォーマット\ngo fmt main.go\n\n# コード検査\ngo vet main.go\n\n# 依存関係の確認\ngo list -m all\n\n# 依存関係の整理\ngo mod tidy\n```\n\n## チェックポイント\n- [ ] Goがインストールされている\n- [ ] GOPATHが設定されている\n- [ ] go mod initでプロジェクトを作成できた\n- [ ] go runでプログラムを実行できた\n- [ ] go buildで実行ファイルを作成できた",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'トラブルシューティング：よくある問題',
                        'content' => "# トラブルシューティング：よくある問題\n\n## 問題1: go command not found\n\n**原因:** PATHが設定されていない\n\n**解決策:**\n```bash\n# Windows (PowerShell)\n\\x24env:PATH += \";C:\\Go\\bin\"\n\n# Linux/Mac (bash)\nexport PATH=\\x24PATH:/usr/local/go/bin\nexport PATH=\\x24PATH:\\x24HOME/go/bin\n\n# 永続化（.bashrc or .zshrc）\necho 'export PATH=\\x24PATH:/usr/local/go/bin' >> ~/.bashrc\n```\n\n## 問題2: package XXX is not in GOROOT\n\n**原因:** Go Modulesが初期化されていない\n\n**解決策:**\n```bash\ngo mod init example.com/myproject\ngo mod tidy\n```\n\n## 問題3: import cycle not allowed\n\n**原因:** パッケージの循環参照\n\n**解決策:**\n- パッケージ構造を見直す\n- 共通の型を別パッケージに分離\n- インターフェースを使って依存関係を逆転\n\n## 問題4: undefined: XXX\n\n**原因:** 変数や関数が未定義\n\n**確認:**\n```bash\n# ビルドエラーの詳細を表示\ngo build -v\n\n# 型情報を表示\ngo doc fmt.Println\n```\n\n## 問題5: race condition detected\n\n**診断:**\n```bash\n# レースディテクターを有効化\ngo run -race main.go\ngo test -race\n```\n\n**解決策:**\n- mutexを使う\n- チャネルで同期\n- atomic操作を使う",
                        'sort_order' => 4
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
                    [
                        'type' => 'note',
                        'title' => 'データ型の詳細',
                        'content' => "# Goのデータ型\n\n## 基本型\n\n### 整数型\n```go\nvar a int8    // -128 ~ 127\nvar b int16   // -32768 ~ 32767\nvar c int32   // -2147483648 ~ 2147483647\nvar d int64   // -9223372036854775808 ~ 9223372036854775807\nvar e int     // アーキテクチャ依存（32 or 64 bit）\n\nvar f uint8   // 0 ~ 255\nvar g uint16  // 0 ~ 65535\nvar h uint32  // 0 ~ 4294967295\nvar i uint64  // 0 ~ 18446744073709551615\nvar j uint    // アーキテクチャ依存\n\nvar k byte    // uint8のエイリアス\nvar l rune    // int32のエイリアス（Unicode code point）\n```\n\n### 浮動小数点型\n```go\nvar x float32  // ±1.18e-38 ~ ±3.4e38\nvar y float64  // ±2.23e-308 ~ ±1.80e308\nvar z complex64   // float32の実部と虚部\nvar w complex128  // float64の実部と虚部\n```\n\n### その他の型\n```go\nvar flag bool        // true or false\nvar text string      // 文字列（UTF-8）\nvar ptr *int         // ポインタ\n```\n\n## ゼロ値\n\nGoでは変数を初期化しないと自動的にゼロ値が設定される：\n\n```go\nvar i int       // 0\nvar f float64   // 0.0\nvar b bool      // false\nvar s string    // \"\" (empty string)\nvar p *int      // nil\n```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '型変換とconst',
                        'content' => "package main\n\nimport \"fmt\"\n\nfunc main() {\n    // 型変換\n    var i int = 42\n    var f float64 = float64(i)\n    var u uint = uint(f)\n    \n    fmt.Printf(\"i=%d, f=%.2f, u=%d\\n\", i, f, u)\n    \n    // 定数\n    const Pi = 3.14159\n    const (\n        StatusOK = 200\n        StatusNotFound = 404\n        StatusError = 500\n    )\n    \n    // iota（連続する定数）\n    const (\n        Sunday = iota     // 0\n        Monday            // 1\n        Tuesday           // 2\n        Wednesday         // 3\n        Thursday          // 4\n        Friday            // 5\n        Saturday          // 6\n    )\n    \n    fmt.Println(\"Monday:\", Monday)\n    fmt.Println(\"Friday:\", Friday)\n    \n    // 型付き定数 vs 型なし定数\n    const TypedConst int = 100\n    const UntypedConst = 100\n    \n    var x int32 = UntypedConst     // OK\n    // var y int32 = TypedConst    // Error: cannot use TypedConst (type int) as type int32\n}",
                        'code_language' => 'go',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '演習：温度変換プログラム',
                        'content' => "# 演習：温度変換プログラム\n\n## 目標\n摂氏と華氏の温度変換プログラムを作成\n\n## 要件\n\n1. ユーザーから摂氏温度を入力\n2. 華氏に変換して表示\n3. 華氏 = 摂氏 × 9/5 + 32\n\n## サンプルコード\n\n```go\npackage main\n\nimport \"fmt\"\n\nfunc main() {\n    var celsius float64\n    \n    fmt.Print(\"摂氏温度を入力: \")\n    fmt.Scan(&celsius)\n    \n    // 華氏に変換\n    fahrenheit := celsius*9/5 + 32\n    \n    fmt.Printf(\"%.2f°C = %.2f°F\\n\", celsius, fahrenheit)\n    \n    // ボーナス: 絶対零度チェック\n    const AbsoluteZero = -273.15\n    if celsius < AbsoluteZero {\n        fmt.Println(\"警告: 絶対零度より低い温度です\")\n    }\n}\n```\n\n## 拡張課題\n\n1. 華氏→摂氏の変換も追加\n2. ケルビン温度も追加\n3. 複数の温度を一度に変換\n4. 温度の妥当性チェック\n\n## 実行例\n\n```\n摂氏温度を入力: 25\n25.00°C = 77.00°F\n\n摂氏温度を入力: -300\n-300.00°C = -508.00°F\n警告: 絶対零度より低い温度です\n```",
                        'sort_order' => 5
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
                    [
                        'type' => 'code_snippet',
                        'title' => 'REST API完全実装例',
                        'content' => "// main.go - Ginを使ったREST API\npackage main\n\nimport (\n    \"github.com/gin-gonic/gin\"\n    \"net/http\"\n    \"strconv\"\n)\n\ntype Book struct {\n    ID     int    \\x60json:\"id\"\\x60\n    Title  string \\x60json:\"title\"\\x60\n    Author string \\x60json:\"author\"\\x60\n    Year   int    \\x60json:\"year\"\\x60\n}\n\nvar books = []Book{\n    {ID: 1, Title: \"The Go Programming Language\", Author: \"Alan Donovan\", Year: 2015},\n    {ID: 2, Title: \"Learning Go\", Author: \"Jon Bodner\", Year: 2021},\n}\n\nfunc main() {\n    router := gin.Default()\n    \n    // CORS middleware\n    router.Use(corsMiddleware())\n    \n    // Routes\n    router.GET(\"/api/books\", getBooks)\n    router.GET(\"/api/books/:id\", getBook)\n    router.POST(\"/api/books\", createBook)\n    router.PUT(\"/api/books/:id\", updateBook)\n    router.DELETE(\"/api/books/:id\", deleteBook)\n    \n    router.Run(\":8080\")\n}\n\n// GET /api/books\nfunc getBooks(c *gin.Context) {\n    c.JSON(http.StatusOK, gin.H{\"data\": books})\n}\n\n// GET /api/books/:id\nfunc getBook(c *gin.Context) {\n    id, _ := strconv.Atoi(c.Param(\"id\"))\n    \n    for _, book := range books {\n        if book.ID == id {\n            c.JSON(http.StatusOK, gin.H{\"data\": book})\n            return\n        }\n    }\n    \n    c.JSON(http.StatusNotFound, gin.H{\"error\": \"Book not found\"})\n}\n\n// POST /api/books\nfunc createBook(c *gin.Context) {\n    var newBook Book\n    \n    if err := c.ShouldBindJSON(&newBook); err != nil {\n        c.JSON(http.StatusBadRequest, gin.H{\"error\": err.Error()})\n        return\n    }\n    \n    newBook.ID = len(books) + 1\n    books = append(books, newBook)\n    \n    c.JSON(http.StatusCreated, gin.H{\"data\": newBook})\n}\n\n// PUT /api/books/:id\nfunc updateBook(c *gin.Context) {\n    id, _ := strconv.Atoi(c.Param(\"id\"))\n    \n    var updated Book\n    if err := c.ShouldBindJSON(&updated); err != nil {\n        c.JSON(http.StatusBadRequest, gin.H{\"error\": err.Error()})\n        return\n    }\n    \n    for i, book := range books {\n        if book.ID == id {\n            books[i].Title = updated.Title\n            books[i].Author = updated.Author\n            books[i].Year = updated.Year\n            c.JSON(http.StatusOK, gin.H{\"data\": books[i]})\n            return\n        }\n    }\n    \n    c.JSON(http.StatusNotFound, gin.H{\"error\": \"Book not found\"})\n}\n\n// DELETE /api/books/:id\nfunc deleteBook(c *gin.Context) {\n    id, _ := strconv.Atoi(c.Param(\"id\"))\n    \n    for i, book := range books {\n        if book.ID == id {\n            books = append(books[:i], books[i+1:]...)\n            c.JSON(http.StatusOK, gin.H{\"message\": \"Book deleted\"})\n            return\n        }\n    }\n    \n    c.JSON(http.StatusNotFound, gin.H{\"error\": \"Book not found\"})\n}\n\nfunc corsMiddleware() gin.HandlerFunc {\n    return func(c *gin.Context) {\n        c.Writer.Header().Set(\"Access-Control-Allow-Origin\", \"*\")\n        c.Writer.Header().Set(\"Access-Control-Allow-Methods\", \"GET,POST,PUT,DELETE,OPTIONS\")\n        c.Writer.Header().Set(\"Access-Control-Allow-Headers\", \"Content-Type\")\n        \n        if c.Request.Method == \"OPTIONS\" {\n            c.AbortWithStatus(http.StatusNoContent)\n            return\n        }\n        \n        c.Next()\n    }\n}",
                        'code_language' => 'go',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '最終課題：Todo API with PostgreSQL',
                        'content' => "# 最終課題：Todo REST API\n\n## 目標\nGin + PostgreSQL + ゴルーチンを使った完全なREST APIを構築\n\n## 要件\n\n### 1. 機能要件\n- [ ] CRUD操作（Create, Read, Update, Delete）\n- [ ] PostgreSQLデータベース接続\n- [ ] JWT認証\n- [ ] バリデーション\n- [ ] エラーハンドリング\n- [ ] ロギング\n- [ ] テスト\n\n### 2. エンドポイント\n\n```\nPOST   /api/auth/register  - ユーザー登録\nPOST   /api/auth/login     - ログイン\nGET    /api/todos          - Todo一覧取得\nGET    /api/todos/:id      - Todo詳細取得\nPOST   /api/todos          - Todo作成\nPUT    /api/todos/:id      - Todo更新\nDELETE /api/todos/:id      - Todo削除\n```\n\n### 3. データベーススキーマ\n\n```sql\nCREATE TABLE users (\n    id SERIAL PRIMARY KEY,\n    username VARCHAR(50) UNIQUE NOT NULL,\n    password VARCHAR(255) NOT NULL,\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);\n\nCREATE TABLE todos (\n    id SERIAL PRIMARY KEY,\n    user_id INT REFERENCES users(id),\n    title VARCHAR(255) NOT NULL,\n    description TEXT,\n    completed BOOLEAN DEFAULT FALSE,\n    due_date TIMESTAMP,\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);\n```\n\n### 4. プロジェクト構造\n\n```\ntodo-api/\n├── main.go\n├── go.mod\n├── go.sum\n├── config/\n│   └── database.go\n├── models/\n│   ├── user.go\n│   └── todo.go\n├── controllers/\n│   ├── auth.go\n│   └── todo.go\n├── middleware/\n│   ├── auth.go\n│   └── logger.go\n├── routes/\n│   └── routes.go\n├── utils/\n│   ├── jwt.go\n│   └── validator.go\n└── tests/\n    ├── auth_test.go\n    └── todo_test.go\n```\n\n### 5. サンプル実装（models/todo.go）\n\n```go\npackage models\n\nimport \"time\"\n\ntype Todo struct {\n    ID          int       \\x60json:\"id\"\\x60\n    UserID      int       \\x60json:\"user_id\"\\x60\n    Title       string    \\x60json:\"title\" binding:\"required,min=3,max=255\"\\x60\n    Description string    \\x60json:\"description\"\\x60\n    Completed   bool      \\x60json:\"completed\"\\x60\n    DueDate     time.Time \\x60json:\"due_date\"\\x60\n    CreatedAt   time.Time \\x60json:\"created_at\"\\x60\n    UpdatedAt   time.Time \\x60json:\"updated_at\"\\x60\n}\n```\n\n### 6. テスト実装\n\n```go\n// tests/todo_test.go\npackage tests\n\nimport (\n    \"testing\"\n    \"github.com/stretchr/testify/assert\"\n)\n\nfunc TestCreateTodo(t *testing.T) {\n    // テストコード\n    assert.Equal(t, expected, actual)\n}\n```\n\n### 7. 実行\n\n```bash\n# 依存関係のインストール\ngo mod init todo-api\ngo get github.com/gin-gonic/gin\ngo get gorm.io/gorm\ngo get gorm.io/driver/postgres\ngo get github.com/golang-jwt/jwt/v5\n\n# 実行\ngo run main.go\n\n# テスト\ngo test ./...\n\n# ビルド\ngo build -o todo-api\n```\n\n## 評価基準\n\n- コード品質: 30点\n- 機能完成度: 30点\n- エラーハンドリング: 15点\n- テスト: 15点\n- ドキュメント: 10点\n\n## ボーナス課題\n\n- [ ] Docker化（+5点）\n- [ ] CI/CD（GitHub Actions）（+5点）\n- [ ] WebSocket通知（+10点）\n- [ ] キャッシング（Redis）（+5点）",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Goベストプラクティスまとめ',
                        'content' => "# Goベストプラクティス完全ガイド\n\n## 1. コーディングスタイル\n\n### フォーマット\n```bash\n# 自動フォーマット\ngo fmt ./...\n\n# より厳密なフォーマット\ngoimports -w .\n```\n\n### 命名規則\n```go\n// パッケージ: 小文字、単数形\npackage user\n\n// 関数: キャメルケース\nfunc GetUserByID(id int) (*User, error)\n\n// 変数: キャメルケース\nvar userCount int\n\n// 定数: キャメルケース\nconst MaxRetries = 3\n\n// エクスポート: 大文字で始まる\ntype User struct {}\n\n// 非エクスポート: 小文字で始まる\ntype user struct {}\n```\n\n## 2. エラーハンドリング\n\n### 基本パターン\n```go\n// エラーチェックは常に行う\nresult, err := someFunction()\nif err != nil {\n    return nil, fmt.Errorf(\"someFunction failed: %w\", err)\n}\n\n// 複数戻り値の最後はerror\nfunc DoSomething() (Result, error)\n\n// カスタムエラー\nvar ErrNotFound = errors.New(\"not found\")\n\ntype ValidationError struct {\n    Field string\n    Message string\n}\n\nfunc (e *ValidationError) Error() string {\n    return fmt.Sprintf(\"%s: %s\", e.Field, e.Message)\n}\n```\n\n## 3. 並行処理\n\n### ゴルーチン\n```go\n// WaitGroupで待機\nvar wg sync.WaitGroup\n\nfor i := 0; i < 10; i++ {\n    wg.Add(1)\n    go func(i int) {\n        defer wg.Done()\n        // 処理\n    }(i)\n}\n\nwg.Wait()\n\n// Contextでキャンセル\nctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)\ndefer cancel()\n\ngo func() {\n    select {\n    case <-ctx.Done():\n        fmt.Println(\"Cancelled\")\n        return\n    case result := <-ch:\n        fmt.Println(result)\n    }\n}()\n```\n\n## 4. パフォーマンス\n\n### メモリ効率\n```go\n// スライスの容量を事前に確保\nslice := make([]int, 0, 100)\n\n// 文字列結合はstrings.Builder\nvar builder strings.Builder\nfor i := 0; i < 1000; i++ {\n    builder.WriteString(\"test\")\n}\nresult := builder.String()\n\n// ポインタ vs 値\n// 小さい構造体は値渡し\ntype Point struct { X, Y int }\nfunc Move(p Point) Point\n\n// 大きい構造体はポインタ\ntype LargeStruct struct { /* many fields */ }\nfunc Process(p *LargeStruct) error\n```\n\n## 5. テスト\n\n### テーブル駆動テスト\n```go\nfunc TestAdd(t *testing.T) {\n    tests := []struct {\n        name string\n        a, b int\n        want int\n    }{\n        {\"positive\", 1, 2, 3},\n        {\"negative\", -1, -2, -3},\n        {\"zero\", 0, 0, 0},\n    }\n    \n    for _, tt := range tests {\n        t.Run(tt.name, func(t *testing.T) {\n            got := Add(tt.a, tt.b)\n            if got != tt.want {\n                t.Errorf(\"Add(%d, %d) = %d; want %d\",\n                    tt.a, tt.b, got, tt.want)\n            }\n        })\n    }\n}\n```\n\n## 6. セキュリティ\n\n```go\n// パスワードハッシュ化\nimport \"golang.org/x/crypto/bcrypt\"\n\nhash, _ := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)\nvalid := bcrypt.CompareHashAndPassword(hash, []byte(password))\n\n// SQL injection対策\ndb.Exec(\"INSERT INTO users (name) VALUES (\\x241)\", name)\n\n// XSS対策\nimport \"html/template\"\nt := template.Must(template.New(\"name\").Parse(tmpl))\n```",
                        'sort_order' => 6
                    ],
                ],
            ],
        ]);

        echo "✅ Go Course Seeder completed successfully!\n";
        echo "📚 Total Content:\n";
        echo "   - 5 Milestones\n";
        echo "   - 15 Weeks of Learning\n";
        echo "   - 120 Hours Total\n";
        echo "   - REST API Capstone Project\n";
        echo "   - Extensive hands-on exercises\n";
        echo "   - Production-ready Go skills\n";
    }
}
