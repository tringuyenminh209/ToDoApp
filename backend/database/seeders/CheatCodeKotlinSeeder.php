<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeKotlinSeeder extends Seeder
{
    /**
     * Seed Kotlin cheat code data from doleaf
     * Reference: https://doleaf.com/kotlin
     */
    public function run(): void
    {
        // Create Kotlin Language
        $kotlinLanguage = CheatCodeLanguage::create([
            'name' => 'kotlin',
            'display_name' => 'Kotlin',
            'slug' => 'kotlin',
            'icon' => 'ic_kotlin',
            'color' => '#7F52FF',
            'description' => '使用方法、例などを含むKotlinのリファレンスチートシート。',
            'category' => 'programming',
            'popularity' => 88,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // Section 1: Introduction to Kotlin
        $section1 = $this->createSection($kotlinLanguage, 'Kotlin入門', 1, 'Kotlinの基本と導入', 'kotlin-introduction');

        $this->createExample($section1, $kotlinLanguage, 'main()', 1,
            "fun main() {\n  println(\"Greetings, Doleaf!\")\n  // Code goes here\n}",
            'main()関数はすべてのKotlinプログラムの開始点です',
            "Greetings, Doleaf!",
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, 'Print文', 2,
            "println(\"Greetings, earthling!\")\nprint(\"Take me to \")\nprint(\"your leader.\")\n\n/*\nPrint:\nGreetings, earthling!\nTake me to your leader.\n*/",
            'Printとprintln文',
            "Greetings, earthling!\nTake me to your leader.",
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, 'コメント', 3,
            "// this is a single line comment\n\n/*\nthis\nnote\nfor\nmany\n*/",
            'コメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, '実行順序', 4,
            "fun main() {\n  println(\"I will be printed first.\")\n  println(\"I will be printed second.\")\n  println(\"I will be printed third.\")\n}",
            'コードの実行順序',
            "I will be printed first.\nI will be printed second.\nI will be printed third.",
            'easy'
        );

        // Section 2: Data Types and Variables
        $section2 = $this->createSection($kotlinLanguage, 'データ型と変数', 2, '変数とデータ型', 'kotlin-data-types-variables');

        $this->createExample($section2, $kotlinLanguage, '可変変数', 1,
            "var age = 25\nage = 26",
            'varによる可変変数',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '不変変数', 2,
            "val goldenRatio = 1.618",
            'valによる不変変数',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '型推論', 3,
            "// The following variables are assigned a literal value inside double quotes\n// so the inferred type is String\n\nvar color = \"Purple\"",
            'Kotlinでの型推論',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '文字列連結', 4,
            "var streetAddress = \"123 Main St.\"\nvar cityState = \"Brooklyn, NY\"\n\nprintln(streetAddress + \" \" + cityState)\n// Print: 123 Main St. Brooklyn, NY",
            '文字列連結',
            "123 Main St. Brooklyn, NY",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '文字列テンプレート', 5,
            "var address = \"123 Main St.\"\nprintln(\"The address is \$address\")\n// prints: The address is 123 Main St.",
            '\$による文字列テンプレート',
            "The address is 123 Main St.",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '組み込みプロパティと関数', 6,
            "var monument = \"the Statue of Liberty\"\n\nprintln(monument.capitalize())\n// print: The Statue of Liberty\nprintln(monument.length)\n// print: 21",
            '文字列プロパティと関数',
            "The Statue of Liberty\n21",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '文字エスケープ', 7,
            "print(\"\\\"Excellent!\\\" I cried. \\\"Elementary,\\\" said he.\")\n\n// Print: \"Excellent!\" I cried. \"Elementary,\" said he.",
            '文字エスケープシーケンス',
            "\"Excellent!\" I cried. \"Elementary,\" said he.",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '算術演算子', 8,
            "5 + 7  // 12\n9 - 2   // 7\n8 * 4   // 32\n25 / 5  // 5\n31 % 2 // 1",
            '算術演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '演算順序', 9,
            "5 + 8 * 2 / 4 - 3 // 6\n3 + (4 + 4) / 2 // 7\n4 * 2 + 1 * 7    // 15\n3 + 18 / 2 * 1   // 12\n6 - 3 % 2 + 2   // 7",
            '演算子の優先順位',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, '拡張代入演算子', 10,
            "var batteryPercentage = 80\n\n// long syntax\nbatteryPercentage = batteryPercentage + 10\n\n// short syntax with augmented assignment operator\nbatteryPercentage += 10",
            '拡張代入演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'インクリメントとデクリメント演算子', 11,
            "var year = 2019\nyear++   // 2020\nyear--   // 2019",
            'インクリメントとデクリメント演算子',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Mathライブラリ', 12,
            "Math.pow(2.0, 3.0) // 8.0\nMath.min(6, 9)     // 6\nMath.max(10, 12)   // 12\nMath.round(13.7)  // 14",
            'Mathライブラリ関数',
            null,
            'easy'
        );

        // Section 3: Conditional Expression
        $section3 = $this->createSection($kotlinLanguage, '条件式', 3, '条件文', 'kotlin-conditionals');

        $this->createExample($section3, $kotlinLanguage, 'If式', 1,
            "var morning = true\n\nif (morning) {\n  println(\"Rise and shine!\")\n}\n// Print: Rise and shine!",
            '基本的なif式',
            "Rise and shine!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Else式', 2,
            "var rained = false\n\nif (rained) {\n  println(\"No need to water the plants today.\")\n} else {\n  println(\"The plant needs to be watered!\")\n}\n// print: The plant needs watering!",
            'If-else式',
            "The plant needs to be watered!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Else-If式', 3,
            "var age = 65\n\nif (age < 18) {\n  println(\"You are considered a minor\")\n} else if (age < 60) {\n  println(\"You are considered an adult\")\n} else {\n  println(\"You are considered senior\")\n}\n\n// print: you are considered senior",
            'Else-ifチェーン',
            "You are considered senior",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, '比較演算子', 4,
            "var myAge = 19\nvar sisterAge = 11\nvar cousinAge = 11\n\nmyAge > sisterAge  // true\nmyAge < cousinAge  // false\nmyAge >= cousinAge // true\nmyAge <= sisterAge // false",
            '比較演算子',
            null,
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, '論理演算子', 5,
            "var humid = true\nvar raining = true\nvar jacket = false\n\nprintln(!humid)\n// print: false\nprintln(jacket && raining)\n// print: false\nprintln(humid || raining)\n// print: true",
            '論理演算子',
            "false\nfalse\ntrue",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'AND演算子: &&', 6,
            "var humid = true\nvar raining = true\nvar shorts = false\nvar sunny = false\n\n// true AND true\nprintln(humid && raining) // true\n// true AND false\nprintln(humid && shorts)  // false\n// false AND true\nprintln(sunny && raining) // false\n// false AND false\nprintln(shorts && sunny)  // false",
            '論理AND演算子',
            "true\nfalse\nfalse\nfalse",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'OR演算子: ||', 7,
            "var late = true\nvar skipBreakfast = true\nvar underslept = false\nvar checkEmails = false\n\n// true OR true\nprintln(skipBreakfast || late) // true\n// true OR false\nprintln(late || checkEmails)   // true\n// false OR true\nprintln(underslept || late)    // true\n// false OR false\nprintln(checkEmails || underslept) // false",
            '論理OR演算子',
            "true\ntrue\ntrue\nfalse",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'NOT演算子', 8,
            "var hungry = true\nvar full = false\n\nprintln(!hungry) // false\nprintln(!full)   // true",
            '論理NOT演算子',
            "false\ntrue",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, '評価順序', 9,
            "!true && (false || true) // false\n/*\n(false || true) is evaluated first to return true.\nThen, evaluate !true && true and return the final result false\n*/\n\n!false && true || false // true\n/*\n!false is evaluated first to return true.\nThen true && true is evaluated, returning true.\nthen, true || evaluates to false and eventually returns true\n*/",
            '演算子の評価順序',
            null,
            'medium'
        );

        $this->createExample($section3, $kotlinLanguage, 'ネストされた条件', 10,
            "var studied = true\nvar wellRested = true\n\nif (wellRested) {\n  println(\"Good luck today!\")\n  if (studied) {\n    println(\"You should prepare for the exam!\")\n  } else {\n    println(\"Spend a few hours studying before the exam!\")\n  }\n}\n\n// Print: Good luck today!\n// print: You should be ready for the exam!",
            'ネストされたif文',
            "Good luck today!\nYou should prepare for the exam!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'When式', 11,
            "var grade = \"A\"\n\nwhen (grade) {\n  \"A\" -> println(\"Great job!\")\n  \"B\" -> println(\"Great job!\")\n  \"C\" -> println(\"You passed!\")\n  else -> println(\"Close! Be sure to prepare more next time!\")\n}\n// print: Great job!",
            'When式（switch風）',
            "Great job!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, '範囲演算子', 12,
            "var height = 46 // inches\n\nif (height in 1..53) {\n  println(\"Sorry, you must be at least 54 inches to ride the coaster\")\n}\n// Prints: Sorry, you must be at least 54 inches to ride the roller coaster",
            '範囲演算子（in）',
            "Sorry, you must be at least 54 inches to ride the coaster",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, '等価演算子', 13,
            "var myAge = 22\nvar sisterAge = 21\n\nmyAge == sisterAge // false\nmyAge != sisterAge // true",
            '等価演算子',
            null,
            'easy'
        );

        // Section 4: Collections
        $section4 = $this->createSection($kotlinLanguage, 'コレクション', 4, 'リスト、セット、マップ', 'kotlin-collections');

        $this->createExample($section4, $kotlinLanguage, '不変リスト', 1,
            "var programmingLanguages = listOf(\"C#\", \"Java\", \"Kotlin\", \"Ruby\")",
            '不変リストの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, '可変リスト', 2,
            "var fruits = mutableListOf(\"Orange\", \"Apple\", \"Banana\", \"Mango\")",
            '可変リストの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'リストへのアクセス', 3,
            "var cars = listOf(\"BMW\", \"Ferrari\", \"Volvo\", \"Tesla\")\n\nprintln(cars[2]) // Prints: Volvo",
            'リスト要素へのアクセス',
            "Volvo",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Size属性', 4,
            "var worldContinents = listOf(\"Asia\", \"Africa\", \"North America\", \"South America\", \"Antarctica\", \"Europe\", \"Australia\")\n\nprintln(worldContinents.size) // Prints: 7",
            'リストサイズの取得',
            "7",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'リスト操作', 5,
            "var seas = listOf(\"Black Sea\", \"Caribbean Sea\", \"North Sea\")\nprintln(seas.contains(\"North Sea\")) // Prints: true\n\n// The contains() function performs a read operation on any list and determines if the element exists\nseas.add(\"Baltic Sea\") // Error: cannot write to immutable list\n// The add() function can only be called on mutable lists, so the code above throws an error",
            'リスト操作',
            "true",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, '不変セット', 6,
            "var primaryColors = setOf(\"Red\", \"Blue\", \"Yellow\")",
            '不変セットの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, '可変セット', 7,
            "var womenInTech = mutableSetOf(\"Ada Lovelace\", \"Grace Hopper\", \"Radia Perlman\", \"Sister Mary Kenneth Keller\")",
            '可変セットの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'コレクション要素へのアクセス', 8,
            "var companies = setOf(\"Facebook\", \"Apple\", \"Netflix\", \"Google\")\n\nprintln(companies.elementAt(3))\n// Prints: Google\nprintln(companies.elementAt(4))\n// Returns and Error\nprintln(companies.elementAtOrNull(4))\n// Prints: null",
            'セット要素へのアクセス',
            "Google\nnull",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, '不変マップ', 9,
            "var averageTemp = mapOf(\"winter\" to 35,  \"spring\" to 60,  \"summer\" to 85, \"fall\" to 55)",
            '不変マップの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, '可変マップ', 10,
            "var europeanDomains = mutableMapOf(\"Germany\" to \"de\", \"Slovakia\" to \"sk\", \"Hungary\" to \"hu\", \"Norway\" to \"no\")",
            '可変マップの作成',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'マップのキーと値の取得', 11,
            "var oscarWinners = mutableMapOf(\"Parasite\" to \"Bong Joon-ho\", \"Green Book\" to \"Jim Burke\", \"The Shape Of Water\" to \"Guillermo del Toro\")\n\nprintln(oscarWinners.keys)\n// Prints: [Parasite, Green Book, The Shape Of Water]\n\nprintln(oscarWinners.values)\n// Prints: [Bong Joon-ho, Jim Burke, Guillermo del Toro]\nprintln(oscarWinners[\"Parasite\"])\n// Prints: Bong Joon-ho",
            'マップのキーと値へのアクセス',
            "[Parasite, Green Book, The Shape Of Water]\n[Bong Joon-ho, Jim Burke, Guillermo del Toro]\nBong Joon-ho",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'マップエントリの追加と削除', 12,
            "var worldCapitals = mutableMapOf(\"United States\" to \"Washington D.C.\", \"Germany\" to \"Berlin\", \"Mexico\" to \"Mexico City\", \"France\" to \"Paris\")\n\nworldCapitals.put(\"Brazil\", \"Brasilia\")\nprintln(worldCapitals)\n// Prints: {United States=Washington D.C., Germany=Berlin, Mexico=Mexico City, France=Paris, Brazil=Brasilia}\n\nworldCapitals.remove(\"Germany\")\nprintln(worldCapitals)\n// Prints: {United States=Washington D.C., Mexico=Mexico City, France=Paris, Brazil=Brasilia}",
            'マップエントリの追加と削除',
            "{United States=Washington D.C., Germany=Berlin, Mexico=Mexico City, France=Paris, Brazil=Brasilia}\n{United States=Washington D.C., Mexico=Mexico City, France=Paris, Brazil=Brasilia}",
            'easy'
        );

        // Section 5: Function
        $section5 = $this->createSection($kotlinLanguage, '関数', 5, '関数の定義と使用', 'kotlin-functions');

        $this->createExample($section5, $kotlinLanguage, '関数', 1,
            "fun greet() {\n  println(\"Hey there!\")\n}\n\nfun main() {\n  //Function call\n  greet() //Prints: Hey there!\n}",
            '基本的な関数定義',
            "Hey there!",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, '関数パラメータ', 2,
            "fun birthday(name: String, age: Int) {\n   println(\"Happy birthday \$name! You turn \$age today!\")\n}\n\nfun main() {\n  birthday(\"Oscar\", 26) \n  //Prints: Happy birthday Oscar! You turn 26 today!\n  birthday(\"Amarah\", 30) \n  //Prints: Happy birthday Amarah! You turn 30 today!\n}",
            'パラメータ付き関数',
            "Happy birthday Oscar! You turn 26 today!\nHappy birthday Amarah! You turn 30 today!",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'デフォルトパラメータ', 3,
            "fun favoriteLanguage(name: String, language: String = \"Kotlin\") {\n  println(\"Hello, \$name. Your favorite programming language is \$language\")  \n}\n\nfun main() {\n  favoriteLanguage(\"Manon\") \n  //Prints: Hello, Manon. Your favorite programming language is Kotlin\n  \n  favoriteLanguage(\"Lee\", \"Java\") \n  //Prints: Hello, Lee. Your favorite programming language is Java\n}",
            'デフォルト関数パラメータ',
            "Hello, Manon. Your favorite programming language is Kotlin\nHello, Lee. Your favorite programming language is Java",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, '名前付きパラメータ', 4,
            "fun findMyAge(currentYear: Int, birthYear: Int) {\n   var myAge = currentYear - birthYear\n   println(\"I am \$myAge years old.\")\n}\n\nfun main() {\n  findMyAge(currentYear = 2020, birthYear = 1995)\n  //Prints: I am 25 years old.\n  findMyAge(birthYear = 1920, currentYear = 2020)\n  //Prints: I am 100 years old.\n}",
            '名前付き関数パラメータ',
            "I am 25 years old.\nI am 100 years old.",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Return文', 5,
            "//Return type is declared outside the parentheses\nfun getArea(length: Int, width: Int): Int {\n  var area = length * width\n\n  //return statement\n  return area\n}\n\nfun main() {\n  var myArea = getArea(10, 8)\n  println(\"The area is \$myArea.\")\n  //Prints: The area is 80.\n}",
            '戻り値の型付き関数',
            "The area is 80.",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, '単一式関数', 6,
            "fun fullName(firstName: String, lastName: String) = \"\$firstName \$lastName\"\n\nfun main() {\n  println(fullName(\"Ariana\", \"Ortega\"))\n  //Prints: Ariana Ortega\n  println(fullName(\"Kai\", \"Gittens\"))\n  //Prints: Kai Gittens\n}",
            '単一式関数',
            "Ariana Ortega\nKai Gittens",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, '関数リテラル', 7,
            "fun main() {\n  //Anonymous Function:\n  var getProduct = fun(num1: Int, num2: Int): Int {\n    return num1 * num2\n  }\n  println(getProduct(8, 3))\n  //Prints: 24\n  //Lambda Expression\n  var getDifference = { num1: Int, num2: Int -> num1 - num2 }\n  println(getDifference(10, 3))\n  //Prints: 7\n}",
            '無名関数とラムダ式',
            "24\n7",
            'medium'
        );

        // Section 6: Class
        $section6 = $this->createSection($kotlinLanguage, 'クラス', 6, 'オブジェクト指向プログラミング', 'kotlin-classes');

        $this->createExample($section6, $kotlinLanguage, 'クラスの例', 1,
            "//class with properties containing default values\nclass Student {\n  var name = \"Lucia\"\n  var semester = \"Fall\"\n  var gpa = 3.95\n}\n\n//shorthand syntax without class body\nclass Student",
            '基本的なクラス定義',
            null,
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, 'クラスインスタンス', 2,
            "// Class\nclass Student {\n  var name = \"Lucia\"\n  var semester = \"Fall\"\n  var gpa = 3.95\n}\n\nfun main() {\n  var student = Student()   \n  // Instance\n  println(student.name)     \n  // Prints: Lucia\n  println(student.semester) \n  // Prints: Fall\n  println(student.gpa)      \n  // Prints: 3.95  \n}",
            'クラスインスタンスの作成',
            "Lucia\nFall\n3.95",
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, 'プライマリコンストラクタ', 3,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) \n\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022) \n  println(student.name)     \n  //Prints: Lucia\n  println(student.gpa)\n  //Prints: 3.95\n  println(student.semester) \n  //Prints: Fall\n  println(student.estimatedGraduationYear) \n  //Prints: 2022\n}",
            'プライマリコンストラクタ',
            "Lucia\n3.95\nFall\n2022",
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, '初期化ブロック', 4,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) {\n  init {\n    println(\"\$name has \${estimatedGraduationYear - 2020} years left in college.\")\n  }\n}\n\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022)\n  //Prints: Lucia has 2 years left in college. \n}",
            '初期化ブロック',
            "Lucia has 2 years left in college.",
            'medium'
        );

        $this->createExample($section6, $kotlinLanguage, 'メンバー関数', 5,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) {\n\n  init {\n    println(\"\$name has \${estimatedGraduationYear - 2020} years left in college.\")\n  }\n\n  //member function\n  fun calculateLetterGrade(): String {\n    return when {\n      gpa >= 3.0 -> \"A\"\n      gpa >= 2.7 -> \"B\"\n      gpa >= 1.7 -> \"C\"\n      gpa >= 1.0 -> \"D\"\n      else -> \"E\"\n    }\n  }\n}\n\n//When the instance is created and the function is called, the when expression will be executed and return the letter grade\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022)\n  //Prints: Lucia has 2 years left in college.\n  println(\"\${student.name}'s letter grade is \${student.calculateLetterGrade()}.\")\n  //Prints: Lucia's letter grade is A.\n}",
            'クラスのメンバー関数',
            "Lucia has 2 years left in college.\nLucia's letter grade is A.",
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($kotlinLanguage);
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
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'constructor') || str_contains($descLower, 'oop')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'list') || str_contains($titleLower, 'set') || str_contains($titleLower, 'map') || str_contains($titleLower, 'collection')) {
            $tags[] = 'collection';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'lambda') || str_contains($titleLower, 'anonymous')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'string') || str_contains($titleLower, 'template')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'when')) {
            $tags[] = 'conditional';
        }
        if (str_contains($titleLower, 'variable') || str_contains($titleLower, 'val') || str_contains($titleLower, 'var')) {
            $tags[] = 'variable';
        }
        if (str_contains($titleLower, 'operator') || str_contains($titleLower, 'logical') || str_contains($titleLower, 'arithmetic')) {
            $tags[] = 'operator';
        }
        if (str_contains($titleLower, 'mutable') || str_contains($titleLower, 'immutable')) {
            $tags[] = 'immutability';
        }

        // Add basic tags
        $tags[] = 'kotlin';
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

