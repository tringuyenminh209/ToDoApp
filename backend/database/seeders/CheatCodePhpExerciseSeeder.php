<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodePhpExerciseSeeder extends Seeder
{
    /**
     * Seed PHP exercise data
     * PHP言語の練習問題
     */
    public function run(): void
    {
        // Get PHP Language
        $phpLanguage = CheatCodeLanguage::where('name', 'php')->first();

        if (!$phpLanguage) {
            $this->command->error('PHP language not found. Please run CheatCodePhpSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $phpLanguage,
            'Hello World出力',
            'PHPの基本',
            '「Hello, World!」という文字列を出力するPHPプログラムを書いてください。',
            "<?php\n\n// ここにコードを書いてください\n",
            "<?php\n\necho \"Hello, World!\";\n",
            ['echo文で出力します', '文はセミコロンで終わります'],
            'easy',
            10,
            ['php', 'basics', 'echo'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数と演算
        $this->createExercise(
            $phpLanguage,
            '2つの数値の合計',
            '変数と算術演算',
            '2つの数値（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "<?php\n\n// 2つの数値を変数に代入\n\n// 合計を計算して出力\n",
            "<?php\n\n\$a = 10;\n\$b = 20;\necho \$a + \$b;\n",
            ['変数は$で始まります', 'echo文で出力できます'],
            'easy',
            10,
            ['php', 'variable', 'arithmetic'],
            2,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 3: 文字列の連結
        $this->createExercise(
            $phpLanguage,
            '文字列を連結して挨拶',
            '文字列連結',
            '変数$name = "Alice"を定義し、「Hello, Alice!」と出力してください。文字列連結演算子(.)を使用します。',
            "<?php\n\n// 変数を定義\n\n// 文字列を連結して出力\n",
            "<?php\n\n\$name = \"Alice\";\necho \"Hello, \" . \$name . \"!\";\n",
            ['.演算子で文字列を連結', 'または\"Hello, \$name!\"でも可能'],
            'easy',
            15,
            ['php', 'string', 'concatenation'],
            3,
            [
                ['', 'Hello, Alice!', '文字列連結', true, false, 1],
            ]
        );

        // Exercise 4: if-else文
        $this->createExercise(
            $phpLanguage,
            '数値が正か負かゼロか判定',
            '条件分岐',
            '変数$num = -5があります。この数値が正の数なら「Positive」、負の数なら「Negative」、0なら「Zero」と出力してください。',
            "<?php\n\n\$num = -5;\n\n// 判定して出力\n",
            "<?php\n\n\$num = -5;\n\nif (\$num > 0) {\n    echo \"Positive\";\n} elseif (\$num < 0) {\n    echo \"Negative\";\n} else {\n    echo \"Zero\";\n}\n",
            ['if, elseif, elseで条件分岐', '条件は()で囲みます'],
            'easy',
            15,
            ['php', 'conditional', 'if-else'],
            4,
            [
                ['', 'Negative', '負の数', true, false, 1],
            ]
        );

        // Exercise 5: forループ
        $this->createExercise(
            $phpLanguage,
            '1から5までの数値を出力',
            'Forループ',
            'forループを使って1から5までの数値を1行ずつ出力してください。',
            "<?php\n\n// 1から5までループ\n",
            "<?php\n\nfor (\$i = 1; \$i <= 5; \$i++) {\n    echo \$i . \"\\n\";\n}\n",
            ['for (初期化; 条件; 更新) { }の形式', '\\nで改行'],
            'easy',
            15,
            ['php', 'loop', 'for'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで出力', true, false, 1],
            ]
        );

        // Exercise 6: Whileループ
        $this->createExercise(
            $phpLanguage,
            'Whileループで5回カウント',
            'Whileループ',
            'whileループを使って0から4までカウントし、1行ずつ出力してください。',
            "<?php\n\n\$count = 0;\n\n// countが5未満の間ループ\n",
            "<?php\n\n\$count = 0;\n\nwhile (\$count < 5) {\n    echo \$count . \"\\n\";\n    \$count++;\n}\n",
            ['while (条件) { }でループ', '$count++でインクリメント'],
            'easy',
            15,
            ['php', 'loop', 'while'],
            6,
            [
                ['', "0\n1\n2\n3\n4", '0から4まで出力', true, false, 1],
            ]
        );

        // Exercise 7: 配列の定義と要素へのアクセス
        $this->createExercise(
            $phpLanguage,
            '配列の最初と最後の要素',
            '配列操作',
            '配列$fruits = ["Apple", "Banana", "Orange"]を定義し、最初の要素と最後の要素を1行ずつ出力してください。',
            "<?php\n\n// 配列を定義\n\n// 最初の要素を出力\n// 最後の要素を出力\n",
            "<?php\n\n\$fruits = [\"Apple\", \"Banana\", \"Orange\"];\n\necho \$fruits[0] . \"\\n\";\necho \$fruits[count(\$fruits) - 1];\n",
            ['配列は[]で定義', 'インデックス0で最初の要素', 'count()で要素数を取得'],
            'easy',
            20,
            ['php', 'array', 'indexing'],
            7,
            [
                ['', "Apple\nOrange", '最初と最後の要素', true, false, 1],
            ]
        );

        // Exercise 8: foreach文
        $this->createExercise(
            $phpLanguage,
            '配列の全要素を出力',
            'Foreach文',
            '配列$colors = ["Red", "Green", "Blue"]の全要素を1行ずつ出力してください。',
            "<?php\n\n\$colors = [\"Red\", \"Green\", \"Blue\"];\n\n// 全要素をループで出力\n",
            "<?php\n\n\$colors = [\"Red\", \"Green\", \"Blue\"];\n\nforeach (\$colors as \$color) {\n    echo \$color . \"\\n\";\n}\n",
            ['foreach ($配列 as $変数) { }で各要素を処理'],
            'easy',
            15,
            ['php', 'array', 'foreach', 'iteration'],
            8,
            [
                ['', "Red\nGreen\nBlue", '全要素を出力', true, false, 1],
            ]
        );

        // Exercise 9: 関数の定義
        $this->createExercise(
            $phpLanguage,
            '挨拶関数を作成',
            '関数の基本',
            '引数として名前を受け取り「Hello, [名前]!」を返すgreet関数を定義し、"World"を引数として呼び出して結果を出力してください。',
            "<?php\n\n// greet関数を定義\n\n// 関数を呼び出して出力\n",
            "<?php\n\nfunction greet(\$name) {\n    return \"Hello, \$name!\";\n}\n\necho greet(\"World\");\n",
            ['function 関数名($引数) { }で関数定義', 'returnで値を返す'],
            'medium',
            20,
            ['php', 'function', 'definition'],
            9,
            [
                ['', 'Hello, World!', '関数を呼び出し', true, false, 1],
            ]
        );

        // Exercise 10: 連想配列
        $this->createExercise(
            $phpLanguage,
            '連想配列で値を取得',
            '連想配列の使用',
            '連想配列を使って動物の鳴き声を管理します。$sounds = ["dog" => "bark", "cat" => "meow"]を作成し、"dog"の鳴き声を出力してください。',
            "<?php\n\n// 連想配列を作成\n\n// dogの鳴き声を出力\n",
            "<?php\n\n\$sounds = [\"dog\" => \"bark\", \"cat\" => \"meow\"];\n\necho \$sounds[\"dog\"];\n",
            ['連想配列は[キー => 値]で定義', '$配列[キー]で値を取得'],
            'medium',
            25,
            ['php', 'associative-array', 'array'],
            10,
            [
                ['', 'bark', '犬の鳴き声', true, false, 1],
            ]
        );

        // Exercise 11: 文字列関数
        $this->createExercise(
            $phpLanguage,
            '文字列を大文字に変換',
            '文字列関数',
            '変数$text = "hello"を大文字に変換して出力してください。',
            "<?php\n\n\$text = \"hello\";\n\n// 大文字に変換して出力\n",
            "<?php\n\n\$text = \"hello\";\n\necho strtoupper(\$text);\n",
            ['strtoupper()で大文字に変換', 'strtolower()で小文字に変換'],
            'easy',
            15,
            ['php', 'string', 'function', 'uppercase'],
            11,
            [
                ['', 'HELLO', '小文字→大文字', true, false, 1],
            ]
        );

        // Exercise 12: 文字列の長さ
        $this->createExercise(
            $phpLanguage,
            '文字列の長さを取得',
            'strlen()関数',
            '変数$text = "Programming"の文字列の長さを出力してください。',
            "<?php\n\n\$text = \"Programming\";\n\n// 長さを出力\n",
            "<?php\n\n\$text = \"Programming\";\n\necho strlen(\$text);\n",
            ['strlen()で長さを取得'],
            'easy',
            15,
            ['php', 'string', 'length'],
            12,
            [
                ['', '11', 'Programmingは11文字', true, false, 1],
            ]
        );

        // Exercise 13: 配列の要素数
        $this->createExercise(
            $phpLanguage,
            '配列の要素数を取得',
            'count()関数',
            '配列$numbers = [1, 2, 3, 4, 5]の要素数を出力してください。',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\n// 要素数を出力\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\necho count(\$numbers);\n",
            ['count()で配列の要素数を取得'],
            'easy',
            15,
            ['php', 'array', 'count'],
            13,
            [
                ['', '5', '要素数は5', true, false, 1],
            ]
        );

        // Exercise 14: 配列の合計
        $this->createExercise(
            $phpLanguage,
            '配列の要素の合計',
            'array_sum()関数',
            '配列$numbers = [1, 2, 3, 4, 5]の全要素の合計を出力してください。',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\n// 合計を出力\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\necho array_sum(\$numbers);\n",
            ['array_sum()で配列の合計を計算'],
            'easy',
            15,
            ['php', 'array', 'sum'],
            14,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1],
            ]
        );

        // Exercise 15: 配列の最大値・最小値
        $this->createExercise(
            $phpLanguage,
            '配列の最大値と最小値',
            'max()とmin()関数',
            '配列$numbers = [3, 1, 4, 1, 5, 9, 2, 6]の最大値と最小値を1行ずつ出力してください。',
            "<?php\n\n\$numbers = [3, 1, 4, 1, 5, 9, 2, 6];\n\n// 最大値を出力\n// 最小値を出力\n",
            "<?php\n\n\$numbers = [3, 1, 4, 1, 5, 9, 2, 6];\n\necho max(\$numbers) . \"\\n\";\necho min(\$numbers);\n",
            ['max()で最大値', 'min()で最小値'],
            'easy',
            20,
            ['php', 'array', 'max', 'min'],
            15,
            [
                ['', "9\n1", '最大値9、最小値1', true, false, 1],
            ]
        );

        // Exercise 16: 配列のフィルタリング
        $this->createExercise(
            $phpLanguage,
            '偶数のみを抽出',
            'array_filter()関数',
            '配列$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]から偶数のみを抽出し、array_values()で再インデックス化して出力してください。',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n\n// 偶数のみを抽出\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n\n\$evens = array_filter(\$numbers, function(\$n) {\n    return \$n % 2 == 0;\n});\n\nprint_r(array_values(\$evens));\n",
            ['array_filter()でフィルタリング', 'コールバック関数で条件を指定', 'print_r()で配列を出力'],
            'medium',
            30,
            ['php', 'array', 'filter', 'callback'],
            16,
            [
                ['', "Array\n(\n    [0] => 2\n    [1] => 4\n    [2] => 6\n    [3] => 8\n    [4] => 10\n)", '偶数のみ抽出', true, false, 1],
            ]
        );

        // Exercise 17: 配列の結合
        $this->createExercise(
            $phpLanguage,
            '配列を文字列に結合',
            'implode()関数',
            '配列$words = ["Hello", "World", "PHP"]を空白で結合して1つの文字列として出力してください。',
            "<?php\n\n\$words = [\"Hello\", \"World\", \"PHP\"];\n\n// 空白で結合して出力\n",
            "<?php\n\n\$words = [\"Hello\", \"World\", \"PHP\"];\n\necho implode(\" \", \$words);\n",
            ['implode(\"区切り文字\", $配列)で結合'],
            'medium',
            20,
            ['php', 'string', 'array', 'implode'],
            17,
            [
                ['', 'Hello World PHP', '空白で結合', true, false, 1],
            ]
        );

        // Exercise 18: 文字列の分割
        $this->createExercise(
            $phpLanguage,
            '文字列を配列に分割',
            'explode()関数',
            '文字列"apple,banana,orange"をカンマで分割して配列にし、print_r()で出力してください。',
            "<?php\n\n\$text = \"apple,banana,orange\";\n\n// カンマで分割して出力\n",
            "<?php\n\n\$text = \"apple,banana,orange\";\n\nprint_r(explode(\",\", \$text));\n",
            ['explode(\"区切り文字\", $文字列)で分割'],
            'easy',
            20,
            ['php', 'string', 'explode', 'array'],
            18,
            [
                ['', "Array\n(\n    [0] => apple\n    [1] => banana\n    [2] => orange\n)", 'カンマで分割', true, false, 1],
            ]
        );

        // Exercise 19: 文字列の置換
        $this->createExercise(
            $phpLanguage,
            '文字列の一部を置換',
            'str_replace()関数',
            '変数$text = "Hello World"の"World"を"PHP"に置換して出力してください。',
            "<?php\n\n\$text = \"Hello World\";\n\n// Worldを PHPに置換して出力\n",
            "<?php\n\n\$text = \"Hello World\";\n\necho str_replace(\"World\", \"PHP\", \$text);\n",
            ['str_replace(検索文字列, 置換文字列, 対象)で置換'],
            'easy',
            20,
            ['php', 'string', 'replace'],
            19,
            [
                ['', 'Hello PHP', '文字列置換', true, false, 1],
            ]
        );

        // Exercise 20: switch文
        $this->createExercise(
            $phpLanguage,
            '曜日の判定',
            'Switch文',
            '変数$day = 1があります。switch文を使って対応する曜日名を出力してください。1=Monday, 2=Tuesday, ..., 7=Sunday。該当しない場合は"Invalid day"と出力。',
            "<?php\n\n\$day = 1;\n\n// switch文で曜日を判定\n",
            "<?php\n\n\$day = 1;\n\nswitch (\$day) {\n    case 1:\n        echo \"Monday\";\n        break;\n    case 2:\n        echo \"Tuesday\";\n        break;\n    case 3:\n        echo \"Wednesday\";\n        break;\n    case 4:\n        echo \"Thursday\";\n        break;\n    case 5:\n        echo \"Friday\";\n        break;\n    case 6:\n        echo \"Saturday\";\n        break;\n    case 7:\n        echo \"Sunday\";\n        break;\n    default:\n        echo \"Invalid day\";\n        break;\n}\n",
            ['switch ($変数) { case 値: ... break; }の形式', 'defaultで他のすべてのケース'],
            'medium',
            30,
            ['php', 'switch', 'conditional'],
            20,
            [
                ['', 'Monday', '月曜日', true, false, 1],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($phpLanguage);

        $this->command->info('PHP exercises seeded successfully!');
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
            'slug' => Str::slug($title),
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
