<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeJavaExerciseSeeder extends Seeder
{
    /**
     * Seed Java exercise data
     * Java言語の練習問題
     */
    public function run(): void
    {
        // Get Java Language
        $javaLanguage = CheatCodeLanguage::where('name', 'java')->first();

        if (!$javaLanguage) {
            $this->command->error('Java language not found. Please run CheatCodeJavaSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $javaLanguage,
            'Hello World出力',
            'Javaの基本',
            '「Hello, World!」という文字列を出力するJavaプログラムを書いてください。',
            "public class Main {\n    public static void main(String[] args) {\n        // ここにコードを書いてください\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, World!\");\n    }\n}",
            ['System.out.println()で出力します', 'mainメソッドが実行開始点です'],
            'easy',
            10,
            ['java', 'basics', 'println'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数と演算
        $this->createExercise(
            $javaLanguage,
            '2つの数値の合計',
            '変数と算術演算',
            '2つの整数（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // 2つの数値を変数に代入\n        \n        // 合計を計算して出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int a = 10;\n        int b = 20;\n        System.out.println(a + b);\n    }\n}",
            ['int型で整数を宣言', '変数名 = 値; で代入します'],
            'easy',
            10,
            ['java', 'variable', 'arithmetic'],
            2,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 3: 文字列の連結
        $this->createExercise(
            $javaLanguage,
            '文字列を連結して挨拶',
            '文字列連結',
            '変数name = "Alice"を定義し、「Hello, Alice!」と出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String name = \"Alice\";\n        // 文字列を連結して出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String name = \"Alice\";\n        System.out.println(\"Hello, \" + name + \"!\");\n    }\n}",
            ['+演算子で文字列を連結', 'String型は文字列を扱います'],
            'easy',
            15,
            ['java', 'string', 'concatenation'],
            3,
            [
                ['', 'Hello, Alice!', '文字列連結', true, false, 1],
            ]
        );

        // Exercise 4: if-else文
        $this->createExercise(
            $javaLanguage,
            '数値が正か負かゼロか判定',
            '条件分岐',
            '変数num = -5があります。この数値が正の数なら「Positive」、負の数なら「Negative」、0なら「Zero」と出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int num = -5;\n        // 判定して出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int num = -5;\n        if (num > 0) {\n            System.out.println(\"Positive\");\n        } else if (num < 0) {\n            System.out.println(\"Negative\");\n        } else {\n            System.out.println(\"Zero\");\n        }\n    }\n}",
            ['if, else if, elseで条件分岐', '条件は()で囲みます'],
            'easy',
            15,
            ['java', 'conditional', 'if-else'],
            4,
            [
                ['', 'Negative', '負の数', true, false, 1],
            ]
        );

        // Exercise 5: forループ
        $this->createExercise(
            $javaLanguage,
            '1から5まで出力',
            'ループ - for',
            'forループを使って1から5までの数字を改行区切りで出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // forループで1から5まで出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        for (int i = 1; i <= 5; i++) {\n            System.out.println(i);\n        }\n    }\n}",
            ['for (初期化; 条件; 更新) の形式', 'i++は i = i + 1と同じ'],
            'easy',
            15,
            ['java', 'loop', 'for'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで', true, false, 1],
            ]
        );

        // Exercise 6: whileループ
        $this->createExercise(
            $javaLanguage,
            'whileループで合計計算',
            'ループ - while',
            '変数sum = 0とi = 1を定義し、whileループでiが5以下の間、sumにiを加算し続けて、最終的なsumの値を出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int sum = 0;\n        int i = 1;\n        // whileループで合計を計算\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int sum = 0;\n        int i = 1;\n        while (i <= 5) {\n            sum += i;\n            i++;\n        }\n        System.out.println(sum);\n    }\n}",
            ['while (条件) { ... }', 'sum += iは sum = sum + iと同じ'],
            'easy',
            20,
            ['java', 'loop', 'while'],
            6,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1],
            ]
        );

        // Exercise 7: 配列の宣言と出力
        $this->createExercise(
            $javaLanguage,
            '配列の要素を出力',
            '配列',
            '整数配列 {10, 20, 30, 40, 50} を宣言し、各要素をカンマ区切りで1行に出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {10, 20, 30, 40, 50};\n        // 配列の要素を出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {10, 20, 30, 40, 50};\n        for (int i = 0; i < numbers.length; i++) {\n            if (i > 0) System.out.print(\",\");\n            System.out.print(numbers[i]);\n        }\n    }\n}",
            ['配列.lengthで配列の長さを取得', 'System.out.print()は改行なしで出力'],
            'medium',
            20,
            ['java', 'array', 'for'],
            7,
            [
                ['', '10,20,30,40,50', '配列をカンマ区切り', true, false, 1],
            ]
        );

        // Exercise 8: メソッドの定義
        $this->createExercise(
            $javaLanguage,
            'メソッドで2つの数を足す',
            'メソッド',
            'add(int a, int b)というメソッドを作成し、2つの整数の合計を返すようにしてください。mainメソッドから5と10で呼び出して結果を出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // addメソッドを呼び出して結果を出力\n    }\n    \n    // addメソッドを定義\n}",
            "public class Main {\n    public static void main(String[] args) {\n        System.out.println(add(5, 10));\n    }\n    \n    public static int add(int a, int b) {\n        return a + b;\n    }\n}",
            ['public static 戻り値型 メソッド名(引数) { ... }', 'returnで値を返します'],
            'medium',
            25,
            ['java', 'method', 'function'],
            8,
            [
                ['', '15', 'メソッド呼び出し', true, false, 1],
            ]
        );

        // Exercise 9: 最大値を求める
        $this->createExercise(
            $javaLanguage,
            '配列の最大値を求める',
            '配列とループ',
            '整数配列 {15, 42, 8, 27, 33} の中から最大値を見つけて出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {15, 42, 8, 27, 33};\n        // 最大値を求める\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {15, 42, 8, 27, 33};\n        int max = numbers[0];\n        for (int i = 1; i < numbers.length; i++) {\n            if (numbers[i] > max) {\n                max = numbers[i];\n            }\n        }\n        System.out.println(max);\n    }\n}",
            ['最初の要素を初期値とする', 'ループで各要素と比較'],
            'medium',
            25,
            ['java', 'array', 'algorithm'],
            9,
            [
                ['', '42', '最大値', true, false, 1],
            ]
        );

        // Exercise 10: switch文
        $this->createExercise(
            $javaLanguage,
            '曜日の判定',
            'Switch文',
            '変数day = 1があります。switch文を使って対応する曜日名を出力してください。1=Monday, 2=Tuesday, ..., 7=Sunday。該当しない場合は"Invalid day"と出力。',
            "public class Main {\n    public static void main(String[] args) {\n        int day = 1;\n        // switch文で曜日を判定\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int day = 1;\n        switch (day) {\n            case 1:\n                System.out.println(\"Monday\");\n                break;\n            case 2:\n                System.out.println(\"Tuesday\");\n                break;\n            case 3:\n                System.out.println(\"Wednesday\");\n                break;\n            case 4:\n                System.out.println(\"Thursday\");\n                break;\n            case 5:\n                System.out.println(\"Friday\");\n                break;\n            case 6:\n                System.out.println(\"Saturday\");\n                break;\n            case 7:\n                System.out.println(\"Sunday\");\n                break;\n            default:\n                System.out.println(\"Invalid day\");\n                break;\n        }\n    }\n}",
            ['switch (変数) { case 値: ... break; }の形式', 'breakを忘れずに'],
            'medium',
            30,
            ['java', 'switch', 'conditional'],
            10,
            [
                ['', 'Monday', '月曜日', true, false, 1],
            ]
        );

        // Exercise 11: 文字列の長さ
        $this->createExercise(
            $javaLanguage,
            '文字列の長さを取得',
            '文字列メソッド',
            '文字列"Hello Java"の長さを取得して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        // 文字列の長さを出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        System.out.println(text.length());\n    }\n}",
            ['String.length()で文字列の長さを取得', 'スペースも1文字としてカウント'],
            'easy',
            10,
            ['java', 'string', 'length'],
            11,
            [
                ['', '10', '文字列の長さ', true, false, 1],
            ]
        );

        // Exercise 12: 文字列の大文字変換
        $this->createExercise(
            $javaLanguage,
            '文字列を大文字に変換',
            '文字列メソッド',
            '文字列"hello world"を大文字に変換して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"hello world\";\n        // 大文字に変換\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"hello world\";\n        System.out.println(text.toUpperCase());\n    }\n}",
            ['toUpperCase()で大文字に変換', 'toLowerCase()は小文字に変換'],
            'easy',
            10,
            ['java', 'string', 'uppercase'],
            12,
            [
                ['', 'HELLO WORLD', '大文字変換', true, false, 1],
            ]
        );

        // Exercise 13: 文字列の置換
        $this->createExercise(
            $javaLanguage,
            '文字列を置換',
            '文字列メソッド',
            '文字列"Hello Java"の"Java"を"World"に置換して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        // Javaをworldに置換\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        System.out.println(text.replace(\"Java\", \"World\"));\n    }\n}",
            ['replace(検索, 置換)で文字列を置換'],
            'easy',
            15,
            ['java', 'string', 'replace'],
            13,
            [
                ['', 'Hello World', '文字列置換', true, false, 1],
            ]
        );

        // Exercise 14: 文字列の分割
        $this->createExercise(
            $javaLanguage,
            '文字列を分割',
            '文字列メソッド',
            '文字列"apple,banana,orange"をカンマで分割し、各要素を改行区切りで出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"apple,banana,orange\";\n        // カンマで分割して出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"apple,banana,orange\";\n        String[] fruits = text.split(\",\");\n        for (String fruit : fruits) {\n            System.out.println(fruit);\n        }\n    }\n}",
            ['split(区切り文字)で文字列を分割', 'for-each文で配列を走査'],
            'medium',
            20,
            ['java', 'string', 'split', 'array'],
            14,
            [
                ['', "apple\nbanana\norange", '文字列分割', true, false, 1],
            ]
        );

        // Exercise 15: 偶数の判定
        $this->createExercise(
            $javaLanguage,
            '1から10の偶数を出力',
            'ループと条件分岐',
            '1から10までの数のうち、偶数だけをカンマ区切りで出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // 1から10の偶数を出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        boolean first = true;\n        for (int i = 1; i <= 10; i++) {\n            if (i % 2 == 0) {\n                if (!first) System.out.print(\",\");\n                System.out.print(i);\n                first = false;\n            }\n        }\n    }\n}",
            ['i % 2 == 0で偶数を判定', '%は剰余演算子'],
            'medium',
            20,
            ['java', 'loop', 'modulo'],
            15,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1],
            ]
        );

        // Exercise 16: クラスの定義
        $this->createExercise(
            $javaLanguage,
            'シンプルなクラスを作成',
            'オブジェクト指向 - Class',
            'nameフィールドとgetName()メソッドを持つPersonクラスを作成し、"John"という名前のインスタンスを作成して名前を出力してください。',
            "class Person {\n    // フィールドとメソッドを定義\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // Personインスタンスを作成して名前を出力\n    }\n}",
            "class Person {\n    private String name;\n    \n    public Person(String name) {\n        this.name = name;\n    }\n    \n    public String getName() {\n        return this.name;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Person person = new Person(\"John\");\n        System.out.println(person.getName());\n    }\n}",
            ['class クラス名 { ... }で定義', 'コンストラクタで初期化', 'thisは自身のインスタンスを指す'],
            'medium',
            30,
            ['java', 'oop', 'class', 'object'],
            16,
            [
                ['', 'John', 'クラスとオブジェクト', true, false, 1],
            ]
        );

        // Exercise 17: 継承
        $this->createExercise(
            $javaLanguage,
            'クラスの継承',
            'オブジェクト指向 - Inheritance',
            'Animalクラスにspeakメソッド、DogクラスがAnimalを継承してspeakメソッドをオーバーライドし"Woof!"と返すようにしてください。',
            "class Animal {\n    // speakメソッドを定義\n}\n\nclass Dog extends Animal {\n    // speakメソッドをオーバーライド\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Dog dog = new Dog();\n        System.out.println(dog.speak());\n    }\n}",
            "class Animal {\n    public String speak() {\n        return \"Some sound\";\n    }\n}\n\nclass Dog extends Animal {\n    @Override\n    public String speak() {\n        return \"Woof!\";\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Dog dog = new Dog();\n        System.out.println(dog.speak());\n    }\n}",
            ['class 子クラス extends 親クラス { ... }で継承', '@Overrideアノテーションでオーバーライド'],
            'medium',
            35,
            ['java', 'oop', 'inheritance', 'extends'],
            17,
            [
                ['', 'Woof!', '継承とオーバーライド', true, false, 1],
            ]
        );

        // Exercise 18: インターフェース
        $this->createExercise(
            $javaLanguage,
            'インターフェースの実装',
            'オブジェクト指向 - Interface',
            'ShapeインターフェースにcalculateArea()メソッドを定義し、Circleクラスで実装して半径5の円の面積(78.5)を返してください。',
            "interface Shape {\n    // calculateAreaメソッドを定義\n}\n\nclass Circle implements Shape {\n    private double radius;\n    \n    public Circle(double radius) {\n        this.radius = radius;\n    }\n    \n    // calculateAreaメソッドを実装\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Circle circle = new Circle(5);\n        System.out.println(circle.calculateArea());\n    }\n}",
            "interface Shape {\n    double calculateArea();\n}\n\nclass Circle implements Shape {\n    private double radius;\n    \n    public Circle(double radius) {\n        this.radius = radius;\n    }\n    \n    public double calculateArea() {\n        return 3.14 * radius * radius;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Circle circle = new Circle(5);\n        System.out.println(circle.calculateArea());\n    }\n}",
            ['interface インターフェース名 { ... }で定義', 'implements キーワードで実装'],
            'hard',
            40,
            ['java', 'oop', 'interface', 'implements'],
            18,
            [
                ['', '78.5', '円の面積', true, false, 1],
            ]
        );

        // Exercise 19: Static変数とメソッド
        $this->createExercise(
            $javaLanguage,
            '静的メソッドの使用',
            'オブジェクト指向 - Static',
            'MathUtilsクラスにstatic add(int a, int b)メソッドを作成し、5と10を足した結果を出力してください。',
            "class MathUtils {\n    // static addメソッドを定義\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // static addメソッドを呼び出す\n    }\n}",
            "class MathUtils {\n    public static int add(int a, int b) {\n        return a + b;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        System.out.println(MathUtils.add(5, 10));\n    }\n}",
            ['public static で静的メソッド定義', 'クラス名.メソッド名()で呼び出し'],
            'medium',
            25,
            ['java', 'oop', 'static', 'method'],
            19,
            [
                ['', '15', '静的メソッド', true, false, 1],
            ]
        );

        // Exercise 20: final変数
        $this->createExercise(
            $javaLanguage,
            '定数の定義と使用',
            '定数',
            'PIという名前のfinal変数を3.14159で定義し、その値を出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // final変数PIを定義\n        \n        // PIを出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        final double PI = 3.14159;\n        System.out.println(PI);\n    }\n}",
            ['final 型 名前 = 値; で定数を定義', '定数名は大文字が慣例'],
            'easy',
            15,
            ['java', 'constant', 'final'],
            20,
            [
                ['', '3.14159', '定数の定義', true, false, 1],
            ]
        );

        // Exercise 21: ArrayList - 追加
        $this->createExercise(
            $javaLanguage,
            'ArrayListに要素を追加',
            'コレクション - ArrayList',
            'String型のArrayListを作成し、"apple", "banana", "orange"を追加して、カンマ区切りで出力してください。',
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        // ArrayListを作成して要素を追加\n    }\n}",
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        ArrayList<String> fruits = new ArrayList<>();\n        fruits.add(\"apple\");\n        fruits.add(\"banana\");\n        fruits.add(\"orange\");\n        for (int i = 0; i < fruits.size(); i++) {\n            if (i > 0) System.out.print(\",\");\n            System.out.print(fruits.get(i));\n        }\n    }\n}",
            ['ArrayList<型> 変数名 = new ArrayList<>();', 'add()で要素を追加', 'get(index)で要素を取得'],
            'medium',
            25,
            ['java', 'arraylist', 'collection'],
            21,
            [
                ['', 'apple,banana,orange', 'ArrayListの使用', true, false, 1],
            ]
        );

        // Exercise 22: ArrayList - サイズ
        $this->createExercise(
            $javaLanguage,
            'ArrayListのサイズを取得',
            'コレクション - ArrayList',
            'ArrayListに "red", "green", "blue" を追加し、そのサイズ（要素数）を出力してください。',
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        ArrayList<String> colors = new ArrayList<>();\n        // 要素を追加してサイズを出力\n    }\n}",
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        ArrayList<String> colors = new ArrayList<>();\n        colors.add(\"red\");\n        colors.add(\"green\");\n        colors.add(\"blue\");\n        System.out.println(colors.size());\n    }\n}",
            ['size()でArrayListの要素数を取得'],
            'easy',
            20,
            ['java', 'arraylist', 'size'],
            22,
            [
                ['', '3', 'ArrayListのサイズ', true, false, 1],
            ]
        );

        // Exercise 23: HashMap - 追加と取得
        $this->createExercise(
            $javaLanguage,
            'HashMapに追加と取得',
            'コレクション - HashMap',
            'HashMap<String, Integer>を作成し、"age" -> 25を追加して、"age"の値を出力してください。',
            "import java.util.HashMap;\n\npublic class Main {\n    public static void main(String[] args) {\n        // HashMapを作成してデータを追加・取得\n    }\n}",
            "import java.util.HashMap;\n\npublic class Main {\n    public static void main(String[] args) {\n        HashMap<String, Integer> map = new HashMap<>();\n        map.put(\"age\", 25);\n        System.out.println(map.get(\"age\"));\n    }\n}",
            ['HashMap<キー型, 値型> 変数名 = new HashMap<>();', 'put(key, value)で追加', 'get(key)で取得'],
            'medium',
            25,
            ['java', 'hashmap', 'collection'],
            23,
            [
                ['', '25', 'HashMapの使用', true, false, 1],
            ]
        );

        // Exercise 24: 三項演算子
        $this->createExercise(
            $javaLanguage,
            '三項演算子',
            '三項演算子',
            '変数age = 18があります。三項演算子を使って、18以上なら"Adult"、未満なら"Minor"と出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int age = 18;\n        // 三項演算子で判定\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int age = 18;\n        System.out.println(age >= 18 ? \"Adult\" : \"Minor\");\n    }\n}",
            ['条件 ? 真の値 : 偽の値', 'if-elseの短縮形'],
            'easy',
            20,
            ['java', 'ternary', 'operator'],
            24,
            [
                ['', 'Adult', '三項演算子', true, false, 1],
            ]
        );

        // Exercise 25: Try-Catch
        $this->createExercise(
            $javaLanguage,
            '例外処理',
            '例外処理 - Try-Catch',
            '文字列"abc"をInteger.parseInt()で整数に変換しようとすると例外が発生します。try-catchで例外をキャッチして"Error"と出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String str = \"abc\";\n        // try-catchで例外処理\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String str = \"abc\";\n        try {\n            int num = Integer.parseInt(str);\n            System.out.println(num);\n        } catch (NumberFormatException e) {\n            System.out.println(\"Error\");\n        }\n    }\n}",
            ['try { ... } catch (例外型 変数) { ... }', 'NumberFormatExceptionは数値変換の例外'],
            'medium',
            30,
            ['java', 'exception', 'try-catch'],
            25,
            [
                ['', 'Error', '例外処理', true, false, 1],
            ]
        );

        // Exercise 26: For-Each Loop
        $this->createExercise(
            $javaLanguage,
            'For-Eachループ',
            'ループ - For-Each',
            '整数配列 {1, 2, 3, 4, 5} の各要素をカンマ区切りで出力してください。for-eachループを使用します。',
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {1, 2, 3, 4, 5};\n        // for-eachループで出力\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {1, 2, 3, 4, 5};\n        boolean first = true;\n        for (int num : numbers) {\n            if (!first) System.out.print(\",\");\n            System.out.print(num);\n            first = false;\n        }\n    }\n}",
            ['for (型 変数 : 配列) { ... }', 'インデックス不要で要素を走査'],
            'medium',
            20,
            ['java', 'loop', 'for-each'],
            26,
            [
                ['', '1,2,3,4,5', 'For-Eachループ', true, false, 1],
            ]
        );

        // Exercise 27: StringBuilder
        $this->createExercise(
            $javaLanguage,
            'StringBuilderで文字列連結',
            'StringBuilder',
            'StringBuilderを使って、"Hello", " ", "World"を連結して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // StringBuilderで文字列を連結\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        StringBuilder sb = new StringBuilder();\n        sb.append(\"Hello\");\n        sb.append(\" \");\n        sb.append(\"World\");\n        System.out.println(sb.toString());\n    }\n}",
            ['StringBuilder sb = new StringBuilder();', 'append()で文字列を追加', 'toString()で文字列に変換'],
            'medium',
            25,
            ['java', 'stringbuilder', 'string'],
            27,
            [
                ['', 'Hello World', 'StringBuilder', true, false, 1],
            ]
        );

        // Exercise 28: Math.max
        $this->createExercise(
            $javaLanguage,
            '2つの数の最大値',
            'Mathクラス',
            'Math.max()を使って、15と25の最大値を出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // Math.maxで最大値を求める\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        System.out.println(Math.max(15, 25));\n    }\n}",
            ['Math.max(a, b)で最大値を取得', 'Math.min(a, b)で最小値を取得'],
            'easy',
            15,
            ['java', 'math', 'max'],
            28,
            [
                ['', '25', '最大値', true, false, 1],
            ]
        );

        // Exercise 29: Math.pow
        $this->createExercise(
            $javaLanguage,
            '累乗の計算',
            'Mathクラス',
            'Math.pow()を使って、2の3乗を計算して整数で出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // 2の3乗を計算\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        System.out.println((int)Math.pow(2, 3));\n    }\n}",
            ['Math.pow(底, 指数)で累乗を計算', '(int)でdoubleをintに変換'],
            'easy',
            20,
            ['java', 'math', 'pow'],
            29,
            [
                ['', '8', '2の3乗', true, false, 1],
            ]
        );

        // Exercise 30: String.substring
        $this->createExercise(
            $javaLanguage,
            '文字列の一部を抽出',
            '文字列メソッド',
            '文字列"Hello World"からインデックス0から5までを抽出して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello World\";\n        // substringで一部を抽出\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello World\";\n        System.out.println(text.substring(0, 5));\n    }\n}",
            ['substring(開始, 終了)で部分文字列を取得', '終了位置は含まれない'],
            'easy',
            15,
            ['java', 'string', 'substring'],
            30,
            [
                ['', 'Hello', '部分文字列', true, false, 1],
            ]
        );

        // Exercise 31: String.contains
        $this->createExercise(
            $javaLanguage,
            '文字列に特定の文字が含まれるか確認',
            '文字列メソッド',
            '文字列"Hello Java"に"Java"が含まれているか確認し、含まれていれば"Found"、なければ"Not found"と出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        // containsで確認\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String text = \"Hello Java\";\n        System.out.println(text.contains(\"Java\") ? \"Found\" : \"Not found\");\n    }\n}",
            ['contains(文字列)で部分文字列の存在を確認', 'trueまたはfalseを返す'],
            'easy',
            20,
            ['java', 'string', 'contains'],
            31,
            [
                ['', 'Found', '文字列検索', true, false, 1],
            ]
        );

        // Exercise 32: 配列のコピー
        $this->createExercise(
            $javaLanguage,
            '配列のコピー',
            '配列',
            '整数配列 {1, 2, 3} をコピーして、コピーした配列の要素をカンマ区切りで出力してください。Arrays.copyOf()を使用します。',
            "import java.util.Arrays;\n\npublic class Main {\n    public static void main(String[] args) {\n        int[] original = {1, 2, 3};\n        // 配列をコピーして出力\n    }\n}",
            "import java.util.Arrays;\n\npublic class Main {\n    public static void main(String[] args) {\n        int[] original = {1, 2, 3};\n        int[] copy = Arrays.copyOf(original, original.length);\n        for (int i = 0; i < copy.length; i++) {\n            if (i > 0) System.out.print(\",\");\n            System.out.print(copy[i]);\n        }\n    }\n}",
            ['Arrays.copyOf(配列, 長さ)で配列をコピー', 'java.util.Arraysをimport'],
            'medium',
            25,
            ['java', 'array', 'copy'],
            32,
            [
                ['', '1,2,3', '配列のコピー', true, false, 1],
            ]
        );

        // Exercise 33: 配列のソート
        $this->createExercise(
            $javaLanguage,
            '配列を昇順にソート',
            '配列',
            '整数配列 {5, 2, 8, 1, 9} をArrays.sort()で昇順にソートして、カンマ区切りで出力してください。',
            "import java.util.Arrays;\n\npublic class Main {\n    public static void main(String[] args) {\n        int[] numbers = {5, 2, 8, 1, 9};\n        // 配列をソートして出力\n    }\n}",
            "import java.util.Arrays;\n\npublic class Main {\n    public static void main(String[] args) {\n        int[] numbers = {5, 2, 8, 1, 9};\n        Arrays.sort(numbers);\n        for (int i = 0; i < numbers.length; i++) {\n            if (i > 0) System.out.print(\",\");\n            System.out.print(numbers[i]);\n        }\n    }\n}",
            ['Arrays.sort(配列)で配列を昇順ソート', '元の配列が変更される'],
            'medium',
            25,
            ['java', 'array', 'sort'],
            33,
            [
                ['', '1,2,5,8,9', '配列のソート', true, false, 1],
            ]
        );

        // Exercise 34: ラムダ式
        $this->createExercise(
            $javaLanguage,
            'ラムダ式でフィルタリング',
            'ラムダ式',
            'ArrayListから偶数のみをフィルタリングして、カンマ区切りで出力してください。removeIf()とラムダ式を使用します。',
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        ArrayList<Integer> numbers = new ArrayList<>();\n        numbers.add(1);\n        numbers.add(2);\n        numbers.add(3);\n        numbers.add(4);\n        numbers.add(5);\n        // 奇数を削除して偶数のみ残す\n    }\n}",
            "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        ArrayList<Integer> numbers = new ArrayList<>();\n        numbers.add(1);\n        numbers.add(2);\n        numbers.add(3);\n        numbers.add(4);\n        numbers.add(5);\n        numbers.removeIf(n -> n % 2 != 0);\n        for (int i = 0; i < numbers.size(); i++) {\n            if (i > 0) System.out.print(\",\");\n            System.out.print(numbers.get(i));\n        }\n    }\n}",
            ['n -> 式 でラムダ式', 'removeIf()で条件に合う要素を削除'],
            'hard',
            35,
            ['java', 'lambda', 'arraylist'],
            34,
            [
                ['', '2,4', 'ラムダ式', true, false, 1],
            ]
        );

        // Exercise 35: Enum
        $this->createExercise(
            $javaLanguage,
            '列挙型の使用',
            '列挙型 - Enum',
            'Day列挙型を定義し(MONDAY, TUESDAY, WEDNESDAY)、MONDAYを出力してください。',
            "enum Day {\n    // 列挙定数を定義\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // MONDAYを出力\n    }\n}",
            "enum Day {\n    MONDAY, TUESDAY, WEDNESDAY\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        System.out.println(Day.MONDAY);\n    }\n}",
            ['enum 名前 { 定数1, 定数2, ... }', '列挙型名.定数名 でアクセス'],
            'medium',
            25,
            ['java', 'enum'],
            35,
            [
                ['', 'MONDAY', '列挙型', true, false, 1],
            ]
        );

        // Exercise 36: Integer.parseInt
        $this->createExercise(
            $javaLanguage,
            '文字列を整数に変換',
            '型変換',
            '文字列"123"をInteger.parseInt()で整数に変換し、10を足して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String str = \"123\";\n        // 整数に変換して10を足す\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String str = \"123\";\n        int num = Integer.parseInt(str);\n        System.out.println(num + 10);\n    }\n}",
            ['Integer.parseInt(文字列)でintに変換', 'Double.parseDouble()はdoubleに変換'],
            'easy',
            15,
            ['java', 'type', 'parsing'],
            36,
            [
                ['', '133', '型変換', true, false, 1],
            ]
        );

        // Exercise 37: String.format
        $this->createExercise(
            $javaLanguage,
            '文字列フォーマット',
            '文字列フォーマット',
            'String.format()を使って、"Name: John, Age: 25"という文字列を作成して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        String name = \"John\";\n        int age = 25;\n        // String.formatでフォーマット\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        String name = \"John\";\n        int age = 25;\n        System.out.println(String.format(\"Name: %s, Age: %d\", name, age));\n    }\n}",
            ['String.format(\"フォーマット\", 値...)でフォーマット', '%sは文字列、%dは整数'],
            'medium',
            25,
            ['java', 'string', 'format'],
            37,
            [
                ['', 'Name: John, Age: 25', '文字列フォーマット', true, false, 1],
            ]
        );

        // Exercise 38: 多次元配列
        $this->createExercise(
            $javaLanguage,
            '2次元配列の合計',
            '多次元配列',
            '2次元配列 {{1, 2}, {3, 4}, {5, 6}} の全要素の合計を計算して出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        int[][] matrix = {{1, 2}, {3, 4}, {5, 6}};\n        // 全要素の合計を計算\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        int[][] matrix = {{1, 2}, {3, 4}, {5, 6}};\n        int sum = 0;\n        for (int i = 0; i < matrix.length; i++) {\n            for (int j = 0; j < matrix[i].length; j++) {\n                sum += matrix[i][j];\n            }\n        }\n        System.out.println(sum);\n    }\n}",
            ['int[][] 変数名 = {...};で2次元配列', 'ネストしたforループで走査'],
            'medium',
            30,
            ['java', 'array', '2d-array'],
            38,
            [
                ['', '21', '2次元配列の合計', true, false, 1],
            ]
        );

        // Exercise 39: Continue文
        $this->createExercise(
            $javaLanguage,
            'Continue文で3の倍数をスキップ',
            'ループ制御 - Continue',
            '1から10までの数のうち、3の倍数以外をカンマ区切りで出力してください。continue文を使用します。',
            "public class Main {\n    public static void main(String[] args) {\n        // 3の倍数をスキップ\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        boolean first = true;\n        for (int i = 1; i <= 10; i++) {\n            if (i % 3 == 0) continue;\n            if (!first) System.out.print(\",\");\n            System.out.print(i);\n            first = false;\n        }\n    }\n}",
            ['continueで次のループへスキップ', 'i % 3 == 0で3の倍数を判定'],
            'medium',
            25,
            ['java', 'loop', 'continue'],
            39,
            [
                ['', '1,2,4,5,7,8,10', '3の倍数以外', true, false, 1],
            ]
        );

        // Exercise 40: Break文
        $this->createExercise(
            $javaLanguage,
            'Break文で5以上でループを終了',
            'ループ制御 - Break',
            '1から10までループし、5に達したらループを終了します。1から4までをカンマ区切りで出力してください。',
            "public class Main {\n    public static void main(String[] args) {\n        // 5でループを終了\n    }\n}",
            "public class Main {\n    public static void main(String[] args) {\n        boolean first = true;\n        for (int i = 1; i <= 10; i++) {\n            if (i >= 5) break;\n            if (!first) System.out.print(\",\");\n            System.out.print(i);\n            first = false;\n        }\n    }\n}",
            ['breakでループを終了', 'i >= 5で条件判定'],
            'easy',
            20,
            ['java', 'loop', 'break'],
            40,
            [
                ['', '1,2,3,4', 'ループの終了', true, false, 1],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($javaLanguage);

        $this->command->info('Java exercises seeded successfully!');
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
