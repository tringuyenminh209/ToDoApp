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

        // Exercise 21: array_map関数
        $this->createExercise(
            $phpLanguage,
            '配列の各要素を2倍にする',
            '配列関数 - array_map',
            '配列[1, 2, 3, 4, 5]の各要素を2倍にして出力してください。array_map関数を使用します。出力形式: "2,4,6,8,10"',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\n// array_mapで各要素を2倍にする\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\$doubled = array_map(function(\$n) {\n    return \$n * 2;\n}, \$numbers);\necho implode(',', \$doubled);\n",
            ['array_map(関数, 配列)で各要素に関数を適用', 'implodeで配列を文字列に結合'],
            'medium',
            25,
            ['php', 'array', 'array_map', 'function'],
            21,
            [
                ['', '2,4,6,8,10', '各要素を2倍', true, false, 1],
            ]
        );

        // Exercise 22: array_filter関数
        $this->createExercise(
            $phpLanguage,
            '偶数のみを抽出',
            '配列関数 - array_filter',
            '配列[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]から偶数のみを抽出して出力してください。出力形式: "2,4,6,8,10"',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n\n// 偶数のみをフィルタリング\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n\$evens = array_filter(\$numbers, function(\$n) {\n    return \$n % 2 === 0;\n});\necho implode(',', \$evens);\n",
            ['array_filter(配列, 関数)で条件に合う要素のみ残す', '偶数は % 2 === 0で判定'],
            'medium',
            25,
            ['php', 'array', 'array_filter', 'function'],
            22,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1],
            ]
        );

        // Exercise 23: Date and Time
        $this->createExercise(
            $phpLanguage,
            '現在の日付をフォーマット',
            '日付と時刻',
            '現在の日付を"2025-01-15"の形式(Y-m-d)で出力してください。date関数を使用します。',
            "<?php\n\n// 現在の日付をY-m-d形式で出力\n",
            "<?php\n\necho date('Y-m-d');\n",
            ['date(\'Y-m-d\')で年-月-日形式', 'Y=4桁年、m=月、d=日'],
            'easy',
            15,
            ['php', 'date', 'time'],
            23,
            [
                ['', date('Y-m-d'), '今日の日付', true, false, 1],
            ]
        );

        // Exercise 24: JSON encoding
        $this->createExercise(
            $phpLanguage,
            '配列をJSONに変換',
            'JSON処理',
            '連想配列["name" => "John", "age" => 30]をJSON文字列に変換して出力してください。',
            "<?php\n\n\$data = ['name' => 'John', 'age' => 30];\n\n// JSONに変換\n",
            "<?php\n\n\$data = ['name' => 'John', 'age' => 30];\necho json_encode(\$data);\n",
            ['json_encode()で配列をJSON文字列に変換'],
            'easy',
            20,
            ['php', 'json', 'encoding'],
            24,
            [
                ['', '{"name":"John","age":30}', 'JSONエンコード', true, false, 1],
            ]
        );

        // Exercise 25: JSON decoding
        $this->createExercise(
            $phpLanguage,
            'JSONから値を取得',
            'JSON処理',
            'JSON文字列"{\"name\":\"Alice\",\"age\":25}"をデコードして、nameの値を出力してください。',
            "<?php\n\n\$json = '{\"name\":\"Alice\",\"age\":25}';\n\n// JSONをデコードしてnameを出力\n",
            "<?php\n\n\$json = '{\"name\":\"Alice\",\"age\":25}';\n\$data = json_decode(\$json, true);\necho \$data['name'];\n",
            ['json_decode(\$json, true)で連想配列に変換', '第2引数trueで連想配列に'],
            'easy',
            20,
            ['php', 'json', 'decoding'],
            25,
            [
                ['', 'Alice', 'nameの値を取得', true, false, 1],
            ]
        );

        // Exercise 26: Class定義
        $this->createExercise(
            $phpLanguage,
            'シンプルなクラスを作成',
            'オブジェクト指向 - Class',
            'nameプロパティとgetName()メソッドを持つPersonクラスを作成し、"John"という名前のインスタンスを作成して名前を出力してください。',
            "<?php\n\n// Personクラスを定義\n\n// インスタンスを作成して名前を出力\n",
            "<?php\n\nclass Person {\n    public \$name;\n    \n    public function __construct(\$name) {\n        \$this->name = \$name;\n    }\n    \n    public function getName() {\n        return \$this->name;\n    }\n}\n\n\$person = new Person('John');\necho \$person->getName();\n",
            ['class クラス名 { ... }で定義', '__construct()はコンストラクタ', '\$this->でプロパティにアクセス'],
            'medium',
            30,
            ['php', 'oop', 'class', 'object'],
            26,
            [
                ['', 'John', 'クラスとオブジェクト', true, false, 1],
            ]
        );

        // Exercise 27: 継承
        $this->createExercise(
            $phpLanguage,
            'クラスの継承',
            'オブジェクト指向 - Inheritance',
            'Animalクラスにspeakメソッド、DogクラスがAnimalを継承してspeakメソッドをオーバーライドし"Woof!"と出力するようにしてください。',
            "<?php\n\n// Animalクラスを定義\n\n// Dogクラスで継承\n\n// Dogインスタンスを作成してspeakを呼び出す\n",
            "<?php\n\nclass Animal {\n    public function speak() {\n        return \"Some sound\";\n    }\n}\n\nclass Dog extends Animal {\n    public function speak() {\n        return \"Woof!\";\n    }\n}\n\n\$dog = new Dog();\necho \$dog->speak();\n",
            ['class 子クラス extends 親クラス { ... }で継承', 'メソッドをオーバーライド可能'],
            'medium',
            35,
            ['php', 'oop', 'inheritance', 'extends'],
            27,
            [
                ['', 'Woof!', '継承とオーバーライド', true, false, 1],
            ]
        );

        // Exercise 28: Static methods
        $this->createExercise(
            $phpLanguage,
            '静的メソッドの使用',
            'オブジェクト指向 - Static',
            'MathUtilsクラスにstatic add(\$a, \$b)メソッドを作成し、5と10を足した結果を出力してください。',
            "<?php\n\n// MathUtilsクラスを定義\n\n// static addメソッドを呼び出す\n",
            "<?php\n\nclass MathUtils {\n    public static function add(\$a, \$b) {\n        return \$a + \$b;\n    }\n}\n\necho MathUtils::add(5, 10);\n",
            ['public static function で静的メソッド定義', 'クラス名::メソッド名()で呼び出し'],
            'medium',
            25,
            ['php', 'oop', 'static', 'method'],
            28,
            [
                ['', '15', '静的メソッド', true, false, 1],
            ]
        );

        // Exercise 29: Constants
        $this->createExercise(
            $phpLanguage,
            '定数の定義と使用',
            '定数',
            'PIという名前の定数を3.14159で定義し、その値を出力してください。',
            "<?php\n\n// 定数PIを定義\n\n// PIを出力\n",
            "<?php\n\ndefine('PI', 3.14159);\necho PI;\n",
            ['define(\'名前\', 値)で定数を定義', '定数名は大文字が慣例'],
            'easy',
            15,
            ['php', 'constant', 'define'],
            29,
            [
                ['', '3.14159', '定数の定義', true, false, 1],
            ]
        );

        // Exercise 30: Type casting
        $this->createExercise(
            $phpLanguage,
            '型キャスト',
            'データ型変換',
            '文字列"123"を整数に変換し、その値に10を足して出力してください。',
            "<?php\n\n\$str = '123';\n\n// 整数に変換して10を足す\n",
            "<?php\n\n\$str = '123';\n\$num = (int)\$str;\necho \$num + 10;\n",
            ['(int)で整数に変換', '(string), (float), (bool)なども使用可能'],
            'easy',
            15,
            ['php', 'type', 'casting', 'conversion'],
            30,
            [
                ['', '133', '型変換', true, false, 1],
            ]
        );

        // Exercise 31: Ternary operator
        $this->createExercise(
            $phpLanguage,
            '三項演算子',
            '三項演算子',
            '変数\$age = 18があります。三項演算子を使って、18以上なら"Adult"、未満なら"Minor"と出力してください。',
            "<?php\n\n\$age = 18;\n\n// 三項演算子で判定\n",
            "<?php\n\n\$age = 18;\necho \$age >= 18 ? 'Adult' : 'Minor';\n",
            ['条件 ? 真の値 : 偽の値', 'if-elseの短縮形'],
            'easy',
            20,
            ['php', 'ternary', 'operator'],
            31,
            [
                ['', 'Adult', '三項演算子', true, false, 1],
            ]
        );

        // Exercise 32: Null coalescing operator
        $this->createExercise(
            $phpLanguage,
            'Null合体演算子',
            'Null合体演算子',
            '変数\$name = nullがあります。Null合体演算子(??)を使って、\$nameがnullなら"Guest"、そうでなければ\$nameの値を出力してください。',
            "<?php\n\n\$name = null;\n\n// Null合体演算子を使用\n",
            "<?php\n\n\$name = null;\necho \$name ?? 'Guest';\n",
            ['\$var ?? デフォルト値', 'nullまたは未定義の場合にデフォルト値を使用'],
            'easy',
            20,
            ['php', 'null-coalescing', 'operator'],
            32,
            [
                ['', 'Guest', 'Null合体演算子', true, false, 1],
            ]
        );

        // Exercise 33: Spread operator
        $this->createExercise(
            $phpLanguage,
            'スプレッド演算子',
            'スプレッド演算子',
            '配列\$arr1 = [1, 2]と\$arr2 = [3, 4]があります。スプレッド演算子(...)を使って結合し、カンマ区切りで出力してください。',
            "<?php\n\n\$arr1 = [1, 2];\n\$arr2 = [3, 4];\n\n// スプレッド演算子で結合\n",
            "<?php\n\n\$arr1 = [1, 2];\n\$arr2 = [3, 4];\n\$merged = [...\$arr1, ...\$arr2];\necho implode(',', \$merged);\n",
            ['...\$arrayで配列を展開', 'PHP 7.4以降で使用可能'],
            'medium',
            25,
            ['php', 'spread', 'operator', 'array'],
            33,
            [
                ['', '1,2,3,4', 'スプレッド演算子', true, false, 1],
            ]
        );

        // Exercise 34: Anonymous function
        $this->createExercise(
            $phpLanguage,
            '無名関数（クロージャ）',
            '無名関数',
            '無名関数を使って、2つの数値を掛け算する関数を作成し、5と6を掛けた結果を出力してください。',
            "<?php\n\n// 無名関数を定義\n\n// 関数を呼び出して結果を出力\n",
            "<?php\n\n\$multiply = function(\$a, \$b) {\n    return \$a * \$b;\n};\n\necho \$multiply(5, 6);\n",
            ['\$var = function() { ... };で無名関数を定義', '変数名()で呼び出し'],
            'medium',
            25,
            ['php', 'anonymous', 'function', 'closure'],
            34,
            [
                ['', '30', '無名関数', true, false, 1],
            ]
        );

        // Exercise 35: Arrow function
        $this->createExercise(
            $phpLanguage,
            'アロー関数',
            'アロー関数',
            'アロー関数(fn)を使って、数値を2乗する関数を作成し、7を2乗した結果を出力してください。',
            "<?php\n\n// アロー関数を定義\n\n// 関数を呼び出して結果を出力\n",
            "<?php\n\n\$square = fn(\$n) => \$n * \$n;\n\necho \$square(7);\n",
            ['fn(\$param) => 式 でアロー関数定義', 'PHP 7.4以降で使用可能', '自動的にreturnされる'],
            'medium',
            25,
            ['php', 'arrow', 'function', 'fn'],
            35,
            [
                ['', '49', 'アロー関数', true, false, 1],
            ]
        );

        // Exercise 36: in_array関数
        $this->createExercise(
            $phpLanguage,
            '配列内の要素を検索',
            '配列関数 - in_array',
            '配列["apple", "banana", "orange"]に"banana"が含まれているか確認し、含まれていれば"Found"、なければ"Not found"と出力してください。',
            "<?php\n\n\$fruits = ['apple', 'banana', 'orange'];\n\n// in_arrayで検索\n",
            "<?php\n\n\$fruits = ['apple', 'banana', 'orange'];\necho in_array('banana', \$fruits) ? 'Found' : 'Not found';\n",
            ['in_array(値, 配列)で存在チェック', '見つかればtrueを返す'],
            'easy',
            20,
            ['php', 'array', 'in_array', 'search'],
            36,
            [
                ['', 'Found', '配列検索', true, false, 1],
            ]
        );

        // Exercise 37: array_reduce関数
        $this->createExercise(
            $phpLanguage,
            '配列の合計を計算',
            '配列関数 - array_reduce',
            '配列[1, 2, 3, 4, 5]の合計をarray_reduce関数で計算して出力してください。',
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\n// array_reduceで合計を計算\n",
            "<?php\n\n\$numbers = [1, 2, 3, 4, 5];\n\$sum = array_reduce(\$numbers, function(\$carry, \$item) {\n    return \$carry + \$item;\n}, 0);\necho \$sum;\n",
            ['array_reduce(配列, 関数, 初期値)', '\$carryは累積値、\$itemは現在の要素'],
            'medium',
            30,
            ['php', 'array', 'array_reduce', 'function'],
            37,
            [
                ['', '15', '配列の合計', true, false, 1],
            ]
        );

        // Exercise 38: count関数
        $this->createExercise(
            $phpLanguage,
            '配列の要素数を数える',
            '配列関数 - count',
            '配列["red", "green", "blue", "yellow"]の要素数を数えて出力してください。',
            "<?php\n\n\$colors = ['red', 'green', 'blue', 'yellow'];\n\n// 要素数を数える\n",
            "<?php\n\n\$colors = ['red', 'green', 'blue', 'yellow'];\necho count(\$colors);\n",
            ['count(\$array)で要素数を取得', 'sizeof()も同じ機能'],
            'easy',
            10,
            ['php', 'array', 'count'],
            38,
            [
                ['', '4', '配列の要素数', true, false, 1],
            ]
        );

        // Exercise 39: str_replace関数
        $this->createExercise(
            $phpLanguage,
            '複数の文字列を置換',
            '文字列関数 - str_replace',
            '文字列"I like apples and apples are good"の"apples"を"oranges"に置換して出力してください。',
            "<?php\n\n\$text = 'I like apples and apples are good';\n\n// applesをorangesに置換\n",
            "<?php\n\n\$text = 'I like apples and apples are good';\necho str_replace('apples', 'oranges', \$text);\n",
            ['str_replace(検索, 置換, 文字列)', 'すべての出現箇所を置換'],
            'easy',
            15,
            ['php', 'string', 'str_replace'],
            39,
            [
                ['', 'I like oranges and oranges are good', '文字列置換', true, false, 1],
            ]
        );

        // Exercise 40: isset関数
        $this->createExercise(
            $phpLanguage,
            '変数が定義されているか確認',
            '変数チェック - isset',
            '変数\$value = "Hello"が定義されているか確認し、定義されていれば"Defined"、未定義なら"Undefined"と出力してください。',
            "<?php\n\n\$value = 'Hello';\n\n// issetで確認\n",
            "<?php\n\n\$value = 'Hello';\necho isset(\$value) ? 'Defined' : 'Undefined';\n",
            ['isset(\$var)で変数の存在とnullでないことを確認'],
            'easy',
            15,
            ['php', 'isset', 'variable'],
            40,
            [
                ['', 'Defined', '変数の存在確認', true, false, 1],
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
