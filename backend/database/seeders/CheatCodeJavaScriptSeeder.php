<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeJavaScriptSeeder extends Seeder
{
    /**
     * Seed JavaScript cheat code data from quickref.me
     * Reference: https://quickref.me/javascript
     */
    public function run(): void
    {
        // Create JavaScript Language
        $jsLanguage = CheatCodeLanguage::create([
            'name' => 'javascript',
            'display_name' => 'JavaScript',
            'slug' => 'javascript',
            'icon' => 'ic_js',
            'color' => '#F7DF1E',
            'description' => 'JavaScriptは、軽量でインタープリタ型のプログラミング言語です。初心者向けの完全なクイックリファレンス。',
            'category' => 'programming',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($jsLanguage, 'はじめに', 1, 'JavaScriptの基本と導入', 'getting-started');

        $this->createExample($section1, $jsLanguage, 'Console', 1,
            "// => Hello world!\nconsole.log('Hello world!');\n\n// => Hello QuickRef.ME\nconsole.warn('hello %s', 'QuickRef.ME');\n\n// Prints error message to stderr\nconsole.error(new Error('Oops!'));",
            '出力用のコンソールメソッド',
            "Hello world!\nHello QuickRef.ME\nError: Oops!",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '数値', 2,
            "let amount = 6;\nlet price = 4.99;",
            '数値の宣言',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '変数', 3,
            "let x = null;\nlet name = \"Tammy\";\nconst found = false;\n\n// => Tammy, false, null\nconsole.log(name, found, x);\n\nvar a;\nconsole.log(a); // => undefined",
            'let、const、varによる変数宣言',
            "Tammy false null\nundefined",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '文字列', 4,
            "let single = 'Wheres my bandit hat?';\nlet double = \"Wheres my bandit hat?\";\n\n// => 21\nconsole.log(single.length);",
            '文字列の宣言とプロパティ',
            "21",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '算術演算子', 5,
            "5 + 5 = 10     // Addition\n10 - 5 = 5     // Subtraction\n5 * 10 = 50    // Multiplication\n10 / 5 = 2     // Division\n10 % 5 = 0     // Modulo",
            '基本的な算術演算子',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'コメント', 6,
            "// This line will denote a comment\n\n/*  \nThe below configuration must be \nchanged before deployment. \n*/",
            'コメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '代入演算子', 7,
            "let number = 100;\n\n// Both statements will add 10\nnumber = number + 10;\nnumber += 10;\n\nconsole.log(number); \n// => 120",
            '代入演算子',
            "120",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, '文字列補間', 8,
            "let age = 7;\n\n// String concatenation\n'Tommy is ' + age + ' years old.';\n\n// String interpolation\n`Tommy is \${age} years old.`;",
            '文字列連結とテンプレートリテラル',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'letキーワード', 9,
            "let count; \nconsole.log(count); // => undefined\ncount = 10;\nconsole.log(count); // => 10",
            'letキーワードの使用',
            "undefined\n10",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'constキーワード', 10,
            "const numberOfColumns = 4;\n\n// TypeError: Assignment to constant...\nnumberOfColumns = 8;",
            'constキーワード - 不変のバインディング',
            null,
            'easy'
        );

        // Section 2: JavaScript Conditionals
        $section2 = $this->createSection($jsLanguage, 'JavaScript条件分岐', 2, '条件文と演算子', 'javascript-conditionals');

        $this->createExample($section2, $jsLanguage, 'if文', 1,
            "const isMailSent = true;\n\nif (isMailSent) {\n  console.log('Mail sent to recipient');\n}",
            '基本的なif文',
            "Mail sent to recipient",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '三項演算子', 2,
            "var x=1;\n\n// => true\nresult = (x == 1) ? true : false;",
            '三項条件演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '論理演算子 ||', 3,
            "true || false;       // true\n10 > 5 || 10 > 20;   // true\nfalse || false;      // false\n10 > 100 || 10 > 20; // false",
            '論理OR演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '論理演算子 &&', 4,
            "true && true;        // true\n1 > 2 && 2 > 1;      // false\ntrue && false;       // false\n4 === 4 && 3 > 1;    // true",
            '論理AND演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '比較演算子', 5,
            "1 > 3                // false\n3 > 1                // true\n250 >= 250           // true\n1 === 1              // true\n1 === 2              // false\n1 === '1'            // false",
            '比較演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '論理演算子 !', 6,
            "let lateToWork = true;\nlet oppositeValue = !lateToWork;\n\n// => false\nconsole.log(oppositeValue);",
            '論理NOT演算子',
            "false",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Null合体演算子 ??', 7,
            "null ?? 'I win';           //  'I win'\nundefined ?? 'Me too';     //  'Me too'\n\nfalse ?? 'I lose'          //  false\n0 ?? 'I lose again'        //  0\n'' ?? 'Damn it'            //  ''",
            'Null合体演算子',
            null,
            'medium'
        );

        $this->createExample($section2, $jsLanguage, 'else if', 8,
            "const size = 10;\n\nif (size > 100) {\n  console.log('Big');\n} else if (size > 20) {\n  console.log('Medium');\n} else if (size > 4) {\n  console.log('Small');\n} else {\n  console.log('Tiny');\n}\n// Print: Small",
            'else ifチェーン',
            "Small",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'switch文', 9,
            "const food = 'salad';\n\nswitch (food) {\n  case 'oyster':\n    console.log('The taste of the sea');\n    break;\n  case 'pizza':\n    console.log('A delicious pie');\n    break;\n  default:\n    console.log('Enjoy your meal');\n}",
            'switch文',
            "Enjoy your meal",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '== vs ===', 10,
            "0 == false   // true\n0 === false  // false, different type\n1 == \"1\"     // true,  automatic type conversion \n1 === \"1\"    // false, different type\nnull == undefined  // true\nnull === undefined // false\n'0' == false       // true\n'0' === false      // false",
            '等価演算子の比較',
            null,
            'easy'
        );

        // Section 3: JavaScript Functions
        $section3 = $this->createSection($jsLanguage, 'JavaScript関数', 3, '関数の定義と使用', 'javascript-functions');

        $this->createExample($section3, $jsLanguage, '関数', 1,
            "// Defining the function:\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// Calling the function:\nsum(3, 6); // 9",
            '関数宣言',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '無名関数', 2,
            "// Named function\nfunction rocketToMars() {\n  return 'BOOM!';\n}\n\n// Anonymous function\nconst rocketToMars = function() {\n  return 'BOOM!';\n}",
            '名前付き関数と無名関数',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'アロー関数 - 2つの引数', 3,
            "const sum = (param1, param2) => { \n  return param1 + param2; \n}; \nconsole.log(sum(2,5)); // => 7",
            '2つのパラメータを持つアロー関数',
            "7",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'アロー関数 - 引数なし', 4,
            "const printHello = () => { \n  console.log('hello'); \n}; \nprintHello(); // => hello",
            'パラメータなしのアロー関数',
            "hello",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'アロー関数 - 単一引数', 5,
            "const checkWeight = weight => { \n  console.log(`Weight : \${weight}`); \n}; \ncheckWeight(25); // => Weight : 25",
            '単一パラメータのアロー関数',
            "Weight : 25",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '簡潔なアロー関数', 6,
            "const multiply = (a, b) => a * b; \n// => 60 \nconsole.log(multiply(2, 30));",
            '簡潔なアロー関数構文',
            "60",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'returnキーワード', 7,
            "// With return\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// The function doesn't output the sum\nfunction sum(num1, num2) {\n  num1 + num2;\n}",
            'returnキーワードの重要性',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '関数の呼び出し', 8,
            "// Defining the function\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// Calling the function\nsum(2, 4); // 6",
            '関数の呼び出し',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '関数式', 9,
            "const dog = function() {\n  return 'Woof!';\n}",
            '関数式',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '関数パラメータ', 10,
            "// The parameter is name\nfunction sayHello(name) {\n  return `Hello, \${name}!`;\n}",
            '関数パラメータ',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, '関数宣言', 11,
            "function add(num1, num2) {\n  return num1 + num2;\n}",
            '関数宣言構文',
            null,
            'easy'
        );

        // Section 4: JavaScript Scope
        $section4 = $this->createSection($jsLanguage, 'JavaScriptスコープ', 4, '変数のスコープと可視性', 'javascript-scope');

        $this->createExample($section4, $jsLanguage, 'スコープ', 1,
            "function myFunction() { \n  \n  var pizzaName = \"Margarita\";\n  // Code here can use pizzaName\n  \n}\n\n// Code here can't use pizzaName",
            '関数スコープ',
            null,
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'ブロックスコープ変数', 2,
            "const isLoggedIn = true;\n\nif (isLoggedIn == true) {\n  const statusMessage = 'Logged in.';\n}\n\n// Uncaught ReferenceError...\nconsole.log(statusMessage);",
            'constによるブロックスコープ',
            null,
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'グローバル変数', 3,
            "// Variable declared globally\nconst color = 'blue';\n\nfunction printColor() {\n  console.log(color);\n}\n\nprintColor(); // => blue",
            'グローバルスコープ',
            "blue",
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'let vs var', 4,
            "for (let i = 0; i < 3; i++) {\n  // This is the Max Scope for 'let'\n  // i accessible ✔️\n}\n// i not accessible ❌\n\nfor (var i = 0; i < 3; i++) {\n  // i accessible ✔️\n}\n// i accessible ✔️",
            'letとvarのスコープの違い',
            null,
            'medium'
        );

        $this->createExample($section4, $jsLanguage, 'クロージャ付きループ', 5,
            "// Prints 3 thrice, not what we meant.\nfor (var i = 0; i < 3; i++) {\n  setTimeout(_ => console.log(i), 10);\n}\n\n// Prints 0, 1 and 2, as expected.\nfor (let j = 0; j < 3; j++) { \n  setTimeout(_ => console.log(j), 10);\n}",
            'varとletでのクロージャの動作',
            "3\n3\n3\n0\n1\n2",
            'medium'
        );

        // Section 5: JavaScript Arrays
        $section5 = $this->createSection($jsLanguage, 'JavaScript配列', 5, '配列操作とメソッド', 'javascript-arrays');

        $this->createExample($section5, $jsLanguage, '配列', 1,
            "const fruits = [\"apple\", \"orange\", \"banana\"];\n\n// Different data types\nconst data = [1, 'chicken', false];",
            '配列の宣言',
            null,
            'easy'
        );

        $this->createExample($section5, $jsLanguage, 'プロパティ .length', 2,
            "const numbers = [1, 2, 3, 4];\n\nconsole.log(numbers.length); // => 4",
            '配列のlengthプロパティ',
            "4",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '要素へのアクセス', 3,
            "const fruits = [\"apple\", \"orange\", \"banana\"];\n\nconsole.log(fruits[0]); // => apple\nconsole.log(fruits[1]); // => orange",
            'インデックスによる配列要素へのアクセス',
            "apple\norange",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.push() メソッド', 4,
            "const items = ['pencil', 'notebook', 'eraser'];\nitems.push('backpack');\nconsole.log(items);",
            '配列の末尾に要素を追加',
            "['pencil', 'notebook', 'eraser', 'backpack']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.pop() メソッド', 5,
            "const items = ['pencil', 'notebook', 'eraser'];\nconst removedItem = items.pop();\nconsole.log(removedItem); // => eraser",
            '配列の最後の要素を削除',
            "eraser",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.shift() メソッド', 6,
            "const items = ['pencil', 'notebook', 'eraser'];\nconst firstItem = items.shift();\nconsole.log(firstItem); // => pencil",
            '配列の最初の要素を削除',
            "pencil",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.unshift() メソッド', 7,
            "const items = ['pencil', 'notebook'];\nitems.unshift('eraser');\nconsole.log(items);",
            '配列の先頭に要素を追加',
            "['eraser', 'pencil', 'notebook']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.slice() メソッド', 8,
            "const fruits = ['apple', 'orange', 'banana', 'mango'];\nconst citrus = fruits.slice(1, 3);\nconsole.log(citrus); // => ['orange', 'banana']",
            '配列の一部を抽出',
            "['orange', 'banana']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.splice() メソッド', 9,
            "const fruits = ['apple', 'orange', 'banana'];\nfruits.splice(1, 1, 'mango');\nconsole.log(fruits); // => ['apple', 'mango', 'banana']",
            '要素の削除と挿入',
            "['apple', 'mango', 'banana']",
            'medium'
        );

        $this->createExample($section5, $jsLanguage, '.forEach() メソッド', 10,
            "const fruits = ['apple', 'orange', 'banana'];\nfruits.forEach(fruit => {\n  console.log(fruit);\n});",
            '配列要素の反復処理',
            "apple\norange\nbanana",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.map() メソッド', 11,
            "const numbers = [1, 2, 3];\nconst doubled = numbers.map(num => num * 2);\nconsole.log(doubled); // => [2, 4, 6]",
            '配列要素の変換',
            "[2, 4, 6]",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.filter() メソッド', 12,
            "const numbers = [1, 2, 3, 4, 5];\nconst evens = numbers.filter(num => num % 2 === 0);\nconsole.log(evens); // => [2, 4]",
            '配列要素のフィルタリング',
            "[2, 4]",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.reduce() メソッド', 13,
            "const numbers = [1, 2, 3, 4];\nconst sum = numbers.reduce((acc, num) => acc + num, 0);\nconsole.log(sum); // => 10",
            '配列を単一の値に縮約',
            "10",
            'medium'
        );

        // Section 6: JavaScript Objects
        $section6 = $this->createSection($jsLanguage, 'JavaScriptオブジェクト', 6, 'オブジェクトの作成と操作', 'javascript-objects');

        $this->createExample($section6, $jsLanguage, 'オブジェクトリテラル', 1,
            "const person = {\n  name: 'John',\n  age: 30,\n  city: 'New York'\n};",
            'オブジェクトリテラル構文',
            null,
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'プロパティへのアクセス', 2,
            "const person = { name: 'John', age: 30 };\n\nconsole.log(person.name);     // => John\nconsole.log(person['age']);   // => 30",
            'オブジェクトプロパティへのアクセス',
            "John\n30",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'プロパティの追加', 3,
            "const person = { name: 'John' };\nperson.age = 30;\nperson['city'] = 'New York';\nconsole.log(person);",
            'オブジェクトへのプロパティの追加',
            "{ name: 'John', age: 30, city: 'New York' }",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'オブジェクトメソッド', 4,
            "const person = {\n  name: 'John',\n  greet: function() {\n    return `Hello, I'm \${this.name}`;\n  }\n};\nconsole.log(person.greet());",
            'オブジェクトメソッド',
            "Hello, I'm John",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Object.keys()', 5,
            "const person = { name: 'John', age: 30 };\nconst keys = Object.keys(person);\nconsole.log(keys); // => ['name', 'age']",
            'オブジェクトのキーを取得',
            "['name', 'age']",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Object.values()', 6,
            "const person = { name: 'John', age: 30 };\nconst values = Object.values(person);\nconsole.log(values); // => ['John', 30]",
            'オブジェクトの値を取得',
            "['John', 30]",
            'easy'
        );

        // Section 7: JavaScript Loops
        $section7 = $this->createSection($jsLanguage, 'JavaScriptループ', 7, 'ループ構造', 'javascript-loops');

        $this->createExample($section7, $jsLanguage, 'forループ', 1,
            "for (let i = 0; i < 5; i++) {\n  console.log(i);\n}",
            '基本的なforループ',
            "0\n1\n2\n3\n4",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'for...ofループ', 2,
            "const fruits = ['apple', 'orange', 'banana'];\nfor (const fruit of fruits) {\n  console.log(fruit);\n}",
            '反復可能オブジェクト用のfor...ofループ',
            "apple\norange\nbanana",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'for...inループ', 3,
            "const person = { name: 'John', age: 30 };\nfor (const key in person) {\n  console.log(key, person[key]);\n}",
            'オブジェクト用のfor...inループ',
            "name John\nage 30",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'whileループ', 4,
            "let i = 0;\nwhile (i < 5) {\n  console.log(i);\n  i++;\n}",
            'whileループ',
            "0\n1\n2\n3\n4",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'do...whileループ', 5,
            "let i = 0;\ndo {\n  console.log(i);\n  i++;\n} while (i < 5);",
            'do...whileループ',
            "0\n1\n2\n3\n4",
            'easy'
        );

        // Section 8: JavaScript Classes
        $section8 = $this->createSection($jsLanguage, 'JavaScriptクラス', 8, 'ES6クラスとOOP', 'javascript-classes');

        $this->createExample($section8, $jsLanguage, 'クラス宣言', 1,
            "class Person {\n  constructor(name) {\n    this.name = name;\n  }\n  \n  greet() {\n    return `Hello, I'm \${this.name}`;\n  }\n}\n\nconst john = new Person('John');\nconsole.log(john.greet());",
            'クラス宣言とインスタンス化',
            "Hello, I'm John",
            'medium'
        );

        $this->createExample($section8, $jsLanguage, 'クラス継承', 2,
            "class Animal {\n  constructor(name) {\n    this.name = name;\n  }\n}\n\nclass Dog extends Animal {\n  bark() {\n    return 'Woof!';\n  }\n}\n\nconst dog = new Dog('Buddy');\nconsole.log(dog.name);",
            'extendsによるクラス継承',
            "Buddy",
            'medium'
        );

        // Section 9: JavaScript Promises
        $section9 = $this->createSection($jsLanguage, 'JavaScript Promise', 9, 'Promiseベースの非同期プログラミング', 'javascript-promises');

        $this->createExample($section9, $jsLanguage, 'Promiseの作成', 1,
            "const promise = new Promise((resolve, reject) => {\n  if (res) {\n    resolve('Resolved!');\n  }\n  else {\n    reject(Error('Error'));\n  }\n});\n\npromise.then((res) => console.log(res), (err) => console.error(err));",
            'Promiseの作成と使用',
            null,
            'medium'
        );

        $this->createExample($section9, $jsLanguage, '.then() メソッド', 2,
            "const promise = new Promise((resolve, reject) => {    \n  setTimeout(() => {\n    resolve('Result');\n  }, 200);\n});\n\npromise.then((res) => {\n  console.log(res);\n}, (err) => {\n  console.error(err);\n});",
            'Promiseのthenメソッド',
            "Result",
            'medium'
        );

        $this->createExample($section9, $jsLanguage, '.catch() メソッド', 3,
            "const promise = new Promise((resolve, reject) => {  \n  setTimeout(() => {\n    reject(Error('Promise Rejected Unconditionally.'));\n  }, 1000);\n});\n\npromise.catch((err) => {\n  console.error(err);\n});",
            'Promiseのcatchメソッド',
            "Error: Promise Rejected Unconditionally.",
            'medium'
        );

        $this->createExample($section9, $jsLanguage, 'Promise.all()', 4,
            "const promise1 = new Promise((resolve, reject) => {\n  setTimeout(() => {\n    resolve(3);\n  }, 300);\n});\nconst promise2 = new Promise((resolve, reject) => {\n  setTimeout(() => {\n    resolve(2);\n  }, 200);\n});\n\nPromise.all([promise1, promise2]).then((res) => {\n  console.log(res[0]);\n  console.log(res[1]);\n});",
            '複数のPromise用のPromise.all',
            "3\n2",
            'medium'
        );

        // Section 10: JavaScript Async-Await
        $section10 = $this->createSection($jsLanguage, 'JavaScript Async-Await', 10, 'Promise用のAsync/await構文', 'javascript-async-await');

        $this->createExample($section10, $jsLanguage, 'Async関数', 1,
            "function helloWorld() {\n  return new Promise(resolve => {\n    setTimeout(() => {\n      resolve('Hello World!');\n    }, 2000);\n  });\n}\n\nasync function msg() {\n  const msg = await helloWorld();\n  console.log('Message:', msg);\n}\n\nmsg(); // Message: Hello World! <-- after 2 seconds",
            'Async/awaitの基本',
            "Message: Hello World!",
            'medium'
        );

        $this->createExample($section10, $jsLanguage, 'エラーハンドリング', 2,
            "let json = '{ \"age\": 30 }'; // incomplete data\n\ntry {\n  let user = JSON.parse(json); // <-- no errors\n  console.log( user.name ); // no name!\n} catch (e) {\n  console.error( \"Invalid JSON data!\" );\n}",
            'Try-catchエラーハンドリング',
            "undefined",
            'easy'
        );

        // Section 11: JavaScript Requests
        $section11 = $this->createSection($jsLanguage, 'JavaScriptリクエスト', 11, 'HTTPリクエストとfetch API', 'javascript-requests');

        $this->createExample($section11, $jsLanguage, 'JSON', 1,
            "const jsonObj = {\n  \"name\": \"Rick\",\n  \"id\": \"11A\",\n  \"level\": 4  \n};",
            'JSONオブジェクト構文',
            null,
            'easy'
        );

        $this->createExample($section11, $jsLanguage, 'fetch API', 2,
            "fetch(url, {\n    method: 'POST',\n    headers: {\n      'Content-type': 'application/json',\n      'apikey': apiKey\n    },\n    body: data\n  }).then(response => {\n    if (response.ok) {\n      return response.json();\n    }\n    throw new Error('Request failed!');\n  }, networkError => {\n    console.log(networkError.message)\n  })",
            'Fetch API POSTリクエスト',
            null,
            'medium'
        );

        $this->createExample($section11, $jsLanguage, 'JSONフォーマット', 3,
            "fetch('url-that-returns-JSON')\n.then(response => response.json())\n.then(jsonResponse => {\n  console.log(jsonResponse);\n});",
            'JSONレスポンス付きFetch API',
            null,
            'easy'
        );

        $this->createExample($section11, $jsLanguage, 'async await構文', 4,
            "const getSuggestions = async () => {\n  const wordQuery = inputField.value;\n  const endpoint = `\${url}\${queryParams}\${wordQuery}`;\n  try{\n    const response = await fetch(endpoint, {cache: 'no-cache'});\n    if(response.ok){\n      const jsonResponse = await response.json()\n    }\n  }\n  catch(error){\n    console.log(error)\n  }\n}",
            'async/await付きFetch API',
            null,
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($jsLanguage);
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
        $descLower = $description ? strtolower($description) : '';

        // Add tags based on title and description
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'object') || str_contains($descLower, 'oop')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array') || str_contains($descLower, 'array')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string') || str_contains($descLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'arrow') || str_contains($descLower, 'function')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'promise') || str_contains($titleLower, 'async') || str_contains($titleLower, 'await')) {
            $tags[] = 'async';
        }
        if (str_contains($titleLower, 'loop') || str_contains($titleLower, 'for') || str_contains($titleLower, 'while')) {
            $tags[] = 'loop';
        }
        if (str_contains($titleLower, 'fetch') || str_contains($titleLower, 'request') || str_contains($titleLower, 'http')) {
            $tags[] = 'http';
        }
        if (str_contains($titleLower, 'scope') || str_contains($titleLower, 'let') || str_contains($titleLower, 'var') || str_contains($titleLower, 'const')) {
            $tags[] = 'scope';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'switch')) {
            $tags[] = 'conditional';
        }

        // Add basic tags
        $tags[] = 'javascript';
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

