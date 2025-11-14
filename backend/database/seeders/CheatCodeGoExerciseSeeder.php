<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeGoExerciseSeeder extends Seeder
{
    /**
     * Seed Go exercise data
     * Go言語の練習問題
     */
    public function run(): void
    {
        // Get Go Language
        $goLanguage = CheatCodeLanguage::where('name', 'go')->first();

        if (!$goLanguage) {
            $this->command->error('Go language not found. Please run CheatCodeGoSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $goLanguage,
            'Hello World出力',
            'Goの基本',
            '「Hello, World!」という文字列を出力するGoプログラムを書いてください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// ここにコードを書いてください\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tfmt.Println(\"Hello, World!\")\n}",
            ['fmt.Println()で出力します', 'importでパッケージをインポート'],
            'easy',
            10,
            ['go', 'basics', 'println'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数と演算
        $this->createExercise(
            $goLanguage,
            '2つの数値の合計',
            '変数と算術演算',
            '2つの整数（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// 2つの数値を変数に代入\n\t\n\t// 合計を計算して出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\ta := 10\n\tb := 20\n\tfmt.Println(a + b)\n}",
            [':= で変数を宣言と初期化', 'varキーワードも使用可能'],
            'easy',
            10,
            ['go', 'variable', 'arithmetic'],
            2,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 3: 文字列の連結
        $this->createExercise(
            $goLanguage,
            '文字列を連結して挨拶',
            '文字列連結',
            '変数name := "Alice"を定義し、「Hello, Alice!」と出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tname := \"Alice\"\n\t// 文字列を連結して出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tname := \"Alice\"\n\tfmt.Println(\"Hello, \" + name + \"!\")\n}",
            ['+演算子で文字列を連結', 'fmt.Sprintfでフォーマットも可能'],
            'easy',
            15,
            ['go', 'string', 'concatenation'],
            3,
            [
                ['', 'Hello, Alice!', '文字列連結', true, false, 1],
            ]
        );

        // Exercise 4: if-else文
        $this->createExercise(
            $goLanguage,
            '数値が正か負かゼロか判定',
            '条件分岐',
            '変数num := -5があります。この数値が正の数なら「Positive」、負の数なら「Negative」、0なら「Zero」と出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnum := -5\n\t// 判定して出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnum := -5\n\tif num > 0 {\n\t\tfmt.Println(\"Positive\")\n\t} else if num < 0 {\n\t\tfmt.Println(\"Negative\")\n\t} else {\n\t\tfmt.Println(\"Zero\")\n\t}\n}",
            ['if, else if, elseで条件分岐', '条件式に()は不要'],
            'easy',
            15,
            ['go', 'conditional', 'if-else'],
            4,
            [
                ['', 'Negative', '負の数', true, false, 1],
            ]
        );

        // Exercise 5: forループ
        $this->createExercise(
            $goLanguage,
            '1から5まで出力',
            'ループ - for',
            'forループを使って1から5までの数字を改行区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// forループで1から5まで出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tfor i := 1; i <= 5; i++ {\n\t\tfmt.Println(i)\n\t}\n}",
            ['for 初期化; 条件; 更新 の形式', 'Goにはwhileがなく、forのみ'],
            'easy',
            15,
            ['go', 'loop', 'for'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで', true, false, 1],
            ]
        );

        // Exercise 6: for（while風）
        $this->createExercise(
            $goLanguage,
            'forループで合計計算',
            'ループ - for (while風)',
            '変数sum := 0とi := 1を定義し、forループでiが5以下の間、sumにiを加算し続けて、最終的なsumの値を出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tsum := 0\n\ti := 1\n\t// forループで合計を計算\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tsum := 0\n\ti := 1\n\tfor i <= 5 {\n\t\tsum += i\n\t\ti++\n\t}\n\tfmt.Println(sum)\n}",
            ['for 条件 { ... } でwhile風のループ', 'sum += iは sum = sum + iと同じ'],
            'easy',
            20,
            ['go', 'loop', 'for'],
            6,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1],
            ]
        );

        // Exercise 7: スライスの宣言と出力
        $this->createExercise(
            $goLanguage,
            'スライスの要素を出力',
            'スライス',
            '整数スライス []int{10, 20, 30, 40, 50} を宣言し、各要素をカンマ区切りで1行に出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnumbers := []int{10, 20, 30, 40, 50}\n\t// スライスの要素を出力\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tnumbers := []int{10, 20, 30, 40, 50}\n\tvar strs []string\n\tfor _, num := range numbers {\n\t\tstrs = append(strs, strconv.Itoa(num))\n\t}\n\tfmt.Println(strings.Join(strs, \",\"))\n}",
            ['rangeでスライスを走査', 'strings.Joinで結合'],
            'medium',
            20,
            ['go', 'slice', 'range'],
            7,
            [
                ['', '10,20,30,40,50', 'スライスをカンマ区切り', true, false, 1],
            ]
        );

        // Exercise 8: 関数の定義
        $this->createExercise(
            $goLanguage,
            '関数で2つの数を足す',
            '関数',
            'add(a int, b int) intという関数を作成し、2つの整数の合計を返すようにしてください。mainから5と10で呼び出して結果を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// add関数を定義\n\nfunc main() {\n\t// add関数を呼び出して結果を出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc add(a int, b int) int {\n\treturn a + b\n}\n\nfunc main() {\n\tfmt.Println(add(5, 10))\n}",
            ['func 関数名(引数 型) 戻り値型 { ... }', 'returnで値を返します'],
            'medium',
            25,
            ['go', 'function'],
            8,
            [
                ['', '15', '関数呼び出し', true, false, 1],
            ]
        );

        // Exercise 9: 最大値を求める
        $this->createExercise(
            $goLanguage,
            'スライスの最大値を求める',
            'スライスとループ',
            '整数スライス []int{15, 42, 8, 27, 33} の中から最大値を見つけて出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnumbers := []int{15, 42, 8, 27, 33}\n\t// 最大値を求める\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnumbers := []int{15, 42, 8, 27, 33}\n\tmax := numbers[0]\n\tfor _, num := range numbers[1:] {\n\t\tif num > max {\n\t\t\tmax = num\n\t\t}\n\t}\n\tfmt.Println(max)\n}",
            ['最初の要素を初期値とする', 'rangeで各要素と比較'],
            'medium',
            25,
            ['go', 'slice', 'algorithm'],
            9,
            [
                ['', '42', '最大値', true, false, 1],
            ]
        );

        // Exercise 10: switch文
        $this->createExercise(
            $goLanguage,
            '曜日の判定',
            'Switch文',
            '変数day := 1があります。switch文を使って対応する曜日名を出力してください。1=Monday, 2=Tuesday, ..., 7=Sunday。該当しない場合は"Invalid day"と出力。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tday := 1\n\t// switch文で曜日を判定\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tday := 1\n\tswitch day {\n\tcase 1:\n\t\tfmt.Println(\"Monday\")\n\tcase 2:\n\t\tfmt.Println(\"Tuesday\")\n\tcase 3:\n\t\tfmt.Println(\"Wednesday\")\n\tcase 4:\n\t\tfmt.Println(\"Thursday\")\n\tcase 5:\n\t\tfmt.Println(\"Friday\")\n\tcase 6:\n\t\tfmt.Println(\"Saturday\")\n\tcase 7:\n\t\tfmt.Println(\"Sunday\")\n\tdefault:\n\t\tfmt.Println(\"Invalid day\")\n\t}\n}",
            ['switch 変数 { case 値: ... }の形式', 'Goではbreakは不要（自動的にbreak）'],
            'medium',
            30,
            ['go', 'switch', 'conditional'],
            10,
            [
                ['', 'Monday', '月曜日', true, false, 1],
            ]
        );

        // Exercise 11: 複数戻り値
        $this->createExercise(
            $goLanguage,
            '複数の値を返す関数',
            '複数戻り値',
            'divide(a int, b int) (int, int)という関数を作成し、商と余りを返すようにしてください。10÷3の結果を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// divide関数を定義\n\nfunc main() {\n\t// divide関数を呼び出して結果を出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc divide(a int, b int) (int, int) {\n\treturn a / b, a % b\n}\n\nfunc main() {\n\tquotient, remainder := divide(10, 3)\n\tfmt.Println(quotient)\n\tfmt.Println(remainder)\n}",
            ['複数の戻り値を(型1, 型2)で定義', 'a, b := func()で複数の値を受け取る'],
            'medium',
            30,
            ['go', 'function', 'multiple-return'],
            11,
            [
                ['', "3\n1", '商と余り', true, false, 1],
            ]
        );

        // Exercise 12: ポインタ
        $this->createExercise(
            $goLanguage,
            'ポインタで値を変更',
            'ポインタ',
            '関数double(n *int)を作成し、渡された値を2倍にします。mainで5を渡して、変更後の値を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// double関数を定義\n\nfunc main() {\n\tnum := 5\n\t// 関数を呼び出して値を出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc double(n *int) {\n\t*n = *n * 2\n}\n\nfunc main() {\n\tnum := 5\n\tdouble(&num)\n\tfmt.Println(num)\n}",
            ['*型でポインタ型を定義', '&でアドレスを取得、*でポインタが指す値を取得'],
            'medium',
            30,
            ['go', 'pointer'],
            12,
            [
                ['', '10', 'ポインタ', true, false, 1],
            ]
        );

        // Exercise 13: マップ（連想配列）
        $this->createExercise(
            $goLanguage,
            'マップに追加と取得',
            'マップ',
            'map[string]intを作成し、"age" -> 25を追加して、"age"の値を出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// マップを作成してデータを追加・取得\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tm := make(map[string]int)\n\tm[\"age\"] = 25\n\tfmt.Println(m[\"age\"])\n}",
            ['make(map[キー型]値型)でマップを作成', 'm[キー] = 値で追加'],
            'medium',
            25,
            ['go', 'map'],
            13,
            [
                ['', '25', 'マップの使用', true, false, 1],
            ]
        );

        // Exercise 14: 構造体
        $this->createExercise(
            $goLanguage,
            '構造体の定義と使用',
            '構造体',
            'Name(string)とAge(int)を持つPerson構造体を定義し、"John", 25のインスタンスを作成して名前を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// Person構造体を定義\n\nfunc main() {\n\t// Personインスタンスを作成して名前を出力\n}",
            "package main\n\nimport \"fmt\"\n\ntype Person struct {\n\tName string\n\tAge  int\n}\n\nfunc main() {\n\tperson := Person{Name: \"John\", Age: 25}\n\tfmt.Println(person.Name)\n}",
            ['type 名前 struct { ... }で定義', '大文字で始まるフィールドはエクスポートされる'],
            'medium',
            30,
            ['go', 'struct'],
            14,
            [
                ['', 'John', '構造体', true, false, 1],
            ]
        );

        // Exercise 15: メソッド
        $this->createExercise(
            $goLanguage,
            'メソッドの定義',
            'メソッド',
            'Person構造体にGetName() stringメソッドを追加し、名前を返すようにしてください。',
            "package main\n\nimport \"fmt\"\n\ntype Person struct {\n\tName string\n\tAge  int\n}\n\n// GetNameメソッドを定義\n\nfunc main() {\n\tperson := Person{Name: \"John\", Age: 25}\n\tfmt.Println(person.GetName())\n}",
            "package main\n\nimport \"fmt\"\n\ntype Person struct {\n\tName string\n\tAge  int\n}\n\nfunc (p Person) GetName() string {\n\treturn p.Name\n}\n\nfunc main() {\n\tperson := Person{Name: \"John\", Age: 25}\n\tfmt.Println(person.GetName())\n}",
            ['func (レシーバ 型) メソッド名() 戻り値型 { ... }'],
            'medium',
            30,
            ['go', 'method', 'struct'],
            15,
            [
                ['', 'John', 'メソッド', true, false, 1],
            ]
        );

        // Exercise 16: 文字列の長さ
        $this->createExercise(
            $goLanguage,
            '文字列の長さを取得',
            '文字列',
            '文字列"Hello Go"の長さを取得して出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\ttext := \"Hello Go\"\n\t// 文字列の長さを出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\ttext := \"Hello Go\"\n\tfmt.Println(len(text))\n}",
            ['len()で文字列の長さを取得', 'バイト数を返す（ルーン数ではない）'],
            'easy',
            10,
            ['go', 'string', 'len'],
            16,
            [
                ['', '8', '文字列の長さ', true, false, 1],
            ]
        );

        // Exercise 17: 文字列の分割
        $this->createExercise(
            $goLanguage,
            '文字列を分割',
            '文字列',
            '文字列"apple,banana,orange"をカンマで分割し、各要素を改行区切りで出力してください。',
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"apple,banana,orange\"\n\t// カンマで分割して出力\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"apple,banana,orange\"\n\tfruits := strings.Split(text, \",\")\n\tfor _, fruit := range fruits {\n\t\tfmt.Println(fruit)\n\t}\n}",
            ['strings.Split(文字列, 区切り)で分割', 'rangeでスライスを走査'],
            'medium',
            20,
            ['go', 'string', 'split'],
            17,
            [
                ['', "apple\nbanana\norange", '文字列分割', true, false, 1],
            ]
        );

        // Exercise 18: 偶数の判定
        $this->createExercise(
            $goLanguage,
            '1から10の偶数を出力',
            'ループと条件分岐',
            '1から10までの数のうち、偶数だけをカンマ区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// 1から10の偶数を出力\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tvar evens []string\n\tfor i := 1; i <= 10; i++ {\n\t\tif i%2 == 0 {\n\t\t\tevens = append(evens, strconv.Itoa(i))\n\t\t}\n\t}\n\tfmt.Println(strings.Join(evens, \",\"))\n}",
            ['i % 2 == 0で偶数を判定', 'appendでスライスに追加'],
            'medium',
            20,
            ['go', 'loop', 'modulo'],
            18,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1],
            ]
        );

        // Exercise 19: インターフェース
        $this->createExercise(
            $goLanguage,
            'インターフェースの実装',
            'インターフェース',
            'Speakerインターフェースにspeak() stringメソッドを定義し、Dog構造体で実装して"Woof!"を返してください。',
            "package main\n\nimport \"fmt\"\n\n// Speakerインターフェースを定義\n\n// Dog構造体を定義\n\n// speakメソッドを実装\n\nfunc main() {\n\tvar s Speaker = Dog{}\n\tfmt.Println(s.speak())\n}",
            "package main\n\nimport \"fmt\"\n\ntype Speaker interface {\n\tspeak() string\n}\n\ntype Dog struct{}\n\nfunc (d Dog) speak() string {\n\treturn \"Woof!\"\n}\n\nfunc main() {\n\tvar s Speaker = Dog{}\n\tfmt.Println(s.speak())\n}",
            ['type 名前 interface { ... }で定義', 'メソッドを実装すると自動的にインターフェースを満たす'],
            'hard',
            35,
            ['go', 'interface'],
            19,
            [
                ['', 'Woof!', 'インターフェース', true, false, 1],
            ]
        );

        // Exercise 20: defer
        $this->createExercise(
            $goLanguage,
            'deferの使用',
            'defer',
            'deferを使って、"Start"の後に"End"と出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// deferを使用\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tdefer fmt.Println(\"End\")\n\tfmt.Println(\"Start\")\n}",
            ['deferは関数終了時に実行される', '後入れ先出し（LIFO）の順序'],
            'medium',
            25,
            ['go', 'defer'],
            20,
            [
                ['', "Start\nEnd", 'defer', true, false, 1],
            ]
        );

        // Exercise 21: エラーハンドリング
        $this->createExercise(
            $goLanguage,
            'エラーハンドリング',
            'エラー',
            'divide(a, b int) (int, error)を作成し、b=0の場合はエラーを返してください。10÷0を呼び出してエラーメッセージを出力してください。',
            "package main\n\nimport (\n\t\"errors\"\n\t\"fmt\"\n)\n\n// divide関数を定義\n\nfunc main() {\n\tresult, err := divide(10, 0)\n\t// エラーチェックして出力\n}",
            "package main\n\nimport (\n\t\"errors\"\n\t\"fmt\"\n)\n\nfunc divide(a, b int) (int, error) {\n\tif b == 0 {\n\t\treturn 0, errors.New(\"division by zero\")\n\t}\n\treturn a / b, nil\n}\n\nfunc main() {\n\tresult, err := divide(10, 0)\n\tif err != nil {\n\t\tfmt.Println(err.Error())\n\t} else {\n\t\tfmt.Println(result)\n\t}\n}",
            ['errors.New()でエラーを作成', 'if err != nilでエラーチェック'],
            'medium',
            30,
            ['go', 'error', 'handling'],
            21,
            [
                ['', 'division by zero', 'エラーハンドリング', true, false, 1],
            ]
        );

        // Exercise 22: goroutine
        $this->createExercise(
            $goLanguage,
            'goroutineの使用',
            'goroutine',
            '関数print5times(msg string)を作成し、5回メッセージを出力します。goキーワードで並行実行してください。"Go"を出力してください。',
            "package main\n\nimport (\n\t\"fmt\"\n\t\"time\"\n)\n\nfunc print5times(msg string) {\n\tfor i := 0; i < 5; i++ {\n\t\tfmt.Println(msg)\n\t\ttime.Sleep(10 * time.Millisecond)\n\t}\n}\n\nfunc main() {\n\t// goroutineで実行\n\ttime.Sleep(60 * time.Millisecond)\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"time\"\n)\n\nfunc print5times(msg string) {\n\tfor i := 0; i < 5; i++ {\n\t\tfmt.Println(msg)\n\t\ttime.Sleep(10 * time.Millisecond)\n\t}\n}\n\nfunc main() {\n\tgo print5times(\"Go\")\n\ttime.Sleep(60 * time.Millisecond)\n}",
            ['go 関数()で並行実行', 'goroutineは軽量スレッド'],
            'hard',
            35,
            ['go', 'goroutine', 'concurrency'],
            22,
            [
                ['', "Go\nGo\nGo\nGo\nGo", 'goroutine', true, false, 1],
            ]
        );

        // Exercise 23: チャネル
        $this->createExercise(
            $goLanguage,
            'チャネルでデータ送受信',
            'チャネル',
            'chan intを作成し、値42を送信して受信し、出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// チャネルを作成してデータ送受信\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tch := make(chan int)\n\tgo func() {\n\t\tch <- 42\n\t}()\n\tvalue := <-ch\n\tfmt.Println(value)\n}",
            ['make(chan 型)でチャネルを作成', 'ch <- 値で送信、値 := <-chで受信'],
            'hard',
            35,
            ['go', 'channel', 'concurrency'],
            23,
            [
                ['', '42', 'チャネル', true, false, 1],
            ]
        );

        // Exercise 24: range（スライス）
        $this->createExercise(
            $goLanguage,
            'rangeでスライスを走査',
            'range',
            'スライス []int{1, 2, 3, 4, 5} をrangeで走査し、カンマ区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnumbers := []int{1, 2, 3, 4, 5}\n\t// rangeで走査\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tnumbers := []int{1, 2, 3, 4, 5}\n\tvar strs []string\n\tfor _, num := range numbers {\n\t\tstrs = append(strs, strconv.Itoa(num))\n\t}\n\tfmt.Println(strings.Join(strs, \",\"))\n}",
            ['for index, value := range スライス', '_でインデックスを無視可能'],
            'medium',
            20,
            ['go', 'range', 'slice'],
            24,
            [
                ['', '1,2,3,4,5', 'rangeでスライス走査', true, false, 1],
            ]
        );

        // Exercise 25: 可変長引数
        $this->createExercise(
            $goLanguage,
            '可変長引数',
            '可変長引数',
            'sum(nums ...int) intを作成し、すべての引数の合計を返してください。1, 2, 3, 4, 5を渡して結果を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// sum関数を定義\n\nfunc main() {\n\tfmt.Println(sum(1, 2, 3, 4, 5))\n}",
            "package main\n\nimport \"fmt\"\n\nfunc sum(nums ...int) int {\n\ttotal := 0\n\tfor _, num := range nums {\n\t\ttotal += num\n\t}\n\treturn total\n}\n\nfunc main() {\n\tfmt.Println(sum(1, 2, 3, 4, 5))\n}",
            ['...型で可変長引数', 'スライスとして扱える'],
            'medium',
            30,
            ['go', 'variadic', 'function'],
            25,
            [
                ['', '15', '可変長引数', true, false, 1],
            ]
        );

        // Exercise 26: 無名関数
        $this->createExercise(
            $goLanguage,
            '無名関数',
            '無名関数',
            '無名関数を使って、2つの数値を掛け算する関数を作成し、5と6を掛けた結果を出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// 無名関数を定義\n\t\n\t// 関数を呼び出して結果を出力\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tmultiply := func(a, b int) int {\n\t\treturn a * b\n\t}\n\tfmt.Println(multiply(5, 6))\n}",
            ['func(引数) 戻り値型 { ... }で無名関数', '変数に代入可能'],
            'medium',
            25,
            ['go', 'anonymous', 'function'],
            26,
            [
                ['', '30', '無名関数', true, false, 1],
            ]
        );

        // Exercise 27: クロージャ
        $this->createExercise(
            $goLanguage,
            'クロージャ',
            'クロージャ',
            'counter() func() intを作成し、呼び出すたびにカウントが増える関数を返してください。3回呼び出して最後の値を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// counter関数を定義\n\nfunc main() {\n\tc := counter()\n\tc()\n\tc()\n\tfmt.Println(c())\n}",
            "package main\n\nimport \"fmt\"\n\nfunc counter() func() int {\n\tcount := 0\n\treturn func() int {\n\t\tcount++\n\t\treturn count\n\t}\n}\n\nfunc main() {\n\tc := counter()\n\tc()\n\tc()\n\tfmt.Println(c())\n}",
            ['クロージャは外側の変数にアクセス可能', '状態を保持できる'],
            'hard',
            35,
            ['go', 'closure', 'function'],
            27,
            [
                ['', '3', 'クロージャ', true, false, 1],
            ]
        );

        // Exercise 28: 型アサーション
        $this->createExercise(
            $goLanguage,
            '型アサーション',
            '型アサーション',
            'interface{}型の変数に整数42を代入し、型アサーションでint型として取り出して出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tvar i interface{} = 42\n\t// 型アサーション\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tvar i interface{} = 42\n\tnum := i.(int)\n\tfmt.Println(num)\n}",
            ['変数.(型)で型アサーション', '値, ok := 変数.(型)で安全にアサーション'],
            'medium',
            25,
            ['go', 'type-assertion', 'interface'],
            28,
            [
                ['', '42', '型アサーション', true, false, 1],
            ]
        );

        // Exercise 29: 型スイッチ
        $this->createExercise(
            $goLanguage,
            '型スイッチ',
            '型スイッチ',
            'interface{}型の引数を受け取り、int型なら"Integer"、string型なら"String"と返す関数を作成してください。42を渡して結果を出力してください。',
            "package main\n\nimport \"fmt\"\n\n// typeCheck関数を定義\n\nfunc main() {\n\tfmt.Println(typeCheck(42))\n}",
            "package main\n\nimport \"fmt\"\n\nfunc typeCheck(i interface{}) string {\n\tswitch i.(type) {\n\tcase int:\n\t\treturn \"Integer\"\n\tcase string:\n\t\treturn \"String\"\n\tdefault:\n\t\treturn \"Unknown\"\n\t}\n}\n\nfunc main() {\n\tfmt.Println(typeCheck(42))\n}",
            ['switch 変数.(type) { ... }で型スイッチ'],
            'hard',
            35,
            ['go', 'type-switch', 'interface'],
            29,
            [
                ['', 'Integer', '型スイッチ', true, false, 1],
            ]
        );

        // Exercise 30: appendでスライス追加
        $this->createExercise(
            $goLanguage,
            'appendでスライスに追加',
            'スライス',
            'スライス []int{1, 2, 3} に4と5を追加して、カンマ区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tnumbers := []int{1, 2, 3}\n\t// appendで追加\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tnumbers := []int{1, 2, 3}\n\tnumbers = append(numbers, 4, 5)\n\tvar strs []string\n\tfor _, num := range numbers {\n\t\tstrs = append(strs, strconv.Itoa(num))\n\t}\n\tfmt.Println(strings.Join(strs, \",\"))\n}",
            ['append(スライス, 要素...)で追加', '新しいスライスが返される'],
            'easy',
            20,
            ['go', 'slice', 'append'],
            30,
            [
                ['', '1,2,3,4,5', 'appendで追加', true, false, 1],
            ]
        );

        // Exercise 31: continue文
        $this->createExercise(
            $goLanguage,
            'Continue文で3の倍数をスキップ',
            'ループ制御 - Continue',
            '1から10までの数のうち、3の倍数以外をカンマ区切りで出力してください。continue文を使用します。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// 3の倍数をスキップ\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tvar nums []string\n\tfor i := 1; i <= 10; i++ {\n\t\tif i%3 == 0 {\n\t\t\tcontinue\n\t\t}\n\t\tnums = append(nums, strconv.Itoa(i))\n\t}\n\tfmt.Println(strings.Join(nums, \",\"))\n}",
            ['continueで次のループへスキップ'],
            'medium',
            25,
            ['go', 'loop', 'continue'],
            31,
            [
                ['', '1,2,4,5,7,8,10', '3の倍数以外', true, false, 1],
            ]
        );

        // Exercise 32: break文
        $this->createExercise(
            $goLanguage,
            'Break文で5以上でループを終了',
            'ループ制御 - Break',
            '1から10までループし、5に達したらループを終了します。1から4までをカンマ区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// 5でループを終了\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tvar nums []string\n\tfor i := 1; i <= 10; i++ {\n\t\tif i >= 5 {\n\t\t\tbreak\n\t\t}\n\t\tnums = append(nums, strconv.Itoa(i))\n\t}\n\tfmt.Println(strings.Join(nums, \",\"))\n}",
            ['breakでループを終了'],
            'easy',
            20,
            ['go', 'loop', 'break'],
            32,
            [
                ['', '1,2,3,4', 'ループの終了', true, false, 1],
            ]
        );

        // Exercise 33: 配列
        $this->createExercise(
            $goLanguage,
            '配列の使用',
            '配列',
            '固定長配列 [5]int{1, 2, 3, 4, 5} を宣言し、要素をカンマ区切りで出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tarr := [5]int{1, 2, 3, 4, 5}\n\t// 配列を出力\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strconv\"\n\t\"strings\"\n)\n\nfunc main() {\n\tarr := [5]int{1, 2, 3, 4, 5}\n\tvar strs []string\n\tfor _, num := range arr {\n\t\tstrs = append(strs, strconv.Itoa(num))\n\t}\n\tfmt.Println(strings.Join(strs, \",\"))\n}",
            ['[サイズ]型 で固定長配列', 'スライスは[]型（サイズなし）'],
            'easy',
            15,
            ['go', 'array'],
            33,
            [
                ['', '1,2,3,4,5', '配列', true, false, 1],
            ]
        );

        // Exercise 34: 文字列の置換
        $this->createExercise(
            $goLanguage,
            '文字列を置換',
            '文字列',
            '文字列"Hello Go"の"Go"を"World"に置換して出力してください。',
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"Hello Go\"\n\t// Goをworldに置換\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"Hello Go\"\n\tfmt.Println(strings.Replace(text, \"Go\", \"World\", -1))\n}",
            ['strings.Replace(文字列, 検索, 置換, 回数)', '-1で全て置換'],
            'easy',
            15,
            ['go', 'string', 'replace'],
            34,
            [
                ['', 'Hello World', '文字列置換', true, false, 1],
            ]
        );

        // Exercise 35: 文字列の大文字変換
        $this->createExercise(
            $goLanguage,
            '文字列を大文字に変換',
            '文字列',
            '文字列"hello world"を大文字に変換して出力してください。',
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"hello world\"\n\t// 大文字に変換\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"hello world\"\n\tfmt.Println(strings.ToUpper(text))\n}",
            ['strings.ToUpper()で大文字に変換', 'strings.ToLower()は小文字に変換'],
            'easy',
            10,
            ['go', 'string', 'uppercase'],
            35,
            [
                ['', 'HELLO WORLD', '大文字変換', true, false, 1],
            ]
        );

        // Exercise 36: 文字列の含有チェック
        $this->createExercise(
            $goLanguage,
            '文字列に部分文字列が含まれるか確認',
            '文字列',
            '文字列"Hello Go"に"Go"が含まれているか確認し、含まれていれば"Found"、なければ"Not found"と出力してください。',
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"Hello Go\"\n\t// Containsで確認\n}",
            "package main\n\nimport (\n\t\"fmt\"\n\t\"strings\"\n)\n\nfunc main() {\n\ttext := \"Hello Go\"\n\tif strings.Contains(text, \"Go\") {\n\t\tfmt.Println(\"Found\")\n\t} else {\n\t\tfmt.Println(\"Not found\")\n\t}\n}",
            ['strings.Contains(文字列, 部分文字列)でチェック', 'trueまたはfalseを返す'],
            'easy',
            20,
            ['go', 'string', 'contains'],
            36,
            [
                ['', 'Found', '文字列検索', true, false, 1],
            ]
        );

        // Exercise 37: fmt.Sprintf
        $this->createExercise(
            $goLanguage,
            '文字列フォーマット',
            'フォーマット',
            'fmt.Sprintf()を使って、"Name: John, Age: 25"という文字列を作成して出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tname := \"John\"\n\tage := 25\n\t// fmt.Sprintfでフォーマット\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tname := \"John\"\n\tage := 25\n\tfmt.Println(fmt.Sprintf(\"Name: %s, Age: %d\", name, age))\n}",
            ['fmt.Sprintf(\"フォーマット\", 値...)でフォーマット', '%sは文字列、%dは整数'],
            'medium',
            25,
            ['go', 'string', 'format'],
            37,
            [
                ['', 'Name: John, Age: 25', '文字列フォーマット', true, false, 1],
            ]
        );

        // Exercise 38: 再帰関数
        $this->createExercise(
            $goLanguage,
            '再帰で階乗を計算',
            '再帰',
            '再帰関数factorial(n int) intを作成し、5の階乗(120)を計算して出力してください。',
            "package main\n\nimport \"fmt\"\n\n// factorial関数を定義\n\nfunc main() {\n\tfmt.Println(factorial(5))\n}",
            "package main\n\nimport \"fmt\"\n\nfunc factorial(n int) int {\n\tif n <= 1 {\n\t\treturn 1\n\t}\n\treturn n * factorial(n-1)\n}\n\nfunc main() {\n\tfmt.Println(factorial(5))\n}",
            ['再帰は関数が自分自身を呼び出す', '基底ケース(n <= 1)が必要'],
            'hard',
            35,
            ['go', 'recursion', 'function'],
            38,
            [
                ['', '120', '5の階乗', true, false, 1],
            ]
        );

        // Exercise 39: init関数
        $this->createExercise(
            $goLanguage,
            'init関数',
            'init',
            'init関数で"Init"と出力し、mainで"Main"と出力してください。',
            "package main\n\nimport \"fmt\"\n\n// init関数を定義\n\nfunc main() {\n\tfmt.Println(\"Main\")\n}",
            "package main\n\nimport \"fmt\"\n\nfunc init() {\n\tfmt.Println(\"Init\")\n}\n\nfunc main() {\n\tfmt.Println(\"Main\")\n}",
            ['init関数はmain前に自動実行される', '初期化処理に使用'],
            'medium',
            25,
            ['go', 'init'],
            39,
            [
                ['', "Init\nMain", 'init関数', true, false, 1],
            ]
        );

        // Exercise 40: 空のインターフェース
        $this->createExercise(
            $goLanguage,
            '空のインターフェース',
            'interface{}',
            'interface{}型の変数に文字列"Hello"を代入し、出力してください。',
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\t// interface{}型の変数を使用\n}",
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n\tvar val interface{} = \"Hello\"\n\tfmt.Println(val)\n}",
            ['interface{}は任意の型を受け入れる', 'any型のエイリアス（Go 1.18+）'],
            'easy',
            15,
            ['go', 'interface', 'empty-interface'],
            40,
            [
                ['', 'Hello', '空のインターフェース', true, false, 1],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($goLanguage);

        $this->command->info('Go exercises seeded successfully!');
    }

    /**
     * Create an exercise with test cases
     */
    private function createExercise(
        CheatCodeLanguage $language,
        string $title,
        string $description,
        string $question,
        string $starterCode,
        string $solution,
        array $hints,
        string $difficulty,
        int $points,
        array $tags,
        int $sortOrder,
        array $testCases
    ): Exercise {
        // Generate unique slug by combining language slug with sort order
        $slug = $language->slug . '-exercise-' . $sortOrder;

        $exercise = Exercise::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'question' => $question,
            'starter_code' => $starterCode,
            'solution' => $solution,
            'hints' => $hints,
            'difficulty' => $difficulty,
            'points' => $points,
            'tags' => $tags,
            'time_limit' => 30,
            'is_published' => true,
            'sort_order' => $sortOrder,
        ]);

        // Create test cases
        foreach ($testCases as $testCase) {
            ExerciseTestCase::create([
                'exercise_id' => $exercise->id,
                'input' => $testCase[0],
                'expected_output' => $testCase[1],
                'description' => $testCase[2],
                'is_sample' => $testCase[3],
                'is_hidden' => $testCase[4],
                'sort_order' => $testCase[5],
            ]);
        }

        return $exercise;
    }

    /**
     * Update language exercise counts
     */
    private function updateLanguageCounts(CheatCodeLanguage $language): void
    {
        $language->update([
            'exercises_count' => $language->exercises()->count(),
        ]);
    }
}
