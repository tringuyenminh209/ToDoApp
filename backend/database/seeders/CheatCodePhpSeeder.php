<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodePhpSeeder extends Seeder
{
    /**
     * Seed PHP cheat code data from Kizamu
     * Reference: https://Kizamu.com/php
     */
    public function run(): void
    {
        // Create PHP Language
        $phpLanguage = CheatCodeLanguage::create([
            'name' => 'php',
            'display_name' => 'PHP',
            'slug' => 'php',
            'icon' => 'ic_php',
            'color' => '#777BB4',
            'description' => 'PHPは、特にWeb開発に適した人気のある汎用スクリプト言語です。',
            'category' => 'programming',
            'popularity' => 85,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($phpLanguage, 'はじめに', 1, 'PHPプログラミングの基礎', 'getting-started');

        $this->createExample($section1, $phpLanguage, 'hello.php', 1,
            "<?php // begin with a PHP open tag.\n\necho \"Hello World\\n\";\nprint(\"Hello Kizamu.com\");\n\n?>",
            'PHPの開始タグから始める',
            "Hello World\nHello Kizamu.com",
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '変数', 2,
            "\$boolean1 = true;\n\$boolean2 = True;\n\n\$int = 12;\n\$float = 3.1415926;\nunset(\$float);  // Delete variable\n\n\$str1 = \"How are you?\";\n\$str2 = 'Fine, thanks';",
            '変数の宣言と型',
            null,
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '文字列', 3,
            "\$url = \"Kizamu.com\";\necho \"I'm learning PHP at \$url\";\n\n// Concatenate strings\necho \"I'm learning PHP at \" . \$url;\n\n\$hello = \"Hello, \";\n\$hello .= \"World!\";\necho \$hello;",
            '文字列操作と連結',
            "I'm learning PHP at Kizamu.com\nI'm learning PHP at Kizamu.com\nHello, World!",
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '配列', 4,
            "\$num = [1, 3, 5, 7, 9];\n\$num[5] = 11;\nunset(\$num[2]);    // Delete variable\nprint_r(\$num);\necho count(\$num);",
            '配列操作',
            null,
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '演算子', 5,
            "\$x = 1;\n\$y = 2;\n\n\$sum = \$x + \$y;\necho \$sum;",
            '基本的な算術演算子',
            '3',
            'easy'
        );

        $this->createExample($section1, $phpLanguage, 'インクルード', 6,
            "<?php\ninclude 'vars.php';\necho \$fruit . \"\\n\";\n\n/* Same as include,\ncause an error if cannot be included*/\nrequire 'vars.php';\n\n// Also works\ninclude('vars.php');\nrequire('vars.php');",
            'includeとrequire文',
            null,
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '関数', 7,
            "function add(\$num1, \$num2 = 1) {\n    return \$num1 + \$num2;\n}\necho add(10);\necho add(10, 5);",
            'デフォルトパラメータを持つ関数定義',
            "11\n15",
            'easy'
        );

        $this->createExample($section1, $phpLanguage, 'コメント', 8,
            "# This is a one line shell-style comment\n\n// This is a one line c++ style comment\n\n/* This is a multi line comment\n   yet another line of comment */",
            'PHPのコメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $phpLanguage, '定数', 9,
            "const MY_CONST = \"hello\";\n\necho MY_CONST;\n\necho 'MY_CONST is: ' . MY_CONST;",
            '定数の宣言',
            "hello\nMY_CONST is: hello",
            'easy'
        );

        $this->createExample($section1, $phpLanguage, 'クラス', 10,
            "class Student {\n    public function __construct(\$name) {\n        \$this->name = \$name;\n    }\n}\n\$alex = new Student(\"Alex\");",
            '基本的なクラス定義とインスタンス化',
            null,
            'medium'
        );

        // Section 2: PHP Types
        $section2 = $this->createSection($phpLanguage, 'PHPの型', 2, 'PHPのデータ型', 'php-types');

        $this->createExample($section2, $phpLanguage, 'ブール型', 1,
            "\$boolean1 = true;\n\$boolean2 = TRUE;\n\$boolean3 = false;\n\$boolean4 = FALSE;\n\n\$boolean5 = (boolean) 1;\n\$boolean6 = (boolean) 0;",
            'ブール型とキャスト',
            null,
            'easy'
        );

        $this->createExample($section2, $phpLanguage, '整数型', 2,
            "\$int1 = 28;\n\$int2 = -32;\n\$int3 = 012;   // octal => 10\n\$int4 = 0x0F;  // hex => 15\n\$int5 = 0b101; // binary => 5\n\n\$int6 = 2_000_100_000;  // PHP 7.4.0",
            '整数型とフォーマット',
            null,
            'easy'
        );

        $this->createExample($section2, $phpLanguage, '浮動小数点型', 3,
            "\$float1 = 1.234;\n\$float2 = 1.2e7;\n\$float3 = 7E-10;\n\n\$float4 = 1_234.567;  // PHP 7.4.0\nvar_dump(\$float4);\n\n\$float5 = 1 + \"10.5\";\n\$float6 = 1 + \"-1.3e3\";",
            '浮動小数点型/倍精度浮動小数点型と数値文字列',
            null,
            'easy'
        );

        $this->createExample($section2, $phpLanguage, 'Null', 4,
            "\$a = null;\n\$b = 'Hello php!';\necho \$a ?? 'a is unset';\necho \$b ?? 'b is unset';\n\n\$a = array();\n\$a == null;\n\$a === null;\nis_null(\$a);",
            'Null合体演算子とnullチェック',
            "a is unset\nHello php",
            'easy'
        );

        // Section 3: PHP Strings
        $section3 = $this->createSection($phpLanguage, 'PHP文字列', 3, 'PHPでの文字列操作', 'php-strings');

        $this->createExample($section3, $phpLanguage, '文字列', 1,
            "\$sgl_quotes = '\$String';\n\n\$dbl_quotes = \"This is a \$sgl_quotes.\";\n\n\$escaped   = \"a \\t tab character.\";\n\n\$unescaped = 'a slash and a t: \\t';",
            'シングルクォートとダブルクォート',
            null,
            'easy'
        );

        $this->createExample($section3, $phpLanguage, '複数行', 2,
            "\$str = \"foo\";\n\n// Uninterpolated multi-liners\n\$nowdoc = <<<'END'\nMulti line string\n\$str\nEND;\n\n// Will do string interpolation\n\$heredoc = <<<END\nMulti line\n\$str\nEND;",
            'HeredocとNowdoc構文',
            null,
            'medium'
        );

        $this->createExample($section3, $phpLanguage, '操作', 3,
            "\$s = \"Hello Phper\";\necho strlen(\$s);\n\necho substr(\$s, 0, 3);\necho substr(\$s, 1);\necho substr(\$s, -4, 3);\n\necho strtoupper(\$s);\necho strtolower(\$s);\n\necho strpos(\$s, \"l\");\nvar_dump(strpos(\$s, \"L\"));",
            '一般的な文字列関数',
            null,
            'easy'
        );

        // Section 4: PHP Arrays
        $section4 = $this->createSection($phpLanguage, 'PHP配列', 4, 'PHPでの配列操作', 'php-arrays');

        $this->createExample($section4, $phpLanguage, '定義', 1,
            "\$a1 = [\"hello\", \"world\", \"!\"];\n\$a2 = array(\"hello\", \"world\", \"!\");\n\$a3 = explode(\",\", \"apple,pear,peach\");",
            '配列を定義する様々な方法',
            null,
            'easy'
        );

        $this->createExample($section4, $phpLanguage, '混合キー', 2,
            "\$array = array(\n    \"foo\" => \"bar\",\n    \"bar\" => \"foo\",\n    100   => -100,\n    -100  => 100,\n);\nvar_dump(\$array);",
            '整数と文字列キーが混在する配列',
            null,
            'easy'
        );

        $this->createExample($section4, $phpLanguage, '多次元配列', 3,
            "\$multiArray = [ \n    [1, 2, 3],\n    [4, 5, 6],\n    [7, 8, 9],\n];\n\nprint_r(\$multiArray[0][0]);\nprint_r(\$multiArray[0][1]);\nprint_r(\$multiArray[0][2]);",
            '多次元配列',
            null,
            'easy'
        );

        $this->createExample($section4, $phpLanguage, '配列操作', 4,
            "\$arr = array(5 => 1, 12 => 2);\n\$arr[] = 56;      // Append\n\$arr[\"x\"] = 42;   // Add with key\nsort(\$arr);       // Sort\nunset(\$arr[5]);   // Remove\nunset(\$arr);      // Remove all",
            '配列操作関数',
            null,
            'easy'
        );

        $this->createExample($section4, $phpLanguage, 'Foreach反復', 5,
            "\$colors = array('red', 'blue', 'green');\n\nforeach (\$colors as \$color) {\n    echo \"Do you like \$color?\\n\";\n}",
            'Foreachループの反復処理',
            null,
            'easy'
        );

        $this->createExample($section4, $phpLanguage, 'キー・値の反復', 6,
            "\$arr = [\"foo\" => \"bar\", \"bar\" => \"foo\"];\n\nforeach ( \$arr as \$key => \$value )\n{\n    echo \"\$key => \$value\\n\";\n}",
            'キー・値のペアを反復処理',
            null,
            'easy'
        );

        // Section 5: PHP Functions
        $section5 = $this->createSection($phpLanguage, 'PHP関数', 5, '関数の定義と使用', 'php-functions');

        $this->createExample($section5, $phpLanguage, '戻り値', 1,
            "function square(\$x)\n{\n    return \$x * \$x;\n}\n\necho square(4);",
            '戻り値を持つ基本的な関数',
            '16',
            'easy'
        );

        $this->createExample($section5, $phpLanguage, '戻り値の型', 2,
            "// Basic return type declaration\nfunction sum(\$a, \$b): float {\n    return \$a + \$b;\n}\nfunction get_item(): string {\n    return \"item\";\n}\n\nclass C {}\n// Returning an object\nfunction getC(): C { \n    return new C; \n}",
            '戻り値の型宣言',
            null,
            'medium'
        );

        $this->createExample($section5, $phpLanguage, 'デフォルトパラメータ', 3,
            "function coffee(\$type = \"cappuccino\")\n{\n    return \"Making a cup of \$type.\\n\";\n}\necho coffee();\necho coffee(null);\necho coffee(\"espresso\");",
            'デフォルトパラメータを持つ関数',
            "Making a cup of cappuccino.\nMaking a cup of .\nMaking a cup of espresso.",
            'easy'
        );

        $this->createExample($section5, $phpLanguage, '無名関数', 4,
            "\$greet = function(\$name)\n{\n    printf(\"Hello %s\\r\\n\", \$name);\n};\n\n\$greet('World');\n\$greet('PHP');",
            '無名関数（クロージャ）',
            "Hello World\nHello PHP",
            'medium'
        );

        $this->createExample($section5, $phpLanguage, 'アロー関数', 5,
            "\$y = 1;\n \n\$fn1 = fn(\$x) => \$x + \$y;\n\n// equivalent to using \$y by value:\n\$fn2 = function (\$x) use (\$y) {\n    return \$x + \$y;\n};\necho \$fn1(5);\necho \$fn2(5);",
            'アロー関数（PHP 7.4+）',
            "6\n6",
            'medium'
        );

        // Section 6: PHP Classes
        $section6 = $this->createSection($phpLanguage, 'PHPクラス', 6, 'オブジェクト指向プログラミング', 'php-classes');

        $this->createExample($section6, $phpLanguage, 'コンストラクタ', 1,
            "class Student {\n    public function __construct(\$name) {\n        \$this->name = \$name;\n    }\n    public function print() {\n        echo \"Name: \" . \$this->name;\n    }\n}\n\$alex = new Student(\"Alex\");\n\$alex->print();",
            'クラスのコンストラクタとメソッド',
            'Name: Alex',
            'medium'
        );

        $this->createExample($section6, $phpLanguage, '継承', 2,
            "class ExtendClass extends SimpleClass\n{\n    // Redefine the parent method\n    function displayVar()\n    {\n        echo \"Extending class\\n\";\n        parent::displayVar();\n    }\n}\n\n\$extended = new ExtendClass();\n\$extended->displayVar();",
            'クラスの継承',
            null,
            'medium'
        );

        $this->createExample($section6, $phpLanguage, 'クラス変数', 3,
            "class MyClass\n{\n    const MY_CONST       = 'value';\n    static \$staticVar    = 'static';\n\n    // Visibility\n    public static \$var1  = 'pubs';\n    private static \$var2 = 'pris';\n    protected static \$var3 = 'pros';\n}\n\n// Access statically\necho MyClass::MY_CONST;\necho MyClass::\$staticVar;",
            'クラス定数と静的変数',
            'value\nstatic',
            'medium'
        );

        $this->createExample($section6, $phpLanguage, 'インターフェース', 4,
            "interface Foo \n{\n    public function doSomething();\n}\ninterface Bar\n{\n    public function doSomethingElse();\n}\nclass Cls implements Foo, Bar \n{\n    public function doSomething() {}\n    public function doSomethingElse() {}\n}",
            'インターフェースと複数の実装',
            null,
            'medium'
        );

        // Section 7: Miscellaneous
        $section7 = $this->createSection($phpLanguage, 'その他', 7, 'エラーハンドリングとその他の機能', 'miscellaneous');

        $this->createExample($section7, $phpLanguage, 'エラーハンドリング', 1,
            "try {\n    // Do something\n} catch (Exception \$e) {\n    // Handle exception\n} finally {\n    echo \"Always print!\";\n}",
            'Try-catch-finallyブロック',
            null,
            'medium'
        );

        $this->createExample($section7, $phpLanguage, '正規表現', 2,
            "\$str = \"Visit Kizamu.com\";\necho preg_match(\"/do/i\", \$str);",
            'preg_matchを使った正規表現',
            '1',
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($phpLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null, ?string $slug = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug ?? Str::slug($title),
            'description' => $description,
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function createExample(
        CheatCodeSection $section,
        CheatCodeLanguage $language,
        string $title,
        int $sortOrder,
        string $code,
        ?string $description = null,
        ?string $output = null,
        string $difficulty = 'easy'
    ): CodeExample {
        return CodeExample::create([
            'section_id' => $section->id,
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'code' => $code,
            'description' => $description,
            'output' => $output,
            'difficulty' => $difficulty,
            'tags' => $this->generateTags($title, $description),
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function generateTags(string $title, ?string $description): array
    {
        $tags = [];
        $titleLower = strtolower($title);

        // Add tags based on title
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'object')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'error') || str_contains($titleLower, 'exception')) {
            $tags[] = 'error-handling';
        }

        // Add basic tag
        $tags[] = 'php';
        $tags[] = 'basics';

        return array_unique($tags);
    }

    private function updateLanguageCounts(CheatCodeLanguage $language): void
    {
        $language->update([
            'sections_count' => $language->sections()->count(),
            'examples_count' => $language->codeExamples()->count(),
            'exercises_count' => $language->exercises()->count(),
        ]);

        // Update section counts
        foreach ($language->sections as $section) {
            $section->update([
                'examples_count' => $section->examples()->count(),
            ]);
        }
    }
}

