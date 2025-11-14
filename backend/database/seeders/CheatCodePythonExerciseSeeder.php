<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodePythonExerciseSeeder extends Seeder
{
    /**
     * Seed Python exercise data
     * Python言語の練習問題
     */
    public function run(): void
    {
        // Get Python Language
        $pythonLanguage = CheatCodeLanguage::where('name', 'python')->first();

        if (!$pythonLanguage) {
            $this->command->error('Python language not found. Please run CheatCodePythonSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $pythonLanguage,
            'Hello World出力',
            'Pythonの基本',
            '「Hello, World!」という文字列を出力するPythonプログラムを書いてください。',
            "# ここにコードを書いてください\n",
            "print(\"Hello, World!\")",
            ['print()関数を使用します', '文字列はシングルまたはダブルクォートで囲みます'],
            'easy',
            10,
            ['python', 'basics', 'print'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数と演算
        $this->createExercise(
            $pythonLanguage,
            '2つの数値の合計',
            '変数と算術演算',
            '2つの数値（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "# 2つの数値を変数に代入\n\n# 合計を計算して出力\n",
            "a = 10\nb = 20\nprint(a + b)",
            ['変数は = で代入します', 'print()で出力できます'],
            'easy',
            10,
            ['python', 'variable', 'arithmetic'],
            2,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 3: 入力の受け取り
        $this->createExercise(
            $pythonLanguage,
            '名前を入力して挨拶',
            '入力の受け取り',
            'input()で名前を入力として受け取り、「Hello, [名前]!」と出力してください。',
            "# 名前を入力として受け取る\n\n# 挨拶を出力\n",
            "name = input()\nprint(f\"Hello, {name}!\")",
            ['input()で入力を受け取れます', 'f文字列で変数を埋め込めます'],
            'easy',
            15,
            ['python', 'input', 'string', 'f-string'],
            3,
            [
                ['Alice', 'Hello, Alice!', '名前Aliceを入力', true, false, 1],
                ['Bob', 'Hello, Bob!', '名前Bobを入力', false, false, 2],
            ]
        );

        // Exercise 4: if-else文
        $this->createExercise(
            $pythonLanguage,
            '数値が正か負かゼロか判定',
            '条件分岐',
            '整数を入力として受け取り、正の数なら「Positive」、負の数なら「Negative」、0なら「Zero」と出力してください。',
            "# 整数を入力として受け取る\nnum = int(input())\n\n# 判定して出力\n",
            "num = int(input())\n\nif num > 0:\n    print(\"Positive\")\nelif num < 0:\n    print(\"Negative\")\nelse:\n    print(\"Zero\")",
            ['if, elif, elseで条件分岐', 'int()で文字列を整数に変換'],
            'easy',
            15,
            ['python', 'conditional', 'if-else'],
            4,
            [
                ['5', 'Positive', '正の数', true, false, 1],
                ['-3', 'Negative', '負の数', true, false, 2],
                ['0', 'Zero', 'ゼロ', true, false, 3],
            ]
        );

        // Exercise 5: forループ
        $this->createExercise(
            $pythonLanguage,
            '1から5までの数値を出力',
            'Forループ',
            'forループを使って1から5までの数値を1行ずつ出力してください。',
            "# 1から5までループ\n",
            "for i in range(1, 6):\n    print(i)",
            ['range(開始, 終了+1)で範囲を指定', 'forループはfor 変数 in シーケンス:'],
            'easy',
            15,
            ['python', 'loop', 'for', 'range'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで出力', true, false, 1],
            ]
        );

        // Exercise 6: Whileループ
        $this->createExercise(
            $pythonLanguage,
            'Whileループで5回カウント',
            'Whileループ',
            'whileループを使って0から4までカウントし、1行ずつ出力してください。',
            "count = 0\n\n# countが5未満の間ループ\n",
            "count = 0\n\nwhile count < 5:\n    print(count)\n    count += 1",
            ['while 条件: でループ', 'count += 1でインクリメント'],
            'easy',
            15,
            ['python', 'loop', 'while'],
            6,
            [
                ['', "0\n1\n2\n3\n4", '0から4まで出力', true, false, 1],
            ]
        );

        // Exercise 7: リストの定義と要素へのアクセス
        $this->createExercise(
            $pythonLanguage,
            'リストの最初と最後の要素',
            'リスト操作',
            'リストfruits = ["Apple", "Banana", "Orange"]を定義し、最初の要素と最後の要素を1行ずつ出力してください。',
            "# リストを定義\n\n# 最初の要素を出力\n# 最後の要素を出力\n",
            "fruits = [\"Apple\", \"Banana\", \"Orange\"]\n\nprint(fruits[0])\nprint(fruits[-1])",
            ['リストは[]で定義', 'インデックス0で最初の要素', 'インデックス-1で最後の要素'],
            'easy',
            20,
            ['python', 'list', 'indexing'],
            7,
            [
                ['', "Apple\nOrange", '最初と最後の要素', true, false, 1],
            ]
        );

        // Exercise 8: リストのループ
        $this->createExercise(
            $pythonLanguage,
            'リストの全要素を出力',
            'リストの反復処理',
            'リストcolors = ["Red", "Green", "Blue"]の全要素を1行ずつ出力してください。',
            "colors = [\"Red\", \"Green\", \"Blue\"]\n\n# 全要素をループで出力\n",
            "colors = [\"Red\", \"Green\", \"Blue\"]\n\nfor color in colors:\n    print(color)",
            ['for 変数 in リスト: で各要素を処理'],
            'easy',
            15,
            ['python', 'list', 'loop', 'iteration'],
            8,
            [
                ['', "Red\nGreen\nBlue", '全要素を出力', true, false, 1],
            ]
        );

        // Exercise 9: 関数の定義
        $this->createExercise(
            $pythonLanguage,
            '挨拶関数を作成',
            '関数の基本',
            '引数として名前を受け取り「Hello, [名前]!」を返すgreet関数を定義し、"World"を引数として呼び出して結果を出力してください。',
            "# greet関数を定義\n\n# 関数を呼び出して出力\n",
            "def greet(name):\n    return f\"Hello, {name}!\"\n\nprint(greet(\"World\"))",
            ['def 関数名(引数): で関数定義', 'returnで値を返す', 'f文字列で変数を埋め込む'],
            'medium',
            20,
            ['python', 'function', 'definition'],
            9,
            [
                ['', 'Hello, World!', '関数を呼び出し', true, false, 1],
            ]
        );

        // Exercise 10: リスト内包表記
        $this->createExercise(
            $pythonLanguage,
            '1から5の2乗をリストで生成',
            'リスト内包表記',
            'リスト内包表記を使って、1から5までの数値の2乗をリストとして生成し、出力してください。',
            "# リスト内包表記で2乗のリストを生成\n",
            "squares = [i**2 for i in range(1, 6)]\nprint(squares)",
            ['[式 for 変数 in シーケンス] でリスト内包表記', '**は累乗演算子'],
            'medium',
            25,
            ['python', 'list', 'comprehension'],
            10,
            [
                ['', '[1, 4, 9, 16, 25]', '1から5の2乗', true, false, 1],
            ]
        );

        // Exercise 11: 辞書の操作
        $this->createExercise(
            $pythonLanguage,
            '辞書で値を取得',
            '辞書の使用',
            '辞書を使って動物の鳴き声を管理します。sounds = {"dog": "bark", "cat": "meow"}を作成し、入力で渡された動物名の鳴き声を出力してください。',
            "# 辞書を作成\n\n# 入力を受け取る\nanimal = input()\n\n# 鳴き声を出力\n",
            "sounds = {\"dog\": \"bark\", \"cat\": \"meow\"}\nanimal = input()\nprint(sounds[animal])",
            ['辞書は{キー: 値}で定義', '辞書[キー]で値を取得'],
            'medium',
            25,
            ['python', 'dictionary', 'dict'],
            11,
            [
                ['dog', 'bark', '犬の鳴き声', true, false, 1],
                ['cat', 'meow', '猫の鳴き声', true, false, 2],
            ]
        );

        // Exercise 12: 文字列のメソッド
        $this->createExercise(
            $pythonLanguage,
            '文字列を大文字に変換',
            '文字列メソッド',
            '入力として渡された文字列をすべて大文字に変換して出力してください。',
            "# 文字列を入力として受け取る\ntext = input()\n\n# 大文字に変換して出力\n",
            "text = input()\nprint(text.upper())",
            ['.upper()で大文字に変換', '.lower()で小文字に変換'],
            'easy',
            15,
            ['python', 'string', 'method', 'uppercase'],
            12,
            [
                ['hello', 'HELLO', '小文字→大文字', true, false, 1],
                ['Python', 'PYTHON', '混在→大文字', true, false, 2],
            ]
        );

        // Exercise 13: 文字列のスライス
        $this->createExercise(
            $pythonLanguage,
            '文字列の一部を抽出',
            '文字列スライス',
            '変数text = "Programming"から最初の4文字を抽出して出力してください。',
            "text = \"Programming\"\n\n# 最初の4文字を抽出\n",
            "text = \"Programming\"\nprint(text[:4])",
            ['文字列[開始:終了]でスライス', '[:4]は最初から4文字目の手前まで'],
            'easy',
            20,
            ['python', 'string', 'slice'],
            13,
            [
                ['', 'Prog', '最初の4文字', true, false, 1],
            ]
        );

        // Exercise 14: len()関数
        $this->createExercise(
            $pythonLanguage,
            '文字列の長さを取得',
            'len()関数',
            '入力として渡された文字列の長さを出力してください。',
            "# 文字列を入力として受け取る\ntext = input()\n\n# 長さを出力\n",
            "text = input()\nprint(len(text))",
            ['len()で長さを取得'],
            'easy',
            15,
            ['python', 'string', 'length', 'len'],
            14,
            [
                ['Hello', '5', 'Helloは5文字', true, false, 1],
                ['Python', '6', 'Pythonは6文字', true, false, 2],
            ]
        );

        // Exercise 15: 最大値・最小値
        $this->createExercise(
            $pythonLanguage,
            'リストの最大値と最小値',
            'max()とmin()関数',
            'リストnumbers = [3, 1, 4, 1, 5, 9, 2, 6]の最大値と最小値を1行ずつ出力してください。',
            "numbers = [3, 1, 4, 1, 5, 9, 2, 6]\n\n# 最大値を出力\n# 最小値を出力\n",
            "numbers = [3, 1, 4, 1, 5, 9, 2, 6]\n\nprint(max(numbers))\nprint(min(numbers))",
            ['max()で最大値', 'min()で最小値'],
            'easy',
            20,
            ['python', 'list', 'max', 'min'],
            15,
            [
                ['', "9\n1", '最大値9、最小値1', true, false, 1],
            ]
        );

        // Exercise 16: リストの合計
        $this->createExercise(
            $pythonLanguage,
            'リストの要素の合計',
            'sum()関数',
            'リストnumbers = [1, 2, 3, 4, 5]の全要素の合計を出力してください。',
            "numbers = [1, 2, 3, 4, 5]\n\n# 合計を出力\n",
            "numbers = [1, 2, 3, 4, 5]\nprint(sum(numbers))",
            ['sum()でリストの合計を計算'],
            'easy',
            15,
            ['python', 'list', 'sum'],
            16,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1],
            ]
        );

        // Exercise 17: 偶数のフィルタリング
        $this->createExercise(
            $pythonLanguage,
            '偶数のみを抽出',
            'リスト内包表記とif',
            'リストnumbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]から偶数のみを抽出して新しいリストを作成し、出力してください。',
            "numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\n\n# 偶数のみを抽出\n",
            "numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\nevens = [n for n in numbers if n % 2 == 0]\nprint(evens)",
            ['[式 for 変数 in シーケンス if 条件]でフィルタリング', 'n % 2 == 0で偶数判定'],
            'medium',
            25,
            ['python', 'list', 'comprehension', 'filter'],
            17,
            [
                ['', '[2, 4, 6, 8, 10]', '偶数のみ抽出', true, false, 1],
            ]
        );

        // Exercise 18: 文字列の結合
        $this->createExercise(
            $pythonLanguage,
            'リストを文字列に結合',
            'join()メソッド',
            'リストwords = ["Hello", "World", "Python"]を空白で結合して1つの文字列として出力してください。',
            "words = [\"Hello\", \"World\", \"Python\"]\n\n# 空白で結合して出力\n",
            "words = [\"Hello\", \"World\", \"Python\"]\nprint(\" \".join(words))",
            ['\"区切り文字\".join(リスト)で結合'],
            'medium',
            20,
            ['python', 'string', 'list', 'join'],
            18,
            [
                ['', 'Hello World Python', '空白で結合', true, false, 1],
            ]
        );

        // Exercise 19: 文字列の分割
        $this->createExercise(
            $pythonLanguage,
            '文字列をリストに分割',
            'split()メソッド',
            '文字列"apple,banana,orange"をカンマで分割してリストにし、出力してください。',
            "text = \"apple,banana,orange\"\n\n# カンマで分割して出力\n",
            "text = \"apple,banana,orange\"\nprint(text.split(\",\"))",
            ['.split(\"区切り文字\")で分割'],
            'easy',
            20,
            ['python', 'string', 'split', 'list'],
            19,
            [
                ['', "['apple', 'banana', 'orange']", 'カンマで分割', true, false, 1],
            ]
        );

        // Exercise 20: 例外処理
        $this->createExercise(
            $pythonLanguage,
            '整数変換のエラー処理',
            'try-except',
            '入力を受け取り、整数に変換して出力してください。変換できない場合は「Invalid input」と出力してください。',
            "# 入力を受け取る\ntext = input()\n\n# 整数に変換してみる\n",
            "text = input()\n\ntry:\n    num = int(text)\n    print(num)\nexcept ValueError:\n    print(\"Invalid input\")",
            ['try-exceptで例外処理', 'ValueErrorは変換エラー'],
            'medium',
            30,
            ['python', 'exception', 'try-except', 'error'],
            20,
            [
                ['123', '123', '正常な整数', true, false, 1],
                ['abc', 'Invalid input', '変換できない文字列', true, false, 2],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($pythonLanguage);

        $this->command->info('Python exercises seeded successfully!');
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
        $exercise = Exercise::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title) ?: $language->slug . '-exercise-' . $sortOrder,
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
