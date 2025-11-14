<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeCppExerciseSeeder extends Seeder
{
    /**
     * Seed C++ exercise data
     * C++言語の練習問題
     */
    public function run(): void
    {
        // Get C++ Language
        $cppLanguage = CheatCodeLanguage::where('name', 'cpp')->first();

        if (!$cppLanguage) {
            $this->command->error('C++ language not found. Please run CheatCodeCppSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $cppLanguage,
            'Hello World出力',
            'C++の基本',
            '「Hello, World!」という文字列を出力するC++プログラムを書いてください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // ここにコードを書いてください\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    cout << \"Hello, World!\" << endl;\n    return 0;\n}",
            ['cout << で出力します', 'endl で改行します'],
            'easy',
            10,
            ['cpp', 'basics', 'cout'],
            1,
            [
                ['', 'Hello, World!', 'Hello Worldを出力', true, false, 1],
            ]
        );

        // Exercise 2: 変数と演算
        $this->createExercise(
            $cppLanguage,
            '2つの数値の合計',
            '変数と算術演算',
            '2つの整数（10と20）を変数に代入し、それらの合計を計算して出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // 2つの数値を変数に代入\n    \n    // 合計を計算して出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int a = 10;\n    int b = 20;\n    cout << a + b << endl;\n    return 0;\n}",
            ['int型で整数を宣言', 'cout <<で出力します'],
            'easy',
            10,
            ['cpp', 'variable', 'arithmetic'],
            2,
            [
                ['', '30', '10 + 20 = 30', true, false, 1],
            ]
        );

        // Exercise 3: 文字列の連結
        $this->createExercise(
            $cppLanguage,
            '文字列を連結して挨拶',
            '文字列連結',
            'string型の変数name = "Alice"を定義し、「Hello, Alice!」と出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string name = \"Alice\";\n    // 文字列を連結して出力\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string name = \"Alice\";\n    cout << \"Hello, \" + name + \"!\" << endl;\n    return 0;\n}",
            ['+演算子で文字列を連結', 'string型は文字列を扱います'],
            'easy',
            15,
            ['cpp', 'string', 'concatenation'],
            3,
            [
                ['', 'Hello, Alice!', '文字列連結', true, false, 1],
            ]
        );

        // Exercise 4: if-else文
        $this->createExercise(
            $cppLanguage,
            '数値が正か負かゼロか判定',
            '条件分岐',
            '変数num = -5があります。この数値が正の数なら「Positive」、負の数なら「Negative」、0なら「Zero」と出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = -5;\n    // 判定して出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = -5;\n    if (num > 0) {\n        cout << \"Positive\" << endl;\n    } else if (num < 0) {\n        cout << \"Negative\" << endl;\n    } else {\n        cout << \"Zero\" << endl;\n    }\n    return 0;\n}",
            ['if, else if, elseで条件分岐', '条件は()で囲みます'],
            'easy',
            15,
            ['cpp', 'conditional', 'if-else'],
            4,
            [
                ['', 'Negative', '負の数', true, false, 1],
            ]
        );

        // Exercise 5: forループ
        $this->createExercise(
            $cppLanguage,
            '1から5まで出力',
            'ループ - for',
            'forループを使って1から5までの数字を改行区切りで出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // forループで1から5まで出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    for (int i = 1; i <= 5; i++) {\n        cout << i << endl;\n    }\n    return 0;\n}",
            ['for (初期化; 条件; 更新) の形式', 'i++は i = i + 1と同じ'],
            'easy',
            15,
            ['cpp', 'loop', 'for'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで', true, false, 1],
            ]
        );

        // Exercise 6: whileループ
        $this->createExercise(
            $cppLanguage,
            'whileループで合計計算',
            'ループ - while',
            '変数sum = 0とi = 1を定義し、whileループでiが5以下の間、sumにiを加算し続けて、最終的なsumの値を出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int sum = 0;\n    int i = 1;\n    // whileループで合計を計算\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int sum = 0;\n    int i = 1;\n    while (i <= 5) {\n        sum += i;\n        i++;\n    }\n    cout << sum << endl;\n    return 0;\n}",
            ['while (条件) { ... }', 'sum += iは sum = sum + iと同じ'],
            'easy',
            20,
            ['cpp', 'loop', 'while'],
            6,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1],
            ]
        );

        // Exercise 7: 配列の宣言と出力
        $this->createExercise(
            $cppLanguage,
            '配列の要素を出力',
            '配列',
            '整数配列 {10, 20, 30, 40, 50} を宣言し、各要素をカンマ区切りで1行に出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {10, 20, 30, 40, 50};\n    // 配列の要素を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {10, 20, 30, 40, 50};\n    int size = sizeof(numbers) / sizeof(numbers[0]);\n    for (int i = 0; i < size; i++) {\n        if (i > 0) cout << \",\";\n        cout << numbers[i];\n    }\n    cout << endl;\n    return 0;\n}",
            ['sizeof(配列)/sizeof(配列[0])で配列の長さを取得'],
            'medium',
            20,
            ['cpp', 'array', 'for'],
            7,
            [
                ['', '10,20,30,40,50', '配列をカンマ区切り', true, false, 1],
            ]
        );

        // Exercise 8: 関数の定義
        $this->createExercise(
            $cppLanguage,
            '関数で2つの数を足す',
            '関数',
            'add(int a, int b)という関数を作成し、2つの整数の合計を返すようにしてください。mainから5と10で呼び出して結果を出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// add関数を定義\n\nint main() {\n    // add関数を呼び出して結果を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint add(int a, int b) {\n    return a + b;\n}\n\nint main() {\n    cout << add(5, 10) << endl;\n    return 0;\n}",
            ['戻り値型 関数名(引数) { ... }', 'returnで値を返します'],
            'medium',
            25,
            ['cpp', 'function'],
            8,
            [
                ['', '15', '関数呼び出し', true, false, 1],
            ]
        );

        // Exercise 9: 最大値を求める
        $this->createExercise(
            $cppLanguage,
            '配列の最大値を求める',
            '配列とループ',
            '整数配列 {15, 42, 8, 27, 33} の中から最大値を見つけて出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {15, 42, 8, 27, 33};\n    int size = 5;\n    // 最大値を求める\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {15, 42, 8, 27, 33};\n    int size = 5;\n    int max = numbers[0];\n    for (int i = 1; i < size; i++) {\n        if (numbers[i] > max) {\n            max = numbers[i];\n        }\n    }\n    cout << max << endl;\n    return 0;\n}",
            ['最初の要素を初期値とする', 'ループで各要素と比較'],
            'medium',
            25,
            ['cpp', 'array', 'algorithm'],
            9,
            [
                ['', '42', '最大値', true, false, 1],
            ]
        );

        // Exercise 10: switch文
        $this->createExercise(
            $cppLanguage,
            '曜日の判定',
            'Switch文',
            '変数day = 1があります。switch文を使って対応する曜日名を出力してください。1=Monday, 2=Tuesday, ..., 7=Sunday。該当しない場合は"Invalid day"と出力。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int day = 1;\n    // switch文で曜日を判定\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int day = 1;\n    switch (day) {\n        case 1:\n            cout << \"Monday\" << endl;\n            break;\n        case 2:\n            cout << \"Tuesday\" << endl;\n            break;\n        case 3:\n            cout << \"Wednesday\" << endl;\n            break;\n        case 4:\n            cout << \"Thursday\" << endl;\n            break;\n        case 5:\n            cout << \"Friday\" << endl;\n            break;\n        case 6:\n            cout << \"Saturday\" << endl;\n            break;\n        case 7:\n            cout << \"Sunday\" << endl;\n            break;\n        default:\n            cout << \"Invalid day\" << endl;\n            break;\n    }\n    return 0;\n}",
            ['switch (変数) { case 値: ... break; }の形式', 'breakを忘れずに'],
            'medium',
            30,
            ['cpp', 'switch', 'conditional'],
            10,
            [
                ['', 'Monday', '月曜日', true, false, 1],
            ]
        );

        // Exercise 11: ポインタの基本
        $this->createExercise(
            $cppLanguage,
            'ポインタで値を出力',
            'ポインタ',
            '整数変数num = 42のアドレスをポインタptrに格納し、ポインタを使って値を出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = 42;\n    // ポインタを使って値を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = 42;\n    int* ptr = &num;\n    cout << *ptr << endl;\n    return 0;\n}",
            ['&でアドレスを取得', '*でポインタが指す値を取得'],
            'medium',
            25,
            ['cpp', 'pointer'],
            11,
            [
                ['', '42', 'ポインタの基本', true, false, 1],
            ]
        );

        // Exercise 12: 参照渡し
        $this->createExercise(
            $cppLanguage,
            '参照渡しで値を変更',
            '参照',
            '関数double_value(int &n)を作成し、渡された値を2倍にします。mainで5を渡して、変更後の値を出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// double_value関数を定義\n\nint main() {\n    int num = 5;\n    // 関数を呼び出して値を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nvoid double_value(int &n) {\n    n = n * 2;\n}\n\nint main() {\n    int num = 5;\n    double_value(num);\n    cout << num << endl;\n    return 0;\n}",
            ['int &で参照渡し', '関数内での変更が元の変数に反映される'],
            'medium',
            30,
            ['cpp', 'reference', 'function'],
            12,
            [
                ['', '10', '参照渡し', true, false, 1],
            ]
        );

        // Exercise 13: vectorの使用
        $this->createExercise(
            $cppLanguage,
            'vectorに要素を追加',
            'STL - vector',
            'vector<int>を作成し、1, 2, 3を追加して、カンマ区切りで出力してください。',
            "#include <iostream>\n#include <vector>\nusing namespace std;\n\nint main() {\n    // vectorを作成して要素を追加\n    return 0;\n}",
            "#include <iostream>\n#include <vector>\nusing namespace std;\n\nint main() {\n    vector<int> nums;\n    nums.push_back(1);\n    nums.push_back(2);\n    nums.push_back(3);\n    for (int i = 0; i < nums.size(); i++) {\n        if (i > 0) cout << \",\";\n        cout << nums[i];\n    }\n    cout << endl;\n    return 0;\n}",
            ['vector<型> 変数名;', 'push_back()で要素を追加', 'size()でサイズを取得'],
            'medium',
            25,
            ['cpp', 'vector', 'stl'],
            13,
            [
                ['', '1,2,3', 'vectorの使用', true, false, 1],
            ]
        );

        // Exercise 14: クラスの定義
        $this->createExercise(
            $cppLanguage,
            'シンプルなクラスを作成',
            'オブジェクト指向 - Class',
            'nameメンバ変数とgetName()メソッドを持つPersonクラスを作成し、"John"という名前のインスタンスを作成して名前を出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nclass Person {\n    // メンバ変数とメソッドを定義\n};\n\nint main() {\n    // Personインスタンスを作成して名前を出力\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nclass Person {\nprivate:\n    string name;\npublic:\n    Person(string n) : name(n) {}\n    string getName() {\n        return name;\n    }\n};\n\nint main() {\n    Person person(\"John\");\n    cout << person.getName() << endl;\n    return 0;\n}",
            ['class クラス名 { ... };', 'コンストラクタで初期化', 'public:, private:でアクセス指定'],
            'medium',
            30,
            ['cpp', 'oop', 'class', 'object'],
            14,
            [
                ['', 'John', 'クラスとオブジェクト', true, false, 1],
            ]
        );

        // Exercise 15: 継承
        $this->createExercise(
            $cppLanguage,
            'クラスの継承',
            'オブジェクト指向 - Inheritance',
            'Animalクラスにspeakメソッド、DogクラスがAnimalを継承してspeakメソッドをオーバーライドし"Woof!"と返すようにしてください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nclass Animal {\n    // speakメソッドを定義\n};\n\nclass Dog : public Animal {\n    // speakメソッドをオーバーライド\n};\n\nint main() {\n    Dog dog;\n    cout << dog.speak() << endl;\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nclass Animal {\npublic:\n    virtual string speak() {\n        return \"Some sound\";\n    }\n};\n\nclass Dog : public Animal {\npublic:\n    string speak() override {\n        return \"Woof!\";\n    }\n};\n\nint main() {\n    Dog dog;\n    cout << dog.speak() << endl;\n    return 0;\n}",
            ['class 子クラス : public 親クラス { ... }で継承', 'virtualでオーバーライド可能に'],
            'medium',
            35,
            ['cpp', 'oop', 'inheritance'],
            15,
            [
                ['', 'Woof!', '継承とオーバーライド', true, false, 1],
            ]
        );

        // Exercise 16: 文字列の長さ
        $this->createExercise(
            $cppLanguage,
            '文字列の長さを取得',
            '文字列メソッド',
            'string型の"Hello C++"の長さを取得して出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello C++\";\n    // 文字列の長さを出力\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello C++\";\n    cout << text.length() << endl;\n    return 0;\n}",
            ['length()またはsize()で文字列の長さを取得'],
            'easy',
            10,
            ['cpp', 'string', 'length'],
            16,
            [
                ['', '9', '文字列の長さ', true, false, 1],
            ]
        );

        // Exercise 17: 文字列の部分取得
        $this->createExercise(
            $cppLanguage,
            '文字列の一部を抽出',
            '文字列メソッド',
            '文字列"Hello World"からインデックス0から5文字を抽出して出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello World\";\n    // substringで一部を抽出\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello World\";\n    cout << text.substr(0, 5) << endl;\n    return 0;\n}",
            ['substr(開始位置, 長さ)で部分文字列を取得'],
            'easy',
            15,
            ['cpp', 'string', 'substr'],
            17,
            [
                ['', 'Hello', '部分文字列', true, false, 1],
            ]
        );

        // Exercise 18: 範囲ベースforループ
        $this->createExercise(
            $cppLanguage,
            '範囲ベースforループ',
            'ループ - Range-based for',
            '配列 {1, 2, 3, 4, 5} の各要素をカンマ区切りで出力してください。範囲ベースforループを使用します。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {1, 2, 3, 4, 5};\n    // 範囲ベースforループで出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int numbers[] = {1, 2, 3, 4, 5};\n    bool first = true;\n    for (int num : numbers) {\n        if (!first) cout << \",\";\n        cout << num;\n        first = false;\n    }\n    cout << endl;\n    return 0;\n}",
            ['for (型 変数 : 配列) { ... }', 'C++11以降で使用可能'],
            'medium',
            20,
            ['cpp', 'loop', 'range-based-for'],
            18,
            [
                ['', '1,2,3,4,5', '範囲ベースforループ', true, false, 1],
            ]
        );

        // Exercise 19: map（連想配列）
        $this->createExercise(
            $cppLanguage,
            'mapに追加と取得',
            'STL - map',
            'map<string, int>を作成し、"age" -> 25を追加して、"age"の値を出力してください。',
            "#include <iostream>\n#include <map>\n#include <string>\nusing namespace std;\n\nint main() {\n    // mapを作成してデータを追加・取得\n    return 0;\n}",
            "#include <iostream>\n#include <map>\n#include <string>\nusing namespace std;\n\nint main() {\n    map<string, int> m;\n    m[\"age\"] = 25;\n    cout << m[\"age\"] << endl;\n    return 0;\n}",
            ['map<キー型, 値型> 変数名;', 'm[キー] = 値;で追加'],
            'medium',
            25,
            ['cpp', 'map', 'stl'],
            19,
            [
                ['', '25', 'mapの使用', true, false, 1],
            ]
        );

        // Exercise 20: 三項演算子
        $this->createExercise(
            $cppLanguage,
            '三項演算子',
            '三項演算子',
            '変数age = 18があります。三項演算子を使って、18以上なら"Adult"、未満なら"Minor"と出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int age = 18;\n    // 三項演算子で判定\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int age = 18;\n    cout << (age >= 18 ? \"Adult\" : \"Minor\") << endl;\n    return 0;\n}",
            ['条件 ? 真の値 : 偽の値', 'if-elseの短縮形'],
            'easy',
            20,
            ['cpp', 'ternary', 'operator'],
            20,
            [
                ['', 'Adult', '三項演算子', true, false, 1],
            ]
        );

        // Exercise 21: const変数
        $this->createExercise(
            $cppLanguage,
            '定数の定義と使用',
            '定数',
            'PIという名前のconst変数を3.14159で定義し、その値を出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // const変数PIを定義\n    \n    // PIを出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    const double PI = 3.14159;\n    cout << PI << endl;\n    return 0;\n}",
            ['const 型 名前 = 値;で定数を定義'],
            'easy',
            15,
            ['cpp', 'constant', 'const'],
            21,
            [
                ['', '3.14159', '定数の定義', true, false, 1],
            ]
        );

        // Exercise 22: 構造体
        $this->createExercise(
            $cppLanguage,
            '構造体の定義と使用',
            '構造体',
            'name(string)とage(int)を持つPerson構造体を定義し、"John", 25のインスタンスを作成して名前を出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\n// Person構造体を定義\n\nint main() {\n    // Personインスタンスを作成して名前を出力\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nstruct Person {\n    string name;\n    int age;\n};\n\nint main() {\n    Person person = {\"John\", 25};\n    cout << person.name << endl;\n    return 0;\n}",
            ['struct 構造体名 { ... };', 'メンバはデフォルトでpublic'],
            'medium',
            25,
            ['cpp', 'struct'],
            22,
            [
                ['', 'John', '構造体', true, false, 1],
            ]
        );

        // Exercise 23: 算術演算（除算）
        $this->createExercise(
            $cppLanguage,
            '除算の結果を小数で出力',
            '算術演算',
            '10を3で割った結果を小数点付きで出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // 10 / 3を小数点付きで出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    cout << 10.0 / 3.0 << endl;\n    return 0;\n}",
            ['少なくとも一方をdouble型にする', '両方がintだと整数除算になる'],
            'easy',
            15,
            ['cpp', 'arithmetic', 'division'],
            23,
            [
                ['', '3.33333', '除算', true, false, 1],
            ]
        );

        // Exercise 24: 偶数の判定
        $this->createExercise(
            $cppLanguage,
            '1から10の偶数を出力',
            'ループと条件分岐',
            '1から10までの数のうち、偶数だけをカンマ区切りで出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // 1から10の偶数を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    bool first = true;\n    for (int i = 1; i <= 10; i++) {\n        if (i % 2 == 0) {\n            if (!first) cout << \",\";\n            cout << i;\n            first = false;\n        }\n    }\n    cout << endl;\n    return 0;\n}",
            ['i % 2 == 0で偶数を判定', '%は剰余演算子'],
            'medium',
            20,
            ['cpp', 'loop', 'modulo'],
            24,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1],
            ]
        );

        // Exercise 25: stringstream
        $this->createExercise(
            $cppLanguage,
            'stringstreamで文字列を分割',
            'stringstream',
            'stringstream を使って文字列"1 2 3 4 5"を数値に変換し、合計を出力してください。',
            "#include <iostream>\n#include <sstream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string str = \"1 2 3 4 5\";\n    // stringstreamで分割して合計を計算\n    return 0;\n}",
            "#include <iostream>\n#include <sstream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string str = \"1 2 3 4 5\";\n    stringstream ss(str);\n    int num, sum = 0;\n    while (ss >> num) {\n        sum += num;\n    }\n    cout << sum << endl;\n    return 0;\n}",
            ['stringstream ss(文字列);', 'ss >> 変数 で読み込み'],
            'hard',
            35,
            ['cpp', 'stringstream', 'parsing'],
            25,
            [
                ['', '15', '文字列から数値', true, false, 1],
            ]
        );

        // Exercise 26: 配列のソート
        $this->createExercise(
            $cppLanguage,
            '配列を昇順にソート',
            'STL - sort',
            'vector<int> {5, 2, 8, 1, 9} をsort関数で昇順にソートして、カンマ区切りで出力してください。',
            "#include <iostream>\n#include <vector>\n#include <algorithm>\nusing namespace std;\n\nint main() {\n    vector<int> numbers = {5, 2, 8, 1, 9};\n    // vectorをソートして出力\n    return 0;\n}",
            "#include <iostream>\n#include <vector>\n#include <algorithm>\nusing namespace std;\n\nint main() {\n    vector<int> numbers = {5, 2, 8, 1, 9};\n    sort(numbers.begin(), numbers.end());\n    for (int i = 0; i < numbers.size(); i++) {\n        if (i > 0) cout << \",\";\n        cout << numbers[i];\n    }\n    cout << endl;\n    return 0;\n}",
            ['sort(begin(), end())で昇順ソート', '#include <algorithm>が必要'],
            'medium',
            25,
            ['cpp', 'vector', 'sort', 'stl'],
            26,
            [
                ['', '1,2,5,8,9', 'vectorのソート', true, false, 1],
            ]
        );

        // Exercise 27: ラムダ式
        $this->createExercise(
            $cppLanguage,
            'ラムダ式で関数を定義',
            'ラムダ式',
            'ラムダ式を使って、2つの数値を掛け算する関数を作成し、5と6を掛けた結果を出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // ラムダ式を定義\n    \n    // ラムダ式を呼び出して結果を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    auto multiply = [](int a, int b) { return a * b; };\n    cout << multiply(5, 6) << endl;\n    return 0;\n}",
            ['[](引数) { 処理 }でラムダ式', 'autoで型推論'],
            'hard',
            35,
            ['cpp', 'lambda'],
            27,
            [
                ['', '30', 'ラムダ式', true, false, 1],
            ]
        );

        // Exercise 28: set（集合）
        $this->createExercise(
            $cppLanguage,
            'setで重複を除去',
            'STL - set',
            'set<int>を使って配列{1, 2, 2, 3, 3, 3, 4}から重複を除去し、カンマ区切りで出力してください。',
            "#include <iostream>\n#include <set>\nusing namespace std;\n\nint main() {\n    int arr[] = {1, 2, 2, 3, 3, 3, 4};\n    // setで重複を除去\n    return 0;\n}",
            "#include <iostream>\n#include <set>\nusing namespace std;\n\nint main() {\n    int arr[] = {1, 2, 2, 3, 3, 3, 4};\n    set<int> s(arr, arr + 7);\n    bool first = true;\n    for (int num : s) {\n        if (!first) cout << \",\";\n        cout << num;\n        first = false;\n    }\n    cout << endl;\n    return 0;\n}",
            ['setは重複を自動的に除去', 'ソート済みで保持される'],
            'medium',
            30,
            ['cpp', 'set', 'stl'],
            28,
            [
                ['', '1,2,3,4', 'setで重複除去', true, false, 1],
            ]
        );

        // Exercise 29: テンプレート関数
        $this->createExercise(
            $cppLanguage,
            'テンプレート関数',
            'テンプレート',
            'テンプレート関数max_value<T>を作成し、2つの値のうち大きい方を返すようにしてください。intの5と10で呼び出して結果を出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// テンプレート関数を定義\n\nint main() {\n    // テンプレート関数を呼び出す\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\ntemplate <typename T>\nT max_value(T a, T b) {\n    return (a > b) ? a : b;\n}\n\nint main() {\n    cout << max_value(5, 10) << endl;\n    return 0;\n}",
            ['template <typename T>でテンプレート定義', '型に依存しない汎用的な関数'],
            'hard',
            40,
            ['cpp', 'template', 'generic'],
            29,
            [
                ['', '10', 'テンプレート関数', true, false, 1],
            ]
        );

        // Exercise 30: 多次元配列
        $this->createExercise(
            $cppLanguage,
            '2次元配列の合計',
            '多次元配列',
            '2次元配列 {{1, 2}, {3, 4}, {5, 6}} の全要素の合計を計算して出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int matrix[3][2] = {{1, 2}, {3, 4}, {5, 6}};\n    // 全要素の合計を計算\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int matrix[3][2] = {{1, 2}, {3, 4}, {5, 6}};\n    int sum = 0;\n    for (int i = 0; i < 3; i++) {\n        for (int j = 0; j < 2; j++) {\n            sum += matrix[i][j];\n        }\n    }\n    cout << sum << endl;\n    return 0;\n}",
            ['int 配列名[行][列] = {...};', 'ネストしたforループで走査'],
            'medium',
            30,
            ['cpp', 'array', '2d-array'],
            30,
            [
                ['', '21', '2次元配列の合計', true, false, 1],
            ]
        );

        // Exercise 31: Continue文
        $this->createExercise(
            $cppLanguage,
            'Continue文で3の倍数をスキップ',
            'ループ制御 - Continue',
            '1から10までの数のうち、3の倍数以外をカンマ区切りで出力してください。continue文を使用します。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // 3の倍数をスキップ\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    bool first = true;\n    for (int i = 1; i <= 10; i++) {\n        if (i % 3 == 0) continue;\n        if (!first) cout << \",\";\n        cout << i;\n        first = false;\n    }\n    cout << endl;\n    return 0;\n}",
            ['continueで次のループへスキップ', 'i % 3 == 0で3の倍数を判定'],
            'medium',
            25,
            ['cpp', 'loop', 'continue'],
            31,
            [
                ['', '1,2,4,5,7,8,10', '3の倍数以外', true, false, 1],
            ]
        );

        // Exercise 32: Break文
        $this->createExercise(
            $cppLanguage,
            'Break文で5以上でループを終了',
            'ループ制御 - Break',
            '1から10までループし、5に達したらループを終了します。1から4までをカンマ区切りで出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    // 5でループを終了\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    bool first = true;\n    for (int i = 1; i <= 10; i++) {\n        if (i >= 5) break;\n        if (!first) cout << \",\";\n        cout << i;\n        first = false;\n    }\n    cout << endl;\n    return 0;\n}",
            ['breakでループを終了', 'i >= 5で条件判定'],
            'easy',
            20,
            ['cpp', 'loop', 'break'],
            32,
            [
                ['', '1,2,3,4', 'ループの終了', true, false, 1],
            ]
        );

        // Exercise 33: 型キャスト
        $this->createExercise(
            $cppLanguage,
            '型キャスト',
            '型変換',
            'double型の変数num = 3.14をint型にキャストして出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    double num = 3.14;\n    // int型にキャスト\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    double num = 3.14;\n    cout << (int)num << endl;\n    return 0;\n}",
            ['(型)変数 でキャスト', 'static_cast<型>(変数)も使用可能'],
            'easy',
            15,
            ['cpp', 'type', 'cast'],
            33,
            [
                ['', '3', '型キャスト', true, false, 1],
            ]
        );

        // Exercise 34: インクリメント・デクリメント
        $this->createExercise(
            $cppLanguage,
            'インクリメントとデクリメント',
            '演算子',
            '変数num = 5があります。++numで1増やし、その後--numで1減らして、最終的な値を出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = 5;\n    // ++numと--numを使用\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int num = 5;\n    ++num;\n    --num;\n    cout << num << endl;\n    return 0;\n}",
            ['++変数で1増やす（前置）', '--変数で1減らす（前置）'],
            'easy',
            10,
            ['cpp', 'operator', 'increment'],
            34,
            [
                ['', '5', 'インクリメント・デクリメント', true, false, 1],
            ]
        );

        // Exercise 35: 論理演算子
        $this->createExercise(
            $cppLanguage,
            '論理演算子',
            '論理演算',
            '変数a = 5, b = 10があります。a > 3 && b < 15 がtrueなら"True"、falseなら"False"と出力してください。',
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int a = 5, b = 10;\n    // 論理演算子で判定\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint main() {\n    int a = 5, b = 10;\n    cout << ((a > 3 && b < 15) ? \"True\" : \"False\") << endl;\n    return 0;\n}",
            ['&&は論理AND', '||は論理OR', '!は論理NOT'],
            'easy',
            20,
            ['cpp', 'logical', 'operator'],
            35,
            [
                ['', 'True', '論理演算子', true, false, 1],
            ]
        );

        // Exercise 36: 文字列検索
        $this->createExercise(
            $cppLanguage,
            '文字列に部分文字列が含まれるか確認',
            '文字列メソッド',
            '文字列"Hello C++"に"C++"が含まれているか確認し、含まれていれば"Found"、なければ"Not found"と出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello C++\";\n    // findで確認\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string text = \"Hello C++\";\n    cout << (text.find(\"C++\") != string::npos ? \"Found\" : \"Not found\") << endl;\n    return 0;\n}",
            ['find(文字列)で検索', 'string::nposは見つからなかった場合の値'],
            'medium',
            25,
            ['cpp', 'string', 'find'],
            36,
            [
                ['', 'Found', '文字列検索', true, false, 1],
            ]
        );

        // Exercise 37: 再帰関数
        $this->createExercise(
            $cppLanguage,
            '再帰で階乗を計算',
            '再帰',
            '再帰関数factorial(int n)を作成し、5の階乗(120)を計算して出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// factorial関数を定義\n\nint main() {\n    cout << factorial(5) << endl;\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nint factorial(int n) {\n    if (n <= 1) return 1;\n    return n * factorial(n - 1);\n}\n\nint main() {\n    cout << factorial(5) << endl;\n    return 0;\n}",
            ['再帰は関数が自分自身を呼び出す', '基底ケース(n <= 1)が必要'],
            'hard',
            35,
            ['cpp', 'recursion', 'function'],
            37,
            [
                ['', '120', '5の階乗', true, false, 1],
            ]
        );

        // Exercise 38: namespace
        $this->createExercise(
            $cppLanguage,
            '名前空間の定義と使用',
            'namespace',
            'MyNamespace名前空間にint add(int a, int b)関数を定義し、5と10を足した結果を出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// MyNamespace名前空間を定義\n\nint main() {\n    // MyNamespace::add()を呼び出す\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nnamespace MyNamespace {\n    int add(int a, int b) {\n        return a + b;\n    }\n}\n\nint main() {\n    cout << MyNamespace::add(5, 10) << endl;\n    return 0;\n}",
            ['namespace 名前 { ... }', '名前空間名::関数名()で呼び出し'],
            'medium',
            30,
            ['cpp', 'namespace'],
            38,
            [
                ['', '15', '名前空間', true, false, 1],
            ]
        );

        // Exercise 39: デフォルト引数
        $this->createExercise(
            $cppLanguage,
            'デフォルト引数',
            '関数',
            'greet(string name, string greeting = "Hello")関数を作成し、引数1つで呼び出すとデフォルト値が使われるようにしてください。"John"で呼び出して結果を出力してください。',
            "#include <iostream>\n#include <string>\nusing namespace std;\n\n// greet関数を定義\n\nint main() {\n    // greet関数を呼び出す\n    return 0;\n}",
            "#include <iostream>\n#include <string>\nusing namespace std;\n\nvoid greet(string name, string greeting = \"Hello\") {\n    cout << greeting << \", \" << name << \"!\" << endl;\n}\n\nint main() {\n    greet(\"John\");\n    return 0;\n}",
            ['引数 = デフォルト値 でデフォルト引数', '省略時にデフォルト値が使われる'],
            'medium',
            25,
            ['cpp', 'function', 'default-argument'],
            39,
            [
                ['', 'Hello, John!', 'デフォルト引数', true, false, 1],
            ]
        );

        // Exercise 40: enum（列挙型）
        $this->createExercise(
            $cppLanguage,
            '列挙型の使用',
            '列挙型 - enum',
            'Day列挙型を定義し(MONDAY, TUESDAY, WEDNESDAY)、MONDAYの値(0)を出力してください。',
            "#include <iostream>\nusing namespace std;\n\n// Day列挙型を定義\n\nint main() {\n    // MONDAYの値を出力\n    return 0;\n}",
            "#include <iostream>\nusing namespace std;\n\nenum Day {\n    MONDAY, TUESDAY, WEDNESDAY\n};\n\nint main() {\n    cout << MONDAY << endl;\n    return 0;\n}",
            ['enum 名前 { 定数1, 定数2, ... };', '値は0から順に割り当てられる'],
            'medium',
            25,
            ['cpp', 'enum'],
            40,
            [
                ['', '0', '列挙型', true, false, 1],
            ]
        );

        // Update language counts
        $this->updateLanguageCounts($cppLanguage);

        $this->command->info('C++ exercises seeded successfully!');
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
