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
                        'title' => 'オブジェクト指向プログラミング（OOP）',
                        'content' => "# オブジェクト指向プログラミング（OOP）とは\n\nオブジェクト指向とは、クラスをそれぞれ１つの物（オブジェクト）として捉えて設計することで、\nそのクラスを後に使いまわしたり、クラスごとにアップデートしたりと管理・修正しやすくすることで、効率よく開発する考え方である。\n\n## OOPの4大原則\n\n| 原則 | 説明 |\n|------|------|\n| **カプセル化** | データとそれを操作するメソッドを1つのクラスにまとめ、外部からの不要なアクセスを制限する |\n| **継承** | 既存のクラスの機能を引き継いで新しいクラスを作成する |\n| **ポリモーフィズム** | 同じインターフェースで異なる実装を扱える多様性 |\n| **抽象化** | 共通の特徴を抽出し、詳細を隠蔽する |\n\n## OOPのメリット\n\n1. **再利用性**: 一度作ったクラスを何度も使える\n2. **保守性**: 修正が必要な箇所を特定しやすい\n3. **拡張性**: 新しい機能を追加しやすい\n4. **可読性**: コードの構造が理解しやすい",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'クラスとは',
                        'content' => "# クラスとは\n\nオブジェクト（部品）に関する情報をひとまとめにした設計図のこと。\nJavaはいわばクラスの集まりであり、クラスはその部品に関するフィールド（属性）とメソッド（操作）を持つ。\n\n## クラスの構成要素\n\n```\nクラス\n├── フィールド（属性・データ）\n├── メソッド（操作・振る舞い）\n└── コンストラクタ（初期化処理）\n```\n\n## クラス名のルール\n\n- クラス名の先頭は**大文字**しか許されない\n- キャメルケース（CamelCase）で記述する\n- 名詞で命名する（動詞は避ける）\n\n**良い例**: `Monster`, `Account`, `UserProfile`\n**悪い例**: `monster`, `getData`, `user_profile`\n\n## インスタンス化（実態化）とは\n\nクラス（設計図）からインスタンス（実態）を作ることをインスタンス化という。\n\n```java\n// Monsterクラスをインスタンス化し、Monster型のdialga変数に代入\nMonster dialga = new Monster();\n```\n\n## newとは\n\nクラスをインスタンス化するために使用する演算子のこと。\nnew 【クラス名】で指定したクラスのインスタンス（実態）がメモリ上に生成される。\nそのインスタンスを使いまわすために、互換のあるクラスの変数に代入する必要がある。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'クラスの定義と使用例',
                        'content' => "// Monsterクラスの定義\npublic class Monster {\n    // フィールド（属性）\n    String name;\n    int hp;\n    int attack;\n    \n    // メソッド（操作）\n    public void introduce() {\n        System.out.println(\"私の名前は\" + name + \"です。\");\n        System.out.println(\"HP: \" + hp + \", 攻撃力: \" + attack);\n    }\n    \n    public void attackEnemy(Monster target) {\n        System.out.println(name + \"は\" + target.name + \"を攻撃した！\");\n        target.hp -= this.attack;\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        // インスタンス化\n        Monster pikachu = new Monster();\n        pikachu.name = \"ピカチュウ\";\n        pikachu.hp = 100;\n        pikachu.attack = 55;\n        \n        Monster raichu = new Monster();\n        raichu.name = \"ライチュウ\";\n        raichu.hp = 120;\n        raichu.attack = 85;\n        \n        // メソッド呼び出し\n        pikachu.introduce();\n        pikachu.attackEnemy(raichu);\n        System.out.println(raichu.name + \"の残りHP: \" + raichu.hp);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => '基本型と参照型の違い',
                        'content' => "# 基本型（プリミティブ型）\n\nint, double, char, booleanなどの型を基本型と呼ぶ。\n基本型で作成した変数に代入することは、その型の箱を作って中に【値を入れた】という認識となる。\n\n```java\nint a = 10; // aに10という値（数値）が入る\nint b = a;  // bにaの中の10という値（数値）が入る\nb = 20;     // bを変更してもaには影響しない\n```\n\n# 参照型\n\n配列, String型, インスタンスを代入したMonster型やScanner型といったクラスの型のことを参照型と呼ぶ。\n参照型で作成した変数に代入することは、その型の箱を作って中に【インスタンスの参照先（アドレス）を入れた】という認識となる。\nその為、インスタンス（実態）そのものが変数に入っている訳ではない。\n\n## 基本型と参照型の比較表\n\n| 特徴 | 基本型 | 参照型 |\n|------|--------|--------|\n| 格納される値 | 実際の値 | メモリアドレス（参照） |\n| メモリ領域 | スタック領域 | ヒープ領域 |\n| デフォルト値 | 0, false等 | null |\n| 代入時の動作 | 値のコピー | 参照のコピー |\n| 比較演算子 | ==で値を比較 | ==で参照を比較 |\n| 種類 | byte, short, int, long, float, double, char, boolean | クラス、配列、インターフェース |\n\n# メモリの仕組み\n\n```\nスタック領域          ヒープ領域\n+-----------+        +------------------+\n| a = 10    |        |                  |\n| b = 10    |        |                  |\n+-----------+        +------------------+\n| monster1  |------->| Monster@123      |\n|           |        | name=\"ピカチュウ\"  |\n| monster2  |------->| hp=100           |\n+-----------+        +------------------+\n```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '参照型の動作例',
                        'content' => "public class Main {\n    public static void main(String[] args) {\n        // 基本型の動作\n        int a = 10;\n        int b = a;      // aの値がコピーされる\n        b = 20;\n        System.out.println(\"a = \" + a);  // a = 10（変更されない）\n        System.out.println(\"b = \" + b);  // b = 20\n        \n        // 参照型の動作\n        Monster pika = new Monster();    // pikaにアドレスが入る\n        Monster rai = new Monster();     // raiに別のアドレスが入る\n        \n        pika.name = \"ピカチュウ\";         // pikaが参照するインスタンスのnameを設定\n        rai.name = \"ライチュウ\";          // raiが参照するインスタンスのnameを設定\n        \n        System.out.println(pika.name);   // ピカチュウ\n        System.out.println(rai.name);    // ライチュウ\n        \n        // 参照の代入\n        pika = rai;                      // pikaがraiと同じアドレスを参照\n        System.out.println(pika.name);   // ライチュウ（同じインスタンスを参照）\n        \n        // 一方を変更すると両方に影響\n        pika.name = \"デンリュウ\";\n        System.out.println(pika.name);   // デンリュウ\n        System.out.println(rai.name);    // デンリュウ（同じインスタンス）\n        \n        // ==演算子は参照を比較\n        System.out.println(pika == rai); // true（同じアドレスを参照）\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：Accountクラス',
                        'content' => "// 銀行口座を表すAccountクラス\npublic class Account {\n    String accountNumber;  // 口座番号\n    String owner;          // 口座名義\n    long balance;          // 残高\n    \n    // 入金メソッド\n    public void deposit(long amount) {\n        if (amount > 0) {\n            balance += amount;\n            System.out.println(amount + \"円入金しました。残高: \" + balance + \"円\");\n        } else {\n            System.out.println(\"入金額は正の値である必要があります。\");\n        }\n    }\n    \n    // 出金メソッド\n    public boolean withdraw(long amount) {\n        if (amount > balance) {\n            System.out.println(\"残高不足です。\");\n            return false;\n        } else if (amount <= 0) {\n            System.out.println(\"出金額は正の値である必要があります。\");\n            return false;\n        } else {\n            balance -= amount;\n            System.out.println(amount + \"円出金しました。残高: \" + balance + \"円\");\n            return true;\n        }\n    }\n    \n    // 残高照会メソッド\n    public void showBalance() {\n        System.out.println(\"[\" + accountNumber + \"] \" + owner + \"様\");\n        System.out.println(\"現在の残高: \" + balance + \"円\");\n    }\n}\n\n// 使用例\npublic class BankSystem {\n    public static void main(String[] args) {\n        Account acc = new Account();\n        acc.accountNumber = \"1234567\";\n        acc.owner = \"山田太郎\";\n        acc.balance = 10000;\n        \n        acc.showBalance();\n        acc.deposit(5000);\n        acc.withdraw(3000);\n        acc.withdraw(15000);  // 残高不足\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => 'nullとNullPointerException',
                        'content' => "# nullとは\n\n**null**は「何も参照していない」ことを示す特別な値。\n参照型の変数はインスタンス化していない場合、デフォルトでnullが代入される。\n\n```java\nMonster monster;           // nullが代入される\nSystem.out.println(monster);  // null\n```\n\n## NullPointerException（NPE）\n\nnullの変数に対してメソッドやフィールドにアクセスしようとすると、\n**NullPointerException**が発生してプログラムが異常終了する。\n\n```java\nMonster monster = null;\nmonster.introduce();  // NullPointerException発生！\n```\n\n## NPEを防ぐ方法\n\n### 1. null チェック\n\n```java\nif (monster != null) {\n    monster.introduce();\n} else {\n    System.out.println(\"モンスターが存在しません\");\n}\n```\n\n### 2. インスタンス化を忘れない\n\n```java\nMonster monster = new Monster();  // 必ずインスタンス化\nmonster.name = \"ピカチュウ\";\nmonster.introduce();  // 安全に呼び出せる\n```\n\n### 3. デフォルト値を設定\n\n```java\npublic class Monster {\n    String name = \"名無し\";  // デフォルト値を設定\n    int hp = 1;\n}\n```\n\n## よくあるNPEのパターン\n\n```java\n// パターン1: 配列の要素がnull\nMonster[] monsters = new Monster[3];  // 配列は作成されたが要素はnull\nmonsters[0].introduce();  // NPE！\n\n// 正しい方法\nmonsters[0] = new Monster();  // 各要素をインスタンス化\nmonsters[0].introduce();  // OK\n\n// パターン2: メソッドの戻り値がnull\npublic Monster findMonster(String name) {\n    // 見つからない場合nullを返す\n    return null;\n}\n\nMonster result = findMonster(\"ミュウツー\");\nresult.introduce();  // resultがnullの場合NPE！\n\n// 正しい方法\nif (result != null) {\n    result.introduce();\n}\n```",
                        'sort_order' => 7
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'thisキーワードの使い方',
                        'content' => "// thisキーワードの用途\npublic class Monster {\n    String name;\n    int hp;\n    \n    // 用途1: フィールドとローカル変数の区別\n    public void setName(String name) {\n        this.name = name;  // this.nameはフィールド、nameは引数\n    }\n    \n    // 用途2: 自分自身を返すメソッドチェーン\n    public Monster withName(String name) {\n        this.name = name;\n        return this;  // 自分自身を返す\n    }\n    \n    public Monster withHp(int hp) {\n        this.hp = hp;\n        return this;\n    }\n    \n    // 用途3: 同じクラス内の他のメソッドを呼び出し\n    public void initialize() {\n        this.setName(\"デフォルト\");\n        this.hp = 100;\n    }\n}\n\n// メソッドチェーンの使用例\npublic class Main {\n    public static void main(String[] args) {\n        // メソッドチェーンで連続設定\n        Monster monster = new Monster()\n            .withName(\"ピカチュウ\")\n            .withHp(100);\n        \n        System.out.println(monster.name);  // ピカチュウ\n        System.out.println(monster.hp);    // 100\n    }\n}\n\n/*\n    thisとは\n    thisとは自分自身のインスタンスを指しており、要は【フィールドの変数を指し示す為】に使用する。\n    thisを付けなかった場合、nameが引数（ローカル変数）を指しているのか、フィールドを指しているのか\n    Javaからは区別が付かず、スコープの観点からどちらも引数（ローカル変数）を指し示してしまいフィールドに値が代入されない。\n    よって、フィールドを指し示す場合にはthis.フィールド名と記述する。\n    設計的に意味が一致しているものには同一の名称を付けるべきである。\n    （nだとかname1だとか自由に付けてしまうと書いた本人しか使えないスパゲッティコードになるので絶対しないように）\n */",
                        'code_language' => 'java',
                        'sort_order' => 8
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
                        'content' => "# コンストラクタとは\n\nクラスをインスタンス化する際に【１度だけ自動的に呼び出されるメソッド】のこと。\nフィールドの値を初期化する場合に使用する為にクラスに定義する。\n\n## コンストラクタの特徴\n\n| 特徴 | 説明 |\n|------|------|\n| **名前** | コンストラクタ名は【クラス名と同じ】でなければならない |\n| **戻り値** | 戻り値の型を記述しない（voidも不要） |\n| **呼び出し** | newでインスタンス化した時に自動的に呼び出される |\n| **目的** | フィールドの初期化を行う |\n| **オーバーロード** | 引数が異なる複数のコンストラクタを定義できる |\n\n## デフォルトコンストラクタ\n\n引数無しのコンストラクタをデフォルトコンストラクタとも呼ぶ。\nコンストラクタを1つも定義していない場合、コンパイラが自動的にデフォルトコンストラクタを生成する。\n\n**重要**: 引数ありコンストラクタを定義すると、デフォルトコンストラクタは自動生成されない！",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'コンストラクタの種類と使い方',
                        'content' => "public class Hero {\n    private String name;\n    private int hp;\n    private int mp;\n    \n    // デフォルトコンストラクタ\n    public Hero() {\n        this.name = \"名無しの勇者\";\n        this.hp = 100;\n        this.mp = 50;\n        System.out.println(\"勇者が誕生した！\");\n    }\n    \n    // 引数ありコンストラクタ（名前のみ指定）\n    public Hero(String name) {\n        this.name = name;\n        this.hp = 100;\n        this.mp = 50;\n    }\n    \n    // 引数ありコンストラクタ（全フィールド指定）\n    public Hero(String name, int hp, int mp) {\n        this.name = name;\n        this.hp = hp;\n        this.mp = mp;\n    }\n    \n    // コンストラクタチェーン（他のコンストラクタを呼び出す）\n    public Hero(String name, int hp) {\n        this(name, hp, 50);  // 3つ目のコンストラクタを呼び出す\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        Hero hero1 = new Hero();                    // デフォルト値で生成\n        Hero hero2 = new Hero(\"アレックス\");          // 名前のみ指定\n        Hero hero3 = new Hero(\"ベロニカ\", 80);       // 名前とHPを指定\n        Hero hero4 = new Hero(\"セーニャ\", 90, 120);  // 全て指定\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'カプセル化とは',
                        'content' => "# カプセル化とは\n\nオブジェクトが持つフィールドやメソッドに対して、外部から不要にアクセスさせない為に情報を隠ぺいする仕組みのこと。\nアクセス修飾子を使用して意図的に参照範囲を指定することで実現できる。\n\n## なぜカプセル化が必要か\n\n```java\n// カプセル化していない場合\npublic class Hero {\n    public int hp;  // 外部から直接アクセス可能\n}\n\nHero hero = new Hero();\nhero.hp = -100;  // 不正な値を設定できてしまう！\n```\n\n上記のように、フィールドをpublicにすると、外部から不正な値を設定できてしまう。\nカプセル化により、**データの整合性を保ち、予期しないバグを防ぐ**ことができる。\n\n## アクセス修飾子\n\n以下の4種類が存在する（範囲が広い順）\n\n| 修飾子 | 同じクラス | 同じパッケージ | サブクラス | 全て |\n|--------|-----------|---------------|-----------|------|\n| **public** | ○ | ○ | ○ | ○ |\n| **protected** | ○ | ○ | ○ | × |\n| **なし（default）** | ○ | ○ | × | × |\n| **private** | ○ | × | × | × |\n\n## カプセル化の実装方法\n\n1. フィールドは**private**にする\n2. フィールドへのアクセスは**public**なgetter/setterメソッドを通して行う\n3. setterで**妥当性チェック**を行い、不正な値を防ぐ\n\n## カプセル化のメリット\n\n- データの不正な操作を防げる\n- 内部実装を隠蔽できる（変更しても外部に影響しない）\n- デバッグが容易（setter内にブレークポイントを設定できる）",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'カプセル化の実装例',
                        'content' => "// カプセル化されたHeroクラス\npublic class Hero {\n    // フィールドはprivateで隠蔽\n    private String name;\n    private int hp;\n    private int mp;\n    private static final int MAX_HP = 999;\n    private static final int MAX_MP = 999;\n    \n    // コンストラクタ\n    public Hero(String name, int hp, int mp) {\n        this.name = name;\n        setHp(hp);  // setterを使って妥当性チェック\n        setMp(mp);\n    }\n    \n    // getter: フィールドの値を取得\n    public String getName() {\n        return this.name;\n    }\n    \n    public int getHp() {\n        return this.hp;\n    }\n    \n    public int getMp() {\n        return this.mp;\n    }\n    \n    // setter: フィールドの値を設定（妥当性チェック付き）\n    public void setName(String name) {\n        if (name == null || name.length() < 1) {\n            throw new IllegalArgumentException(\"名前が不正です\");\n        }\n        this.name = name;\n    }\n    \n    public void setHp(int hp) {\n        if (hp < 0) {\n            this.hp = 0;  // 0未満は0にする\n        } else if (hp > MAX_HP) {\n            this.hp = MAX_HP;  // 上限を超えたら最大値\n        } else {\n            this.hp = hp;\n        }\n    }\n    \n    public void setMp(int mp) {\n        if (mp < 0) {\n            this.mp = 0;\n        } else if (mp > MAX_MP) {\n            this.mp = MAX_MP;\n        } else {\n            this.mp = mp;\n        }\n    }\n    \n    // ビジネスロジック\n    public void attack() {\n        if (this.mp >= 10) {\n            System.out.println(name + \"の攻撃！\");\n            this.mp -= 10;\n        } else {\n            System.out.println(\"MPが足りない！\");\n        }\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        Hero hero = new Hero(\"勇者\", 100, 50);\n        \n        // getterで値を取得\n        System.out.println(hero.getName());  // 勇者\n        System.out.println(hero.getHp());    // 100\n        \n        // setterで値を設定（妥当性チェックが働く）\n        hero.setHp(-50);     // 0未満は0になる\n        hero.setHp(10000);   // 上限を超えたら999になる\n        \n        // hero.hp = -100;   // privateなので直接アクセス不可（コンパイルエラー）\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：SecretNumberクラス',
                        'content' => "// 秘密の数字を扱うクラス（読み取り専用フィールドの例）\npublic class SecretNumber {\n    private int secretNumber;  // 秘密の数字\n    private int attemptCount;  // 試行回数\n    \n    public SecretNumber(int max) {\n        // 1からmaxまでのランダムな数字を生成\n        this.secretNumber = (int)(Math.random() * max) + 1;\n        this.attemptCount = 0;\n    }\n    \n    // getterのみ提供（setterは提供しない = 読み取り専用）\n    public int getAttemptCount() {\n        return this.attemptCount;\n    }\n    \n    // secretNumberにはgetterも提供しない（完全に隠蔽）\n    \n    // ビジネスロジック：予想が当たっているかチェック\n    public boolean guess(int number) {\n        this.attemptCount++;\n        \n        if (number == this.secretNumber) {\n            System.out.println(\"正解！ \" + attemptCount + \"回で当てました！\");\n            return true;\n        } else if (number < this.secretNumber) {\n            System.out.println(\"もっと大きいです\");\n            return false;\n        } else {\n            System.out.println(\"もっと小さいです\");\n            return false;\n        }\n    }\n    \n    // 答えを表示（ゲームオーバー時のみ使用）\n    public void reveal() {\n        System.out.println(\"答えは \" + this.secretNumber + \" でした\");\n    }\n}\n\n// 使用例：数当てゲーム\nimport java.util.Scanner;\n\npublic class NumberGuessGame {\n    public static void main(String[] args) {\n        SecretNumber game = new SecretNumber(100);  // 1-100の数字\n        Scanner scanner = new Scanner(System.in);\n        \n        System.out.println(\"1から100の数字を当ててください！\");\n        \n        while (true) {\n            System.out.print(\"予想 > \");\n            int guess = scanner.nextInt();\n            \n            if (game.guess(guess)) {\n                break;  // 正解したら終了\n            }\n            \n            if (game.getAttemptCount() >= 10) {\n                System.out.println(\"ゲームオーバー！\");\n                game.reveal();\n                break;\n            }\n        }\n        \n        scanner.close();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    },
                    [
                        'type' => 'note',
                        'title' => 'getter/setterのベストプラクティス',
                        'content' => "# getter/setterのベストプラクティス\n\n## 命名規則\n\n```java\nprivate String name;\n\n// getter: get + フィールド名（先頭大文字）\npublic String getName() {\n    return this.name;\n}\n\n// setter: set + フィールド名（先頭大文字）\npublic void setName(String name) {\n    this.name = name;\n}\n\n// boolean型のgetterは「is」で始めることが多い\nprivate boolean alive;\npublic boolean isAlive() {\n    return this.alive;\n}\n```\n\n## setterで妥当性チェックを行う\n\n```java\npublic void setAge(int age) {\n    if (age < 0 || age > 150) {\n        throw new IllegalArgumentException(\"年齢は0-150の範囲で指定してください\");\n    }\n    this.age = age;\n}\n\npublic void setEmail(String email) {\n    if (email == null || !email.contains(\"@\")) {\n        throw new IllegalArgumentException(\"メールアドレスが不正です\");\n    }\n    this.email = email;\n}\n```\n\n## getterで計算結果を返すこともできる\n\n```java\nprivate String firstName;\nprivate String lastName;\n\n// フィールドを組み合わせて返す\npublic String getFullName() {\n    return this.firstName + \" \" + this.lastName;\n}\n\nprivate int hp;\nprivate int maxHp;\n\n// 割合を計算して返す\npublic double getHpPercentage() {\n    return (double)this.hp / this.maxHp * 100;\n}\n```\n\n## 必要なものだけgetterを提供する\n\n```java\npublic class User {\n    private String username;     // getter/setterを提供\n    private String passwordHash; // getterは提供しない（セキュリティ）\n    private int loginAttempts;   // getterのみ提供（内部でカウント）\n}\n```\n\n## コレクションのgetterは防御的コピーを返す\n\n```java\nprivate List<String> items = new ArrayList<>();\n\n// 悪い例：内部のリストを直接返す\npublic List<String> getItems() {\n    return this.items;  // 外部から変更される可能性\n}\n\n// 良い例：コピーを返す\npublic List<String> getItems() {\n    return new ArrayList<>(this.items);\n}\n\n// または、変更不可のビューを返す\npublic List<String> getItems() {\n    return Collections.unmodifiableList(this.items);\n}",
                        'sort_order' => 6
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
                        'content' => "# メンバとは\n\nクラスが持つ属性（フィールド）と操作（メソッド）のことをメンバと呼ぶ。\n\n- **クラスメンバ** = クラスに属するフィールド/メソッド（static付き）\n- **インスタンスメンバ** = インスタンスに属するフィールド/メソッド（staticなし）\n\n## クラス変数（クラスフィールド・クラスメンバ）\n\nstatic修飾子を付けたフィールドのこと。`クラス名.変数名`で参照できる。\nクラスに属する為、そのクラスからインスタンス化した【全てのインスタンスが共通で保持】している。\n\n```java\npublic class Student {\n    static int count = 0;  // 全インスタンスで共有\n    String name;           // インスタンスごとに個別\n}\n\nStudent.count++;  // クラス名で直接アクセス可能\n```\n\n## インスタンス変数（インスタンスフィールド・インスタンスメンバ）\n\nstatic修飾子を付けていないフィールドのこと。`インスタンス名.変数名`で参照できる。\nインスタンスに属する為、そのクラスからインスタンス化した【それぞれのインスタンスが個別に保持】している。\n\n```java\nStudent student1 = new Student();\nstudent1.name = \"太郎\";  // このインスタンス固有の名前\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'static（静的）とは',
                        'content' => "# static（静的）とは\n\n変数やメソッドに付けられる修飾子であり、付けると静的変数（クラス変数）・静的メソッド（クラスメソッド）として扱われる。\n静的の対義語は動的（dynamic）であり、staticを付けていないものはデフォルトで動的として扱われる。\n\n## 静的化の特徴\n\n| 特徴 | 説明 |\n|------|------|\n| **メモリ確保時期** | プログラム実行開始時に確保、終了時まで解放しない |\n| **アクセス方法** | クラス名.メンバ名 でアクセス（インスタンス不要） |\n| **共有性** | 全インスタンスで共有される |\n| **制約** | 静的メソッドから非静的メンバにはアクセスできない |\n\n## staticを使うべき場面\n\n1. **定数の定義**\n   ```java\n   public static final double PI = 3.14159;\n   ```\n\n2. **ユーティリティメソッド**（Mathクラスなど）\n   ```java\n   public static int max(int a, int b) {\n       return (a > b) ? a : b;\n   }\n   ```\n\n3. **インスタンスのカウント**\n   ```java\n   private static int count = 0;\n   public Student() {\n       count++;  // 生成されたインスタンスをカウント\n   }\n   ```\n\n4. **ファクトリーメソッド**\n   ```java\n   public static Student createStudent(String name) {\n       return new Student(name);\n   }\n   ```\n\n## staticを使うべきでない場面\n\n- インスタンスごとに異なる値を持つべきフィールド\n- インスタンスの状態に依存するメソッド",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'staticの基本使用例',
                        'content' => "public class Student {\n    // クラス変数（全インスタンスで共有）\n    private static int totalCount = 0;\n    private static final String SCHOOL_NAME = \"ABC高校\";\n    \n    // インスタンス変数（インスタンスごとに個別）\n    private String name;\n    private int id;\n    \n    // コンストラクタ\n    public Student(String name) {\n        this.name = name;\n        this.id = ++totalCount;  // 自動採番\n    }\n    \n    // クラスメソッド（静的メソッド）\n    public static int getTotalCount() {\n        return totalCount;          // クラス変数参照可\n        // return this.name;        // インスタンス変数参照不可（コンパイルエラー）\n        // ※thisはインスタンスを指すが、静的メソッドはインスタンス生成前から使えるため\n    }\n    \n    public static String getSchoolName() {\n        return SCHOOL_NAME;\n    }\n    \n    // インスタンスメソッド（動的メソッド）\n    public void introduce() {\n        System.out.println(SCHOOL_NAME + \"の\" + name + \"です（ID: \" + id + \"）\");  // クラス変数もインスタンス変数も参照可\n        System.out.println(\"現在の生徒数: \" + totalCount);\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        // インスタンス生成前でもクラスメソッドは使える\n        System.out.println(\"学校名: \" + Student.getSchoolName());\n        System.out.println(\"生徒数: \" + Student.getTotalCount());  // 0\n        \n        // インスタンス生成\n        Student s1 = new Student(\"太郎\");\n        Student s2 = new Student(\"花子\");\n        Student s3 = new Student(\"次郎\");\n        \n        // クラス変数は全インスタンスで共有されている\n        System.out.println(\"生徒数: \" + Student.getTotalCount());  // 3\n        \n        s1.introduce();\n        // 出力: ABC高校の太郎です（ID: 1）\n        //      現在の生徒数: 3\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：Cardクラス（トランプ）',
                        'content' => "public class Card {\n    // クラス定数（全カードで共通）\n    private static final String[] SUITS = {\"♠\", \"♥\", \"♦\", \"♣\"};\n    private static final String[] RANKS = {\"A\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\", \"10\", \"J\", \"Q\", \"K\"};\n    \n    // クラス変数（全カードで共有）\n    private static int totalCardsCreated = 0;\n    \n    // インスタンス変数（各カード固有）\n    private String suit;  // スート（マーク）\n    private String rank;  // ランク（数字・記号）\n    private int value;    // 値\n    \n    // コンストラクタ\n    public Card(String suit, String rank) {\n        this.suit = suit;\n        this.rank = rank;\n        this.value = calculateValue(rank);\n        totalCardsCreated++;\n    }\n    \n    // 静的メソッド：デッキ（52枚）を生成するファクトリーメソッド\n    public static Card[] createDeck() {\n        Card[] deck = new Card[52];\n        int index = 0;\n        \n        for (String suit : SUITS) {\n            for (String rank : RANKS) {\n                deck[index++] = new Card(suit, rank);\n            }\n        }\n        \n        return deck;\n    }\n    \n    // 静的メソッド：生成されたカード総数を取得\n    public static int getTotalCardsCreated() {\n        return totalCardsCreated;\n    }\n    \n    // 静的メソッド：ランクから値を計算（ユーティリティ）\n    private static int calculateValue(String rank) {\n        switch (rank) {\n            case \"A\": return 11;\n            case \"J\":\n            case \"Q\":\n            case \"K\": return 10;\n            default: return Integer.parseInt(rank);\n        }\n    }\n    \n    // インスタンスメソッド\n    public String toString() {\n        return suit + rank;\n    }\n    \n    public int getValue() {\n        return this.value;\n    }\n}\n\n// 使用例\npublic class CardGame {\n    public static void main(String[] args) {\n        // ファクトリーメソッドでデッキを生成\n        Card[] deck = Card.createDeck();\n        \n        System.out.println(\"デッキを生成しました（52枚）\");\n        System.out.println(\"生成されたカード総数: \" + Card.getTotalCardsCreated());\n        \n        // 最初の5枚を表示\n        System.out.println(\"\\n最初の5枚:\");\n        for (int i = 0; i < 5; i++) {\n            System.out.println(deck[i] + \" (値: \" + deck[i].getValue() + \")\");\n        }\n    }\n}\n\n// 出力例:\n// デッキを生成しました（52枚）\n// 生成されたカード総数: 52\n//\n// 最初の5枚:\n// ♠A (値: 11)\n// ♠2 (値: 2)\n// ♠3 (値: 3)\n// ♠4 (値: 4)\n// ♠5 (値: 5)",
                        'code_language' => 'java',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'note',
                        'title' => 'staticフィールドとメソッドの制約',
                        'content' => "# staticメソッドからのアクセス制約\n\n## ❌ 静的メソッドからできないこと\n\n```java\npublic class Example {\n    private String instanceVar = \"インスタンス変数\";\n    private static String classVar = \"クラス変数\";\n    \n    public static void staticMethod() {\n        // ❌ インスタンス変数にアクセスできない\n        // System.out.println(instanceVar);  // コンパイルエラー\n        \n        // ❌ thisキーワードは使えない\n        // this.classVar = \"変更\";  // コンパイルエラー\n        \n        // ❌ インスタンスメソッドを呼び出せない\n        // instanceMethod();  // コンパイルエラー\n    }\n    \n    public void instanceMethod() {\n        System.out.println(\"インスタンスメソッド\");\n    }\n}\n```\n\n## ✅ 静的メソッドからできること\n\n```java\npublic class Example {\n    private static String classVar = \"クラス変数\";\n    \n    public static void staticMethod1() {\n        // ✅ クラス変数にアクセスできる\n        System.out.println(classVar);\n        \n        // ✅ 他の静的メソッドを呼び出せる\n        staticMethod2();\n        \n        // ✅ インスタンスを生成すればインスタンスメンバにアクセスできる\n        Example obj = new Example();\n        obj.instanceMethod();\n    }\n    \n    public static void staticMethod2() {\n        System.out.println(\"静的メソッド2\");\n    }\n    \n    public void instanceMethod() {\n        System.out.println(\"インスタンスメソッド\");\n    }\n}\n```\n\n## なぜこのような制約があるのか\n\n```\nプログラム起動\n    ↓\n【静的メンバがメモリに配置】← この時点で使える\n    ↓\nクラスのロード\n    ↓\nnew でインスタンス化\n    ↓\n【インスタンスメンバがメモリに配置】← この時点で使える\n```\n\n静的メソッドは**インスタンス化前から存在**しているため、\nまだ存在しないインスタンスメンバにはアクセスできない。\n\n## staticの誤用例\n\n```java\n// ❌ 悪い例：全てをstaticにする\npublic class BadExample {\n    private static String name;\n    private static int age;\n    \n    public static void setName(String n) {\n        name = n;  // 全インスタンスで共有されてしまう！\n    }\n}\n\n// ✅ 良い例：インスタンスごとに異なる値はstaticにしない\npublic class GoodExample {\n    private String name;  // インスタンス変数\n    private int age;      // インスタンス変数\n    \n    public void setName(String name) {\n        this.name = name;  // 各インスタンス固有\n    }\n}",
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'インスタンス配列の使用例',
                        'content' => "// インスタンス配列とは：インスタンスを配列で管理する\npublic class Monster {\n    private String name;\n    private int hp;\n    \n    public Monster(String name, int hp) {\n        this.name = name;\n        this.hp = hp;\n    }\n    \n    public void introduce() {\n        System.out.println(name + \" (HP: \" + hp + \")\");\n    }\n    \n    public String getName() {\n        return name;\n    }\n}\n\npublic class MonsterParty {\n    public static void main(String[] args) {\n        // インスタンス配列の宣言と生成\n        Monster[] party = new Monster[3];\n        \n        // 注意：配列を作っただけでは各要素はnull\n        // System.out.println(party[0].getName());  // NullPointerException!\n        \n        // 各要素をインスタンス化する必要がある\n        party[0] = new Monster(\"ピカチュウ\", 100);\n        party[1] = new Monster(\"リザードン\", 150);\n        party[2] = new Monster(\"カビゴン\", 200);\n        \n        // 配列をループで処理\n        System.out.println(\"=== パーティメンバー ===\");\n        for (int i = 0; i < party.length; i++) {\n            System.out.print((i + 1) + \". \");\n            party[i].introduce();\n        }\n        \n        // 拡張for文（for-each）でも処理可能\n        System.out.println(\"\\n=== 拡張for文 ===\");\n        for (Monster monster : party) {\n            monster.introduce();\n        }\n        \n        // 配列の初期化を同時に行う方法\n        Monster[] enemies = {\n            new Monster(\"スライム\", 50),\n            new Monster(\"ゴブリン\", 80),\n            new Monster(\"ドラゴン\", 300)\n        };\n        \n        System.out.println(\"\\n=== 敵モンスター ===\");\n        for (Monster enemy : enemies) {\n            enemy.introduce();\n        }\n    }\n}\n\n// 出力:\n// === パーティメンバー ===\n// 1. ピカチュウ (HP: 100)\n// 2. リザードン (HP: 150)\n// 3. カビゴン (HP: 200)\n//\n// === 拡張for文 ===\n// ピカチュウ (HP: 100)\n// リザードン (HP: 150)\n// カビゴン (HP: 200)\n//\n// === 敵モンスター ===\n// スライム (HP: 50)\n// ゴブリン (HP: 80)\n// ドラゴン (HP: 300)",
                        'code_language' => 'java',
                        'sort_order' => 6
                    },
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
                        'content' => "# 継承とは\n\n新しく作成するクラスに既存のクラスを引き継ぐこと。\n継承を行うことで、既存のクラス側のフィールド（属性）やメソッド（操作）の機能を持ち使用することができる。\n\n## 継承の用語\n\n| 用語 | 別名 | 説明 |\n|------|------|------|\n| **スーパークラス** | 親クラス、基底クラス | 継承される側のクラス |\n| **サブクラス** | 子クラス、派生クラス | 継承する側のクラス |\n\n## 継承の基本構文\n\n```java\npublic class サブクラス extends スーパークラス {\n    // サブクラス独自のフィールドとメソッド\n}\n```\n\n## 継承の特徴\n\n- **extends**修飾子を使って継承\n- コンストラクタは継承されない\n- **単一継承**のみ（1つのクラスからしか継承できない）\n- privateメンバは継承されるが、直接アクセスできない\n- protectedメンバはサブクラスからアクセス可能\n\n## なぜ継承するのか\n\n汎用性が高いということは逆に言えば【影響力も高い】ということになります。\nモンスターの中には空を飛べる・水中を泳げる・地中に潜れるといったそれぞれ固有の特徴（機能）を持ったものもいます。\nMonsterクラスにそういった固有の機能を全て搭載してしまった場合、そのMonsterクラスは\n空も飛べて、水中を泳げて、地中に潜れるモンスターを生み出す為の設計図となります。\n\nオブジェクト指向の考えとしては【飛べないモンスターに空を飛ぶという機能や、泳げないモンスターに泳ぐという機能は与えるべきではない】ということです。\n\n## 継承のメリット\n\n1. **コードの再利用**: 新たに同じコードを書く必要がなく、開発工数が減る\n2. **保守性の向上**: 修正する際の影響度が低くなる\n3. **一括更新**: スーパークラスの変更が全サブクラスに反映される\n4. **is-a関係の表現**: 「FireMonster は Monster である」という関係を表現できる",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '継承の基本例',
                        'content' => "// スーパークラス（親クラス）\npublic class Monster {\n    protected String name;  // protectedでサブクラスからアクセス可能\n    protected int hp;\n    protected int attack;\n    \n    public Monster(String name, int hp, int attack) {\n        this.name = name;\n        this.hp = hp;\n        this.attack = attack;\n    }\n    \n    public void introduce() {\n        System.out.println(\"私は\" + name + \"です。HP:\" + hp);\n    }\n    \n    public void normalAttack() {\n        System.out.println(name + \"の通常攻撃！ダメージ:\" + attack);\n    }\n}\n\n// サブクラス（子クラス）- 炎タイプのモンスター\npublic class FireMonster extends Monster {\n    private int firepower;  // サブクラス独自のフィールド\n    \n    public FireMonster(String name, int hp, int attack, int firepower) {\n        super(name, hp, attack);  // スーパークラスのコンストラクタ呼び出し\n        this.firepower = firepower;\n    }\n    \n    // サブクラス独自のメソッド\n    public void fireAttack() {\n        System.out.println(name + \"の火炎放射！ダメージ:\" + (attack + firepower));\n    }\n}\n\n// サブクラス - 水タイプのモンスター\npublic class WaterMonster extends Monster {\n    private int waterpower;\n    \n    public WaterMonster(String name, int hp, int attack, int waterpower) {\n        super(name, hp, attack);\n        this.waterpower = waterpower;\n    }\n    \n    public void waterAttack() {\n        System.out.println(name + \"のハイドロポンプ！ダメージ:\" + (attack + waterpower));\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        FireMonster charmander = new FireMonster(\"ヒトカゲ\", 80, 50, 30);\n        WaterMonster squirtle = new WaterMonster(\"ゼニガメ\", 90, 45, 35);\n        \n        // スーパークラスから継承したメソッド\n        charmander.introduce();      // 私はヒトカゲです。HP:80\n        charmander.normalAttack();   // ヒトカゲの通常攻撃！ダメージ:50\n        \n        // サブクラス独自のメソッド\n        charmander.fireAttack();     // ヒトカゲの火炎放射！ダメージ:80\n        \n        squirtle.introduce();\n        squirtle.waterAttack();      // ゼニガメのハイドロポンプ！ダメージ:80\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => 'superキーワード',
                        'content' => "# superとは\n\n**super**とは【継承しているスーパー（親）クラス】を指すキーワード。\nスーパークラスのメンバやコンストラクタにアクセスする際に使用する。\n\n## superの3つの用途\n\n### 1. スーパークラスのコンストラクタを呼び出す\n\n```java\nsuper(引数);  // スーパークラスのコンストラクタ呼び出し\n```\n\n- **必ずコンストラクタの最初の行**に記述する\n- 記述しない場合、コンパイラが自動的に`super();`を追加\n- スーパークラスにデフォルトコンストラクタがない場合はエラー\n\n### 2. スーパークラスのフィールドにアクセス\n\n```java\nsuper.フィールド名\n```\n\nサブクラスで同名のフィールドを定義している場合に、\nスーパークラスのフィールドを明示的にアクセスできる。\n\n### 3. スーパークラスのメソッドを呼び出す\n\n```java\nsuper.メソッド名()\n```\n\nオーバーライドしたメソッド内から、スーパークラスの元のメソッドを呼び出せる。\n\n## super使用時の注意点\n\n```java\npublic class FireMonster extends Monster {\n    public FireMonster(String name, int hp) {\n        // ❌ エラー：super()はコンストラクタの最初に書く必要がある\n        this.firepower = 100;\n        super(name, hp, 50);  // コンパイルエラー\n    }\n    \n    public FireMonster(String name, int hp) {\n        // ✅ 正しい：最初の行に記述\n        super(name, hp, 50);\n        this.firepower = 100;\n    }\n}",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'superの実践的な使用例',
                        'content' => "public class Monster {\n    protected String name;\n    protected int hp;\n    protected int level;\n    \n    public Monster(String name, int hp, int level) {\n        this.name = name;\n        this.hp = hp;\n        this.level = level;\n        System.out.println(\"Monsterのコンストラクタが実行されました\");\n    }\n    \n    public void showStatus() {\n        System.out.println(\"[\" + name + \"] HP:\" + hp + \" Level:\" + level);\n    }\n}\n\npublic class FireMonster extends Monster {\n    private int firepower;\n    \n    public FireMonster(String name, int hp, int level, int firepower) {\n        // super()でスーパークラスのコンストラクタを呼び出す\n        super(name, hp, level);  // 必ず最初の行\n        this.firepower = firepower;\n        System.out.println(\"FireMonsterのコンストラクタが実行されました\");\n    }\n    \n    // メソッドのオーバーライド\n    @Override\n    public void showStatus() {\n        // super.メソッド名()でスーパークラスのメソッドを呼び出す\n        super.showStatus();  // Monsterクラスのshow Statusを実行\n        System.out.println(\"火力:\" + firepower);\n    }\n    \n    public void levelUp() {\n        // super.フィールド名でスーパークラスのフィールドにアクセス\n        super.level++;  // protectedなのでアクセス可能\n        this.firepower += 10;\n        System.out.println(name + \"がレベルアップした！ Level:\" + level);\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        FireMonster charmander = new FireMonster(\"ヒトカゲ\", 80, 5, 50);\n        // 出力:\n        // Monsterのコンストラクタが実行されました\n        // FireMonsterのコンストラクタが実行されました\n        \n        charmander.showStatus();\n        // 出力:\n        // [ヒトカゲ] HP:80 Level:5\n        // 火力:50\n        \n        charmander.levelUp();\n        // 出力: ヒトカゲがレベルアップした！ Level:6\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    },
                    [
                        'type' => 'note',
                        'title' => 'メソッドのオーバーライド',
                        'content' => "# オーバーライドとは\n\nスーパークラスに定義しているメソッドと**同一のシグネチャ**を持つメソッドをサブクラスに定義すること。\n\n**シグネチャ** = メソッド名 + 引数の型 + 引数の数\n\nサブクラスのオブジェクトに対してメソッドを呼び出した場合、【サブクラス側のメソッドが優先して実行される】\n\n## オーバーライドの条件\n\n| 項目 | 条件 |\n|------|------|\n| メソッド名 | 同じ |\n| 引数の型・数・順序 | 同じ |\n| 戻り値の型 | 同じ（または共変戻り値型） |\n| アクセス修飾子 | 同じか、より広い範囲 |\n\n## @Overrideアノテーション\n\n```java\n@Override\npublic void attack() {\n    // オーバーライドしていることを明示\n}\n```\n\n- オーバーライドの意図を明示する\n- シグネチャが一致しない場合、コンパイルエラーを出してくれる\n- **必須ではないが、使用を強く推奨**\n\n## オーバーライドできるもの・できないもの\n\n### ✅ オーバーライドできる\n- publicメソッド\n- protectedメソッド\n- デフォルト（package-private）メソッド\n\n### ❌ オーバーライドできない\n- privateメソッド（サブクラスから見えない）\n- staticメソッド（クラスに属するため）\n- finalメソッド（変更不可として宣言されているため）\n- コンストラクタ（継承されないため）\n\n## オーバーライドの例\n\n```java\npublic class Monster {\n    public void attack() {\n        System.out.println(\"通常攻撃！\");\n    }\n}\n\npublic class FireMonster extends Monster {\n    @Override\n    public void attack() {\n        System.out.println(\"火炎攻撃！\");  // 上書き\n    }\n    \n    // ❌ エラー：戻り値の型が異なる\n    // @Override\n    // public int attack() { return 0; }\n    \n    // ❌ エラー：アクセス修飾子を狭くできない\n    // @Override\n    // private void attack() { }\n    \n    // ✅ OK：アクセス修飾子を広くするのはOK\n    @Override\n    public void attack() { }\n}",
                        'sort_order' => 5
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：Monsterクラスの継承階層',
                        'content' => "// 基底クラス\npublic class Monster {\n    protected String name;\n    protected int hp;\n    protected int attack;\n    protected String type;\n    \n    public Monster(String name, int hp, int attack, String type) {\n        this.name = name;\n        this.hp = hp;\n        this.attack = attack;\n        this.type = type;\n    }\n    \n    public void introduce() {\n        System.out.println(\"名前:\" + name + \" / タイプ:\" + type);\n    }\n    \n    public void attack(Monster target) {\n        System.out.println(name + \"の攻撃！\");\n        target.receiveDamage(this.attack);\n    }\n    \n    public void receiveDamage(int damage) {\n        this.hp -= damage;\n        System.out.println(name + \"は\" + damage + \"のダメージを受けた！ 残りHP:\" + hp);\n        if (this.hp <= 0) {\n            System.out.println(name + \"は倒れた...\");\n        }\n    }\n}\n\n// 炎タイプ\npublic class FireMonster extends Monster {\n    private int firepower;\n    \n    public FireMonster(String name, int hp, int attack, int firepower) {\n        super(name, hp, attack, \"炎\");\n        this.firepower = firepower;\n    }\n    \n    @Override\n    public void attack(Monster target) {\n        System.out.println(name + \"の火炎攻撃！\");\n        int totalDamage = this.attack + this.firepower;\n        target.receiveDamage(totalDamage);\n    }\n    \n    public void fireBreath() {\n        System.out.println(name + \"の大火炎！威力:\" + (attack + firepower * 2));\n    }\n}\n\n// 水タイプ\npublic class WaterMonster extends Monster {\n    private int waterpower;\n    \n    public WaterMonster(String name, int hp, int attack, int waterpower) {\n        super(name, hp, attack, \"水\");\n        this.waterpower = waterpower;\n    }\n    \n    @Override\n    public void attack(Monster target) {\n        System.out.println(name + \"の水鉄砲！\");\n        int totalDamage = this.attack + this.waterpower;\n        \n        // 相手が炎タイプなら2倍ダメージ\n        if (target instanceof FireMonster) {\n            totalDamage *= 2;\n            System.out.println(\"効果は抜群だ！\");\n        }\n        \n        target.receiveDamage(totalDamage);\n    }\n}\n\n// 使用例\npublic class Battle {\n    public static void main(String[] args) {\n        FireMonster lizardon = new FireMonster(\"リザードン\", 150, 50, 40);\n        WaterMonster kamex = new WaterMonster(\"カメックス\", 160, 45, 45);\n        \n        lizardon.introduce();  // 名前:リザードン / タイプ:炎\n        kamex.introduce();     // 名前:カメックス / タイプ:水\n        \n        System.out.println(\"\\n=== バトル開始！ ===\");\n        \n        // ラウンド1\n        lizardon.attack(kamex);  // リザードンの火炎攻撃！\n        // カメックスは90のダメージを受けた！ 残りHP:70\n        \n        // ラウンド2\n        kamex.attack(lizardon);  // カメックスの水鉄砲！\n        // 効果は抜群だ！\n        // リザードンは180のダメージを受けた！ 残りHP:-30\n        // リザードンは倒れた...\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 6
                    },
                    [
                        'type' => 'note',
                        'title' => '継承の設計原則',
                        'content' => "# is-a関係とhas-a関係\n\n## is-a関係（継承を使う）\n\n「FireMonster **は** Monster **である**」という関係\n\n```java\npublic class FireMonster extends Monster { }\n// FireMonsterはMonsterの一種\n```\n\n適切な例：\n- 犬 is-a 動物 ✅\n- リンゴ is-a 果物 ✅\n- 正社員 is-a 従業員 ✅\n\n## has-a関係（コンポジションを使う）\n\n「Monster **は** Weapon を**持っている**」という関係\n\n```java\npublic class Monster {\n    private Weapon weapon;  // MonsterはWeaponを持つ\n}\n```\n\n適切な例：\n- 車 has-a エンジン ✅\n- 人 has-a 住所 ✅\n- モンスター has-a 武器 ✅\n\n## 継承を使うべき場合・使うべきでない場合\n\n### ✅ 継承を使うべき場合\n- 明確なis-a関係がある\n- スーパークラスのメソッドをサブクラスでオーバーライドする必要がある\n- ポリモーフィズムを活用したい\n\n### ❌ 継承を使うべきでない場合\n```java\n// 悪い例：Stackは Vectorではない（実装の継承）\nclass Stack extends Vector { }  // ❌\n\n// 良い例：Stackは Vectorを持つ（コンポジション）\nclass Stack {\n    private Vector data;  // ✅\n}\n```\n\n```java\n// 悪い例：コードを再利用したいだけで継承\nclass Employee extends Person { }  // Personの機能が欲しいだけ ❌\n\n// 良い例：本当にis-a関係があるときだけ継承\nclass Manager extends Employee { }  // ManagerはEmployeeの一種 ✅\n```\n\n## 継承階層の深さ\n\n```\n深すぎる継承階層は避ける：\nAnimal → Mammal → Carnivore → Feline → Cat → DomesticCat → ペルシャ猫 ❌\n\n適切な深さ：\nAnimal → Mammal → Cat ✅\n```\n\n一般的には**3～4階層まで**が管理しやすい。",
                        'sort_order' => 7
                    },
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
                        'title' => 'ポリモーフィズムとは',
                        'content' => "# ポリモーフィズム（多態性）とは\n\n直訳では**多態性**を意味し、オブジェクト指向においては\n【同じ名称のメソッドを呼び出しても、オブジェクトによって異なる処理が実行されること】を指す。\n\n## ポリモーフィズムの3つのメリット\n\n1. **コードの簡潔性**: 同じコードで異なる処理を扱える\n2. **拡張性**: 新しいサブクラスを追加しても既存コードの変更が不要\n3. **保守性**: 処理の共通化により保守が容易\n\n## ポリモーフィズムが実現される条件\n\n1. 継承関係がある（サブクラスがスーパークラスを継承）\n2. メソッドがオーバーライドされている\n3. スーパークラス型の変数でサブクラスのインスタンスを参照\n\n```java\n// ポリモーフィズムの基本形\nMonster monster1 = new FireMonster();  // アップキャスト\nMonster monster2 = new WaterMonster(); // アップキャスト\n\nmonster1.attack();  // FireMonsterのattackが実行される\nmonster2.attack();  // WaterMonsterのattackが実行される\n// ↑ 同じメソッド名だが、実際の型に応じて異なる処理が実行される\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '型の互換性とアップキャスト',
                        'content' => "# 型の互換性\n\nスーパークラスを継承したサブクラスはスーパークラスの機能を引き継ぐ為、互換性を持つ。\nその為、サブクラスのインスタンスはスーパークラスのデータ型変数に格納することが可能である。\n\n```java\n// 例：MonsterクラスをFireMonsterクラスが継承している場合\nFireMonster hitokage = new FireMonster();\n// ↑ 型が一致している為、もちろん格納可能\n\nMonster hitokage = new FireMonster();\n// ↑ FireMonsterインスタンスはMonsterクラスの機能を保持している為、格納可能\n\nFireMonster hitokage = new Monster();\n// ↑ MonsterインスタンスはFireMonsterクラスの機能を持たない為、コンパイルエラーとなる\n```\n\n## アップキャスト（暗黙の型変換）\n\nサブクラス型からスーパークラス型への変換を**アップキャスト**という。\nアップキャストは**自動的に行われる**（明示的なキャストが不要）。\n\n```java\nFireMonster fire = new FireMonster(\"ヒトカゲ\", 80, 50, 30);\nMonster monster = fire;  // アップキャスト（自動）\n```\n\n## アップキャスト後のアクセス制限\n\n```java\nMonster monster = new FireMonster(\"ヒトカゲ\", 80, 50, 30);\nmonster.introduce();  // ✅ OK\n// monster.fireBreath();  // ❌ エラー: FireMonster固有のメソッドは呼び出せない\n```",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'ポリモーフィズムの実践例',
                        'content' => "public class Monster {\n    protected String name;\n    protected int hp;\n    \n    public Monster(String name, int hp) {\n        this.name = name;\n        this.hp = hp;\n    }\n    \n    public void attack() {\n        System.out.println(name + \"の攻撃！\");\n    }\n}\n\npublic class FireMonster extends Monster {\n    public FireMonster(String name, int hp) {\n        super(name, hp);\n    }\n    \n    @Override\n    public void attack() {\n        System.out.println(name + \"の火炎攻撃！！！\");\n    }\n}\n\npublic class WaterMonster extends Monster {\n    public WaterMonster(String name, int hp) {\n        super(name, hp);\n    }\n    \n    @Override\n    public void attack() {\n        System.out.println(name + \"の水鉄砲！\");\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // ポリモーフィズム：スーパークラス型の配列で管理\n        Monster[] party = new Monster[3];\n        party[0] = new FireMonster(\"ヒトカゲ\", 80);\n        party[1] = new WaterMonster(\"ゼニガメ\", 90);\n        party[2] = new Monster(\"ピカチュウ\", 85);\n        \n        // 同じコードで異なる処理を実行！\n        for (Monster m : party) {\n            m.attack();  // 実際の型に応じた攻撃が実行される\n        }\n        \n        /* 出力:\n        ヒトカゲの火炎攻撃！！！\n        ゼニガメの水鉄砲！\n        ピカチュウの攻撃！\n        */\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'note',
                        'title' => 'ダウンキャストとinstanceof',
                        'content' => "# ダウンキャスト\n\nスーパークラス型からサブクラス型への変換を**ダウンキャスト**という。\nダウンキャストは**明示的にキャストが必要**で、実行時エラーのリスクがある。\n\n```java\nMonster monster = new FireMonster(\"ヒトカゲ\", 80, 50, 30);\nFireMonster fire = (FireMonster) monster;  // ダウンキャスト\nfire.fireBreath();  // OK\n```\n\n## ダウンキャストの危険性\n\n```java\nMonster monster = new WaterMonster(\"ゼニガメ\", 90);\nFireMonster fire = (FireMonster) monster;  // ❌ ClassCastException!\n```\n\n## instanceof演算子で安全にチェック\n\n```java\nMonster monster = new FireMonster(\"ヒトカゲ\", 80, 50, 30);\n\nif (monster instanceof FireMonster) {\n    FireMonster fire = (FireMonster) monster;\n    fire.fireBreath();  // 安全\n}\n```\n\n## instanceofの使用例\n\n```java\nfor (Monster m : monsters) {\n    if (m instanceof FireMonster) {\n        System.out.println(\"炎タイプ！\");\n    } else if (m instanceof WaterMonster) {\n        System.out.println(\"水タイプ！\");\n    }\n}\n```",
                        'sort_order' => 4
                    },
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
                        'title' => '抽象クラスとは',
                        'content' => "# 抽象クラスとは\n\n**抽象クラス**は、インスタンス化できない不完全なクラス。\n\n## 抽象の概念\n\n多くの物から共通属性を抜き出し、一般的概念としてとらえること。\n\n- ピカチュウ・ヒトカゲ → **モンスター**\n- 飛行機・船・車 → **乗り物**\n\n## 抽象クラスの特徴\n\n| 特徴 | 可否 |\n|------|------|\n| インスタンス化 | ❌ 不可 |\n| 継承 | ✅ 可能 |\n| 抽象メソッド | ✅ 持てる |\n| 具象メソッド | ✅ 持てる |\n| フィールド | ✅ 持てる |\n\n## 使う理由\n\n1. **共通処理の提供**\n2. **実装の強制**\n3. **実装忘れ防止**",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '抽象クラスの定義と実装',
                        'content' => "// 抽象クラス\npublic abstract class RPGCharacter {\n    protected String name;\n    protected int hp;\n    \n    public RPGCharacter(String name, int hp) {\n        this.name = name;\n        this.hp = hp;\n    }\n    \n    // 具象メソッド\n    public void showStatus() {\n        System.out.println(name + \" HP:\" + hp);\n    }\n    \n    // 抽象メソッド\n    public abstract void attack();\n    public abstract void specialSkill();\n}\n\n// 具象クラス\npublic class Warrior extends RPGCharacter {\n    public Warrior(String name) {\n        super(name, 150);\n    }\n    \n    @Override\n    public void attack() {\n        System.out.println(name + \"は剣で攻撃！\");\n    }\n    \n    @Override\n    public void specialSkill() {\n        System.out.println(name + \"の必殺技！\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => 'Template Methodパターン',
                        'content' => "# Template Methodパターン\n\n処理の枠組みを抽象クラスで定義し、具体処理をサブクラスで実装。\n\n## メリット\n\n1. 処理の流れを統一\n2. 共通処理の一元管理\n3. 実装の強制\n\n```java\npublic abstract class Game {\n    public final void play() {\n        start();     // 共通\n        doAction();  // 抽象\n        end();       // 共通\n    }\n    \n    protected abstract void doAction();\n}\n```",
                        'sort_order' => 3
                    },
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
                        'content' => "# インターフェイスとは\n\nクラスが持つべきメソッドを記した**契約書**・**ルールブック**。\n\n## 特徴\n\n| 項目 | 内容 |\n|------|------|\n| メソッド | 全て抽象（public abstract省略可） |\n| フィールド | 定数のみ（public static final省略可） |\n| 多重実装 | ✅ 可能 |\n| インスタンス化 | ❌ 不可 |\n\n```java\npublic interface Flyable {\n    void fly();  // public abstract省略\n}\n\npublic class Bird implements Flyable {\n    @Override\n    public void fly() {\n        System.out.println(\"飛んでいます\");\n    }\n}\n```",
                        'sort_order' => 1
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => '複数インターフェイスの実装',
                        'content' => "// インターフェイス定義\npublic interface Flyable {\n    void fly();\n}\n\npublic interface Swimmable {\n    void swim();\n}\n\n// 複数実装（カンマ区切り）\npublic class Duck implements Flyable, Swimmable {\n    @Override\n    public void fly() {\n        System.out.println(\"アヒルが飛んでいます\");\n    }\n    \n    @Override\n    public void swim() {\n        System.out.println(\"アヒルが泳いでいます\");\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        Duck duck = new Duck();\n        duck.fly();\n        duck.swim();\n        \n        // ポリモーフィズム\n        Flyable flyer = new Duck();\n        flyer.fly();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => '抽象クラス vs インターフェイス',
                        'content' => "# 比較表\n\n| 項目 | 抽象クラス | インターフェイス |\n|------|-----------|----------------|\n| 具象メソッド | ✅ 可 | ❌ 不可 |\n| フィールド | ✅ 可 | 定数のみ |\n| 多重継承 | ❌ 不可 | ✅ 可 |\n| コンストラクタ | ✅ 可 | ❌ 不可 |\n\n## 使い分け\n\n**抽象クラス**: is-a関係、共通実装あり  \n**インターフェイス**: can-do関係、契約のみ\n\n```java\n// 抽象クラス：共通実装\nabstract class Animal {\n    void eat() { /* 共通 */ }\n    abstract void move();\n}\n\n// インターフェイス：機能契約\ninterface Flyable {\n    void fly();\n}\n```",
                        'sort_order' => 3
                    },
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '拡張for文（for-each）',
                        'content' => "# 拡張for文とは\n\n配列やコレクションの全要素を簡単に処理できる構文。\n\n## 構文\n\n```java\nfor (型 変数名 : 配列orコレクション) {\n    // 処理\n}\n```\n\n## メリット\n\n- インデックス不要\n- シンプルで読みやすい\n- 範囲外エラーなし\n\n```java\nString[] names = {\"太郎\", \"花子\", \"次郎\"};\n\n// 従来のfor文\nfor (int i = 0; i < names.length; i++) {\n    System.out.println(names[i]);\n}\n\n// 拡張for文\nfor (String name : names) {\n    System.out.println(name);\n}\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '列挙型（enum）とは',
                        'content' => "# 列挙型（enum）\n\n関連する定数をグループ化した特殊なクラス。\n\n## 定義\n\n```java\npublic enum Season {\n    SPRING, SUMMER, AUTUMN, WINTER\n}\n```\n\n## メリット\n\n1. **型安全**: 定義された値のみ使用可能\n2. **可読性**: 意味が明確\n3. **保守性**: 一箇所で管理\n\n## 使用例\n\n```java\nSeason season = Season.SPRING;\n\nswitch (season) {\n    case SPRING:\n        System.out.println(\"春です\");\n        break;\n    case SUMMER:\n        System.out.println(\"夏です\");\n        break;\n}\n```",
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'enum実践例：じゃんけん',
                        'content' => "// じゃんけんenum\npublic enum Hand {\n    ROCK(\"グー\"),\n    SCISSORS(\"チョキ\"),\n    PAPER(\"パー\");\n    \n    private String name;\n    \n    Hand(String name) {\n        this.name = name;\n    }\n    \n    public String getName() {\n        return name;\n    }\n    \n    // 勝敗判定\n    public boolean beats(Hand other) {\n        return (this == ROCK && other == SCISSORS) ||\n               (this == SCISSORS && other == PAPER) ||\n               (this == PAPER && other == ROCK);\n    }\n}\n\n// 使用例\npublic class Main {\n    public static void main(String[] args) {\n        Hand player = Hand.ROCK;\n        Hand computer = Hand.SCISSORS;\n        \n        System.out.println(\"あなた: \" + player.getName());\n        System.out.println(\"相手: \" + computer.getName());\n        \n        if (player.beats(computer)) {\n            System.out.println(\"勝ち！\");\n        } else if (computer.beats(player)) {\n            System.out.println(\"負け！\");\n        } else {\n            System.out.println(\"引き分け！\");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    },
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'コレクションフレームワーク',
                        'content' => "# コレクションフレームワークとは\n\n複数のデータをまとめて管理するための仕組み。配列より柔軟で便利。\n\n## 主要インターフェイス\n\n| インターフェイス | 特徴 | 実装クラス |\n|---------------|------|----------|\n| **List** | 順序あり、重複可 | ArrayList, LinkedList |\n| **Set** | 順序なし、重複不可 | HashSet, TreeSet |\n| **Map** | キーと値のペア | HashMap, TreeMap |\n\n## 配列との違い\n\n```java\n// 配列: サイズ固定\nString[] array = new String[3];\n\n// ArrayList: サイズ可変\nArrayList<String> list = new ArrayList<>();\nlist.add(\"要素1\");\nlist.add(\"要素2\");\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ArrayList（List）の使い方',
                        'content' => "import java.util.ArrayList;\n\npublic class Main {\n    public static void main(String[] args) {\n        // ArrayList作成\n        ArrayList<String> names = new ArrayList<>();\n        \n        // 追加\n        names.add(\"太郎\");\n        names.add(\"花子\");\n        names.add(\"次郎\");\n        \n        // 取得\n        System.out.println(names.get(0));  // 太郎\n        \n        // サイズ\n        System.out.println(names.size());  // 3\n        \n        // 削除\n        names.remove(1);  // 花子を削除\n        \n        // 存在チェック\n        System.out.println(names.contains(\"太郎\"));  // true\n        \n        // 全要素表示\n        for (String name : names) {\n            System.out.println(name);\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'HashSet（Set）の使い方',
                        'content' => "import java.util.HashSet;\n\npublic class Main {\n    public static void main(String[] args) {\n        // HashSet作成（重複なし）\n        HashSet<String> set = new HashSet<>();\n        \n        // 追加\n        set.add(\"りんご\");\n        set.add(\"バナナ\");\n        set.add(\"りんご\");  // 重複は追加されない\n        \n        System.out.println(set.size());  // 2\n        \n        // 存在チェック\n        System.out.println(set.contains(\"りんご\"));  // true\n        \n        // 全要素表示（順序不定）\n        for (String fruit : set) {\n            System.out.println(fruit);\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    },
                    [
                        'type' => 'code_snippet',
                        'title' => 'HashMap（Map）の使い方',
                        'content' => "import java.util.HashMap;\n\npublic class Main {\n    public static void main(String[] args) {\n        // HashMap作成（キー→値）\n        HashMap<String, Integer> scores = new HashMap<>();\n        \n        // 追加\n        scores.put(\"太郎\", 85);\n        scores.put(\"花子\", 92);\n        scores.put(\"次郎\", 78);\n        \n        // 取得\n        System.out.println(scores.get(\"花子\"));  // 92\n        \n        // キー存在チェック\n        System.out.println(scores.containsKey(\"太郎\"));  // true\n        \n        // 全要素表示\n        for (String name : scores.keySet()) {\n            System.out.println(name + \": \" + scores.get(name));\n        }\n        \n        // または\n        for (var entry : scores.entrySet()) {\n            System.out.println(entry.getKey() + \": \" + entry.getValue());\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    },
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '例外処理とは',
                        'content' => "# 例外処理\n\nプログラム実行中のエラーを適切に処理する仕組み。\n\n## なぜ必要か\n\n```java\n// 例外処理なし → プログラムが異常終了\nint result = 10 / 0;  // ArithmeticException!\n\n// 例外処理あり → エラーを捕捉して処理継続\ntry {\n    int result = 10 / 0;\n} catch (ArithmeticException e) {\n    System.out.println(\"0で割れません\");\n}\nSystem.out.println(\"処理続行\");\n```\n\n## 基本構文\n\n```java\ntry {\n    // エラーが発生する可能性のある処理\n} catch (例外型 変数名) {\n    // エラー発生時の処理\n} finally {\n    // 必ず実行される処理（省略可）\n}\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'try-catch の実践例',
                        'content' => "import java.util.Scanner;\n\npublic class Main {\n    public static void main(String[] args) {\n        Scanner scanner = new Scanner(System.in);\n        \n        try {\n            System.out.print(\"数値を入力: \");\n            String input = scanner.nextLine();\n            int num = Integer.parseInt(input);\n            \n            System.out.print(\"割る数: \");\n            int divisor = Integer.parseInt(scanner.nextLine());\n            \n            int result = num / divisor;\n            System.out.println(\"結果: \" + result);\n            \n        } catch (NumberFormatException e) {\n            System.out.println(\"数値を入力してください\");\n        } catch (ArithmeticException e) {\n            System.out.println(\"0では割れません\");\n        } catch (Exception e) {\n            System.out.println(\"エラー: \" + e.getMessage());\n        } finally {\n            System.out.println(\"処理終了\");\n            scanner.close();\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    },
                    [
                        'type' => 'note',
                        'title' => '例外の種類',
                        'content' => "# 例外の階層\n\n```\nThrowable\n├── Error（システムエラー）\n└── Exception\n    ├── RuntimeException（実行時例外）\n    │   ├── NullPointerException\n    │   ├── ArithmeticException\n    │   └── ArrayIndexOutOfBoundsException\n    └── IOException等（チェック例外）\n```\n\n## 2種類の例外\n\n| 種類 | 説明 | 例 |\n|------|------|----|\n| **RuntimeException** | 実行時例外、try-catch不要 | NullPointerException |\n| **チェック例外** | コンパイル時チェック、try-catch必須 | IOException |\n\n```java\n// RuntimeException（try-catchなしでもOK）\nint[] arr = {1, 2, 3};\nSystem.out.println(arr[5]);  // 実行時エラー\n\n// チェック例外（try-catch必須）\nFileReader fr = new FileReader(\"file.txt\");  // ❌ コンパイルエラー\n\n// 正しい書き方\ntry {\n    FileReader fr = new FileReader(\"file.txt\");\n} catch (FileNotFoundException e) {\n    e.printStackTrace();\n}\n```",
                        'sort_order' => 3
                    },
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'スレッド（マルチスレッド）とは',
                        'content' => "# スレッドとは\n\n**スレッド**は、プログラムの実行単位です。1つのプログラム内で複数の処理を同時に実行することを**マルチスレッド**といいます。\n\n## スレッドのメリット\n- 複数の処理を並行実行できる\n- 時間のかかる処理中も他の処理を継続できる\n- CPUの複数コアを有効活用できる\n\n## スレッドの作成方法\nJavaでスレッドを作成するには2つの方法があります：\n1. **Threadクラスを継承**する方法\n2. **Runnableインターフェイスを実装**する方法",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Threadクラスを継承する方法',
                        'content' => "// Threadクラスを継承してスレッドを作成\npublic class MyThread extends Thread {\n    @Override\n    public void run() {\n        // スレッドで実行される処理\n        for (int i = 0; i < 5; i++) {\n            System.out.println(Thread.currentThread().getName() + \": \" + i);\n            try {\n                Thread.sleep(500); // 0.5秒待機\n            } catch (InterruptedException e) {\n                e.printStackTrace();\n            }\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        MyThread thread1 = new MyThread();\n        MyThread thread2 = new MyThread();\n        \n        thread1.start(); // スレッド開始\n        thread2.start(); // 別スレッド開始\n        \n        System.out.println(\"メインスレッド終了\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Runnableインターフェイスを実装する方法',
                        'content' => "// Runnableインターフェイスを実装（推奨）\npublic class MyRunnable implements Runnable {\n    @Override\n    public void run() {\n        for (int i = 0; i < 5; i++) {\n            System.out.println(Thread.currentThread().getName() + \": \" + i);\n            try {\n                Thread.sleep(500);\n            } catch (InterruptedException e) {\n                e.printStackTrace();\n            }\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // Runnableをインスタンス化\n        MyRunnable runnable = new MyRunnable();\n        \n        // ThreadにRunnableを渡す\n        Thread thread1 = new Thread(runnable);\n        Thread thread2 = new Thread(runnable);\n        \n        thread1.start();\n        thread2.start();\n    }\n}\n\n// Runnableは他のクラスを継承できるため、より柔軟です",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'スレッドの同期（synchronized）',
                        'content' => "# スレッドの同期\n\n複数のスレッドが同じリソース（変数、オブジェクト）にアクセスすると、**競合状態**が発生する可能性があります。\n\n## 同期化の必要性\n```java\nclass Counter {\n    private int count = 0;\n    \n    public void increment() {\n        count++; // 非アトミック操作：読み込み→加算→書き込み\n    }\n}\n// 複数スレッドから同時にincrement()を呼ぶと、期待通りの結果にならない可能性がある\n```\n\n## synchronizedキーワード\n**synchronized**を使うことで、同時に1つのスレッドだけがメソッドを実行できます。\n\n```java\nclass Counter {\n    private int count = 0;\n    \n    // メソッド全体を同期化\n    public synchronized void increment() {\n        count++;\n    }\n    \n    public synchronized int getCount() {\n        return count;\n    }\n}\n```\n\n## synchronizedブロック\n```java\npublic void increment() {\n    // 必要な部分だけ同期化\n    synchronized(this) {\n        count++;\n    }\n}\n```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '同期化の実践例',
                        'content' => "class BankAccount {\n    private int balance = 1000;\n    \n    // 預金（同期化）\n    public synchronized void deposit(int amount) {\n        balance += amount;\n        System.out.println(Thread.currentThread().getName() + \n                         \" deposited \" + amount + \", balance: \" + balance);\n    }\n    \n    // 引き出し（同期化）\n    public synchronized void withdraw(int amount) {\n        if (balance >= amount) {\n            balance -= amount;\n            System.out.println(Thread.currentThread().getName() + \n                             \" withdrew \" + amount + \", balance: \" + balance);\n        } else {\n            System.out.println(\"残高不足\");\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        BankAccount account = new BankAccount();\n        \n        // 複数のスレッドが同じ口座にアクセス\n        Thread t1 = new Thread(() -> account.deposit(500));\n        Thread t2 = new Thread(() -> account.withdraw(300));\n        Thread t3 = new Thread(() -> account.withdraw(800));\n        \n        t1.start();\n        t2.start();\n        t3.start();\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'ファイル入出力の基本',
                        'content' => "# ファイル入出力とは\n\nJavaでファイルにデータを書き込んだり、ファイルからデータを読み込んだりする処理です。\n\n## 主なクラス\n\n### テキストファイル用\n- **FileReader**: ファイルから文字を読み込む\n- **FileWriter**: ファイルに文字を書き込む\n- **BufferedReader**: バッファを使って効率的に読み込む\n- **BufferedWriter**: バッファを使って効率的に書き込む\n\n### バイナリファイル用\n- **FileInputStream**: バイトデータを読み込む\n- **FileOutputStream**: バイトデータを書き込む\n\n## try-with-resources構文\nファイル操作では必ずリソースを閉じる必要があります。try-with-resources構文を使うと自動的にクローズされます。\n\n```java\ntry (FileReader fr = new FileReader(\"file.txt\")) {\n    // ファイル操作\n} catch (IOException e) {\n    e.printStackTrace();\n}\n// 自動的にfr.close()が呼ばれる\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイルの読み込み（BufferedReader）',
                        'content' => "import java.io.*;\n\npublic class FileReadExample {\n    public static void main(String[] args) {\n        // try-with-resources構文（自動クローズ）\n        try (BufferedReader br = new BufferedReader(\n                new FileReader(\"data.txt\"))) {\n            \n            String line;\n            // 1行ずつ読み込む\n            while ((line = br.readLine()) != null) {\n                System.out.println(line);\n            }\n            \n        } catch (FileNotFoundException e) {\n            System.out.println(\"ファイルが見つかりません: \" + e.getMessage());\n        } catch (IOException e) {\n            System.out.println(\"読み込みエラー: \" + e.getMessage());\n        }\n    }\n}\n\n// BufferedReaderを使うとreadLine()で1行ずつ効率的に読める",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイルの書き込み（BufferedWriter）',
                        'content' => "import java.io.*;\n\npublic class FileWriteExample {\n    public static void main(String[] args) {\n        // try-with-resources構文\n        try (BufferedWriter bw = new BufferedWriter(\n                new FileWriter(\"output.txt\"))) {\n            \n            // 文字列を書き込む\n            bw.write(\"Hello, World!\");\n            bw.newLine(); // 改行\n            \n            bw.write(\"これはテストファイルです。\");\n            bw.newLine();\n            \n            bw.write(\"Java ファイル入出力\");\n            \n            System.out.println(\"ファイルに書き込みました\");\n            \n        } catch (IOException e) {\n            System.out.println(\"書き込みエラー: \" + e.getMessage());\n        }\n    }\n}\n\n// FileWriter(\"file.txt\", true)の第2引数をtrueにすると追記モード",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイルのコピー（実践例）',
                        'content' => "import java.io.*;\n\npublic class FileCopyExample {\n    public static void main(String[] args) {\n        String sourceFile = \"source.txt\";\n        String destFile = \"destination.txt\";\n        \n        // 読み込みと書き込みを同時に行う\n        try (BufferedReader br = new BufferedReader(new FileReader(sourceFile));\n             BufferedWriter bw = new BufferedWriter(new FileWriter(destFile))) {\n            \n            String line;\n            while ((line = br.readLine()) != null) {\n                bw.write(line);\n                bw.newLine();\n            }\n            \n            System.out.println(\"ファイルをコピーしました\");\n            \n        } catch (FileNotFoundException e) {\n            System.out.println(\"ファイルが見つかりません\");\n        } catch (IOException e) {\n            System.out.println(\"エラーが発生しました: \" + e.getMessage());\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ファイル操作のベストプラクティス',
                        'content' => "# ファイル操作のポイント\n\n## 1. 必ずtry-with-resourcesを使う\n```java\n// ❌ 古い方法（手動クローズ）\nFileReader fr = null;\ntry {\n    fr = new FileReader(\"file.txt\");\n    // 処理\n} finally {\n    if (fr != null) fr.close();\n}\n\n// ✅ 推奨方法（自動クローズ）\ntry (FileReader fr = new FileReader(\"file.txt\")) {\n    // 処理\n}\n```\n\n## 2. Bufferedクラスを使う\n- BufferedReaderとBufferedWriterを使うことで、I/O処理が高速化されます\n- readLine()で1行ずつ読める便利さもあります\n\n## 3. 例外処理を適切に行う\n- FileNotFoundException: ファイルが存在しない\n- IOException: 読み書き中のエラー\n\n## 4. ファイルパス\n- 相対パス: \"data.txt\" （プログラムの実行ディレクトリからの相対）\n- 絶対パス: \"/home/user/data.txt\" （ルートからの絶対）",
                        'sort_order' => 5
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'ストリームとは',
                        'content' => "# ストリームとは\n\n**ストリーム**は、データの入出力を抽象化した概念です。データを「流れ」として扱います。\n\n## ストリームの種類\n\n### 1. バイトストリーム（バイナリデータ）\n- **InputStream**: バイトデータの入力\n- **OutputStream**: バイトデータの出力\n- 画像、音声、動画などのバイナリファイルを扱う\n\n### 2. キャラクタストリーム（テキストデータ）\n- **Reader**: 文字データの入力\n- **Writer**: 文字データの出力\n- テキストファイルを扱う（前回学習）\n\n## 主なバイトストリームクラス\n- **FileInputStream / FileOutputStream**: ファイル入出力\n- **ObjectInputStream / ObjectOutputStream**: オブジェクトの入出力（シリアライズ）\n- **DataInputStream / DataOutputStream**: プリミティブ型の入出力",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'バイナリファイルのコピー',
                        'content' => "import java.io.*;\n\npublic class BinaryFileCopy {\n    public static void main(String[] args) {\n        // 画像ファイルなどのバイナリファイルをコピー\n        try (FileInputStream fis = new FileInputStream(\"input.jpg\");\n             FileOutputStream fos = new FileOutputStream(\"output.jpg\")) {\n            \n            int byteData;\n            // 1バイトずつ読み込んで書き込む\n            while ((byteData = fis.read()) != -1) {\n                fos.write(byteData);\n            }\n            \n            System.out.println(\"ファイルをコピーしました\");\n            \n        } catch (IOException e) {\n            System.out.println(\"エラー: \" + e.getMessage());\n        }\n    }\n}\n\n// 効率化：byte配列を使う\n// byte[] buffer = new byte[1024];\n// int length;\n// while ((length = fis.read(buffer)) != -1) {\n//     fos.write(buffer, 0, length);\n// }",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'シリアライズとは',
                        'content' => "# シリアライズ（直列化）\n\n**シリアライズ**は、Javaオブジェクトをバイトストリームに変換し、ファイルやネットワークで送受信できるようにする技術です。\n\n## シリアライズの用途\n- オブジェクトをファイルに保存\n- ネットワーク経由でオブジェクトを送信\n- オブジェクトの状態を永続化\n\n## Serializableインターフェイス\nシリアライズ可能なクラスは**Serializable**インターフェイスを実装する必要があります。\n\n```java\nimport java.io.Serializable;\n\npublic class Person implements Serializable {\n    private String name;\n    private int age;\n    \n    // コンストラクタ、getter、setterなど\n}\n```\n\n## 重要な注意点\n- **transient**キーワードを付けたフィールドはシリアライズされない\n- **serialVersionUID**を定義することを推奨（クラスのバージョン管理）",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'オブジェクトのシリアライズと復元',
                        'content' => "import java.io.*;\n\n// シリアライズ可能なクラス\nclass Person implements Serializable {\n    private static final long serialVersionUID = 1L;\n    \n    private String name;\n    private int age;\n    \n    public Person(String name, int age) {\n        this.name = name;\n        this.age = age;\n    }\n    \n    @Override\n    public String toString() {\n        return \"Person{name='\" + name + \"', age=\" + age + \"}\";\n    }\n}\n\npublic class SerializeExample {\n    public static void main(String[] args) {\n        Person person = new Person(\"田中太郎\", 25);\n        \n        // オブジェクトをファイルに保存（シリアライズ）\n        try (ObjectOutputStream oos = new ObjectOutputStream(\n                new FileOutputStream(\"person.dat\"))) {\n            \n            oos.writeObject(person);\n            System.out.println(\"オブジェクトを保存しました\");\n            \n        } catch (IOException e) {\n            e.printStackTrace();\n        }\n        \n        // ファイルからオブジェクトを復元（デシリアライズ）\n        try (ObjectInputStream ois = new ObjectInputStream(\n                new FileInputStream(\"person.dat\"))) {\n            \n            Person loadedPerson = (Person) ois.readObject();\n            System.out.println(\"復元: \" + loadedPerson);\n            \n        } catch (IOException | ClassNotFoundException e) {\n            e.printStackTrace();\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'transientキーワードの使用',
                        'content' => "import java.io.*;\n\nclass User implements Serializable {\n    private static final long serialVersionUID = 1L;\n    \n    private String username;\n    private transient String password; // シリアライズしない\n    private int loginCount;\n    \n    public User(String username, String password, int loginCount) {\n        this.username = username;\n        this.password = password;\n        this.loginCount = loginCount;\n    }\n    \n    @Override\n    public String toString() {\n        return \"User{username='\" + username + \"', password='\" + \n               password + \"', loginCount=\" + loginCount + \"}\";\n    }\n}\n\npublic class TransientExample {\n    public static void main(String[] args) {\n        User user = new User(\"admin\", \"secret123\", 100);\n        System.out.println(\"保存前: \" + user);\n        \n        // シリアライズ\n        try (ObjectOutputStream oos = new ObjectOutputStream(\n                new FileOutputStream(\"user.dat\"))) {\n            oos.writeObject(user);\n        } catch (IOException e) {\n            e.printStackTrace();\n        }\n        \n        // デシリアライズ\n        try (ObjectInputStream ois = new ObjectInputStream(\n                new FileInputStream(\"user.dat\"))) {\n            User loaded = (User) ois.readObject();\n            System.out.println(\"復元後: \" + loaded);\n            // passwordはnullになる（transientなので保存されない）\n        } catch (IOException | ClassNotFoundException e) {\n            e.printStackTrace();\n        }\n    }\n}\n\n// 出力:\n// 保存前: User{username='admin', password='secret123', loginCount=100}\n// 復元後: User{username='admin', password='null', loginCount=100}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'throwキーワード：例外を投げる',
                        'content' => "# throwキーワード\n\n**throw**キーワードを使うと、プログラマが意図的に例外を発生させることができます。\n\n## throwの使い方\n```java\nthrow new 例外クラス(\"エラーメッセージ\");\n```\n\n## 使用場面\n- 不正な値や状態を検出したとき\n- ビジネスロジックのエラーを表現したいとき\n- 事前条件や事後条件が満たされないとき\n\n## 基本例\n```java\npublic void setAge(int age) {\n    if (age < 0) {\n        throw new IllegalArgumentException(\"年齢は0以上でなければなりません\");\n    }\n    this.age = age;\n}\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'throwの実践例',
                        'content' => "public class BankAccount {\n    private int balance;\n    \n    public BankAccount(int initialBalance) {\n        if (initialBalance < 0) {\n            throw new IllegalArgumentException(\n                \"初期残高は0以上でなければなりません\");\n        }\n        this.balance = initialBalance;\n    }\n    \n    public void withdraw(int amount) {\n        if (amount < 0) {\n            throw new IllegalArgumentException(\n                \"引き出し金額は0以上でなければなりません\");\n        }\n        if (balance < amount) {\n            throw new IllegalStateException(\n                \"残高不足です。残高: \" + balance + \", 引き出し額: \" + amount);\n        }\n        balance -= amount;\n    }\n    \n    public int getBalance() {\n        return balance;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        try {\n            BankAccount account = new BankAccount(1000);\n            account.withdraw(1500); // IllegalStateException発生\n        } catch (IllegalStateException e) {\n            System.out.println(\"エラー: \" + e.getMessage());\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'throwsキーワード：例外を宣言する',
                        'content' => "# throwsキーワード\n\n**throws**キーワードは、メソッドが例外を投げる可能性があることを宣言します。\n\n## throwsの使い方\n```java\npublic void メソッド名() throws 例外クラス {\n    // 例外を投げる可能性のある処理\n}\n```\n\n## checked例外とunchecked例外\n\n### Checked例外（検査例外）\n- **コンパイル時にチェックされる**例外\n- throwsで宣言するか、try-catchで処理する必要がある\n- 例: IOException, SQLException, ClassNotFoundException\n\n### Unchecked例外（非検査例外）\n- **実行時に発生する**例外（RuntimeException継承）\n- throwsで宣言する必要はない\n- 例: NullPointerException, IllegalArgumentException, ArithmeticException\n\n## 複数の例外を宣言\n```java\npublic void readFile() throws IOException, FileNotFoundException {\n    // ファイル読み込み処理\n}\n```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'throwsの実践例',
                        'content' => "import java.io.*;\n\npublic class FileProcessor {\n    \n    // IOExceptionをthrowsで宣言\n    public String readFile(String filename) throws IOException {\n        BufferedReader br = new BufferedReader(new FileReader(filename));\n        StringBuilder content = new StringBuilder();\n        String line;\n        \n        while ((line = br.readLine()) != null) {\n            content.append(line).append(\"\\n\");\n        }\n        br.close();\n        return content.toString();\n    }\n    \n    // 呼び出し側で例外処理が必要\n    public void processFile(String filename) {\n        try {\n            String content = readFile(filename);\n            System.out.println(content);\n        } catch (FileNotFoundException e) {\n            System.out.println(\"ファイルが見つかりません: \" + filename);\n        } catch (IOException e) {\n            System.out.println(\"読み込みエラー: \" + e.getMessage());\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        FileProcessor processor = new FileProcessor();\n        processor.processFile(\"data.txt\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'カスタム例外クラスの作成',
                        'content' => "# カスタム例外クラス\n\n独自の例外クラスを作成することで、アプリケーション固有のエラーを表現できます。\n\n## カスタム例外の作成方法\n\n### 1. Exceptionを継承（Checked例外）\n```java\npublic class MyException extends Exception {\n    public MyException(String message) {\n        super(message);\n    }\n}\n```\n\n### 2. RuntimeExceptionを継承（Unchecked例外）\n```java\npublic class MyRuntimeException extends RuntimeException {\n    public MyRuntimeException(String message) {\n        super(message);\n    }\n}\n```\n\n## カスタム例外を使う理由\n- ビジネスロジックのエラーを明確に表現\n- エラーの種類を区別しやすい\n- 適切な例外処理を促進\n\n## 命名規則\n- クラス名は「○○Exception」とする\n- エラーの内容が分かる名前にする",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'カスタム例外の実装例',
                        'content' => "// カスタム例外クラス（Checked例外）\nclass InsufficientBalanceException extends Exception {\n    private int balance;\n    private int amount;\n    \n    public InsufficientBalanceException(int balance, int amount) {\n        super(\"残高不足: 残高=\" + balance + \", 引き出し額=\" + amount);\n        this.balance = balance;\n        this.amount = amount;\n    }\n    \n    public int getBalance() { return balance; }\n    public int getShortfall() { return amount - balance; }\n}\n\nclass BankAccount {\n    private int balance;\n    \n    public BankAccount(int balance) {\n        this.balance = balance;\n    }\n    \n    // カスタム例外を投げる\n    public void withdraw(int amount) throws InsufficientBalanceException {\n        if (balance < amount) {\n            throw new InsufficientBalanceException(balance, amount);\n        }\n        balance -= amount;\n    }\n    \n    public int getBalance() { return balance; }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        BankAccount account = new BankAccount(5000);\n        \n        try {\n            account.withdraw(7000);\n        } catch (InsufficientBalanceException e) {\n            System.out.println(\"エラー: \" + e.getMessage());\n            System.out.println(\"不足額: \" + e.getShortfall() + \"円\");\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 6
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'ガーベッジコレクションとは',
                        'content' => "# ガーベッジコレクション（GC）\n\n**ガーベッジコレクション**は、Javaが自動的に不要になったメモリを解放する仕組みです。\n\n## メモリ管理\n\n### 他の言語（C言語など）\n```c\n// メモリを手動で確保\nint* ptr = malloc(sizeof(int));\n// 使用後に手動で解放が必要\nfree(ptr);\n```\n\n### Javaの場合\n```java\n// メモリは自動で確保される\nPerson person = new Person();\n// 解放は不要（GCが自動で行う）\n```\n\n## ガーベッジコレクションのタイミング\n- オブジェクトへの参照がなくなったとき\n- メモリが不足してきたとき\n- System.gc()を呼び出したとき（推奨されない）\n\n## メモリリーク防止\n```java\n// ❌ 静的コレクションに追加し続けるとメモリリーク\npublic class Cache {\n    private static List<Object> cache = new ArrayList<>();\n    public static void add(Object obj) {\n        cache.add(obj); // 参照が残り続ける\n    }\n}\n\n// ✅ 適切にクリアする\ncache.clear(); // 参照を削除\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'コレクションとラムダ式',
                        'content' => "# コレクションとラムダ式の組み合わせ\n\nJava 8以降、コレクションにラムダ式を使った便利なメソッドが追加されました。\n\n## forEach()メソッド\n```java\nList<String> names = Arrays.asList(\"太郎\", \"花子\", \"次郎\");\n\n// 従来のfor文\nfor (String name : names) {\n    System.out.println(name);\n}\n\n// ラムダ式を使用\nnames.forEach(name -> System.out.println(name));\n\n// メソッド参照（さらに簡潔）\nnames.forEach(System.out::println);\n```\n\n## removeIf()メソッド\n```java\nList<Integer> numbers = new ArrayList<>(Arrays.asList(1, 2, 3, 4, 5, 6));\n\n// 偶数を削除\nnumbers.removeIf(n -> n % 2 == 0);\n// 結果: [1, 3, 5]\n```\n\n## sort()メソッド\n```java\nList<String> names = new ArrayList<>(Arrays.asList(\"佐藤\", \"田中\", \"鈴木\"));\n\n// 昇順ソート\nnames.sort((a, b) -> a.compareTo(b));\n\n// メソッド参照\nnames.sort(String::compareTo);\n```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Stream APIの基礎',
                        'content' => "import java.util.*;\nimport java.util.stream.*;\n\npublic class StreamExample {\n    public static void main(String[] args) {\n        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);\n        \n        // Stream APIを使った処理のパイプライン\n        List<Integer> result = numbers.stream()\n            .filter(n -> n % 2 == 0)        // 偶数のみフィルタ\n            .map(n -> n * 2)                // 2倍にする\n            .collect(Collectors.toList());  // リストに変換\n        \n        System.out.println(result); // [4, 8, 12, 16, 20]\n        \n        // 合計を計算\n        int sum = numbers.stream()\n            .filter(n -> n % 2 == 0)\n            .mapToInt(n -> n)\n            .sum();\n        \n        System.out.println(\"偶数の合計: \" + sum); // 30\n        \n        // 文字列のStream\n        List<String> names = Arrays.asList(\"田中\", \"佐藤\", \"鈴木\", \"高橋\");\n        \n        names.stream()\n            .filter(name -> name.startsWith(\"田\"))\n            .forEach(System.out::println); // 田中\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Stream APIの主なメソッド',
                        'content' => "# Stream APIの主なメソッド\n\n## 中間操作（Intermediate Operations）\nStreamを返すため、メソッドチェーンで繋げられます。\n\n- **filter()**: 条件に合う要素だけを残す\n- **map()**: 各要素を変換する\n- **sorted()**: ソートする\n- **distinct()**: 重複を除去する\n- **limit()**: 最初のN個だけ取得\n- **skip()**: 最初のN個をスキップ\n\n## 終端操作（Terminal Operations）\n結果を生成してStreamを終了します。\n\n- **forEach()**: 各要素に処理を実行\n- **collect()**: リストやマップに変換\n- **count()**: 要素数を数える\n- **anyMatch()**: 条件に合う要素があるか\n- **allMatch()**: 全ての要素が条件に合うか\n- **findFirst()**: 最初の要素を取得\n- **reduce()**: 要素を集約する\n\n## 例\n```java\nList<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);\n\n// 3より大きい数があるか\nboolean hasLarge = numbers.stream()\n    .anyMatch(n -> n > 3); // true\n\n// 全て10未満か\nboolean allSmall = numbers.stream()\n    .allMatch(n -> n < 10); // true\n\n// 要素数をカウント\nlong count = numbers.stream()\n    .filter(n -> n % 2 == 0)\n    .count(); // 2\n```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Stream APIの実践例',
                        'content' => "import java.util.*;\nimport java.util.stream.*;\n\nclass Person {\n    private String name;\n    private int age;\n    \n    public Person(String name, int age) {\n        this.name = name;\n        this.age = age;\n    }\n    \n    public String getName() { return name; }\n    public int getAge() { return age; }\n}\n\npublic class StreamPractice {\n    public static void main(String[] args) {\n        List<Person> people = Arrays.asList(\n            new Person(\"田中\", 25),\n            new Person(\"佐藤\", 30),\n            new Person(\"鈴木\", 20),\n            new Person(\"高橋\", 35)\n        );\n        \n        // 25歳以上の人の名前を取得\n        List<String> names = people.stream()\n            .filter(p -> p.getAge() >= 25)\n            .map(Person::getName)\n            .collect(Collectors.toList());\n        \n        System.out.println(\"25歳以上: \" + names); // [田中, 佐藤, 高橋]\n        \n        // 平均年齢を計算\n        double avgAge = people.stream()\n            .mapToInt(Person::getAge)\n            .average()\n            .orElse(0.0);\n        \n        System.out.println(\"平均年齢: \" + avgAge); // 27.5\n        \n        // 最年長の人を取得\n        Optional<Person> oldest = people.stream()\n            .max(Comparator.comparing(Person::getAge));\n        \n        oldest.ifPresent(p -> \n            System.out.println(\"最年長: \" + p.getName() + \"(\" + p.getAge() + \"歳)\"));\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Compositeパターンとは',
                        'content' => "# Compositeパターン（コンポジットパターン）\n\n**Compositeパターン**は、オブジェクトを木構造で表現し、個別のオブジェクトと複合オブジェクトを同じように扱えるようにするデザインパターンです。\n\n## 使用場面\n- ファイルシステム（ファイルとフォルダ）\n- 組織図（社員と部署）\n- GUIのコンポーネント（ボタンやパネル）\n- メニュー（メニュー項目とサブメニュー）\n\n## 構成要素\n1. **Component（共通インターフェイス）**: 葉と枝に共通の操作を定義\n2. **Leaf（葉）**: 子を持たない末端のオブジェクト\n3. **Composite（枝）**: 子を持つことができるオブジェクト\n\n## メリット\n- 単一オブジェクトと複合オブジェクトを同じ方法で扱える\n- 木構造を簡単に表現できる\n- 新しい種類のコンポーネントを追加しやすい",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Compositeパターンの実装例（ファイルシステム）',
                        'content' => "import java.util.*;\n\n// Component（共通インターフェイス）\nabstract class FileSystemComponent {\n    protected String name;\n    \n    public FileSystemComponent(String name) {\n        this.name = name;\n    }\n    \n    public abstract void print(String indent);\n    public abstract int getSize();\n}\n\n// Leaf（ファイル）\nclass File extends FileSystemComponent {\n    private int size;\n    \n    public File(String name, int size) {\n        super(name);\n        this.size = size;\n    }\n    \n    @Override\n    public void print(String indent) {\n        System.out.println(indent + \"📄 \" + name + \" (\" + size + \"KB)\");\n    }\n    \n    @Override\n    public int getSize() {\n        return size;\n    }\n}\n\n// Composite（フォルダ）\nclass Folder extends FileSystemComponent {\n    private List<FileSystemComponent> children = new ArrayList<>();\n    \n    public Folder(String name) {\n        super(name);\n    }\n    \n    public void add(FileSystemComponent component) {\n        children.add(component);\n    }\n    \n    @Override\n    public void print(String indent) {\n        System.out.println(indent + \"📁 \" + name);\n        for (FileSystemComponent child : children) {\n            child.print(indent + \"  \");\n        }\n    }\n    \n    @Override\n    public int getSize() {\n        int total = 0;\n        for (FileSystemComponent child : children) {\n            total += child.getSize();\n        }\n        return total;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // ルートフォルダ\n        Folder root = new Folder(\"root\");\n        \n        // ドキュメントフォルダ\n        Folder documents = new Folder(\"Documents\");\n        documents.add(new File(\"report.pdf\", 500));\n        documents.add(new File(\"memo.txt\", 10));\n        \n        // 画像フォルダ\n        Folder images = new Folder(\"Images\");\n        images.add(new File(\"photo1.jpg\", 2000));\n        images.add(new File(\"photo2.jpg\", 1800));\n        \n        root.add(documents);\n        root.add(images);\n        root.add(new File(\"readme.txt\", 5));\n        \n        root.print(\"\");\n        System.out.println(\"\\n合計サイズ: \" + root.getSize() + \"KB\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Stateパターンとは',
                        'content' => "# Stateパターン（ステートパターン）\n\n**Stateパターン**は、オブジェクトの状態によって振る舞いを変えるデザインパターンです。状態を表すクラスを作成し、状態の切り替えをカプセル化します。\n\n## 使用場面\n- 自動販売機（待機、商品選択、支払い、商品提供）\n- ゲームキャラクター（通常、攻撃、防御、死亡）\n- TCPコネクション（接続待ち、確立、切断）\n- ストップウォッチ（停止、実行中、一時停止）\n\n## 問題点：if-elseの乱用\n```java\nif (state == WAITING) {\n    // 待機中の処理\n} else if (state == RUNNING) {\n    // 実行中の処理\n} else if (state == PAUSED) {\n    // 一時停止中の処理\n}\n```\n状態が増えると複雑になり、メンテナンスが困難になります。\n\n## Stateパターンの解決策\n各状態をクラスとして独立させ、状態ごとの振る舞いをカプセル化します。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Stateパターンの実装例（信号機）',
                        'content' => "// Stateインターフェイス\ninterface TrafficLightState {\n    void handle(TrafficLight light);\n    String getColor();\n}\n\n// 赤信号\nclass RedState implements TrafficLightState {\n    @Override\n    public void handle(TrafficLight light) {\n        System.out.println(\"赤 → 青に変更\");\n        light.setState(new GreenState());\n    }\n    \n    @Override\n    public String getColor() { return \"赤\"; }\n}\n\n// 青信号\nclass GreenState implements TrafficLightState {\n    @Override\n    public void handle(TrafficLight light) {\n        System.out.println(\"青 → 黄に変更\");\n        light.setState(new YellowState());\n    }\n    \n    @Override\n    public String getColor() { return \"青\"; }\n}\n\n// 黄信号\nclass YellowState implements TrafficLightState {\n    @Override\n    public void handle(TrafficLight light) {\n        System.out.println(\"黄 → 赤に変更\");\n        light.setState(new RedState());\n    }\n    \n    @Override\n    public String getColor() { return \"黄\"; }\n}\n\n// コンテキスト\nclass TrafficLight {\n    private TrafficLightState state;\n    \n    public TrafficLight() {\n        this.state = new RedState();\n    }\n    \n    public void setState(TrafficLightState state) {\n        this.state = state;\n    }\n    \n    public void change() {\n        state.handle(this);\n    }\n    \n    public String getCurrentColor() {\n        return state.getColor();\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        TrafficLight light = new TrafficLight();\n        \n        System.out.println(\"現在: \" + light.getCurrentColor());\n        light.change();\n        System.out.println(\"現在: \" + light.getCurrentColor());\n        light.change();\n        System.out.println(\"現在: \" + light.getCurrentColor());\n        light.change();\n        System.out.println(\"現在: \" + light.getCurrentColor());\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Stateパターンのメリット',
                        'content' => "# Stateパターンのメリット\n\n## 1. コードの整理\n- 各状態の処理が独立したクラスになる\n- if-elseの複雑な条件分岐が不要になる\n\n## 2. 新しい状態の追加が容易\n- 新しいStateクラスを追加するだけ\n- 既存のコードを変更する必要がない\n\n## 3. 状態遷移の管理が明確\n- 各状態クラスで次の状態を決定\n- 状態遷移のルールが分かりやすい\n\n## 4. 単一責任の原則\n- 各状態クラスは1つの状態のみを担当\n- 責任が明確に分離される\n\n## デメリット\n- クラス数が増える\n- 簡単な状態管理には複雑すぎる場合がある",
                        'sort_order' => 3
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Iteratorパターンとは',
                        'content' => "# Iteratorパターン（イテレータパターン）\n\n**Iteratorパターン**は、コレクション（集合体）の要素に順番にアクセスする方法を提供するデザインパターンです。内部構造を公開せずに要素を走査できます。\n\n## 使用場面\n- 配列やリストの要素を順番に処理\n- データベースのレコードを順番に読み込み\n- ファイルの行を順番に読み込み\n\n## Javaの拡張for文との関係\n```java\nfor (String item : list) {\n    System.out.println(item);\n}\n```\nこの構文は内部的にIteratorを使用しています。\n\n## 構成要素\n1. **Iterator**: 要素を走査するインターフェイス（hasNext, next）\n2. **ConcreteIterator**: Iteratorの具体的な実装\n3. **Aggregate**: コレクションのインターフェイス（iterator()メソッド）\n4. **ConcreteAggregate**: コレクションの具体的な実装",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Iteratorパターンの実装例',
                        'content' => "import java.util.*;\n\n// Iteratorインターフェイス\ninterface Iterator<T> {\n    boolean hasNext();\n    T next();\n}\n\n// Aggregateインターフェイス\ninterface Aggregate<T> {\n    Iterator<T> iterator();\n}\n\n// 本棚クラス（ConcreteAggregate）\nclass BookShelf implements Aggregate<String> {\n    private List<String> books = new ArrayList<>();\n    \n    public void addBook(String book) {\n        books.add(book);\n    }\n    \n    @Override\n    public Iterator<String> iterator() {\n        return new BookShelfIterator(this);\n    }\n    \n    public int getLength() {\n        return books.size();\n    }\n    \n    public String getBookAt(int index) {\n        return books.get(index);\n    }\n}\n\n// 本棚のIterator（ConcreteIterator）\nclass BookShelfIterator implements Iterator<String> {\n    private BookShelf bookShelf;\n    private int index;\n    \n    public BookShelfIterator(BookShelf bookShelf) {\n        this.bookShelf = bookShelf;\n        this.index = 0;\n    }\n    \n    @Override\n    public boolean hasNext() {\n        return index < bookShelf.getLength();\n    }\n    \n    @Override\n    public String next() {\n        String book = bookShelf.getBookAt(index);\n        index++;\n        return book;\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        BookShelf shelf = new BookShelf();\n        shelf.addBook(\"Javaの基本\");\n        shelf.addBook(\"デザインパターン\");\n        shelf.addBook(\"アルゴリズム入門\");\n        shelf.addBook(\"データベース設計\");\n        \n        // Iteratorを使って走査\n        Iterator<String> it = shelf.iterator();\n        while (it.hasNext()) {\n            String book = it.next();\n            System.out.println(book);\n        }\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Iteratorパターンのメリット',
                        'content' => "# Iteratorパターンのメリット\n\n## 1. 内部構造を隠蔽\n- コレクションの内部実装（配列、リストなど）を知る必要がない\n- 配列からリストに変更してもクライアントコードは変わらない\n\n## 2. 統一的なアクセス方法\n```java\n// どんなコレクションでも同じ方法で走査できる\nIterator<String> it = collection.iterator();\nwhile (it.hasNext()) {\n    String item = it.next();\n    // 処理\n}\n```\n\n## 3. 複数の走査を同時実行\n```java\nIterator<String> it1 = shelf.iterator();\nIterator<String> it2 = shelf.iterator();\n// 独立して走査できる\n```\n\n## 4. 拡張for文のサポート\nJavaの`Iterable`インターフェイスを実装すれば、拡張for文が使えます。\n```java\nfor (String book : shelf) {\n    System.out.println(book);\n}\n```",
                        'sort_order' => 3
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Adapterパターンとは',
                        'content' => "# Adapterパターン（アダプターパターン）\n\n**Adapterパターン**は、互換性のないインターフェイス同士を接続するデザインパターンです。既存のクラスを変更せずに、別のインターフェイスに適合させます。\n\n## 使用場面\n- 既存のライブラリを新しいインターフェイスで使いたい\n- 古いコードと新しいコードを連携させたい\n- 異なるシステム間でデータをやり取りしたい\n\n## 身近な例\n- 電源アダプター（日本の100Vプラグを海外の220Vに変換）\n- USBの変換アダプター（USB-C to USB-A）\n- メモリーカードリーダー（SDカードをUSBに変換）\n\n## 構成要素\n1. **Target**: クライアントが期待するインターフェイス\n2. **Adaptee**: 既存の（適合させたい）クラス\n3. **Adapter**: AdapteeをTargetに適合させるクラス",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Adapterパターンの実装例',
                        'content' => "// Target（新しいインターフェイス）\ninterface MediaPlayer {\n    void play(String filename);\n}\n\n// Adaptee（既存のクラス）\nclass OldAudioPlayer {\n    public void playMp3(String filename) {\n        System.out.println(\"MP3再生中: \" + filename);\n    }\n}\n\n// Adapter\nclass AudioPlayerAdapter implements MediaPlayer {\n    private OldAudioPlayer oldPlayer;\n    \n    public AudioPlayerAdapter(OldAudioPlayer oldPlayer) {\n        this.oldPlayer = oldPlayer;\n    }\n    \n    @Override\n    public void play(String filename) {\n        // 新しいインターフェイスを古いメソッドに変換\n        oldPlayer.playMp3(filename);\n    }\n}\n\n// クライアント\npublic class Main {\n    public static void main(String[] args) {\n        // 既存のプレイヤー\n        OldAudioPlayer oldPlayer = new OldAudioPlayer();\n        \n        // Adapterで新しいインターフェイスに適合\n        MediaPlayer player = new AudioPlayerAdapter(oldPlayer);\n        \n        // 新しいインターフェイスで操作\n        player.play(\"song.mp3\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Commandパターンとは',
                        'content' => "# Commandパターン（コマンドパターン）\n\n**Commandパターン**は、命令（コマンド）をオブジェクトとしてカプセル化するデザインパターンです。処理の実行、取り消し、履歴管理などが可能になります。\n\n## 使用場面\n- Undo/Redo機能の実装\n- トランザクション処理\n- マクロ機能（複数のコマンドを記録・再生）\n- ボタンやメニューの動作定義\n\n## メリット\n1. **処理の履歴管理**: 実行したコマンドを記録できる\n2. **Undo/Redo**: コマンドを取り消したり、やり直したりできる\n3. **疎結合**: 命令の送信者と受信者を分離\n4. **コマンドの組み合わせ**: 複数のコマンドをまとめて実行できる\n\n## 構成要素\n1. **Command**: コマンドのインターフェイス\n2. **ConcreteCommand**: 具体的なコマンド\n3. **Receiver**: 実際に処理を行うオブジェクト\n4. **Invoker**: コマンドを実行するオブジェクト",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Commandパターンの実装例（テキストエディタ）',
                        'content' => "import java.util.*;\n\n// Receiver（実際に処理を行う）\nclass TextEditor {\n    private StringBuilder text = new StringBuilder();\n    \n    public void write(String str) {\n        text.append(str);\n    }\n    \n    public void delete(int length) {\n        int start = text.length() - length;\n        if (start >= 0) {\n            text.delete(start, text.length());\n        }\n    }\n    \n    public String getText() {\n        return text.toString();\n    }\n}\n\n// Commandインターフェイス\ninterface Command {\n    void execute();\n    void undo();\n}\n\n// ConcreteCommand: 書き込みコマンド\nclass WriteCommand implements Command {\n    private TextEditor editor;\n    private String text;\n    \n    public WriteCommand(TextEditor editor, String text) {\n        this.editor = editor;\n        this.text = text;\n    }\n    \n    @Override\n    public void execute() {\n        editor.write(text);\n    }\n    \n    @Override\n    public void undo() {\n        editor.delete(text.length());\n    }\n}\n\n// Invoker: コマンド管理\nclass CommandManager {\n    private Stack<Command> history = new Stack<>();\n    \n    public void executeCommand(Command command) {\n        command.execute();\n        history.push(command);\n    }\n    \n    public void undo() {\n        if (!history.isEmpty()) {\n            Command command = history.pop();\n            command.undo();\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        TextEditor editor = new TextEditor();\n        CommandManager manager = new CommandManager();\n        \n        // コマンド実行\n        manager.executeCommand(new WriteCommand(editor, \"Hello \"));\n        System.out.println(editor.getText()); // Hello \n        \n        manager.executeCommand(new WriteCommand(editor, \"World!\"));\n        System.out.println(editor.getText()); // Hello World!\n        \n        // Undo\n        manager.undo();\n        System.out.println(editor.getText()); // Hello \n        \n        manager.undo();\n        System.out.println(editor.getText()); // (空)\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Visitorパターンとは',
                        'content' => "# Visitorパターン（ビジターパターン）\n\n**Visitorパターン**は、データ構造と処理を分離するデザインパターンです。データ構造を変更せずに新しい操作を追加できます。\n\n## 使用場面\n- コンパイラ（構文木の走査と処理）\n- ファイルシステム（ファイルとフォルダの処理）\n- レポート生成（異なる形式で同じデータを出力）\n\n## 問題点：処理の追加が困難\n```java\nclass File {\n    void print() { /* 表示処理 */ }\n    void export() { /* エクスポート処理 */ }\n    // 新しい処理を追加するたびにクラスを修正\n}\n```\n\n## Visitorパターンの解決策\nデータ構造（要素）と処理（Visitor）を分離し、新しい処理はVisitorクラスとして追加します。\n\n## メリット\n- 新しい操作を追加しやすい\n- 関連する処理を1つのクラスにまとめられる\n- データ構造のクラスを変更しなくて済む",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Visitorパターンの実装例（簡易版）',
                        'content' => "import java.util.*;\n\n// Visitor インターフェイス\ninterface Visitor {\n    void visit(File file);\n    void visit(Folder folder);\n}\n\n// Element インターフェイス\ninterface Element {\n    void accept(Visitor visitor);\n}\n\n// File クラス\nclass File implements Element {\n    private String name;\n    private int size;\n    \n    public File(String name, int size) {\n        this.name = name;\n        this.size = size;\n    }\n    \n    public String getName() { return name; }\n    public int getSize() { return size; }\n    \n    @Override\n    public void accept(Visitor visitor) {\n        visitor.visit(this);\n    }\n}\n\n// Folder クラス\nclass Folder implements Element {\n    private String name;\n    private List<Element> elements = new ArrayList<>();\n    \n    public Folder(String name) {\n        this.name = name;\n    }\n    \n    public void add(Element element) {\n        elements.add(element);\n    }\n    \n    public String getName() { return name; }\n    public List<Element> getElements() { return elements; }\n    \n    @Override\n    public void accept(Visitor visitor) {\n        visitor.visit(this);\n    }\n}\n\n// PrintVisitor（表示処理）\nclass PrintVisitor implements Visitor {\n    private String indent = \"\";\n    \n    @Override\n    public void visit(File file) {\n        System.out.println(indent + \"📄 \" + file.getName() + \" (\" + file.getSize() + \"KB)\");\n    }\n    \n    @Override\n    public void visit(Folder folder) {\n        System.out.println(indent + \"📁 \" + folder.getName());\n        String prevIndent = indent;\n        indent += \"  \";\n        for (Element element : folder.getElements()) {\n            element.accept(this);\n        }\n        indent = prevIndent;\n    }\n}\n\n// SizeCalculator（サイズ計算）\nclass SizeCalculator implements Visitor {\n    private int totalSize = 0;\n    \n    @Override\n    public void visit(File file) {\n        totalSize += file.getSize();\n    }\n    \n    @Override\n    public void visit(Folder folder) {\n        for (Element element : folder.getElements()) {\n            element.accept(this);\n        }\n    }\n    \n    public int getTotalSize() { return totalSize; }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        Folder root = new Folder(\"root\");\n        root.add(new File(\"readme.txt\", 5));\n        \n        Folder docs = new Folder(\"docs\");\n        docs.add(new File(\"manual.pdf\", 500));\n        root.add(docs);\n        \n        // 表示\n        PrintVisitor printer = new PrintVisitor();\n        root.accept(printer);\n        \n        // サイズ計算\n        SizeCalculator calculator = new SizeCalculator();\n        root.accept(calculator);\n        System.out.println(\"\\n合計: \" + calculator.getTotalSize() + \"KB\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Singletonパターンとは',
                        'content' => "# Singletonパターン（シングルトンパターン）\n\n**Singletonパターン**は、クラスのインスタンスが1つだけ存在することを保証するデザインパターンです。\n\n## 使用場面\n- アプリケーション設定（1つだけ必要）\n- データベース接続（接続プールの管理）\n- ログ出力（1つのログファイルに書き込む）\n- キャッシュ管理\n\n## 実装方法\n1. コンストラクタをprivateにする\n2. 唯一のインスタンスをstatic変数で保持\n3. インスタンスを取得するstaticメソッドを提供\n\n## メリット\n- インスタンスが1つだけであることを保証\n- グローバルなアクセスポイント\n- 遅延初期化が可能\n\n## デメリット\n- グローバル変数のように使われがち\n- テストが難しくなる場合がある\n- マルチスレッド環境では注意が必要",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Singletonパターンの実装例',
                        'content' => "// Eager Initialization（即座に初期化）\nclass DatabaseConnection {\n    // クラスロード時に唯一のインスタンスを生成\n    private static final DatabaseConnection INSTANCE = new DatabaseConnection();\n    \n    // privateコンストラクタ（外部からnewできない）\n    private DatabaseConnection() {\n        System.out.println(\"データベース接続を確立\");\n    }\n    \n    // 唯一のインスタンスを取得\n    public static DatabaseConnection getInstance() {\n        return INSTANCE;\n    }\n    \n    public void query(String sql) {\n        System.out.println(\"実行: \" + sql);\n    }\n}\n\n// Lazy Initialization（遅延初期化）\nclass Logger {\n    private static Logger instance;\n    \n    private Logger() {\n        System.out.println(\"ロガーを初期化\");\n    }\n    \n    // 最初に呼ばれたときだけインスタンス生成\n    public static synchronized Logger getInstance() {\n        if (instance == null) {\n            instance = new Logger();\n        }\n        return instance;\n    }\n    \n    public void log(String message) {\n        System.out.println(\"[LOG] \" + message);\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        // 同じインスタンス\n        DatabaseConnection db1 = DatabaseConnection.getInstance();\n        DatabaseConnection db2 = DatabaseConnection.getInstance();\n        System.out.println(db1 == db2); // true\n        \n        // ロガー使用\n        Logger logger = Logger.getInstance();\n        logger.log(\"アプリケーション開始\");\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Observerパターンとは',
                        'content' => "# Observerパターン（オブザーバーパターン）\n\n**Observerパターン**は、オブジェクトの状態変化を他のオブジェクトに自動的に通知するデザインパターンです。別名「発行-購読（Publish-Subscribe）」パターンとも呼ばれます。\n\n## 使用場面\n- GUIのイベント処理（ボタンクリック、テキスト変更）\n- データの変更を複数のビューに反映（MVC、MVVM）\n- ニュース配信（購読者に通知）\n- リアルタイム通知システム\n\n## 構成要素\n1. **Subject（被観察者）**: 状態を持ち、Observerを管理\n2. **Observer（観察者）**: 状態変化の通知を受け取る\n3. **ConcreteSubject**: Subjectの具体的な実装\n4. **ConcreteObserver**: Observerの具体的な実装\n\n## メリット\n- SubjectとObserverが疎結合\n- 動的にObserverを追加・削除できる\n- 1つの変更を複数のオブジェクトに伝播",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Observerパターンの実装例（温度センサー）',
                        'content' => "import java.util.*;\n\n// Observer インターフェイス\ninterface Observer {\n    void update(float temperature);\n}\n\n// Subject インターフェイス\ninterface Subject {\n    void registerObserver(Observer observer);\n    void removeObserver(Observer observer);\n    void notifyObservers();\n}\n\n// ConcreteSubject: 温度センサー\nclass TemperatureSensor implements Subject {\n    private List<Observer> observers = new ArrayList<>();\n    private float temperature;\n    \n    @Override\n    public void registerObserver(Observer observer) {\n        observers.add(observer);\n    }\n    \n    @Override\n    public void removeObserver(Observer observer) {\n        observers.remove(observer);\n    }\n    \n    @Override\n    public void notifyObservers() {\n        for (Observer observer : observers) {\n            observer.update(temperature);\n        }\n    }\n    \n    // 温度が変化したら通知\n    public void setTemperature(float temperature) {\n        System.out.println(\"\\n温度変化: \" + temperature + \"℃\");\n        this.temperature = temperature;\n        notifyObservers();\n    }\n}\n\n// ConcreteObserver: 温度表示\nclass TemperatureDisplay implements Observer {\n    private String name;\n    \n    public TemperatureDisplay(String name) {\n        this.name = name;\n    }\n    \n    @Override\n    public void update(float temperature) {\n        System.out.println(name + \": 現在の温度は \" + temperature + \"℃ です\");\n    }\n}\n\n// ConcreteObserver: 警告システム\nclass WarningSystem implements Observer {\n    @Override\n    public void update(float temperature) {\n        if (temperature > 30) {\n            System.out.println(\"⚠️ 警告: 高温注意！\");\n        } else if (temperature < 10) {\n            System.out.println(\"⚠️ 警告: 低温注意！\");\n        }\n    }\n}\n\npublic class Main {\n    public static void main(String[] args) {\n        TemperatureSensor sensor = new TemperatureSensor();\n        \n        // Observerを登録\n        TemperatureDisplay display1 = new TemperatureDisplay(\"ディスプレイ1\");\n        TemperatureDisplay display2 = new TemperatureDisplay(\"ディスプレイ2\");\n        WarningSystem warning = new WarningSystem();\n        \n        sensor.registerObserver(display1);\n        sensor.registerObserver(display2);\n        sensor.registerObserver(warning);\n        \n        // 温度変化 → 全Observerに通知\n        sensor.setTemperature(25.0f);\n        sensor.setTemperature(35.0f);\n        sensor.setTemperature(5.0f);\n    }\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
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

