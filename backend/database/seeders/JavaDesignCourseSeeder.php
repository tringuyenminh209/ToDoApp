<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class JavaDesignCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Javaプログラミング設計演習 - OOPとデザインパターンの完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Javaプログラミング設計演習',
            'description' => 'オブジェクト指向プログラミングとデザインパターンを学ぶ実践的なコース。30回の課題を通じて、クラス設計からGoFデザインパターンまで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 240,
            'tags' => ['java', 'oop', 'design-patterns', '継承', 'ポリモーフィズム', 'デザインパターン'],
            'icon' => 'ic_java',
            'color' => '#ED8B00',
            'is_featured' => true,
        ]);

        // Milestone 1: OOP基礎 (第1回～第4回)
        $milestone1 = $template->milestones()->create([
            'title' => 'オブジェクト指向プログラミング基礎',
            'description' => 'フィールド、クラス、コンストラクタ、カプセル化、staticメンバ',
            'sort_order' => 1,
            'estimated_hours' => 20,
            'deliverables' => [
                'フィールドとメソッドを理解',
                'クラスとインスタンスの概念を理解',
                'コンストラクタとカプセル化を実装',
                'staticメンバとインスタンスメンバの違いを理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第1回：フィールド',
                'description' => 'フィールド（クラス変数）の定義と使い方、変数のスコープ',
                'sort_order' => 1,
                'estimated_minutes' => 120,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'フィールドの基本定義', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'public staticフィールドの使い方', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'フィールドのスコープを理解', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題：のび太のおつかい', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'フィールドとは',
                        'content' => "# フィールドとは\n\n**フィールド**とは、クラスの直下、メソッドの外部に記述する変数のこと。\n同じクラス内の【全てのメソッド内】から参照できる。\nクラスに属する変数の為、**クラス変数**とも呼ばれる。\n\n## フィールド vs ローカル変数\n\n| 項目 | フィールド | ローカル変数 |\n|------|-----------|-------------|\n| 宣言場所 | クラスの直下 | メソッド内 |\n| スコープ | クラス全体 | 宣言したブロック内のみ |\n| 初期値 | 自動的に初期化される | 初期化が必要 |\n| ライフタイム | クラス/インスタンスが存在する間 | メソッド実行中のみ |\n\n## フィールドの特徴\n\n- **static修飾子**を付けるとクラス変数、付けないとインスタンス変数として扱われる\n- **public修飾子**を付けると他クラスやパッケージからも参照できるグローバル変数として扱われる\n- メソッド内で宣言した変数（ローカル変数）は宣言したブロック内でしか参照できない（スコープ）\n- フィールドは宣言時に初期化しなくてもデフォルト値が設定される\n  - 数値型: 0\n  - boolean型: false\n  - 参照型: null",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'スコープ（変数の有効範囲）',
                        'content' => "# スコープ（変数の有効範囲）\n\n**スコープ**とは、変数が参照できる範囲のこと。\n\n## 1. ローカル変数のスコープ\n\nメソッド内で宣言した変数は、**宣言したブロック（{ }）内**でしか参照できない。\n\n```java\npublic void method1() {\n    int x = 10;  // xのスコープはmethod1内のみ\n    System.out.println(x);  // OK\n}\n\npublic void method2() {\n    System.out.println(x);  // エラー！xは参照できない\n}\n```\n\n## 2. フィールドのスコープ\n\nクラスの直下で宣言したフィールドは、**同じクラス内の全てのメソッド**から参照できる。\n\n```java\npublic class Example {\n    private int count = 0;  // フィールド\n    \n    public void increment() {\n        count++;  // OK: フィールドを参照\n    }\n    \n    public void display() {\n        System.out.println(count);  // OK: フィールドを参照\n    }\n}\n```\n\n## 3. ブロックスコープ\n\nif文、for文などのブロック内で宣言した変数は、そのブロック内でのみ有効。\n\n```java\nif (condition) {\n    int temp = 5;  // tempのスコープはこのif文内のみ\n    System.out.println(temp);  // OK\n}\nSystem.out.println(temp);  // エラー！\n```\n\n## スコープのベストプラクティス\n\n- 変数のスコープは**できるだけ狭く**する\n- 必要な場所でのみ宣言する\n- グローバル変数の乱用は避ける",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'フィールドとローカル変数の違い',
                        'content' => "import java.util.Scanner;\n\npublic class FieldVsLocal {\n    // フィールド（クラス変数）\n    public static int fieldCount = 0;\n    \n    public static void method1() {\n        // ローカル変数\n        int localCount = 0;\n        \n        fieldCount++;  // フィールドは全メソッドから参照可能\n        localCount++;  // ローカル変数はこのメソッド内のみ\n        \n        System.out.println(\"method1 - fieldCount: \" + fieldCount);\n        System.out.println(\"method1 - localCount: \" + localCount);\n    }\n    \n    public static void method2() {\n        fieldCount++;  // OK: フィールドは参照可能\n        // localCount++;  // エラー！ローカル変数は参照できない\n        \n        System.out.println(\"method2 - fieldCount: \" + fieldCount);\n    }\n    \n    public static void main(String[] args) {\n        System.out.println(\"初期 - fieldCount: \" + fieldCount);\n        \n        method1();  // fieldCount: 1, localCount: 1\n        method1();  // fieldCount: 2, localCount: 1（毎回初期化される）\n        method2();  // fieldCount: 3\n        \n        System.out.println(\"最終 - fieldCount: \" + fieldCount);  // 3\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'フィールドの基本例',
                        'content' => "import java.util.Scanner;\n\npublic class Note01_Field {\n    // フィールドを定義\n    public static int sum = 100;    // 合計値\n    /*\n        ※補足\n        フィールドはstatic修飾子を付けるとクラス変数、付けないとインスタンス変数として扱われる。\n        更にpublic修飾子を付けると他クラスやパッケージからも参照できるグローバル変数として扱われる。\n        これらの意味は別の回で説明する為、現時点では一先ずpublic staticを前に記述しておいてください。\n     */\n    \n    /**\n     * 加算メソッド\n     * @param x 加算する値\n     * @return 加算後の合計値\n     */\n    public static int add(int x){ \n        return sum += x; \n    }\n    \n    /**\n     * 減算メソッド\n     * @param x 減算する値\n     * @return 減算後の合計値\n     */\n    public static int subtract(int x){ \n        return sum -= x; \n    }\n    \n    public static void main(String[] args) {\n        // 入力用オブジェクト\n        Scanner in = new Scanner(System.in);\n        // 入力値用変数\n        int num;\n        /*\n            復習：これら（in,num）のメソッド内で宣言した変数はローカル変数といい、\n            宣言したブロック内でしか参照できない。参照できる範囲をスコープという。\n         */\n        \n        System.out.println(\"加算前の合計値：\" + sum);\n        System.out.print(\"加算する数値＞\");\n        num = in.nextInt();\n        System.out.println(\"加算後：\" + add(num));\n        \n        System.out.print(\"減算する数値＞\");\n        num = in.nextInt();\n        System.out.println(\"減算後：\" + subtract(num));\n        \n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'note',
                        'title' => 'フィールドのデフォルト値',
                        'content' => "# フィールドのデフォルト値\n\nフィールドは初期化しなくても、**自動的にデフォルト値**が設定されます。\n\n## 基本型のデフォルト値\n\n| 型 | デフォルト値 |\n|---|------------|\n| byte | 0 |\n| short | 0 |\n| int | 0 |\n| long | 0L |\n| float | 0.0f |\n| double | 0.0d |\n| char | '\\u0000' |\n| boolean | false |\n\n## 参照型のデフォルト値\n\n- すべての参照型（String, 配列, オブジェクトなど）: **null**\n\n```java\npublic class DefaultValues {\n    private int number;        // 0\n    private boolean flag;      // false\n    private String text;       // null\n    private int[] array;       // null\n    \n    public void display() {\n        System.out.println(\"number: \" + number);  // 0\n        System.out.println(\"flag: \" + flag);      // false\n        System.out.println(\"text: \" + text);      // null\n    }\n}\n```\n\n## 注意点\n\n**ローカル変数**は自動初期化されません！\n使用前に必ず初期化が必要です。\n\n```java\npublic void method() {\n    int x;  // デフォルト値なし\n    System.out.println(x);  // コンパイルエラー！\n}\n```",
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：のび太のおつかい',
                        'content' => "import java.util.Scanner;\n\npublic class NobiMoney {\n    // フィールド：のび太の所持金\n    public static int money = 1000;\n    \n    /**\n     * 買い物メソッド\n     * @param itemName 商品名\n     * @param price 価格\n     */\n    public static void buy(String itemName, int price) {\n        if (money >= price) {\n            money -= price;\n            System.out.println(itemName + \"を\" + price + \"円で購入しました。\");\n            System.out.println(\"残金：\" + money + \"円\");\n        } else {\n            System.out.println(\"お金が足りません！\");\n            System.out.println(\"残金：\" + money + \"円、必要額：\" + price + \"円\");\n        }\n    }\n    \n    public static void main(String[] args) {\n        Scanner in = new Scanner(System.in);\n        \n        System.out.println(\"のび太のおつかいゲーム\");\n        System.out.println(\"所持金：\" + money + \"円\");\n        System.out.println(\"==================\");\n        \n        // ドラ焼きを買う\n        buy(\"ドラ焼き\", 150);\n        \n        // 漫画を買う  \n        buy(\"漫画\", 500);\n        \n        // ゲームを買おうとする\n        buy(\"ゲーム\", 5000);\n        \n        System.out.println(\"\\n最終残金：\" + money + \"円\");\n        in.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 6
                    },
                ],
            ],
            [
                'title' => '第2回：クラス',
                'description' => 'クラスとインスタンス、オブジェクト指向の基本概念',
                'sort_order' => 2,
                'estimated_minutes' => 150,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'クラスの定義', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'インスタンスの生成', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'メソッドの定義と呼び出し', 'estimated_minutes' => 30, 'sort_order' => 3],
                    ['title' => '実践問題：Accountクラス、Monsterクラス', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'クラスとは',
                        'content' => "# クラスとは\n\nオブジェクト（部品）に関する情報をひとまとめにした設計図のこと。\nJavaはいわばクラスの集まりであり、クラスはその部品に関するフィールド（属性）とメソッド（操作）を持つ。\n\nオブジェクト指向とはクラスをそれぞれ１つの物（オブジェクト）として捉えて設計することで、\nそのクラスを後に使いまわしたり、クラスごとにアップデートしたりと管理・修正しやすくすることで、効率よく開発する考え方である。\n\n## クラス名のルール\n\n- クラス名の先頭は大文字しか許されない\n\n## インスタンス化（実態化）とは\n\nクラス（設計図）からインスタンス（実態）を作ることをインスタンス化という。\n\n```java\n// Monsterクラスをインスタンス化し、Monster型のdialga変数に代入\nMonster dialga = new Monster();\n```\n\n## newとは\n\nクラスをインスタンス化するために使用する演算子のこと。\nnew 【クラス名】で指定したクラスのインスタンス（実態）がメモリ上に生成される。\nそのインスタンスを使いまわすために、互換のあるクラスの変数に代入する必要がある。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '基本型と参照型',
                        'content' => "# 基本型（プリミティブ型）\n\nint, double, char, booleanなどの型を基本型と呼ぶ。\n基本型で作成した変数に代入することは、その型の箱を作って中に【値を入れた】という認識となる。\n\n```java\nint a = 10; // aに10という値（数値）が入る\nint b = a;  // bにaの中の10という値（数値）が入る\n```\n\n# 参照型\n\n配列, String型, インスタンスを代入したMonster型やScanner型といったクラスの型のことを参照型と呼ぶ。\n参照型で作成した変数に代入することは、その型の箱を作って中に【インスタンスの参照先（アドレス）を入れた】という認識となる。\nその為、インスタンス（実態）そのものが変数に入っている訳ではない。\n\n```java\nMonster pika = new Monster();    // pikaにMonster型から生成したインスタンスのアドレスが入る\nMonster rai = new Monster();     // raiにMonster型から生成したインスタンスのアドレスが入る\npika.name = \"ピカチュウ\";         // pikaオブジェクトが参照するアドレスのnameフィールドに\"ピカチュウ\"が入る\nrai.name = \"ライチュウ\";          // raiオブジェクトが参照するアドレスのnameフィールドに\"ライチュウ\"が入る\npika = rai;                     // pikaオブジェクトにraiオブジェクトが参照するアドレスが入る\nSystem.out.println(pika.name);   // 出力結果：ライチュウ\n```",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第3回：コンストラクタとカプセル化',
                'description' => 'コンストラクタ、private修飾子、getter/setter、カプセル化',
                'sort_order' => 3,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'コンストラクタの定義', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'private修飾子とカプセル化', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'getter/setterメソッド', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：SecretNumberクラス、Heroクラス', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'コンストラクタとは',
                        'content' => "# コンストラクタとは\n\nクラスをインスタンス化する際に【１度だけ自動的に呼び出されるメソッド】のこと。\nフィールドの値を初期化する場合に使用する為にクラスに定義する。\n\n## コンストラクタの特徴\n\n- コンストラクタ名（メソッド名）は【クラス名と同じ】でなければならない\n- 引数無しのコンストラクタをデフォルトコンストラクタとも呼ぶ\n- new（インスタンス化）したタイミングで自動的にコンストラクタが呼び出される\n- インスタンスごとに別の値を持たせたい場合は、【インスタンス化時にコンストラクタに引き渡す】べき\n\n```java\n// 引数ありコンストラクタ\npublic Note03_ConstructorCapsule(String n, int h) {\n    name = n;\n    hp = h;\n}\n\n// 使用例\nNote03_ConstructorCapsule cc = new Note03_ConstructorCapsule(\"小林\", 30);\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'カプセル化とは',
                        'content' => "# カプセル化とは\n\nオブジェクトが持つフィールドやメソッドに対して、外部から不要にアクセスさせない為に情報を隠ぺいする仕組みのこと。\nアクセス修飾子を使用して意図的に参照範囲を指定することで実現できる。\n\n## アクセス修飾子\n\n以下の4種類が存在する（範囲が広い順）\n\n- **public**: どのクラスからもアクセスが可能\n- **protected**: サブクラスまた同じパッケージ内のクラスからアクセスが可能\n- **なし（デフォルト）**: 同じパッケージ内のクラスのみアクセスが可能\n- **private**: 同じクラス内のみアクセスが可能\n\n## カプセル化の実装方法\n\nフィールドはprivateにして、フィールドの値を操作するメソッドをpublicにする。\nそうすることで開発者の想定した操作しか行わせないようにすることができる。\n\n- **セッターメソッド**: フィールドの値を設定するメソッド\n- **ゲッターメソッド**: フィールドの値を取得するメソッド\n- これらをアクセサと総称して呼ぶ",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'thisキーワード',
                        'content' => "// セッターの記述例\npublic void setName(String name) {\n    this.name = name;\n}\n\n// ゲッターの記述例\npublic String getName() {\n    return this.name;\n}\n\n/*\n    thisとは\n    thisとは自分自身のインスタンスを指しており、要は【フィールドの変数を指し示す為】に使用する。\n    thisを付けなかった場合、nameが引数（ローカル変数）を指しているのか、フィールドを指しているのか\n    Javaからは区別が付かず、スコープの観点からどちらも引数（ローカル変数）を指し示してしまいフィールドに値が代入されない。\n    よって、フィールドを指し示す場合にはthis.フィールド名と記述する。\n    設計的に意味が一致しているものには同一の名称を付けるべきである。\n    （nだとかname1だとか自由に付けてしまうと書いた本人しか使えないスパゲッティコードになるので絶対しないように）\n */",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第4回：インスタンスメンバとクラスメンバ',
                'description' => 'static修飾子、インスタンスメンバとクラスメンバの違い',
                'sort_order' => 4,
                'estimated_minutes' => 150,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'static修飾子の理解', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'インスタンスメンバとクラスメンバの違い', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'staticメソッドとstaticフィールド', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：Studentクラス、Cardクラス', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'メンバとは',
                        'content' => "# メンバとは\n\nクラスが持つ属性（フィールド）のことをメンバとも呼ぶ。\n\n- **クラスメンバ** = クラスに属するフィールド = クラス変数\n- **インスタンスメンバ** = インスタンスに属するフィールド = インスタンス変数\n\n## クラス変数（クラスフィールド・クラスメンバ）\n\nstatic修飾子を付けたフィールドのこと。クラス名.変数名で参照できる。\nクラスに属する為、そのクラスからインスタンス化した【全てのインスタンスが共通で保持】している。\n\n## インスタンス変数（インスタンスフィールド・インスタンスメンバ）\n\nstatic修飾子を付けていないフィールドのこと。インスタンス名.変数名で参照できる。\nインスタンスに属する為、そのクラスからインスタンス化した【それぞれのインスタンスが個別に保持】している。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'static（静的）とは',
                        'content' => "# static（静的）とは\n\n変数やメソッドに付けられる修飾子であり、付けると静的変数（クラス変数）・静的メソッド（クラスメソッド）として扱われる。\n静的の対義語は動的（dynamic）であり、staticを付けていないものはデフォルトで動的として扱われる。\n\n## 静的化の特徴\n\n- メモリ使用領域をプログラム実行開始時から保持し続けて、終了時まで解放しない\n- プログラム実行開始時に領域を確保する静的メソッド（クラスメソッド）から動的変数（インスタンス変数）は参照できない\n\n## インスタンス配列\n\nインスタンスを配列によって管理したもの。\nインスタンスに互換性のある型を配列として定義することで使用できる。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'staticの使用例',
                        'content' => "public class Note04_StaticDynamic {\n    private static String classVar = \"クラス変数\";\n    private String instanceVar = \"インスタンス変数\";\n    \n    /**\n     * クラスメソッド（静的メソッド）\n     */\n    public static void classMethod() {\n        System.out.println(classVar);          // クラス変数参照可\n        // System.out.println(instanceVar);    // インスタンス変数参照不可（コンパイルエラー）\n        // ※インスタンス変数が領域を確保する前に静的メソッドが参照してしまうのでエラーとなる\n    }\n    \n    /**\n     * インスタンスメソッド（動的メソッド）\n     */\n    public void instanceMethod() {\n        System.out.println(classVar);      // クラス変数参照可\n        System.out.println(instanceVar);   // インスタンス変数参照可\n    }\n}\n\n// インスタンス配列の例\nMonster[] pokemon = new Monster[3];\npokemon[0] = new Monster();\npokemon[0].setName(\"ピチュー\");",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 2: 継承とポリモーフィズム (第5回～第9回)
        $milestone2 = $template->milestones()->create([
            'title' => '継承とポリモーフィズム',
            'description' => 'クラスの継承、ポリモーフィズム、抽象クラス、インターフェイス、列挙型',
            'sort_order' => 2,
            'estimated_hours' => 30,
            'deliverables' => [
                '継承を実装できる',
                'ポリモーフィズムを理解',
                '抽象クラスとインターフェイスを使える',
                '列挙型（enum）を使える'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第5回：クラスの継承',
                'description' => 'extendsキーワード、super、メソッドのオーバーライド',
                'sort_order' => 5,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'extendsキーワードで継承', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'superでスーパークラスを呼び出す', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'メソッドのオーバーライド', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：Monsterクラスの継承', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '継承とは',
                        'content' => "# 継承とは\n\n新しく作成するクラスに既存のクラスを引き継ぐこと。\n継承を行うことで、既存のクラス側のフィールド（属性）やメソッド（操作）の機能を持ち使用することができる。\n\n- 継承される側のクラスをスーパークラス（親クラス）\n- 継承する側のクラスをサブクラス（子クラス）\n- extends修飾子を使って継承\n- コンストラクタは継承されない\n- クラスの多重継承は禁止されている\n\n## なぜ継承するのか\n\n汎用性が高いということは逆に言えば【影響力も高い】ということになります。\nモンスターの中には空を飛べる・水中を泳げる・地中に潜れるといったそれぞれ固有の特徴（機能）を持ったものもいます。\nMonsterクラスにそういった固有の機能を全て搭載してしまった場合、そのMonsterクラスは\n空も飛べて、水中を泳げて、地中に潜れるモンスターを生み出す為の設計図となります。\n\nオブジェクト指向の考えとしては【飛べないモンスターに空を飛ぶという機能や、泳げないモンスターに泳ぐという機能は与えるべきではない】ということです。\n\n## 継承のメリット\n\n- 機能を引き継ぐことで、新たに同じクラスを作成する必要がなくなり、開発工数が減る → 効率アップ\n- クラス（設計図）ごとに属性や操作を独立させられ、修正する際の影響度が低くなる → アップデートしやすい\n- アップデートした場合、継承したクラス全てに影響を与えられる → 必要なクラスに必要な機能を一度に与えられる",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'オーバーライドとsuper',
                        'content' => "# オーバーライドとは\n\nスーパークラスに定義しているメソッドと同一のシグネチャを持つメソッドをサブクラスに定義すること。\n※シグネチャ → メソッド名・引数の型・引数の数を一括りにした呼称\nサブクラスのオブジェクトに対して呼び出した場合、【サブクラス側のメソッドが優先して実行される】\nオーバーライドにより、サブクラス（固有の機能を持った）クラス側で操作を上書きすることができる。\n\n# superとは\n\nsuperとは【継承しているスーパー（親）クラス】を指す。\nスーパークラスが持つフィールドやメソッドに対して、\n- super.フィールド名\n- super.メソッド名()\nで明示的に参照できる。\n\nコンストラクタはいわば名前を持たないメソッドなので、\nsuper();でスーパークラスのコンストラクタを【意図的に】呼び出すことができる。",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第6回：ポリモーフィズム',
                'description' => 'ポリモーフィズムの概念、アップキャスト、ダウンキャスト',
                'sort_order' => 6,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ポリモーフィズムの概念', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'アップキャストとダウンキャスト', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'instanceof演算子', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：Monsterクラスのポリモーフィズム', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '型の互換性',
                        'content' => "# 型の互換性\n\nスーパークラスを継承したサブクラスはスーパークラスの機能を引き継ぐ為、互換性を持つ。\nその為、サブクラスのインスタンスはスーパークラスのデータ型変数に格納することが可能である。\n\n```java\n// 例：MonsterクラスをFireMonsterクラスが継承している場合\nFireMonster hitokage = new FireMonster();\n// ↑ 型が一致している為、もちろん格納可能\n\nMonster hitokage = new FireMonster();\n// ↑ FireMonsterインスタンスはMonsterクラスの機能を保持している為、格納可能\n\nFireMonster hitokage = new Monster();\n// ↑ MonsterインスタンスはFireMonsterクラスの機能を持たない為、コンパイルエラーとなる\n```\n\n継承を行うことで、サブクラスはスーパークラスが持つフィールドやメソッドを引き継ぎます。\nよってサブクラスはスーパークラスが持つフィールドやメソッドを扱えますが、\n【スーパークラスはサブクラスのフィールドやメソッドを扱えません】",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ポリモーフィズムとは',
                        'content' => "# ポリモーフィズムとは\n\n直訳では多態性を意味し、オブジェクト指向においては\n【同じ名称のメソッドを呼び出しても、オブジェクトによって異なる処理が実行されること】を指す。\n\n前述したスーパークラスの参照と型の互換性を活かして、\nオブジェクトを【互換性のあるスーパークラスの型で定義し、サブクラスでオーバーライドした\n同名称のメソッドを呼び出す】ことにより、各オブジェクトに対して同じ操作で異なる処理を実現することが可能となる。\n\nポリモーフィズムを意識してプログラムを設計することで、コードを簡略化することができる。",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第7回：抽象クラス',
                'description' => 'abstractキーワード、抽象メソッド、抽象クラスの継承',
                'sort_order' => 7,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'abstractクラスの定義', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '抽象メソッドの定義', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '抽象クラスの継承', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：RPGCharacterクラス', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '抽象とは',
                        'content' => "# 抽象とは\n\n多くの物や事柄や具体的な概念から、それらの範囲の全部に共通な属性を抜き出し、\nこれを一般的な概念としてとらえること。\n\n（例①）ピカチュウ・ヤドン・ヒトカゲ　→　ざっくり「モンスター」と抽象的にとらえられる。\n（例②）飛行機・船・車　→　ざっくり「乗り物」と抽象的にとらえられる。\n\nJavaにおける抽象的とは、【実態や処理を持たないもの】を指す。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '抽象メソッドと抽象クラス',
                        'content' => "# 抽象メソッドとは\n\n処理を持たせずに定義だけ行ったメソッドを抽象メソッドという。\nabstract修飾子をメソッドに記述することで、抽象化させることができる。\n\n```java\npublic abstract void attack();\n// 抽象メソッドは処理を持たない為、{}で開く（処理部を持つ）必要が無い。\n```\n\n# 抽象クラスとは\n\n抽象メソッドを１つ以上定義したクラスを抽象クラスという。\n抽象メソッドと同様に、abstract修飾子をクラスに記述することで、抽象化させることができる。\n抽象クラスから【インスタンス化（new）を行うことはできない】。\n\n```java\npublic abstract class RPGCharacter { ~ }\n```\n\n## なぜ抽象メソッド・抽象クラスを使うのか\n\n処理を持てないメソッドということでメソッドの下位互換に思えるかも知れないが、\n処理を持てない ＝ 持たせる必要の無いメソッドとして定義できるメリットにもなる。\n\nRPGのキャラクターは攻撃・防御という操作（メソッド）は必要だが、操作内容は個別のインスタンスに持たせたい為、\nスーパークラスには処理を持たせず【抽象的に定義だけ行い、それを継承したサブクラスでオーバーライドして】処理を持たせる。\n\nまた、抽象メソッドを持った抽象クラスを継承した具象クラスでは、\n【継承した抽象メソッドを必ずオーバーライドしなければならない】というルールがある為、\n処理の実装忘れという人為的ミスを防ぐこともメリットとなる。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Template Methodパターン',
                        'content' => "# Template Methodパターン\n\nオブジェクト指向言語におけるデザインパターン（設計手法）のひとつで、\nスーパークラスで処理の枠組みを定義し、サブクラスでその具体的な処理を定義する。\n\nメソッドを呼び出す順序だけを定義したメソッド（Template Method）をスーパークラスに定義し、\n各行動の処理をサブクラス側で定義すれば、mainメソッドではそのTemplate Methodを\n呼び出すだけとなり、スーパークラス側で行動パターンを管理することができる。",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第8回：インターフェイス',
                'description' => 'interfaceキーワード、implements、インターフェイスの実装',
                'sort_order' => 8,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'インターフェイスの定義', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'implementsキーワード', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '複数のインターフェイスの実装', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：ICamera、IPhoneインターフェイス', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'インターフェイスとは',
                        'content' => "# インターフェイスとは\n\nクラスが持つべきメソッドを記したルールブックのようなもの。\nimplements修飾子によってクラスにインターフェイスを実装することができ、実装したクラスは\nインターフェース内で定義されたメソッドを全て定義（オーバーライド）しなければならない。\n定義しなかった（人為的ミスした）場合にコンパイルエラーを出してくれるメリットがある。\n\n```java\n// インターフェースの定義例\npublic interface IFairy {\n    void intro();\n}\n// インターフェース内に定義するメソッドは抽象メソッドと同様に処理を持たない為{}で開く必要はない。\n// また、インターフェース内のメソッドは全て抽象化されている前提なので【public abstract】が省略されている。\n\n// クラスでインターフェースを実装する例\npublic class Light implements IFairy {\n    public void intro() {\n        System.out.println(\"わたしは光の妖精！この者に祝福を！！\");\n    }\n}\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '抽象クラスとインターフェイスの違い',
                        'content' => "# 抽象クラスとインターフェイスの違い\n\n## 抽象クラス\n\n- 抽象メソッドと具象メソッド（処理のあるメソッド）のどちらも定義可能\n- 多重継承が禁止されている\n\n## インターフェイス\n\n- 抽象メソッド（処理のないメソッド）しか定義できない\n- 多重継承（実装）が許可されている　※implementsの後にカンマ(,)区切りで記述できる\n\n## 使い分け方\n\n抽象クラスとインターフェイスはどちらも定義したものを継承・実装先に引き継がせるという用途としては同じなので\n使い分け方がわかりづらいが、要は【継承関連の中に必須なものかどうか】で判断すると良い。\n\nクラスはそのオブジェクトに対する設計図であり多重継承が禁止されている為、\n機能を引き継がせる必要がある継承関連の設計上で使用する。\nインターフェイスは継承関連上で必須ではないが、搭載したい機能を実装する場合に使用する。",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第9回：拡張for文と列挙型',
                'description' => '拡張for文（for-each）、enum型、列挙型の使い方',
                'sort_order' => 9,
                'estimated_minutes' => 150,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => '拡張for文の使い方', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'enum型の定義', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => '列挙型の実践', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題：じゃんけんゲーム', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
            ],
        ]);

        // Milestone 3: チャレンジ課題とテスト (第10回～第12回)
        $milestone3 = $template->milestones()->create([
            'title' => 'チャレンジ課題と中間テスト',
            'description' => '前半の総合的な復習とテスト',
            'sort_order' => 3,
            'estimated_hours' => 12,
            'deliverables' => [
                'チャレンジ課題①を完了',
                'クラス替えテストの練習',
                'チャレンジ課題②を完了'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '第10回：チャレンジ課題①（予備日）',
                'description' => '第1回～第9回までの総合的な復習課題',
                'sort_order' => 10,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第11回：クラス替えテスト（練習問題）',
                'description' => '第1回～第10回までの理解度を確認するテスト練習',
                'sort_order' => 11,
                'estimated_minutes' => 120,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '第12回：チャレンジ課題②（予備日）',
                'description' => 'より高度な総合課題（ビンゴゲームなど）',
                'sort_order' => 12,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 4: コレクションと例外処理 (第13回～第18回)
        $milestone4 = $template->milestones()->create([
            'title' => 'コレクションと例外処理',
            'description' => 'コレクション、例外処理、スレッド、ファイル入出力、ストリーム',
            'sort_order' => 4,
            'estimated_hours' => 36,
            'deliverables' => [
                'コレクション（List、Set、Map）を使える',
                '例外処理（try-catch、throw、throws）を実装',
                'マルチスレッドプログラミングを理解',
                'ファイル入出力とストリームを扱える'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第13回：コレクションとデータ構造',
                'description' => 'List、Set、Map、ArrayList、HashMap、データ構造',
                'sort_order' => 13,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ListインターフェイスとArrayList', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'SetインターフェイスとHashSet', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'MapインターフェイスとHashMap', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
            ],
            [
                'title' => '第14回：例外処理（try～catch）',
                'description' => 'try-catch文、例外の種類、例外処理の基本',
                'sort_order' => 14,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'try-catch文の基本', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => '例外の種類（RuntimeException、IOExceptionなど）', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'finallyブロック', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
            ],
            [
                'title' => '第15回：スレッド',
                'description' => 'Threadクラス、Runnableインターフェイス、マルチスレッド',
                'sort_order' => 15,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Threadクラスの継承', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'Runnableインターフェイスの実装', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'スレッドの同期', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
            ],
            [
                'title' => '第16回：ファイル入出力',
                'description' => 'FileReader、FileWriter、BufferedReader、BufferedWriter',
                'sort_order' => 16,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ファイルの読み込み', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'ファイルの書き込み', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 80, 'sort_order' => 3],
                ],
            ],
            [
                'title' => '第17回：ストリーム',
                'description' => 'バイナリストリーム、ObjectInputStream、ObjectOutputStream',
                'sort_order' => 17,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'バイナリストリーム', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'オブジェクトのシリアライズ', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 80, 'sort_order' => 3],
                ],
            ],
            [
                'title' => '第18回：例外処理（throw、throws）',
                'description' => 'throwキーワード、throwsキーワード、カスタム例外',
                'sort_order' => 18,
                'estimated_minutes' => 150,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'throwで例外を投げる', 'estimated_minutes' => 40, 'sort_order' => 1],
                    ['title' => 'throwsで例外を宣言', 'estimated_minutes' => 40, 'sort_order' => 2],
                    ['title' => 'カスタム例外クラスの作成', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 30, 'sort_order' => 4],
                ],
            ],
        ]);

        // Milestone 5: ラムダ式とメモリ管理 (第19回～第21回)
        $milestone5 = $template->milestones()->create([
            'title' => 'ラムダ式とメモリ管理',
            'description' => 'ラムダ式、匿名クラス、ガーベッジコレクション',
            'sort_order' => 5,
            'estimated_hours' => 12,
            'deliverables' => [
                'ラムダ式を使える',
                '匿名クラスを理解',
                'メモリ管理とガーベッジコレクションを理解'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => '第19回：ラムダ式',
                'description' => 'ラムダ式の構文、関数型インターフェイス、メソッド参照',
                'sort_order' => 19,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ラムダ式の基本構文', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => '関数型インターフェイス', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => 'メソッド参照', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 40, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '匿名クラスとは',
                        'content' => "# 匿名クラスとは\n\nクラスを定義する際や、生成したインスタンスに対して名称を付けなかったものを匿名クラスという。\nそのクラスやインスタンス内の処理をプログラム内で１度しか使用しない場合、【名称を付ける必要もない】為、\n名称を付けないことによりコードを短く、簡潔に記述できるメリットがある。\n\n```java\n// 匿名クラスを使用せずに\nScanner in = new Scanner(System.in);\nString text = in.next();\n\n// 匿名クラスを使用した場合\nString text = new Scanner(System.in).next();\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '関数型インターフェイスとは',
                        'content' => "# 関数型インターフェイスとは\n\n（抽象）メソッドを１つしか持たないインターフェースのことをいう。\n\n```java\ninterface SayHello {\n    void hello();\n}\n```\n\nインターフェースの特性も兼ねて、このSayHelloインターフェースを実装したクラスは、\n必ず【戻り値がvoidで引数が無いhelloメソッドを持つ】ということが暗黙的に確約される。\nそれを利用して実装先のコードを簡略化することができる。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ラムダ式とは',
                        'content' => "# ラムダ式とは\n\nJava8から実装された、様々なコードを簡略化する記述方式のこと。\n\n例えば関数型インターフェースを実装した匿名クラスを使用（インスタンスを生成）する際、\n【helloメソッドを１つだけ持つSayHelloインターフェースを実装している】ことは確約されている為、\nメソッドを１つだけ定義する場合はインターフェースやメソッドの情報を省略できる。\n\n```java\n// 匿名クラス\nGreeting.greet(\n    new SayHello() {\n        public void hello() { System.out.println(\"匿名クラス②：こんにちは！\"); }\n    }\n);\n\n// ラムダ式（インターフェース・メソッド情報を省略）\nGreeting.greet( () -> { System.out.println(\"ラムダ式②：こんにちは！\"); } );\n```\n\nラムダ式では条件さえ満たせば型や {}(); などの本来Javaの記述ルールとして必要だったものも省略できる。",
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第20回：メモリ管理、コレクションとラムダ式',
                'description' => 'ガーベッジコレクション、コレクションとラムダ式の組み合わせ',
                'sort_order' => 20,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'ガーベッジコレクションの理解', 'estimated_minutes' => 50, 'sort_order' => 1],
                    ['title' => 'コレクションとラムダ式', 'estimated_minutes' => 50, 'sort_order' => 2],
                    ['title' => 'Stream APIの基礎', 'estimated_minutes' => 40, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 40, 'sort_order' => 4],
                ],
            ],
            [
                'title' => '第21回：チャレンジ課題③（予備日）',
                'description' => '第13回～第20回までの総合的な復習課題',
                'sort_order' => 21,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 6: デザインパターン基礎 (第22回～第25回)
        $milestone6 = $template->milestones()->create([
            'title' => 'デザインパターン基礎',
            'description' => 'Strategy、Composite、State、Iteratorパターン',
            'sort_order' => 6,
            'estimated_hours' => 24,
            'deliverables' => [
                'Strategyパターンを実装',
                'Compositeパターンを理解',
                'Stateパターンを実装',
                'Iteratorパターンを実装'
            ],
        ]);

        $milestone6->tasks()->createMany([
            [
                'title' => '第22回：プログラム設計①（Strategy）',
                'description' => 'Strategyパターン、デリゲート、アルゴリズムの切り替え',
                'sort_order' => 22,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'デリゲート（委譲）の理解', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'Strategyパターンの概念', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'Strategyパターンの実装', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '実践問題', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Delegate（デリゲート）とは',
                        'content' => "# Delegate（デリゲート）とは\n\n直訳すると委譲（他に委ねる）という意味であり、\nプログラミング用語としてはあるメソッドを呼び出した際、そのメソッド内で処理を記述するのではなく\n【他クラスのメソッドを呼び出（利用）して処理を行う手法】を指す。要は処理の丸投げである。\n他クラスで定義した同じ処理を記述する必要がないというメリットがある。\n\n## デリゲートと継承の違い\n\n継承はフィールドやコンストラクタ、メソッドなど全てのメンバを引き継げるといった強みの反面、\nインスタンス化時にスーパークラスのコンストラクタが自動で実行したり、そのコンストラクタを\n修正した場合に継承先の全てのサブクラスへ修正が影響してしまったりといったクラス間の独立性が低くなる、\nかつそもそも多重継承できず柔軟に対応できないケースがある弱点が存在する。\n\n反対にデリゲートは継承関係を持たず委譲元と委譲先のクラス間は完全に独立している為、\n修正の影響範囲や多重継承を気にする必要がなく柔軟性が高いというメリットも存在する。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Strategyパターンとは',
                        'content' => "# Strategyパターンとは\n\n直訳すると戦略パターンという意味であり、\n【何かしらの条件次第で戦略（処理）を切り替えやすいように設計する方法】を指す。\n\n選択された値によって処理（アルゴリズム）を切り替えなければならないといった場合、\nアルゴリズムのインスタンスを受け取ってデリゲートするクラスを作成し、\nその受け取ったインスタンスのメソッドを呼び出すだけで処理を容易に切り替えることができる。\n\n## Strategyパターンのメリット\n\n- アルゴリズムを独立したクラスとして定義できる\n- 実行時にアルゴリズムを切り替えられる\n- 新しいアルゴリズムを追加しやすい\n- コードの重複を避けられる",
                        'sort_order' => 2
                    ],
                ],
            ],
            [
                'title' => '第23回：プログラム設計③（Composite）',
                'description' => 'Compositeパターン、木構造の表現',
                'sort_order' => 23,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Compositeパターンの概念', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'Compositeパターンの実装', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
            ],
            [
                'title' => '第24回：プログラム設計③（State）',
                'description' => 'Stateパターン、状態遷移の管理',
                'sort_order' => 24,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Stateパターンの概念', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'Stateパターンの実装', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '実践問題：ガチャマシン、ストップウォッチ', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
            ],
            [
                'title' => '第25回：プログラム設計④（Iterator）',
                'description' => 'Iteratorパターン、コレクションの走査',
                'sort_order' => 25,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Iteratorパターンの概念', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'Iteratorパターンの実装', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '実践問題：Menuクラス', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
            ],
        ]);

        // Milestone 7: デザインパターン応用 (第26回～第30回)
        $milestone7 = $template->milestones()->create([
            'title' => 'デザインパターン応用と総合演習',
            'description' => 'Adapter、Command、Visitor、Singleton、Observerパターン、期末テスト',
            'sort_order' => 7,
            'estimated_hours' => 30,
            'deliverables' => [
                'Adapterパターンを実装',
                'Commandパターンを実装',
                'Visitorパターンを理解',
                'SingletonパターンとObserverパターンを実装',
                '期末テストに合格'
            ],
        ]);

        $milestone7->tasks()->createMany([
            [
                'title' => '第26回：プログラム設計⑤（Adapter・Command）',
                'description' => 'Adapterパターン、Commandパターン',
                'sort_order' => 26,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Adapterパターンの概念と実装', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Commandパターンの概念と実装', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
            ],
            [
                'title' => '第27回：プログラム設計⑥（Visitor）',
                'description' => 'Visitorパターン、データ構造と処理の分離',
                'sort_order' => 27,
                'estimated_minutes' => 240,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Visitorパターンの概念', 'estimated_minutes' => 80, 'sort_order' => 1],
                    ['title' => 'Visitorパターンの実装', 'estimated_minutes' => 80, 'sort_order' => 2],
                    ['title' => '実践問題', 'estimated_minutes' => 80, 'sort_order' => 3],
                ],
            ],
            [
                'title' => '第28回：プログラム設計⑦（Singleton・Observer）',
                'description' => 'Singletonパターン、Observerパターン',
                'sort_order' => 28,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Singletonパターンの概念と実装', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Observerパターンの概念と実装', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
            ],
            [
                'title' => '第29回：期末テスト（練習問題）',
                'description' => '全30回の理解度を確認する期末テスト練習',
                'sort_order' => 29,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '第30回：チャレンジ課題④（予備日）',
                'description' => '最終的な総合課題',
                'sort_order' => 30,
                'estimated_minutes' => 180,
                'priority' => 4,
                'resources' => [],
            ],
        ]);
    }
}

