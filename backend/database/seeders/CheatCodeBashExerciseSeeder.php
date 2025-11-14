<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeBashExerciseSeeder extends Seeder
{
    /**
     * Seed Bash exercise data
     * Bash言語の練習問題
     */
    public function run(): void
    {
        // Get Bash Language
        $bashLanguage = CheatCodeLanguage::where('name', 'bash')->first();

        if (!$bashLanguage) {
            $this->command->error('Bash language not found. Please run CheatCodeBashSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $bashLanguage,
            'Hello World出力',
            'Bashスクリプトの基本',
            '「Hello, World!」という文字列を出力するBashスクリプトを書いてください。',
            "#!/bin/bash\n\n# ここにコードを書いてください\n",
            "#!/bin/bash\n\necho \"Hello, World!\"",
            ['基本的なechoコマンドを使用します', '文字列はダブルクォートで囲みます'],
            'easy',
            10,
            ['bash', 'basics', 'echo'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数の使用
        $this->createExercise(
            $bashLanguage,
            '変数を使った挨拶',
            '変数の宣言と使用',
            '変数NAMEに「Alice」を代入し、「Hello, Alice!」と出力してください。',
            "#!/bin/bash\n\n# 変数NAMEに\"Alice\"を代入\n\n# 変数を使って挨拶を出力\n",
            "#!/bin/bash\n\nNAME=\"Alice\"\necho \"Hello, \$NAME!\"",
            ['変数の代入は「変数名=値」の形式です（スペースなし）', '変数を使うときは$を付けます'],
            'easy',
            15,
            ['bash', 'variable', 'basics'],
            2,
            [
                ['', 'Hello, Alice!', '変数を使った挨拶', true, false, 1],
            ]
        );

        // Exercise 3: 引数の使用
        $this->createExercise(
            $bashLanguage,
            'コマンドライン引数を使う',
            'スクリプト引数の処理',
            'コマンドライン引数として渡された名前を使って「Hello, [名前]!」と出力してください。第1引数（$1）を使用します。',
            "#!/bin/bash\n\n# 第1引数を使って挨拶を出力\n",
            "#!/bin/bash\n\necho \"Hello, \$1!\"",
            ['第1引数は$1で取得できます', 'echoコマンドで変数を表示できます'],
            'easy',
            15,
            ['bash', 'arguments', 'basics'],
            3,
            [
                ['Alice', 'Hello, Alice!', '引数Aliceを渡す', true, false, 1],
                ['Bob', 'Hello, Bob!', '引数Bobを渡す', false, false, 2],
                ['世界', 'Hello, 世界!', '日本語引数', false, false, 3],
            ]
        );

        // Exercise 4: 算術演算
        $this->createExercise(
            $bashLanguage,
            '2つの数値の合計',
            '算術展開',
            '2つの数値（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "#!/bin/bash\n\n# 2つの数値を変数に代入\n\n# 合計を計算して出力\n",
            "#!/bin/bash\n\nA=10\nB=20\nSUM=\$((A + B))\necho \$SUM",
            ['算術展開は$((式))の形式です', '変数の値を使って計算できます'],
            'easy',
            20,
            ['bash', 'arithmetic', 'calculation'],
            4,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 5: 条件分岐 (文字列の比較)
        $this->createExercise(
            $bashLanguage,
            '文字列が空かチェック',
            '条件分岐の基本',
            '変数STRが空の場合は「Empty」、空でない場合は「Not empty」と出力してください。',
            "#!/bin/bash\n\nSTR=\"\$1\"\n\n# STRが空かチェック\n",
            "#!/bin/bash\n\nSTR=\"\$1\"\n\nif [[ -z \"\$STR\" ]]; then\n    echo \"Empty\"\nelse\n    echo \"Not empty\"\nfi",
            ['-zオプションで文字列が空かチェックできます', 'if [[ 条件 ]]; then ... fi の形式です'],
            'medium',
            25,
            ['bash', 'conditional', 'string', 'if-else'],
            5,
            [
                ['', 'Empty', '空文字列の場合', true, false, 1],
                ['Hello', 'Not empty', '文字列がある場合', true, false, 2],
                ['123', 'Not empty', '数値文字列の場合', false, false, 3],
            ]
        );

        // Exercise 6: 整数の比較
        $this->createExercise(
            $bashLanguage,
            '数値の大小比較',
            '整数比較演算子',
            '2つの引数（整数）を受け取り、第1引数が第2引数より大きい場合は「Greater」、等しい場合は「Equal」、小さい場合は「Less」と出力してください。',
            "#!/bin/bash\n\nNUM1=\$1\nNUM2=\$2\n\n# NUM1とNUM2を比較\n",
            "#!/bin/bash\n\nNUM1=\$1\nNUM2=\$2\n\nif [[ \$NUM1 -gt \$NUM2 ]]; then\n    echo \"Greater\"\nelif [[ \$NUM1 -eq \$NUM2 ]]; then\n    echo \"Equal\"\nelse\n    echo \"Less\"\nfi",
            ['-gt は greater than (>)', '-eq は equal (==)', '-lt は less than (<)'],
            'medium',
            30,
            ['bash', 'conditional', 'comparison', 'integer'],
            6,
            [
                ["10\n5", 'Greater', '10 > 5', true, false, 1],
                ["5\n5", 'Equal', '5 == 5', true, false, 2],
                ["3\n7", 'Less', '3 < 7', true, false, 3],
                ["100\n1", 'Greater', '100 > 1', false, false, 4],
            ]
        );

        // Exercise 7: Forループ (範囲)
        $this->createExercise(
            $bashLanguage,
            '1から5までの数値を出力',
            'Forループの基本',
            'forループを使って1から5までの数値を1行ずつ出力してください。',
            "#!/bin/bash\n\n# 1から5までループ\n",
            "#!/bin/bash\n\nfor i in {1..5}; do\n    echo \$i\ndone",
            ['{開始..終了}の形式で範囲を指定できます', 'forループはfor 変数 in リスト; do ... doneの形式です'],
            'easy',
            20,
            ['bash', 'loop', 'for', 'range'],
            7,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで出力', true, false, 1],
            ]
        );

        // Exercise 8: Whileループ
        $this->createExercise(
            $bashLanguage,
            'Whileループで5回カウント',
            'Whileループの使用',
            'whileループを使って0から4までカウントし、1行ずつ出力してください。',
            "#!/bin/bash\n\ncount=0\n\n# countが5未満の間ループ\n",
            "#!/bin/bash\n\ncount=0\n\nwhile [ \$count -lt 5 ]; do\n    echo \$count\n    ((count++))\ndone",
            ['whileループはwhile [ 条件 ]; do ... doneの形式です', '((count++))でインクリメントできます'],
            'medium',
            25,
            ['bash', 'loop', 'while', 'increment'],
            8,
            [
                ['', "0\n1\n2\n3\n4", '0から4まで出力', true, false, 1],
            ]
        );

        // Exercise 9: 配列の定義と要素へのアクセス
        $this->createExercise(
            $bashLanguage,
            '配列の最初と最後の要素',
            '配列操作の基本',
            '配列Fruits=("Apple" "Banana" "Orange")を定義し、最初の要素と最後の要素を1行ずつ出力してください。',
            "#!/bin/bash\n\n# 配列を定義\n\n# 最初の要素を出力\n# 最後の要素を出力\n",
            "#!/bin/bash\n\nFruits=(\"Apple\" \"Banana\" \"Orange\")\n\necho \${Fruits[0]}\necho \${Fruits[-1]}",
            ['配列は変数名=(要素1 要素2 ...)の形式で定義', '${配列名[0]}で最初の要素', '${配列名[-1]}で最後の要素'],
            'medium',
            25,
            ['bash', 'array', 'indexing'],
            9,
            [
                ['', "Apple\nOrange", '最初と最後の要素', true, false, 1],
            ]
        );

        // Exercise 10: 配列のループ
        $this->createExercise(
            $bashLanguage,
            '配列の全要素を出力',
            '配列の反復処理',
            '配列Colors=("Red" "Green" "Blue")の全要素を1行ずつ出力してください。',
            "#!/bin/bash\n\nColors=(\"Red\" \"Green\" \"Blue\")\n\n# 全要素をループで出力\n",
            "#!/bin/bash\n\nColors=(\"Red\" \"Green\" \"Blue\")\n\nfor color in \"\${Colors[@]}\"; do\n    echo \$color\ndone",
            ['${配列名[@]}で全要素を取得', 'forループで各要素を処理'],
            'medium',
            25,
            ['bash', 'array', 'loop', 'iteration'],
            10,
            [
                ['', "Red\nGreen\nBlue", '全要素を出力', true, false, 1],
            ]
        );

        // Exercise 11: 関数の定義と呼び出し
        $this->createExercise(
            $bashLanguage,
            '挨拶関数を作成',
            '関数の基本',
            '引数として名前を受け取り「Hello, [名前]!」を出力するgreet関数を定義し、"World"を引数として呼び出してください。',
            "#!/bin/bash\n\n# greet関数を定義\n\n# 関数を呼び出し\n",
            "#!/bin/bash\n\ngreet() {\n    echo \"Hello, \$1!\"\n}\n\ngreet \"World\"",
            ['関数は 関数名() { ... } の形式で定義', '関数内では$1, $2などで引数を取得', '関数名 引数 で呼び出し'],
            'medium',
            30,
            ['bash', 'function', 'definition'],
            11,
            [
                ['', 'Hello, World!', '関数を呼び出し', true, false, 1],
            ]
        );

        // Exercise 12: 文字列の置換
        $this->createExercise(
            $bashLanguage,
            '文字列の一部を置換',
            'パラメータ展開',
            '変数STR="Hello World"があります。"World"を"Bash"に置換して出力してください。',
            "#!/bin/bash\n\nSTR=\"Hello World\"\n\n# Worldを Bashに置換して出力\n",
            "#!/bin/bash\n\nSTR=\"Hello World\"\n\necho \${STR/World/Bash}",
            ['${変数/検索文字列/置換文字列}で置換できます'],
            'medium',
            25,
            ['bash', 'string', 'substitution', 'parameter-expansion'],
            12,
            [
                ['', 'Hello Bash', '文字列置換', true, false, 1],
            ]
        );

        // Exercise 13: 部分文字列の取得
        $this->createExercise(
            $bashLanguage,
            '文字列の一部を抽出',
            '部分文字列',
            '変数TEXT="Programming"から最初の4文字を抽出して出力してください。',
            "#!/bin/bash\n\nTEXT=\"Programming\"\n\n# 最初の4文字を抽出\n",
            "#!/bin/bash\n\nTEXT=\"Programming\"\n\necho \${TEXT:0:4}",
            ['${変数:開始位置:文字数}で部分文字列を取得', '開始位置は0から始まります'],
            'medium',
            25,
            ['bash', 'string', 'substring', 'parameter-expansion'],
            13,
            [
                ['', 'Prog', '最初の4文字', true, false, 1],
            ]
        );

        // Exercise 14: ファイル存在チェック
        $this->createExercise(
            $bashLanguage,
            'ファイルの存在確認',
            'ファイル条件テスト',
            '引数として渡されたファイル名が存在する場合は「File exists」、存在しない場合は「File not found」と出力してください。',
            "#!/bin/bash\n\nFILE=\$1\n\n# ファイルの存在をチェック\n",
            "#!/bin/bash\n\nFILE=\$1\n\nif [[ -f \"\$FILE\" ]]; then\n    echo \"File exists\"\nelse\n    echo \"File not found\"\nfi",
            ['-f オプションでファイルの存在をチェック', 'ファイルパスは変数で渡されます'],
            'medium',
            30,
            ['bash', 'file', 'conditional', 'test'],
            14,
            [
                ['/etc/passwd', 'File exists', '存在するファイル', true, false, 1],
                ['/nonexistent/file.txt', 'File not found', '存在しないファイル', true, false, 2],
            ]
        );

        // Exercise 15: Case文
        $this->createExercise(
            $bashLanguage,
            '曜日の判定',
            'Case文による分岐',
            '引数として曜日（1-7）を受け取り、case文を使って対応する曜日名を出力してください。1=Monday, 2=Tuesday, ..., 7=Sunday。該当しない場合は"Invalid day"と出力。',
            "#!/bin/bash\n\nDAY=\$1\n\n# case文で曜日を判定\n",
            "#!/bin/bash\n\nDAY=\$1\n\ncase \$DAY in\n    1)\n        echo \"Monday\"\n        ;;\n    2)\n        echo \"Tuesday\"\n        ;;\n    3)\n        echo \"Wednesday\"\n        ;;\n    4)\n        echo \"Thursday\"\n        ;;\n    5)\n        echo \"Friday\"\n        ;;\n    6)\n        echo \"Saturday\"\n        ;;\n    7)\n        echo \"Sunday\"\n        ;;\n    *)\n        echo \"Invalid day\"\n        ;;\nesac",
            ['case 変数 in パターン) 処理 ;; esac の形式', '*)で他のすべてのパターンにマッチ'],
            'hard',
            35,
            ['bash', 'case', 'conditional', 'switch'],
            15,
            [
                ['1', 'Monday', '月曜日', true, false, 1],
                ['5', 'Friday', '金曜日', true, false, 2],
                ['7', 'Sunday', '日曜日', false, false, 3],
                ['8', 'Invalid day', '無効な値', false, false, 4],
            ]
        );

        // Exercise 16: 連想配列
        $this->createExercise(
            $bashLanguage,
            '連想配列で値を取得',
            '連想配列の使用',
            '連想配列を使って動物の鳴き声を管理します。declare -A soundsで宣言し、sounds[dog]="bark"、sounds[cat]="meow"を設定して、引数で渡された動物名の鳴き声を出力してください。',
            "#!/bin/bash\n\n# 連想配列を宣言\n\n# 鳴き声を設定\n\n# 引数の動物名で鳴き声を出力\n",
            "#!/bin/bash\n\ndeclare -A sounds\n\nsounds[dog]=\"bark\"\nsounds[cat]=\"meow\"\n\necho \${sounds[\$1]}",
            ['declare -Aで連想配列を宣言', '連想配列[キー]=値 で設定', '${連想配列[キー]}で値を取得'],
            'hard',
            35,
            ['bash', 'associative-array', 'dictionary'],
            16,
            [
                ['dog', 'bark', '犬の鳴き声', true, false, 1],
                ['cat', 'meow', '猫の鳴き声', true, false, 2],
            ]
        );

        // Exercise 17: 複数の引数の合計
        $this->createExercise(
            $bashLanguage,
            'すべての引数の合計',
            '引数の反復処理',
            'コマンドライン引数として渡されたすべての数値の合計を計算して出力してください。$@を使ってすべての引数にアクセスできます。',
            "#!/bin/bash\n\nsum=0\n\n# すべての引数をループで処理\n",
            "#!/bin/bash\n\nsum=0\n\nfor num in \"\$@\"; do\n    sum=\$((sum + num))\ndone\n\necho \$sum",
            ['$@ですべての引数を取得', 'forループで各引数を処理', '算術展開で合計を計算'],
            'hard',
            40,
            ['bash', 'arguments', 'loop', 'arithmetic'],
            17,
            [
                ["1\n2\n3", '6', '1+2+3=6', true, false, 1],
                ["10\n20\n30\n40", '100', '10+20+30+40=100', true, false, 2],
                ["5", '5', '単一引数', false, false, 3],
            ]
        );

        // Exercise 18: 文字列の長さ
        $this->createExercise(
            $bashLanguage,
            '文字列の文字数をカウント',
            '文字列の長さ取得',
            '引数として渡された文字列の長さ（文字数）を出力してください。',
            "#!/bin/bash\n\nSTR=\$1\n\n# 文字列の長さを出力\n",
            "#!/bin/bash\n\nSTR=\$1\n\necho \${#STR}",
            ['${#変数}で文字列の長さを取得'],
            'easy',
            20,
            ['bash', 'string', 'length'],
            18,
            [
                ['Hello', '5', 'Helloは5文字', true, false, 1],
                ['Bash', '4', 'Bashは4文字', true, false, 2],
                ['Programming', '11', 'Programmingは11文字', false, false, 3],
            ]
        );

        // Exercise 19: デフォルト値
        $this->createExercise(
            $bashLanguage,
            'デフォルト値の設定',
            'パラメータ展開でデフォルト値',
            '引数が渡されなかった場合は"Guest"、渡された場合はその値を使って「Hello, [名前]!」と出力してください。${1:-デフォルト値}を使用します。',
            "#!/bin/bash\n\n# 引数がなければ\"Guest\"を使用\n",
            "#!/bin/bash\n\nNAME=\${1:-Guest}\necho \"Hello, \$NAME!\"",
            ['${変数:-デフォルト値}で変数が未設定の場合にデフォルト値を使用'],
            'medium',
            25,
            ['bash', 'parameter-expansion', 'default-value'],
            19,
            [
                ['', 'Hello, Guest!', '引数なし', true, false, 1],
                ['Alice', 'Hello, Alice!', '引数あり', true, false, 2],
            ]
        );

        // Exercise 20: 大文字・小文字変換
        $this->createExercise(
            $bashLanguage,
            '文字列を大文字に変換',
            '大文字変換',
            '引数として渡された文字列をすべて大文字に変換して出力してください。',
            "#!/bin/bash\n\nSTR=\$1\n\n# 大文字に変換して出力\n",
            "#!/bin/bash\n\nSTR=\$1\n\necho \${STR^^}",
            ['${変数^^}ですべて大文字に変換', '${変数,,}ですべて小文字に変換'],
            'medium',
            25,
            ['bash', 'string', 'uppercase', 'parameter-expansion'],
            20,
            [
                ['hello', 'HELLO', '小文字→大文字', true, false, 1],
                ['Bash', 'BASH', '混在→大文字', true, false, 2],
                ['WORLD', 'WORLD', 'すでに大文字', false, false, 3],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($bashLanguage);

        $this->command->info('Bash exercises seeded successfully!');
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
