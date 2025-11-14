<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeKotlinExerciseSeeder extends Seeder
{
    /**
     * Seed Kotlin exercises
     */
    public function run(): void
    {
        $kotlinLanguage = CheatCodeLanguage::where('slug', 'kotlin')->first();

        if (!$kotlinLanguage) {
            $this->command->error('Kotlin language not found. Please run CheatCodeKotlinSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $kotlinLanguage,
            'Hello Worldを出力',
            'main関数',
            '「Hello, World!」という文字列を出力してください。',
            "fun main() {\n    // Hello, World!を出力\n}\n",
            "fun main() {\n    println(\"Hello, World!\")\n}\n",
            ['println()を使用して出力します'],
            'easy',
            10,
            ['kotlin', 'hello-world', 'println'],
            1,
            [
                ['', 'Hello, World!', '正しい出力', true, false, 1]
            ]
        );

        // Exercise 2: Variables - val
        $this->createExercise(
            $kotlinLanguage,
            '変数の宣言と出力',
            '変数 - val',
            'valで変数nameに「Taro」を代入し、その値を出力してください。',
            "fun main() {\n    // valでname変数を宣言して出力\n}\n",
            "fun main() {\n    val name = \"Taro\"\n    println(name)\n}\n",
            ['valは変更不可の変数', 'println()で出力'],
            'easy',
            10,
            ['kotlin', 'variable', 'val'],
            2,
            [
                ['', 'Taro', '変数の値を出力', true, false, 1]
            ]
        );

        // Exercise 3: Variables - var
        $this->createExercise(
            $kotlinLanguage,
            '変更可能な変数',
            '変数 - var',
            'varで変数countを10で初期化し、20に変更してから出力してください。',
            "fun main() {\n    // varでcount変数を宣言\n    // 20に変更\n    // 出力\n}\n",
            "fun main() {\n    var count = 10\n    count = 20\n    println(count)\n}\n",
            ['varは変更可能な変数', '再代入ができる'],
            'easy',
            15,
            ['kotlin', 'variable', 'var'],
            3,
            [
                ['', '20', '変更後の値', true, false, 1]
            ]
        );

        // Exercise 4: String Template
        $this->createExercise(
            $kotlinLanguage,
            '文字列テンプレート',
            '文字列 - テンプレート',
            'name変数が「Hanako」の時、「Hello, Hanako!」を文字列テンプレートで出力してください。',
            "fun main() {\n    val name = \"Hanako\"\n    // 文字列テンプレートで出力\n}\n",
            "fun main() {\n    val name = \"Hanako\"\n    println(\"Hello, \$name!\")\n}\n",
            ['$変数名で文字列に埋め込む'],
            'easy',
            15,
            ['kotlin', 'string', 'template'],
            4,
            [
                ['', 'Hello, Hanako!', 'テンプレート文字列', true, false, 1]
            ]
        );

        // Exercise 5: If Expression
        $this->createExercise(
            $kotlinLanguage,
            'if式で比較',
            '条件分岐 - if',
            '変数numが10より大きい場合は「Large」、そうでない場合は「Small」を出力してください。num = 15でテストします。',
            "fun main() {\n    val num = 15\n    // if式で比較して出力\n}\n",
            "fun main() {\n    val num = 15\n    if (num > 10) {\n        println(\"Large\")\n    } else {\n        println(\"Small\")\n    }\n}\n",
            ['if (条件) { 処理 } else { 処理 }'],
            'easy',
            15,
            ['kotlin', 'conditional', 'if'],
            5,
            [
                ['', 'Large', '15は10より大きい', true, false, 1]
            ]
        );

        // Exercise 6: When Expression
        $this->createExercise(
            $kotlinLanguage,
            'when式で分岐',
            '条件分岐 - when',
            '変数dayが1の時「Monday」、2の時「Tuesday」、それ以外は「Other」を出力してください。day = 1でテストします。',
            "fun main() {\n    val day = 1\n    // when式で分岐\n}\n",
            "fun main() {\n    val day = 1\n    when (day) {\n        1 -> println(\"Monday\")\n        2 -> println(\"Tuesday\")\n        else -> println(\"Other\")\n    }\n}\n",
            ['when (変数) { 値 -> 処理 }', 'else節でデフォルト処理'],
            'medium',
            20,
            ['kotlin', 'conditional', 'when'],
            6,
            [
                ['', 'Monday', 'day=1の時', true, false, 1]
            ]
        );

        // Exercise 7: For Loop - Range
        $this->createExercise(
            $kotlinLanguage,
            '範囲で繰り返し',
            'ループ - for',
            'for文を使って1から5までの数字をそれぞれ改行して出力してください。',
            "fun main() {\n    // 1から5まで出力\n}\n",
            "fun main() {\n    for (i in 1..5) {\n        println(i)\n    }\n}\n",
            ['for (変数 in 範囲)', '1..5は1から5まで'],
            'easy',
            15,
            ['kotlin', 'loop', 'for', 'range'],
            7,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで', true, false, 1]
            ]
        );

        // Exercise 8: List Creation
        $this->createExercise(
            $kotlinLanguage,
            'リストの作成',
            'コレクション - List',
            'listOf(10, 20, 30, 40, 50)を作成し、その要素数を出力してください。',
            "fun main() {\n    // リストを作成してサイズを出力\n}\n",
            "fun main() {\n    val list = listOf(10, 20, 30, 40, 50)\n    println(list.size)\n}\n",
            ['listOf()で不変リスト作成', 'sizeプロパティで要素数取得'],
            'easy',
            15,
            ['kotlin', 'collection', 'list'],
            8,
            [
                ['', '5', 'リストのサイズは5', true, false, 1]
            ]
        );

        // Exercise 9: List Access
        $this->createExercise(
            $kotlinLanguage,
            'リストの要素にアクセス',
            'コレクション - アクセス',
            'リストlistOf(\"apple\", \"banana\", \"cherry\")の2番目の要素（banana）を出力してください。',
            "fun main() {\n    val fruits = listOf(\"apple\", \"banana\", \"cherry\")\n    // 2番目の要素を出力\n}\n",
            "fun main() {\n    val fruits = listOf(\"apple\", \"banana\", \"cherry\")\n    println(fruits[1])\n}\n",
            ['リスト[インデックス]でアクセス', 'インデックスは0から始まる'],
            'easy',
            15,
            ['kotlin', 'collection', 'list', 'access'],
            9,
            [
                ['', 'banana', '2番目の要素', true, false, 1]
            ]
        );

        // Exercise 10: MutableList Add
        $this->createExercise(
            $kotlinLanguage,
            'ミュータブルリストに追加',
            'コレクション - MutableList',
            'mutableListOf(1, 2, 3)に要素4を追加して、リスト全体をカンマ区切りで出力してください。',
            "fun main() {\n    val numbers = mutableListOf(1, 2, 3)\n    // 4を追加して出力\n}\n",
            "fun main() {\n    val numbers = mutableListOf(1, 2, 3)\n    numbers.add(4)\n    println(numbers.joinToString(\",\"))\n}\n",
            ['add()で要素追加', 'joinToString()で文字列に変換'],
            'easy',
            20,
            ['kotlin', 'collection', 'mutablelist'],
            10,
            [
                ['', '1,2,3,4', '4を追加', true, false, 1]
            ]
        );

        // Exercise 11: Map Function
        $this->createExercise(
            $kotlinLanguage,
            'リストの各要素を変換',
            'コレクション - map',
            'リストlistOf(1, 2, 3, 4, 5)の各要素を2倍にした新しいリストを作成し、カンマ区切りで出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    // mapで各要素を2倍\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    val doubled = numbers.map { it * 2 }\n    println(doubled.joinToString(\",\"))\n}\n",
            ['map { 変換処理 }', 'itは現在の要素'],
            'medium',
            20,
            ['kotlin', 'collection', 'map'],
            11,
            [
                ['', '2,4,6,8,10', '各要素を2倍', true, false, 1]
            ]
        );

        // Exercise 12: Filter Function
        $this->createExercise(
            $kotlinLanguage,
            '偶数のみを抽出',
            'コレクション - filter',
            'リストlistOf(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)から偶数のみを抽出して、カンマ区切りで出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)\n    // filterで偶数のみ抽出\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)\n    val evens = numbers.filter { it % 2 == 0 }\n    println(evens.joinToString(\",\"))\n}\n",
            ['filter { 条件 }', 'it % 2 == 0で偶数判定'],
            'medium',
            20,
            ['kotlin', 'collection', 'filter'],
            12,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1]
            ]
        );

        // Exercise 13: Sum Function
        $this->createExercise(
            $kotlinLanguage,
            'リストの合計',
            'コレクション - sum',
            'リストlistOf(1, 2, 3, 4, 5)の合計を計算して出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    // 合計を計算\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    println(numbers.sum())\n}\n",
            ['sum()で合計を計算'],
            'easy',
            15,
            ['kotlin', 'collection', 'sum'],
            13,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1]
            ]
        );

        // Exercise 14: Function Definition
        $this->createExercise(
            $kotlinLanguage,
            '関数の定義',
            '関数 - 基本',
            '引数を2倍にして返す関数doubleを定義し、double(7)の結果を出力してください。',
            "// 引数を2倍にする関数doubleを定義\n\nfun main() {\n    // double(7)を呼び出して出力\n}\n",
            "fun double(n: Int): Int {\n    return n * 2\n}\n\nfun main() {\n    println(double(7))\n}\n",
            ['fun 関数名(引数: 型): 戻り値型'],
            'easy',
            20,
            ['kotlin', 'function', 'basic'],
            14,
            [
                ['', '14', '7の2倍', true, false, 1]
            ]
        );

        // Exercise 15: Single Expression Function
        $this->createExercise(
            $kotlinLanguage,
            '単一式関数',
            '関数 - 単一式',
            '単一式関数でadd(a, b)を定義し、add(3, 5)の結果を出力してください。',
            "// 単一式関数でaddを定義\n\nfun main() {\n    // add(3, 5)を出力\n}\n",
            "fun add(a: Int, b: Int) = a + b\n\nfun main() {\n    println(add(3, 5))\n}\n",
            ['fun 関数名(引数) = 式', '戻り値型は推論される'],
            'easy',
            20,
            ['kotlin', 'function', 'expression'],
            15,
            [
                ['', '8', '3+5=8', true, false, 1]
            ]
        );

        // Exercise 16: Default Parameter
        $this->createExercise(
            $kotlinLanguage,
            'デフォルト引数',
            '関数 - デフォルト引数',
            '関数greet(name: String = "Guest")を定義し、引数なしで呼び出して「Hello, Guest」を出力してください。',
            "// デフォルト引数を持つ関数を定義\n\nfun main() {\n    // 引数なしで呼び出す\n}\n",
            "fun greet(name: String = \"Guest\") {\n    println(\"Hello, \$name\")\n}\n\nfun main() {\n    greet()\n}\n",
            ['引数: 型 = デフォルト値'],
            'medium',
            20,
            ['kotlin', 'function', 'default-parameter'],
            16,
            [
                ['', 'Hello, Guest', 'デフォルト引数', true, false, 1]
            ]
        );

        // Exercise 17: Nullable Type
        $this->createExercise(
            $kotlinLanguage,
            'Null許容型',
            'Null安全 - Nullable',
            'Null許容型String?の変数nameにnullを代入し、エルビス演算子で「Unknown」をデフォルト値として出力してください。',
            "fun main() {\n    val name: String? = null\n    // エルビス演算子でデフォルト値を設定\n}\n",
            "fun main() {\n    val name: String? = null\n    println(name ?: \"Unknown\")\n}\n",
            ['型?でNull許容型', '?: でデフォルト値'],
            'medium',
            25,
            ['kotlin', 'null-safety', 'elvis'],
            17,
            [
                ['', 'Unknown', 'nullの場合はUnknown', true, false, 1]
            ]
        );

        // Exercise 18: Safe Call
        $this->createExercise(
            $kotlinLanguage,
            'セーフコール',
            'Null安全 - セーフコール',
            'Null許容型String?の変数textに「Hello」を代入し、セーフコールで長さを出力してください。',
            "fun main() {\n    val text: String? = \"Hello\"\n    // セーフコールで長さを出力\n}\n",
            "fun main() {\n    val text: String? = \"Hello\"\n    println(text?.length)\n}\n",
            ['?.でセーフコール', 'nullの場合はnullを返す'],
            'medium',
            20,
            ['kotlin', 'null-safety', 'safe-call'],
            18,
            [
                ['', '5', 'Helloの長さは5', true, false, 1]
            ]
        );

        // Exercise 19: Data Class
        $this->createExercise(
            $kotlinLanguage,
            'データクラス',
            'クラス - data class',
            'data class Person(val name: String, val age: Int)を定義し、Person("Taro", 25)のnameを出力してください。',
            "// データクラスPersonを定義\n\nfun main() {\n    // インスタンスを作成してnameを出力\n}\n",
            "data class Person(val name: String, val age: Int)\n\nfun main() {\n    val person = Person(\"Taro\", 25)\n    println(person.name)\n}\n",
            ['data classで自動的にequals, hashCode, toStringが生成される'],
            'medium',
            25,
            ['kotlin', 'class', 'data-class'],
            19,
            [
                ['', 'Taro', 'nameプロパティ', true, false, 1]
            ]
        );

        // Exercise 20: Pair
        $this->createExercise(
            $kotlinLanguage,
            'Pairの使用',
            'コレクション - Pair',
            'Pair("Tokyo", 13)を作成し、firstとsecondをカンマ区切りで出力してください。',
            "fun main() {\n    // Pairを作成\n    // first,secondを出力\n}\n",
            "fun main() {\n    val pair = Pair(\"Tokyo\", 13)\n    println(\"\${pair.first},\${pair.second}\")\n}\n",
            ['Pair(第1要素, 第2要素)', 'first, secondでアクセス'],
            'easy',
            20,
            ['kotlin', 'pair', 'tuple'],
            20,
            [
                ['', 'Tokyo,13', 'PairのfirstとSecond', true, false, 1]
            ]
        );

        // Exercise 21: Extension Function
        $this->createExercise(
            $kotlinLanguage,
            '拡張関数',
            '関数 - 拡張関数',
            'String型に拡張関数fun String.addExclamation()を定義して、「Hello」に適用し「Hello!」を出力してください。',
            "// String型の拡張関数を定義\n\nfun main() {\n    // 拡張関数を適用\n}\n",
            "fun String.addExclamation() = this + \"!\"\n\nfun main() {\n    println(\"Hello\".addExclamation())\n}\n",
            ['fun 型.関数名()で拡張関数', 'thisは対象オブジェクト'],
            'medium',
            25,
            ['kotlin', 'function', 'extension'],
            21,
            [
                ['', 'Hello!', '拡張関数適用', true, false, 1]
            ]
        );

        // Exercise 22: Lambda Expression
        $this->createExercise(
            $kotlinLanguage,
            'ラムダ式',
            '関数 - ラムダ',
            'ラムダ式でval multiply = { a: Int, b: Int -> a * b }を定義し、multiply(3, 4)を出力してください。',
            "fun main() {\n    // ラムダ式を定義\n    // multiply(3, 4)を出力\n}\n",
            "fun main() {\n    val multiply = { a: Int, b: Int -> a * b }\n    println(multiply(3, 4))\n}\n",
            ['{ 引数 -> 処理 }', 'ラムダ式は変数に代入可能'],
            'medium',
            25,
            ['kotlin', 'function', 'lambda'],
            22,
            [
                ['', '12', '3*4=12', true, false, 1]
            ]
        );

        // Exercise 23: Map Collection
        $this->createExercise(
            $kotlinLanguage,
            'マップの作成',
            'コレクション - Map',
            'mapOf("a" to 1, "b" to 2, "c" to 3)を作成し、キー"b"の値を出力してください。',
            "fun main() {\n    // マップを作成\n    // キー\"b\"の値を出力\n}\n",
            "fun main() {\n    val map = mapOf(\"a\" to 1, \"b\" to 2, \"c\" to 3)\n    println(map[\"b\"])\n}\n",
            ['mapOf(キー to 値)', 'マップ[キー]でアクセス'],
            'easy',
            20,
            ['kotlin', 'collection', 'map'],
            23,
            [
                ['', '2', 'キーbの値', true, false, 1]
            ]
        );

        // Exercise 24: Set Collection
        $this->createExercise(
            $kotlinLanguage,
            'セットで重複削除',
            'コレクション - Set',
            'listOf(1, 2, 2, 3, 3, 3, 4)をSetに変換して重複を削除し、カンマ区切りで出力してください。',
            "fun main() {\n    val list = listOf(1, 2, 2, 3, 3, 3, 4)\n    // Setに変換\n}\n",
            "fun main() {\n    val list = listOf(1, 2, 2, 3, 3, 3, 4)\n    val set = list.toSet()\n    println(set.joinToString(\",\"))\n}\n",
            ['toSet()でSetに変換', 'Setは重複を許さない'],
            'medium',
            20,
            ['kotlin', 'collection', 'set'],
            24,
            [
                ['', '1,2,3,4', '重複削除後', true, false, 1]
            ]
        );

        // Exercise 25: String Split
        $this->createExercise(
            $kotlinLanguage,
            '文字列を分割',
            '文字列 - split',
            '文字列「apple,banana,cherry」をカンマで分割し、リストのサイズを出力してください。',
            "fun main() {\n    val str = \"apple,banana,cherry\"\n    // カンマで分割してサイズを出力\n}\n",
            "fun main() {\n    val str = \"apple,banana,cherry\"\n    val list = str.split(\",\")\n    println(list.size)\n}\n",
            ['split(区切り文字)でリストに変換'],
            'easy',
            15,
            ['kotlin', 'string', 'split'],
            25,
            [
                ['', '3', '3つの要素', true, false, 1]
            ]
        );

        // Exercise 26: String Replace
        $this->createExercise(
            $kotlinLanguage,
            '文字列を置換',
            '文字列 - replace',
            '文字列「Hello World」の「World」を「Kotlin」に置換して出力してください。',
            "fun main() {\n    val str = \"Hello World\"\n    // Worldを置換\n}\n",
            "fun main() {\n    val str = \"Hello World\"\n    println(str.replace(\"World\", \"Kotlin\"))\n}\n",
            ['replace(旧文字列, 新文字列)'],
            'easy',
            15,
            ['kotlin', 'string', 'replace'],
            26,
            [
                ['', 'Hello Kotlin', '置換後の文字列', true, false, 1]
            ]
        );

        // Exercise 27: String Contains
        $this->createExercise(
            $kotlinLanguage,
            '文字列が含まれるか確認',
            '文字列 - contains',
            '文字列「Kotlin is fun」に「fun」が含まれる場合は「Yes」、含まれない場合は「No」を出力してください。',
            "fun main() {\n    val str = \"Kotlin is fun\"\n    // funが含まれるか確認\n}\n",
            "fun main() {\n    val str = \"Kotlin is fun\"\n    if (str.contains(\"fun\")) {\n        println(\"Yes\")\n    } else {\n        println(\"No\")\n    }\n}\n",
            ['contains(文字列)で含まれるか確認'],
            'easy',
            15,
            ['kotlin', 'string', 'contains'],
            27,
            [
                ['', 'Yes', 'funが含まれる', true, false, 1]
            ]
        );

        // Exercise 28: Range Check
        $this->createExercise(
            $kotlinLanguage,
            '範囲のチェック',
            '演算子 - in',
            '変数age = 25が18から30の範囲内かどうかをチェックし、範囲内なら「In Range」、範囲外なら「Out of Range」を出力してください。',
            "fun main() {\n    val age = 25\n    // 18..30の範囲内かチェック\n}\n",
            "fun main() {\n    val age = 25\n    if (age in 18..30) {\n        println(\"In Range\")\n    } else {\n        println(\"Out of Range\")\n    }\n}\n",
            ['in 範囲で範囲チェック', '18..30は18から30まで'],
            'medium',
            20,
            ['kotlin', 'range', 'in'],
            28,
            [
                ['', 'In Range', '25は範囲内', true, false, 1]
            ]
        );

        // Exercise 29: Any Function
        $this->createExercise(
            $kotlinLanguage,
            '条件を満たす要素があるか',
            'コレクション - any',
            'リストlistOf(1, 3, 5, 7, 9)に偶数が含まれる場合は「Yes」、含まれない場合は「No」を出力してください。',
            "fun main() {\n    val numbers = listOf(1, 3, 5, 7, 9)\n    // 偶数が含まれるか確認\n}\n",
            "fun main() {\n    val numbers = listOf(1, 3, 5, 7, 9)\n    val hasEven = numbers.any { it % 2 == 0 }\n    println(if (hasEven) \"Yes\" else \"No\")\n}\n",
            ['any { 条件 }で条件を満たす要素があるか'],
            'medium',
            25,
            ['kotlin', 'collection', 'any'],
            29,
            [
                ['', 'No', '偶数が含まれない', true, false, 1]
            ]
        );

        // Exercise 30: All Function
        $this->createExercise(
            $kotlinLanguage,
            '全要素が条件を満たすか',
            'コレクション - all',
            'リストlistOf(2, 4, 6, 8, 10)の全要素が偶数の場合は「Yes」、そうでない場合は「No」を出力してください。',
            "fun main() {\n    val numbers = listOf(2, 4, 6, 8, 10)\n    // 全要素が偶数か確認\n}\n",
            "fun main() {\n    val numbers = listOf(2, 4, 6, 8, 10)\n    val allEven = numbers.all { it % 2 == 0 }\n    println(if (allEven) \"Yes\" else \"No\")\n}\n",
            ['all { 条件 }で全要素が条件を満たすか'],
            'medium',
            25,
            ['kotlin', 'collection', 'all'],
            30,
            [
                ['', 'Yes', '全て偶数', true, false, 1]
            ]
        );

        // Exercise 31: Let Scope Function
        $this->createExercise(
            $kotlinLanguage,
            'let関数',
            'スコープ関数 - let',
            'Null許容型の変数name: String? = "Taro"にlet関数を使って長さを出力してください。',
            "fun main() {\n    val name: String? = \"Taro\"\n    // let関数で長さを出力\n}\n",
            "fun main() {\n    val name: String? = \"Taro\"\n    name?.let {\n        println(it.length)\n    }\n}\n",
            ['?.let { 処理 }', 'nullでない場合のみ実行'],
            'medium',
            25,
            ['kotlin', 'scope-function', 'let'],
            31,
            [
                ['', '4', 'Taroの長さは4', true, false, 1]
            ]
        );

        // Exercise 32: Apply Scope Function
        $this->createExercise(
            $kotlinLanguage,
            'apply関数',
            'スコープ関数 - apply',
            'mutableListOf<Int>()にapplyを使って1, 2, 3を追加し、リストをカンマ区切りで出力してください。',
            "fun main() {\n    val list = mutableListOf<Int>().apply {\n        // ここに要素を追加\n    }\n    // リストを出力\n}\n",
            "fun main() {\n    val list = mutableListOf<Int>().apply {\n        add(1)\n        add(2)\n        add(3)\n    }\n    println(list.joinToString(\",\"))\n}\n",
            ['apply { 処理 }', 'オブジェクト自身を返す'],
            'medium',
            25,
            ['kotlin', 'scope-function', 'apply'],
            32,
            [
                ['', '1,2,3', 'applyで要素追加', true, false, 1]
            ]
        );

        // Exercise 33: Destructuring Declaration
        $this->createExercise(
            $kotlinLanguage,
            '分解宣言',
            '分解宣言',
            'Pair(10, 20)を分解宣言で(a, b)に代入し、aを出力してください。',
            "fun main() {\n    // 分解宣言で代入\n    // aを出力\n}\n",
            "fun main() {\n    val (a, b) = Pair(10, 20)\n    println(a)\n}\n",
            ['val (変数1, 変数2) = Pair', '各要素に分解して代入'],
            'medium',
            20,
            ['kotlin', 'destructuring', 'pair'],
            33,
            [
                ['', '10', '最初の要素', true, false, 1]
            ]
        );

        // Exercise 34: Zip Function
        $this->createExercise(
            $kotlinLanguage,
            'リストを結合',
            'コレクション - zip',
            'リストlistOf("a", "b", "c")とlistOf(1, 2, 3)をzipで結合し、最初のPairのfirstを出力してください。',
            "fun main() {\n    val list1 = listOf(\"a\", \"b\", \"c\")\n    val list2 = listOf(1, 2, 3)\n    // zipで結合\n    // 最初のPairのfirstを出力\n}\n",
            "fun main() {\n    val list1 = listOf(\"a\", \"b\", \"c\")\n    val list2 = listOf(1, 2, 3)\n    val zipped = list1.zip(list2)\n    println(zipped[0].first)\n}\n",
            ['zip()でPairのリストに変換'],
            'hard',
            30,
            ['kotlin', 'collection', 'zip'],
            34,
            [
                ['', 'a', '最初のPairのfirst', true, false, 1]
            ]
        );

        // Exercise 35: Take Function
        $this->createExercise(
            $kotlinLanguage,
            '最初のN個を取得',
            'コレクション - take',
            'リストlistOf(1, 2, 3, 4, 5)から最初の3個を取得し、カンマ区切りで出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    // 最初の3個を取得\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    val first3 = numbers.take(3)\n    println(first3.joinToString(\",\"))\n}\n",
            ['take(N)で最初のN個を取得'],
            'easy',
            20,
            ['kotlin', 'collection', 'take'],
            35,
            [
                ['', '1,2,3', '最初の3個', true, false, 1]
            ]
        );

        // Exercise 36: Drop Function
        $this->createExercise(
            $kotlinLanguage,
            '最初のN個をスキップ',
            'コレクション - drop',
            'リストlistOf(1, 2, 3, 4, 5)の最初の2個をスキップし、残りをカンマ区切りで出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    // 最初の2個をスキップ\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5)\n    val rest = numbers.drop(2)\n    println(rest.joinToString(\",\"))\n}\n",
            ['drop(N)で最初のN個をスキップ'],
            'easy',
            20,
            ['kotlin', 'collection', 'drop'],
            36,
            [
                ['', '3,4,5', '最初の2個をスキップ', true, false, 1]
            ]
        );

        // Exercise 37: Flatten Function
        $this->createExercise(
            $kotlinLanguage,
            'ネストしたリストを平坦化',
            'コレクション - flatten',
            'listOf(listOf(1, 2), listOf(3, 4), listOf(5))を平坦化し、カンマ区切りで出力してください。',
            "fun main() {\n    val nested = listOf(listOf(1, 2), listOf(3, 4), listOf(5))\n    // 平坦化\n}\n",
            "fun main() {\n    val nested = listOf(listOf(1, 2), listOf(3, 4), listOf(5))\n    val flat = nested.flatten()\n    println(flat.joinToString(\",\"))\n}\n",
            ['flatten()でネストしたリストを平坦化'],
            'medium',
            25,
            ['kotlin', 'collection', 'flatten'],
            37,
            [
                ['', '1,2,3,4,5', '平坦化後', true, false, 1]
            ]
        );

        // Exercise 38: GroupBy Function
        $this->createExercise(
            $kotlinLanguage,
            'グループ化',
            'コレクション - groupBy',
            'リストlistOf(1, 2, 3, 4, 5, 6)を偶数と奇数でグループ化し、偶数グループのサイズを出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6)\n    // 偶数と奇数でグループ化\n    // 偶数グループのサイズを出力\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6)\n    val grouped = numbers.groupBy { it % 2 == 0 }\n    println(grouped[true]?.size)\n}\n",
            ['groupBy { 条件 }でグループ化', 'Map<条件結果, リスト>を返す'],
            'hard',
            30,
            ['kotlin', 'collection', 'groupBy'],
            38,
            [
                ['', '3', '偶数は3個', true, false, 1]
            ]
        );

        // Exercise 39: Method Chaining
        $this->createExercise(
            $kotlinLanguage,
            'メソッドチェーン',
            'コレクション - チェーン',
            'リストlistOf(1, 2, 3, 4, 5, 6)から偶数のみを抽出し、それぞれを2倍にして、カンマ区切りで出力してください。',
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6)\n    // filterとmapをチェーン\n}\n",
            "fun main() {\n    val numbers = listOf(1, 2, 3, 4, 5, 6)\n    val result = numbers\n        .filter { it % 2 == 0 }\n        .map { it * 2 }\n    println(result.joinToString(\",\"))\n}\n",
            ['メソッドをドットで繋ぐ'],
            'hard',
            30,
            ['kotlin', 'collection', 'chaining'],
            39,
            [
                ['', '4,8,12', '偶数を2倍', true, false, 1]
            ]
        );

        // Exercise 40: Higher-Order Function
        $this->createExercise(
            $kotlinLanguage,
            '高階関数',
            '関数 - 高階関数',
            '関数を引数に取る関数execute(operation: () -> Unit)を定義し、ラムダ式で「Executed」を出力してください。',
            "// 高階関数を定義\n\nfun main() {\n    // ラムダ式で呼び出す\n}\n",
            "fun execute(operation: () -> Unit) {\n    operation()\n}\n\nfun main() {\n    execute { println(\"Executed\") }\n}\n",
            ['関数を引数として受け取る', '() -> Unitは引数なし戻り値なしの関数型'],
            'hard',
            30,
            ['kotlin', 'function', 'higher-order'],
            40,
            [
                ['', 'Executed', '高階関数実行', true, false, 1]
            ]
        );

        $this->command->info('Kotlin exercises seeded successfully!');
    }

    private function createExercise(
        CheatCodeLanguage $language,
        string $title,
        string $category,
        string $description,
        string $starterCode,
        string $solution,
        array $hints,
        string $difficulty,
        int $points,
        array $tags,
        int $sortOrder,
        array $testCases
    ): void {
        $slug = Str::slug($title) ?: $language->slug . '-exercise-' . $sortOrder;

        $exercise = Exercise::create([
            'cheat_code_language_id' => $language->id,
            'title' => $title,
            'slug' => $slug,
            'category' => $category,
            'description' => $description,
            'starter_code' => $starterCode,
            'solution' => $solution,
            'hints' => $hints,
            'difficulty' => $difficulty,
            'points' => $points,
            'tags' => $tags,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

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
    }
}
