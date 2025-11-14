<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeJavaScriptExerciseSeeder extends Seeder
{
    /**
     * Seed JavaScript exercises
     */
    public function run(): void
    {
        $jsLanguage = CheatCodeLanguage::where('slug', 'javascript')->first();

        if (!$jsLanguage) {
            $this->command->error('JavaScript language not found. Please run CheatCodeJavaScriptSeeder first.');
            return;
        }

        // Exercise 1: Hello World
        $this->createExercise(
            $jsLanguage,
            'Hello Worldを出力',
            'コンソール出力',
            '「Hello, World!」という文字列をコンソールに出力してください。',
            "// Hello, World!を出力してください\n",
            "console.log('Hello, World!');\n",
            ['console.log()を使用して出力します'],
            'easy',
            10,
            ['javascript', 'console', 'hello-world'],
            1,
            [
                ['', 'Hello, World!', '正しい出力', true, false, 1]
            ]
        );

        // Exercise 2: Variables
        $this->createExercise(
            $jsLanguage,
            '変数の宣言と出力',
            '変数 - let/const',
            'name変数に「Taro」を代入し、その値をコンソールに出力してください。',
            "// name変数を宣言して出力\n",
            "let name = 'Taro';\nconsole.log(name);\n",
            ['letで変数を宣言', 'console.log()で出力'],
            'easy',
            10,
            ['javascript', 'variable', 'let'],
            2,
            [
                ['', 'Taro', '変数の値を出力', true, false, 1]
            ]
        );

        // Exercise 3: Template Literals
        $this->createExercise(
            $jsLanguage,
            'テンプレート文字列で出力',
            'テンプレートリテラル',
            'name変数が「Hanako」の時、「Hello, Hanako!」を出力してください。テンプレートリテラルを使用すること。',
            "let name = 'Hanako';\n// テンプレートリテラルを使って出力\n",
            "let name = 'Hanako';\nconsole.log(`Hello, ${name}!`);\n",
            ['バッククォート(`)で囲む', '${変数名}で埋め込む'],
            'easy',
            15,
            ['javascript', 'template-literal', 'string'],
            3,
            [
                ['', 'Hello, Hanako!', 'テンプレート文字列の出力', true, false, 1]
            ]
        );

        // Exercise 4: Conditionals - if
        $this->createExercise(
            $jsLanguage,
            '数値の比較',
            '条件分岐 - if文',
            '変数numが10より大きい場合は「Large」、そうでない場合は「Small」を出力してください。num = 15の場合でテストします。',
            "let num = 15;\n// if文で比較して出力\n",
            "let num = 15;\nif (num > 10) {\n    console.log('Large');\n} else {\n    console.log('Small');\n}\n",
            ['if (条件) { 処理 }', 'else { 処理 }'],
            'easy',
            15,
            ['javascript', 'conditional', 'if'],
            4,
            [
                ['', 'Large', '15は10より大きい', true, false, 1],
                ['', 'Small', '5は10以下', false, false, 2]
            ]
        );

        // Exercise 5: For Loop
        $this->createExercise(
            $jsLanguage,
            '1から5まで出力',
            'ループ - for文',
            'for文を使って1から5までの数字をそれぞれ改行して出力してください。',
            "// 1から5まで出力\n",
            "for (let i = 1; i <= 5; i++) {\n    console.log(i);\n}\n",
            ['for (初期化; 条件; 更新)', 'console.log()で出力'],
            'easy',
            15,
            ['javascript', 'loop', 'for'],
            5,
            [
                ['', "1\n2\n3\n4\n5", '1から5まで', true, false, 1]
            ]
        );

        // Exercise 6: Array Creation
        $this->createExercise(
            $jsLanguage,
            '配列の作成と出力',
            '配列 - 基本',
            '配列[10, 20, 30, 40, 50]を作成し、その長さ（要素数）を出力してください。',
            "// 配列を作成して長さを出力\n",
            "let arr = [10, 20, 30, 40, 50];\nconsole.log(arr.length);\n",
            ['配列は[]で作成', 'lengthプロパティで長さを取得'],
            'easy',
            15,
            ['javascript', 'array', 'length'],
            6,
            [
                ['', '5', '配列の長さは5', true, false, 1]
            ]
        );

        // Exercise 7: Array Access
        $this->createExercise(
            $jsLanguage,
            '配列の要素にアクセス',
            '配列 - アクセス',
            '配列[\"apple\", \"banana\", \"cherry\"]の2番目の要素（banana）を出力してください。',
            "let fruits = ['apple', 'banana', 'cherry'];\n// 2番目の要素を出力\n",
            "let fruits = ['apple', 'banana', 'cherry'];\nconsole.log(fruits[1]);\n",
            ['配列[インデックス]でアクセス', 'インデックスは0から始まる'],
            'easy',
            15,
            ['javascript', 'array', 'access'],
            7,
            [
                ['', 'banana', '2番目の要素', true, false, 1]
            ]
        );

        // Exercise 8: Array Push
        $this->createExercise(
            $jsLanguage,
            '配列に要素を追加',
            '配列 - push',
            '配列[1, 2, 3]に要素4を追加して、配列全体を出力してください。出力形式: 1,2,3,4',
            "let numbers = [1, 2, 3];\n// 4を追加して出力\n",
            "let numbers = [1, 2, 3];\nnumbers.push(4);\nconsole.log(numbers.join(','));\n",
            ['push()で末尾に追加', 'join()で配列を文字列に変換'],
            'easy',
            15,
            ['javascript', 'array', 'push'],
            8,
            [
                ['', '1,2,3,4', '4を追加した配列', true, false, 1]
            ]
        );

        // Exercise 9: Array Map
        $this->createExercise(
            $jsLanguage,
            '配列の各要素を2倍にする',
            '配列 - map',
            '配列[1, 2, 3, 4, 5]の各要素を2倍にした新しい配列を作成し、カンマ区切りで出力してください。',
            "let numbers = [1, 2, 3, 4, 5];\n// mapで各要素を2倍にする\n",
            "let numbers = [1, 2, 3, 4, 5];\nlet doubled = numbers.map(n => n * 2);\nconsole.log(doubled.join(','));\n",
            ['map()で各要素に関数を適用', 'アロー関数 n => n * 2'],
            'medium',
            20,
            ['javascript', 'array', 'map'],
            9,
            [
                ['', '2,4,6,8,10', '各要素を2倍', true, false, 1]
            ]
        );

        // Exercise 10: Array Filter
        $this->createExercise(
            $jsLanguage,
            '偶数のみを抽出',
            '配列 - filter',
            '配列[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]から偶数のみを抽出して、カンマ区切りで出力してください。',
            "let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n// filterで偶数のみ抽出\n",
            "let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\nlet evens = numbers.filter(n => n % 2 === 0);\nconsole.log(evens.join(','));\n",
            ['filter()で条件に合う要素を抽出', 'n % 2 === 0で偶数判定'],
            'medium',
            20,
            ['javascript', 'array', 'filter'],
            10,
            [
                ['', '2,4,6,8,10', '偶数のみ', true, false, 1]
            ]
        );

        // Exercise 11: Array Reduce
        $this->createExercise(
            $jsLanguage,
            '配列の合計を計算',
            '配列 - reduce',
            '配列[1, 2, 3, 4, 5]の全要素の合計を計算して出力してください。',
            "let numbers = [1, 2, 3, 4, 5];\n// reduceで合計を計算\n",
            "let numbers = [1, 2, 3, 4, 5];\nlet sum = numbers.reduce((acc, n) => acc + n, 0);\nconsole.log(sum);\n",
            ['reduce()で累積計算', '初期値は0'],
            'medium',
            25,
            ['javascript', 'array', 'reduce'],
            11,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1]
            ]
        );

        // Exercise 12: Function Declaration
        $this->createExercise(
            $jsLanguage,
            '関数の定義と呼び出し',
            '関数 - 基本',
            '引数を2倍にして返す関数doubleを定義し、double(7)の結果を出力してください。',
            "// 引数を2倍にする関数doubleを定義\n// double(7)を呼び出して出力\n",
            "function double(n) {\n    return n * 2;\n}\nconsole.log(double(7));\n",
            ['function 関数名(引数) { return 値; }', '関数名()で呼び出し'],
            'easy',
            20,
            ['javascript', 'function', 'basic'],
            12,
            [
                ['', '14', '7の2倍', true, false, 1]
            ]
        );

        // Exercise 13: Arrow Function
        $this->createExercise(
            $jsLanguage,
            'アロー関数で2つの数を足す',
            '関数 - アロー関数',
            'アロー関数でadd(a, b)を定義し、add(3, 5)の結果を出力してください。',
            "// アロー関数でaddを定義\n// add(3, 5)を出力\n",
            "const add = (a, b) => a + b;\nconsole.log(add(3, 5));\n",
            ['const 関数名 = (引数) => 式', '式が1つなら{}不要'],
            'easy',
            20,
            ['javascript', 'function', 'arrow'],
            13,
            [
                ['', '8', '3+5=8', true, false, 1]
            ]
        );

        // Exercise 14: Object Creation
        $this->createExercise(
            $jsLanguage,
            'オブジェクトの作成',
            'オブジェクト - 基本',
            'name: "Taro", age: 25のオブジェクトを作成し、nameプロパティを出力してください。',
            "// オブジェクトを作成してnameを出力\n",
            "let person = { name: 'Taro', age: 25 };\nconsole.log(person.name);\n",
            ['{ key: value }でオブジェクト作成', 'オブジェクト.keyでアクセス'],
            'easy',
            15,
            ['javascript', 'object', 'basic'],
            14,
            [
                ['', 'Taro', 'nameプロパティ', true, false, 1]
            ]
        );

        // Exercise 15: Object Method
        $this->createExercise(
            $jsLanguage,
            'オブジェクトにメソッドを追加',
            'オブジェクト - メソッド',
            'greet()メソッドを持つオブジェクトを作成し、greet()を呼び出して「Hello!」を出力してください。',
            "// greet()メソッドを持つオブジェクトを作成\n// greet()を呼び出す\n",
            "let obj = {\n    greet: function() {\n        console.log('Hello!');\n    }\n};\nobj.greet();\n",
            ['メソッドは関数値を持つプロパティ', 'オブジェクト.メソッド()で呼び出し'],
            'medium',
            20,
            ['javascript', 'object', 'method'],
            15,
            [
                ['', 'Hello!', 'メソッド呼び出し', true, false, 1]
            ]
        );

        // Exercise 16: Destructuring Array
        $this->createExercise(
            $jsLanguage,
            '配列の分割代入',
            '分割代入 - 配列',
            '配列[10, 20, 30]から最初の2つの要素をfirst, secondに分割代入し、firstを出力してください。',
            "let arr = [10, 20, 30];\n// 分割代入でfirst, secondに代入\n// firstを出力\n",
            "let arr = [10, 20, 30];\nlet [first, second] = arr;\nconsole.log(first);\n",
            ['[変数1, 変数2] = 配列', '順番に代入される'],
            'medium',
            20,
            ['javascript', 'destructuring', 'array'],
            16,
            [
                ['', '10', '最初の要素', true, false, 1]
            ]
        );

        // Exercise 17: Destructuring Object
        $this->createExercise(
            $jsLanguage,
            'オブジェクトの分割代入',
            '分割代入 - オブジェクト',
            'オブジェクト{ name: "Hanako", age: 30 }からnameを分割代入で取り出して出力してください。',
            "let person = { name: 'Hanako', age: 30 };\n// 分割代入でnameを取り出す\n// nameを出力\n",
            "let person = { name: 'Hanako', age: 30 };\nlet { name } = person;\nconsole.log(name);\n",
            ['{ プロパティ名 } = オブジェクト', 'プロパティ名と変数名が同じ'],
            'medium',
            20,
            ['javascript', 'destructuring', 'object'],
            17,
            [
                ['', 'Hanako', 'nameプロパティ', true, false, 1]
            ]
        );

        // Exercise 18: Spread Operator Array
        $this->createExercise(
            $jsLanguage,
            'スプレッド演算子で配列を結合',
            'スプレッド演算子 - 配列',
            '配列[1, 2, 3]と[4, 5, 6]をスプレッド演算子で結合し、カンマ区切りで出力してください。',
            "let arr1 = [1, 2, 3];\nlet arr2 = [4, 5, 6];\n// スプレッド演算子で結合\n",
            "let arr1 = [1, 2, 3];\nlet arr2 = [4, 5, 6];\nlet combined = [...arr1, ...arr2];\nconsole.log(combined.join(','));\n",
            ['...配列で展開', '[...arr1, ...arr2]で結合'],
            'medium',
            25,
            ['javascript', 'spread', 'array'],
            18,
            [
                ['', '1,2,3,4,5,6', '2つの配列を結合', true, false, 1]
            ]
        );

        // Exercise 19: Spread Operator Object
        $this->createExercise(
            $jsLanguage,
            'スプレッド演算子でオブジェクトを結合',
            'スプレッド演算子 - オブジェクト',
            'オブジェクト{ a: 1, b: 2 }と{ c: 3, d: 4 }をスプレッド演算子で結合し、結果のbプロパティを出力してください。',
            "let obj1 = { a: 1, b: 2 };\nlet obj2 = { c: 3, d: 4 };\n// スプレッド演算子で結合\n// bを出力\n",
            "let obj1 = { a: 1, b: 2 };\nlet obj2 = { c: 3, d: 4 };\nlet merged = { ...obj1, ...obj2 };\nconsole.log(merged.b);\n",
            ['{ ...obj1, ...obj2 }で結合', 'プロパティが展開される'],
            'medium',
            25,
            ['javascript', 'spread', 'object'],
            19,
            [
                ['', '2', 'bプロパティは2', true, false, 1]
            ]
        );

        // Exercise 20: String Methods - Split
        $this->createExercise(
            $jsLanguage,
            '文字列を分割',
            '文字列 - split',
            '文字列「apple,banana,cherry」をカンマで分割し、配列の長さを出力してください。',
            "let str = 'apple,banana,cherry';\n// カンマで分割して長さを出力\n",
            "let str = 'apple,banana,cherry';\nlet arr = str.split(',');\nconsole.log(arr.length);\n",
            ['split(区切り文字)で配列に変換', '長さはlengthで取得'],
            'easy',
            15,
            ['javascript', 'string', 'split'],
            20,
            [
                ['', '3', '3つの要素', true, false, 1]
            ]
        );

        // Exercise 21: String Methods - toUpperCase
        $this->createExercise(
            $jsLanguage,
            '文字列を大文字に変換',
            '文字列 - toUpperCase',
            '文字列「hello」を大文字に変換して出力してください。',
            "let str = 'hello';\n// 大文字に変換\n",
            "let str = 'hello';\nconsole.log(str.toUpperCase());\n",
            ['toUpperCase()で大文字に変換'],
            'easy',
            10,
            ['javascript', 'string', 'toUpperCase'],
            21,
            [
                ['', 'HELLO', '大文字変換', true, false, 1]
            ]
        );

        // Exercise 22: String Methods - Replace
        $this->createExercise(
            $jsLanguage,
            '文字列を置換',
            '文字列 - replace',
            '文字列「Hello World」の「World」を「JavaScript」に置換して出力してください。',
            "let str = 'Hello World';\n// Worldを置換\n",
            "let str = 'Hello World';\nlet newStr = str.replace('World', 'JavaScript');\nconsole.log(newStr);\n",
            ['replace(旧文字列, 新文字列)で置換'],
            'easy',
            15,
            ['javascript', 'string', 'replace'],
            22,
            [
                ['', 'Hello JavaScript', '置換後の文字列', true, false, 1]
            ]
        );

        // Exercise 23: String Methods - Includes
        $this->createExercise(
            $jsLanguage,
            '文字列が含まれるか確認',
            '文字列 - includes',
            '文字列「JavaScript is fun」に「Script」が含まれる場合は「Yes」、含まれない場合は「No」を出力してください。',
            "let str = 'JavaScript is fun';\n// Scriptが含まれるか確認\n",
            "let str = 'JavaScript is fun';\nif (str.includes('Script')) {\n    console.log('Yes');\n} else {\n    console.log('No');\n}\n",
            ['includes(文字列)で含まれるか確認', 'true/falseを返す'],
            'medium',
            20,
            ['javascript', 'string', 'includes'],
            23,
            [
                ['', 'Yes', 'Scriptが含まれる', true, false, 1]
            ]
        );

        // Exercise 24: Math Operations
        $this->createExercise(
            $jsLanguage,
            '最大値を求める',
            'Math - max',
            '配列[3, 7, 2, 9, 5]の最大値を求めて出力してください。',
            "let numbers = [3, 7, 2, 9, 5];\n// 最大値を求める\n",
            "let numbers = [3, 7, 2, 9, 5];\nconsole.log(Math.max(...numbers));\n",
            ['Math.max()で最大値', 'スプレッド演算子で展開'],
            'medium',
            20,
            ['javascript', 'math', 'max'],
            24,
            [
                ['', '9', '最大値は9', true, false, 1]
            ]
        );

        // Exercise 25: Math Operations - Round
        $this->createExercise(
            $jsLanguage,
            '小数を四捨五入',
            'Math - round',
            '数値3.7を四捨五入して出力してください。',
            "let num = 3.7;\n// 四捨五入\n",
            "let num = 3.7;\nconsole.log(Math.round(num));\n",
            ['Math.round()で四捨五入'],
            'easy',
            10,
            ['javascript', 'math', 'round'],
            25,
            [
                ['', '4', '3.7を四捨五入', true, false, 1]
            ]
        );

        // Exercise 26: JSON Parse
        $this->createExercise(
            $jsLanguage,
            'JSON文字列をパース',
            'JSON - parse',
            'JSON文字列\'{"name":"Taro","age":25}\'をパースして、nameプロパティを出力してください。',
            "let json = '{\"name\":\"Taro\",\"age\":25}';\n// パースしてnameを出力\n",
            "let json = '{\"name\":\"Taro\",\"age\":25}';\nlet obj = JSON.parse(json);\nconsole.log(obj.name);\n",
            ['JSON.parse()でオブジェクトに変換'],
            'medium',
            20,
            ['javascript', 'json', 'parse'],
            26,
            [
                ['', 'Taro', 'nameプロパティ', true, false, 1]
            ]
        );

        // Exercise 27: JSON Stringify
        $this->createExercise(
            $jsLanguage,
            'オブジェクトをJSON文字列に変換',
            'JSON - stringify',
            'オブジェクト{ name: "Hanako", age: 30 }をJSON文字列に変換して出力してください。',
            "let obj = { name: 'Hanako', age: 30 };\n// JSON文字列に変換\n",
            "let obj = { name: 'Hanako', age: 30 };\nlet json = JSON.stringify(obj);\nconsole.log(json);\n",
            ['JSON.stringify()で文字列に変換'],
            'medium',
            20,
            ['javascript', 'json', 'stringify'],
            27,
            [
                ['', '{"name":"Hanako","age":30}', 'JSON文字列', true, false, 1]
            ]
        );

        // Exercise 28: Try Catch
        $this->createExercise(
            $jsLanguage,
            'エラーハンドリング',
            'エラー処理 - try/catch',
            'try/catchを使ってJSON.parse("invalid")のエラーをキャッチし、「Error」を出力してください。',
            "// try/catchでエラーをキャッチ\n",
            "try {\n    JSON.parse('invalid');\n} catch (e) {\n    console.log('Error');\n}\n",
            ['try { 処理 } catch (e) { エラー処理 }', 'エラーがあればcatchブロック実行'],
            'medium',
            25,
            ['javascript', 'error', 'try-catch'],
            28,
            [
                ['', 'Error', 'エラーをキャッチ', true, false, 1]
            ]
        );

        // Exercise 29: Ternary Operator
        $this->createExercise(
            $jsLanguage,
            '三項演算子を使う',
            '三項演算子',
            '変数age = 18の時、20以上なら「Adult」、未満なら「Minor」を三項演算子で判定して出力してください。',
            "let age = 18;\n// 三項演算子で判定\n",
            "let age = 18;\nlet result = age >= 20 ? 'Adult' : 'Minor';\nconsole.log(result);\n",
            ['条件 ? 真の値 : 偽の値', '簡潔な条件式'],
            'easy',
            15,
            ['javascript', 'ternary', 'operator'],
            29,
            [
                ['', 'Minor', '18は20未満', true, false, 1]
            ]
        );

        // Exercise 30: ForEach
        $this->createExercise(
            $jsLanguage,
            'forEachで各要素を出力',
            '配列 - forEach',
            '配列[\"a\", \"b\", \"c\"]の各要素をforEachで改行して出力してください。',
            "let arr = ['a', 'b', 'c'];\n// forEachで各要素を出力\n",
            "let arr = ['a', 'b', 'c'];\narr.forEach(item => console.log(item));\n",
            ['forEach(要素 => 処理)', '各要素に対して処理を実行'],
            'easy',
            20,
            ['javascript', 'array', 'forEach'],
            30,
            [
                ['', "a\nb\nc", '各要素を改行出力', true, false, 1]
            ]
        );

        // Exercise 31: Find
        $this->createExercise(
            $jsLanguage,
            '配列から要素を検索',
            '配列 - find',
            '配列[5, 12, 8, 130, 44]から10より大きい最初の要素を見つけて出力してください。',
            "let numbers = [5, 12, 8, 130, 44];\n// 10より大きい最初の要素\n",
            "let numbers = [5, 12, 8, 130, 44];\nlet found = numbers.find(n => n > 10);\nconsole.log(found);\n",
            ['find()で条件に合う最初の要素', '見つからない場合はundefined'],
            'medium',
            20,
            ['javascript', 'array', 'find'],
            31,
            [
                ['', '12', '最初の10より大きい数', true, false, 1]
            ]
        );

        // Exercise 32: Some
        $this->createExercise(
            $jsLanguage,
            '配列に条件を満たす要素があるか',
            '配列 - some',
            '配列[1, 3, 5, 7, 9]に偶数が含まれる場合は「Yes」、含まれない場合は「No」を出力してください。',
            "let numbers = [1, 3, 5, 7, 9];\n// 偶数が含まれるか確認\n",
            "let numbers = [1, 3, 5, 7, 9];\nlet hasEven = numbers.some(n => n % 2 === 0);\nconsole.log(hasEven ? 'Yes' : 'No');\n",
            ['some()で条件を満たす要素があるか', 'true/falseを返す'],
            'medium',
            25,
            ['javascript', 'array', 'some'],
            32,
            [
                ['', 'No', '偶数が含まれない', true, false, 1]
            ]
        );

        // Exercise 33: Every
        $this->createExercise(
            $jsLanguage,
            '配列の全要素が条件を満たすか',
            '配列 - every',
            '配列[2, 4, 6, 8, 10]の全要素が偶数の場合は「Yes」、そうでない場合は「No」を出力してください。',
            "let numbers = [2, 4, 6, 8, 10];\n// 全要素が偶数か確認\n",
            "let numbers = [2, 4, 6, 8, 10];\nlet allEven = numbers.every(n => n % 2 === 0);\nconsole.log(allEven ? 'Yes' : 'No');\n",
            ['every()で全要素が条件を満たすか', 'true/falseを返す'],
            'medium',
            25,
            ['javascript', 'array', 'every'],
            33,
            [
                ['', 'Yes', '全て偶数', true, false, 1]
            ]
        );

        // Exercise 34: Set
        $this->createExercise(
            $jsLanguage,
            'Setで重複を削除',
            'Set - 重複削除',
            '配列[1, 2, 2, 3, 3, 3, 4, 4, 4, 4]からSetを使って重複を削除し、カンマ区切りで出力してください。',
            "let arr = [1, 2, 2, 3, 3, 3, 4, 4, 4, 4];\n// Setで重複削除\n",
            "let arr = [1, 2, 2, 3, 3, 3, 4, 4, 4, 4];\nlet unique = [...new Set(arr)];\nconsole.log(unique.join(','));\n",
            ['new Set(配列)で重複削除', 'スプレッド演算子で配列に戻す'],
            'medium',
            25,
            ['javascript', 'set', 'unique'],
            34,
            [
                ['', '1,2,3,4', '重複削除後', true, false, 1]
            ]
        );

        // Exercise 35: Default Parameter
        $this->createExercise(
            $jsLanguage,
            'デフォルト引数',
            '関数 - デフォルト引数',
            '関数greet(name = "Guest")を定義し、引数なしで呼び出して「Hello, Guest」を出力してください。',
            "// デフォルト引数を持つ関数を定義\n// 引数なしで呼び出す\n",
            "function greet(name = 'Guest') {\n    console.log(`Hello, ${name}`);\n}\ngreet();\n",
            ['引数 = デフォルト値', '引数が渡されない場合に使用'],
            'medium',
            20,
            ['javascript', 'function', 'default-parameter'],
            35,
            [
                ['', 'Hello, Guest', 'デフォルト引数', true, false, 1]
            ]
        );

        // Exercise 36: Rest Parameter
        $this->createExercise(
            $jsLanguage,
            '可変長引数',
            '関数 - 残余引数',
            '関数sum(...numbers)を定義して、sum(1, 2, 3, 4, 5)の合計を出力してください。',
            "// 残余引数を使った関数を定義\n// sum(1, 2, 3, 4, 5)を呼び出す\n",
            "function sum(...numbers) {\n    return numbers.reduce((a, b) => a + b, 0);\n}\nconsole.log(sum(1, 2, 3, 4, 5));\n",
            ['...引数名で可変長引数', '配列として受け取る'],
            'medium',
            25,
            ['javascript', 'function', 'rest-parameter'],
            36,
            [
                ['', '15', '1+2+3+4+5=15', true, false, 1]
            ]
        );

        // Exercise 37: Object Keys
        $this->createExercise(
            $jsLanguage,
            'オブジェクトのキー一覧',
            'Object - keys',
            'オブジェクト{ a: 1, b: 2, c: 3 }のキーを配列で取得し、カンマ区切りで出力してください。',
            "let obj = { a: 1, b: 2, c: 3 };\n// キーの配列を取得\n",
            "let obj = { a: 1, b: 2, c: 3 };\nlet keys = Object.keys(obj);\nconsole.log(keys.join(','));\n",
            ['Object.keys(オブジェクト)でキー配列', 'プロパティ名の配列を返す'],
            'medium',
            20,
            ['javascript', 'object', 'keys'],
            37,
            [
                ['', 'a,b,c', 'キーの配列', true, false, 1]
            ]
        );

        // Exercise 38: Object Values
        $this->createExercise(
            $jsLanguage,
            'オブジェクトの値一覧',
            'Object - values',
            'オブジェクト{ a: 1, b: 2, c: 3 }の値を配列で取得し、カンマ区切りで出力してください。',
            "let obj = { a: 1, b: 2, c: 3 };\n// 値の配列を取得\n",
            "let obj = { a: 1, b: 2, c: 3 };\nlet values = Object.values(obj);\nconsole.log(values.join(','));\n",
            ['Object.values(オブジェクト)で値配列', 'プロパティ値の配列を返す'],
            'medium',
            20,
            ['javascript', 'object', 'values'],
            38,
            [
                ['', '1,2,3', '値の配列', true, false, 1]
            ]
        );

        // Exercise 39: Chaining Methods
        $this->createExercise(
            $jsLanguage,
            'メソッドチェーン',
            '配列 - メソッドチェーン',
            '配列[1, 2, 3, 4, 5, 6]から偶数のみを抽出し、それぞれを2倍にして、カンマ区切りで出力してください。',
            "let numbers = [1, 2, 3, 4, 5, 6];\n// filterとmapをチェーン\n",
            "let numbers = [1, 2, 3, 4, 5, 6];\nlet result = numbers\n    .filter(n => n % 2 === 0)\n    .map(n => n * 2);\nconsole.log(result.join(','));\n",
            ['メソッドをドットで繋ぐ', 'filter().map()の順で処理'],
            'hard',
            30,
            ['javascript', 'array', 'chaining'],
            39,
            [
                ['', '4,8,12', '偶数を2倍', true, false, 1]
            ]
        );

        // Exercise 40: Callback Function
        $this->createExercise(
            $jsLanguage,
            'コールバック関数',
            '関数 - コールバック',
            '関数execute(callback)を定義し、callbackを実行して「Callback executed」を出力してください。',
            "// コールバックを受け取る関数を定義\n// 呼び出す\n",
            "function execute(callback) {\n    callback();\n}\nexecute(() => console.log('Callback executed'));\n",
            ['関数を引数として渡す', 'callback()で実行'],
            'medium',
            25,
            ['javascript', 'function', 'callback'],
            40,
            [
                ['', 'Callback executed', 'コールバック実行', true, false, 1]
            ]
        );

        $this->command->info('JavaScript exercises seeded successfully!');
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
