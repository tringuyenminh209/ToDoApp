<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class LearningPathTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: Java Developer
        $this->createJavaDeveloperTemplate();

        // Template 2: React Frontend
        $this->createReactFrontendTemplate();

        // Template 3: Python Data Science
        $this->createPythonDataScienceTemplate();

        // Template 4: UI/UX Design
        $this->createUIUXDesignTemplate();

        // Template 5: Full-Stack Web Development
        $this->createFullStackTemplate();

        // Template 6: Android Development
        $this->createAndroidDevelopmentTemplate();

        // Template 7: English for Business
        $this->createBusinessEnglishTemplate();

        // Template 8: Digital Marketing
        $this->createDigitalMarketingTemplate();

        // Template 9: Machine Learning Basics
        $this->createMachineLearningTemplate();
    }

    private function createJavaDeveloperTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Javaデベロッパーになる',
            'description' => '6ヶ月で初心者からジュニアJavaデベロッパーになるための完全ロードマップ。基礎からSpring Bootまで学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 240,
            'tags' => ['java', 'backend', 'spring', 'oop'],
            'icon' => 'ic_java',
            'color' => '#ED8B00',
            'is_featured' => true,
        ]);

        // Milestone 1: Java Fundamentals
        $milestone1 = $template->milestones()->create([
            'title' => 'Java基礎',
            'description' => 'Java言語の基本構文とオブジェクト指向プログラミングの基礎を学習',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => [
                '50個のコーディング練習問題を完了',
                'コンソール計算機アプリを構築',
                'OOP原則を理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'Java構文とデータ型',
                'description' => '変数、演算子、制御フロー、配列を学習',
                'sort_order' => 1,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => [
                    'https://docs.oracle.com/javase/tutorial/',
                    'https://www.codecademy.com/learn/learn-java'
                ],
                'subtasks' => [
                    ['title' => '変数と型の基礎を学習', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '演算子と式の理解', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => '制御フロー（if/switch）', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'ループ構文（for/while）', 'estimated_minutes' => 60, 'sort_order' => 4],
                    ['title' => '配列の操作', 'estimated_minutes' => 60, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Java基本データ型',
                        'content' => "# Javaの基本データ型\n\n## プリミティブ型\n\n### 整数型\n- **byte**: 8ビット（-128〜127）\n- **short**: 16ビット（-32,768〜32,767）\n- **int**: 32ビット（-2,147,483,648〜2,147,483,647）\n- **long**: 64ビット（-9,223,372,036,854,775,808〜9,223,372,036,854,775,807）\n\n### 浮動小数点型\n- **float**: 32ビット単精度\n- **double**: 64ビット倍精度\n\n### その他\n- **boolean**: true/false\n- **char**: 16ビットUnicode文字\n\n## 参照型\n- String（文字列）\n- 配列\n- クラスのインスタンス",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '変数宣言と初期化の例',
                        'content' => "// 整数型\nint age = 25;\nlong population = 1000000L;\n\n// 浮動小数点型\ndouble price = 19.99;\nfloat rate = 0.05f;\n\n// 真偽値\nboolean isActive = true;\n\n// 文字\nchar grade = 'A';\n\n// 文字列\nString name = \"太郎\";\n\n// 配列\nint[] numbers = {1, 2, 3, 4, 5};\nString[] fruits = new String[3];",
                        'code_language' => 'java',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Java演算子',
                        'content' => "# Java演算子\n\n## 算術演算子\n- `+` 加算\n- `-` 減算\n- `*` 乗算\n- `/` 除算\n- `%` 剰余\n\n## 比較演算子\n- `==` 等しい\n- `!=` 等しくない\n- `>` より大きい\n- `<` より小さい\n- `>=` 以上\n- `<=` 以下\n\n## 論理演算子\n- `&&` AND（かつ）\n- `||` OR（または）\n- `!` NOT（否定）\n\n## 代入演算子\n- `=` 代入\n- `+=` 加算代入\n- `-=` 減算代入\n- `*=` 乗算代入\n- `/=` 除算代入",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '制御フローの例',
                        'content' => "// if-else文\nint score = 85;\nif (score >= 90) {\n    System.out.println(\"優秀\");\n} else if (score >= 70) {\n    System.out.println(\"良好\");\n} else {\n    System.out.println(\"要改善\");\n}\n\n// switch文\nString day = \"月曜日\";\nswitch (day) {\n    case \"月曜日\":\n        System.out.println(\"週の始まり\");\n        break;\n    case \"金曜日\":\n        System.out.println(\"もうすぐ週末\");\n        break;\n    default:\n        System.out.println(\"普通の日\");\n}",
                        'code_language' => 'java',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ループ構文の例',
                        'content' => "// for文\nfor (int i = 0; i < 5; i++) {\n    System.out.println(\"カウント: \" + i);\n}\n\n// while文\nint count = 0;\nwhile (count < 3) {\n    System.out.println(\"While: \" + count);\n    count++;\n}\n\n// do-while文\nint num = 0;\ndo {\n    System.out.println(\"Do-While: \" + num);\n    num++;\n} while (num < 3);\n\n// 拡張for文（配列）\nint[] numbers = {10, 20, 30, 40, 50};\nfor (int number : numbers) {\n    System.out.println(\"数値: \" + number);\n}",
                        'code_language' => 'java',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '配列の操作例',
                        'content' => "// 配列の宣言と初期化\nint[] scores = new int[5];\nscores[0] = 90;\nscores[1] = 85;\n\n// 初期化付き宣言\nString[] names = {\"太郎\", \"花子\", \"次郎\"};\n\n// 配列の長さ\nSystem.out.println(\"要素数: \" + names.length);\n\n// 配列のループ処理\nfor (int i = 0; i < names.length; i++) {\n    System.out.println(names[i]);\n}\n\n// 2次元配列\nint[][] matrix = {\n    {1, 2, 3},\n    {4, 5, 6},\n    {7, 8, 9}\n};\n\n// Arrays クラスの便利メソッド\nimport java.util.Arrays;\nint[] nums = {5, 2, 8, 1, 9};\nArrays.sort(nums);  // ソート\nSystem.out.println(Arrays.toString(nums));",
                        'code_language' => 'java',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '演習: FizzBuzz問題',
                        'question' => '1から100までの数字を出力するプログラムを作成してください。ただし、以下の条件に従います：\n- 3の倍数の時は数字の代わりに「Fizz」と出力\n- 5の倍数の時は数字の代わりに「Buzz」と出力\n- 3と5両方の倍数の時は「FizzBuzz」と出力\n\nヒント: for文と剰余演算子(%)を使います。',
                        'answer' => "for (int i = 1; i <= 100; i++) {\n    if (i % 15 == 0) {\n        System.out.println(\"FizzBuzz\");\n    } else if (i % 3 == 0) {\n        System.out.println(\"Fizz\");\n    } else if (i % 5 == 0) {\n        System.out.println(\"Buzz\");\n    } else {\n        System.out.println(i);\n    }\n}",
                        'difficulty' => 'easy',
                        'sort_order' => 7
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '演習: 配列の最大値を求める',
                        'question' => '整数配列から最大値を見つけて返すメソッドを作成してください。\n\nメソッド名: findMax\n引数: int[] array\n戻り値: int（配列内の最大値）',
                        'answer' => "public static int findMax(int[] array) {\n    if (array == null || array.length == 0) {\n        throw new IllegalArgumentException(\"配列が空です\");\n    }\n    \n    int max = array[0];\n    for (int i = 1; i < array.length; i++) {\n        if (array[i] > max) {\n            max = array[i];\n        }\n    }\n    return max;\n}\n\n// 使用例\nint[] numbers = {5, 2, 9, 1, 7};\nint maxValue = findMax(numbers);\nSystem.out.println(\"最大値: \" + maxValue);  // 出力: 9",
                        'difficulty' => 'easy',
                        'sort_order' => 8
                    ],
                    [
                        'type' => 'resource_link',
                        'title' => 'Oracle公式チュートリアル - Java言語の基礎',
                        'url' => 'https://docs.oracle.com/javase/tutorial/java/nutsandbolts/index.html',
                        'description' => 'Javaの変数、演算子、制御フローの公式ドキュメント',
                        'sort_order' => 9
                    ],
                    [
                        'type' => 'resource_link',
                        'title' => 'Java入門 - Qiita',
                        'url' => 'https://qiita.com/tags/java',
                        'description' => '日本語で書かれたJavaの解説記事が豊富',
                        'sort_order' => 10
                    ]
                ],
            ],
            [
                'title' => 'オブジェクト指向プログラミング',
                'description' => 'クラス、オブジェクト、継承、ポリモーフィズムを学習',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [
                    'https://www.baeldung.com/java-oop'
                ],
                'subtasks' => [
                    ['title' => 'クラスとオブジェクトの基礎', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'コンストラクタとメソッド', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '継承の理解と実装', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'ポリモーフィズムの活用', 'estimated_minutes' => 90, 'sort_order' => 4],
                    ['title' => 'カプセル化の実践', 'estimated_minutes' => 90, 'sort_order' => 5],
                ],
            ],
            [
                'title' => 'Java例外処理',
                'description' => 'try-catch、カスタム例外、エラーハンドリング',
                'sort_order' => 3,
                'estimated_minutes' => 240,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'try-catch構文の基礎', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '例外の種類を学習', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'カスタム例外の作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'エラーハンドリングのベストプラクティス', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Javaコレクション',
                'description' => 'List、Set、Map、Queue、Stackの使い方',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'List インターフェースの理解', 'estimated_minutes' => 70, 'sort_order' => 1],
                    ['title' => 'Set と Map の使用', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'イテレータの活用', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'ジェネリクスの理解', 'estimated_minutes' => 70, 'sort_order' => 4],
                    ['title' => 'ストリーム API の基礎', 'estimated_minutes' => 70, 'sort_order' => 5],
                ],
            ],
        ]);

        // Milestone 2: Advanced Java
        $milestone2 = $template->milestones()->create([
            'title' => 'Java応用',
            'description' => 'Stream API、マルチスレッド、ファイルI/Oなどの高度な機能',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => [
                'マルチスレッドアプリケーションを構築',
                'Stream APIをマスター',
                'ファイル処理システムを実装'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Java Stream API',
                'description' => 'ラムダ式、map、filter、reduce、collectを学習',
                'sort_order' => 1,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'マルチスレッドとコンカレンシー',
                'description' => 'Thread、Executor、Synchronization、Lockを学習',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'ファイルI/O',
                'description' => 'ファイル読み書き、NIO、Path、Filesクラス',
                'sort_order' => 3,
                'estimated_minutes' => 300,
                'priority' => 3,
                'resources' => [],
            ],
        ]);

        // Milestone 3: Database & JDBC
        $milestone3 = $template->milestones()->create([
            'title' => 'データベースとJDBC',
            'description' => 'SQLとJDBCを使用したデータベース操作',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => [
                'CRUDアプリケーションを構築',
                'JDBCを使用したデータベース接続',
                'SQLクエリの最適化'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'SQL基礎',
                'description' => 'SELECT、INSERT、UPDATE、DELETE、JOIN',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'JDBC',
                'description' => 'データベース接続、PreparedStatement、ResultSet',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 4: Spring Boot
        $milestone4 = $template->milestones()->create([
            'title' => 'Spring Boot',
            'description' => 'Spring Bootを使用したRESTful APIの構築',
            'sort_order' => 4,
            'estimated_hours' => 80,
            'deliverables' => [
                'RESTful APIを構築',
                'Spring Data JPAを使用',
                'セキュリティを実装'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'Spring Boot基礎',
                'description' => 'プロジェクト構造、依存性注入、自動設定',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring MVC',
                'description' => 'Controller、Service、Repository層の実装',
                'sort_order' => 2,
                'estimated_minutes' => 720,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring Data JPA',
                'description' => 'エンティティ、リポジトリ、クエリメソッド',
                'sort_order' => 3,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring Security',
                'description' => '認証、認可、JWT',
                'sort_order' => 4,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        // Milestone 5: Testing & Deployment
        $milestone5 = $template->milestones()->create([
            'title' => 'テストとデプロイ',
            'description' => 'ユニットテスト、統合テスト、本番環境へのデプロイ',
            'sort_order' => 5,
            'estimated_hours' => 20,
            'deliverables' => [
                'テストカバレッジ80%以上',
                'CI/CDパイプラインを構築',
                'アプリケーションをデプロイ'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => 'JUnitとMockito',
                'description' => 'ユニットテストとモックの作成',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'Docker',
                'description' => 'Dockerfileの作成、コンテナ化',
                'sort_order' => 2,
                'estimated_minutes' => 300,
                'priority' => 3,
                'resources' => [],
            ],
            [
                'title' => 'デプロイ',
                'description' => 'Heroku/AWS/GCPへのデプロイ',
                'sort_order' => 3,
                'estimated_minutes' => 240,
                'priority' => 3,
                'resources' => [],
            ],
        ]);
    }

    private function createReactFrontendTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'React フロントエンド開発',
            'description' => '4ヶ月でモダンなReactアプリケーションを構築できるようになる',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 180,
            'tags' => ['react', 'javascript', 'frontend', 'web'],
            'icon' => 'ic_reactnative',
            'color' => '#61DAFB',
            'is_featured' => true,
        ]);

        // Milestone 1: HTML/CSS/JavaScript
        $milestone1 = $template->milestones()->create([
            'title' => 'Web基礎',
            'description' => 'HTML、CSS、JavaScriptの基礎を学習',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => [
                'レスポンシブWebサイトを構築',
                'JavaScriptの基本をマスター',
                'DOM操作を理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'HTML5とセマンティックHTML',
                'description' => 'タグ、属性、フォーム、アクセシビリティ',
                'sort_order' => 1,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'CSS3とFlexbox/Grid',
                'description' => 'レイアウト、レスポンシブデザイン、アニメーション',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'JavaScript基礎',
                'description' => '変数、関数、配列、オブジェクト、DOM操作',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ES6+モダンJavaScript',
                'description' => 'アロー関数、分割代入、Promise、async/await',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 2: React Basics
        $milestone2 = $template->milestones()->create([
            'title' => 'React基礎',
            'description' => 'コンポーネント、State、Propsを学習',
            'sort_order' => 2,
            'estimated_hours' => 50,
            'deliverables' => [
                'Todoアプリを構築',
                'コンポーネントの再利用',
                'State管理を理解'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Reactセットアップ',
                'description' => 'Create React App、プロジェクト構造',
                'sort_order' => 1,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'コンポーネントとJSX',
                'description' => '関数コンポーネント、Props、条件レンダリング',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'State と Hooks',
                'description' => 'useState、useEffect、カスタムフック',
                'sort_order' => 3,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'イベント処理とフォーム',
                'description' => 'イベントハンドラー、制御コンポーネント',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        // Milestone 3: Advanced React
        $milestone3 = $template->milestones()->create([
            'title' => 'React応用',
            'description' => 'Context API、React Router、パフォーマンス最適化',
            'sort_order' => 3,
            'estimated_hours' => 50,
            'deliverables' => [
                'SPAを構築',
                'グローバルState管理',
                'パフォーマンス最適化'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'React Router',
                'description' => 'ルーティング、ナビゲーション、動的ルート',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Context API',
                'description' => 'グローバルState、useContext',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'パフォーマンス最適化',
                'description' => 'useMemo、useCallback、React.memo',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 3,
                'resources' => [],
            ],
        ]);

        // Milestone 4: State Management & API
        $milestone4 = $template->milestones()->create([
            'title' => 'State管理とAPI連携',
            'description' => 'Redux/Zustand、REST API、認証',
            'sort_order' => 4,
            'estimated_hours' => 40,
            'deliverables' => [
                'Redux/Zustandを実装',
                'API連携',
                '認証システム'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'REST API連携',
                'description' => 'fetch、axios、エラーハンドリング',
                'sort_order' => 1,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Redux/Zustand',
                'description' => 'グローバルState管理ライブラリ',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '認証とセキュリティ',
                'description' => 'JWT、ローカルストレージ、保護ルート',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
        ]);
    }

    private function createPythonDataScienceTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Pythonデータサイエンス',
            'description' => 'データ分析と機械学習の基礎を5ヶ月で習得',
            'category' => 'data_science',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 200,
            'tags' => ['python', 'data-science', 'machine-learning', 'pandas'],
            'icon' => 'ic_python',
            'color' => '#3776AB',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'Python基礎',
            'description' => 'Python言語の基本構文とライブラリ',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['Python基礎をマスター', 'NumPy/Pandasを使用'],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'Python構文',
                'description' => '変数、関数、クラス、モジュール',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'NumPy',
                'description' => '配列操作、数値計算',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Pandas',
                'description' => 'DataFrame、データ操作、分析',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'データ可視化',
            'description' => 'Matplotlib、Seabornでデータを可視化',
            'sort_order' => 2,
            'estimated_hours' => 30,
            'deliverables' => ['グラフ作成', 'ダッシュボード構築'],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Matplotlib',
                'description' => '基本的なグラフ、カスタマイズ',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Seaborn',
                'description' => '統計的可視化、ヒートマップ',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => '機械学習基礎',
            'description' => 'Scikit-learnで機械学習モデルを構築',
            'sort_order' => 3,
            'estimated_hours' => 80,
            'deliverables' => ['予測モデル構築', 'モデル評価'],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '教師あり学習',
                'description' => '回帰、分類、決定木',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '教師なし学習',
                'description' => 'クラスタリング、次元削減',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'モデル評価',
                'description' => '精度、再現率、F1スコア',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'プロジェクト実践',
            'description' => '実データでプロジェクトを完成',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['Kaggleコンペ参加', 'ポートフォリオ作成'],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'データ収集とクリーニング',
                'description' => 'Web scraping、データ前処理',
                'sort_order' => 1,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'エンドツーエンドプロジェクト',
                'description' => '問題定義からデプロイまで',
                'sort_order' => 2,
                'estimated_minutes' => 720,
                'priority' => 5,
                'resources' => [],
            ],
        ]);
    }

    private function createUIUXDesignTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'UI/UXデザイン',
            'description' => '3ヶ月でUI/UXデザイナーになる',
            'category' => 'design',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['ui', 'ux', 'figma', 'design'],
            'icon' => 'ic_design',
            'color' => '#F24E1E',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'デザイン基礎',
            'description' => 'デザイン原則とツール',
            'sort_order' => 1,
            'estimated_hours' => 30,
            'deliverables' => ['デザイン原則理解', 'Figmaマスター'],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'デザイン原則',
                'description' => 'タイポグラフィ、色彩理論、レイアウト',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Figma基礎',
                'description' => 'ツール操作、コンポーネント、Auto Layout',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'UXリサーチ',
            'description' => 'ユーザー調査と分析',
            'sort_order' => 2,
            'estimated_hours' => 30,
            'deliverables' => ['ユーザーインタビュー', 'ペルソナ作成'],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'ユーザーリサーチ',
                'description' => 'インタビュー、アンケート、観察',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ペルソナとユーザージャーニー',
                'description' => 'ペルソナ作成、ジャーニーマップ',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'UIデザイン実践',
            'description' => 'モバイル・Webアプリのデザイン',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => ['モバイルアプリデザイン', 'Webサイトデザイン'],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'ワイヤーフレーム',
                'description' => 'ローファイ・ハイファイワイヤーフレーム',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'プロトタイプ',
                'description' => 'インタラクティブプロトタイプ作成',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'デザインシステム',
                'description' => 'コンポーネントライブラリ、スタイルガイド',
                'sort_order' => 3,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'ポートフォリオ',
            'description' => 'ポートフォリオ作成とプレゼン',
            'sort_order' => 4,
            'estimated_hours' => 20,
            'deliverables' => ['ポートフォリオサイト', 'ケーススタディ3件'],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'ケーススタディ作成',
                'description' => 'プロセス、課題、解決策を文書化',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ポートフォリオサイト',
                'description' => 'オンラインポートフォリオ構築',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);
    }

    private function createFullStackTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'フルスタックWeb開発',
            'description' => '8ヶ月でフロントエンドからバックエンドまで習得',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 320,
            'tags' => ['fullstack', 'react', 'node', 'database'],
            'icon' => 'ic_fullstack',
            'color' => '#68A063',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'フロントエンド',
            'description' => 'HTML/CSS/JavaScript/React',
            'sort_order' => 1,
            'estimated_hours' => 80,
            'deliverables' => ['レスポンシブWebサイト', 'Reactアプリ'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'バックエンド',
            'description' => 'Node.js/Express/API',
            'sort_order' => 2,
            'estimated_hours' => 80,
            'deliverables' => ['RESTful API', '認証システム'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'データベース',
            'description' => 'SQL/NoSQL/ORM',
            'sort_order' => 3,
            'estimated_hours' => 60,
            'deliverables' => ['データベース設計', 'CRUD操作'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'デプロイとDevOps',
            'description' => 'Docker/CI/CD/クラウド',
            'sort_order' => 4,
            'estimated_hours' => 60,
            'deliverables' => ['本番環境デプロイ', 'CI/CD構築'],
        ]);

        $milestone5 = $template->milestones()->create([
            'title' => 'フルスタックプロジェクト',
            'description' => 'エンドツーエンドアプリ構築',
            'sort_order' => 5,
            'estimated_hours' => 40,
            'deliverables' => ['完全なWebアプリ', 'ポートフォリオ'],
        ]);
    }

    private function createAndroidDevelopmentTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Androidアプリ開発',
            'description' => 'Kotlin/Jetpack Composeで6ヶ月でAndroid開発者に',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 220,
            'tags' => ['android', 'kotlin', 'mobile', 'jetpack-compose'],
            'icon' => 'ic_android',
            'color' => '#3DDC84',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'Kotlin基礎',
            'description' => 'Kotlin言語の基本',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['Kotlin構文マスター', 'OOP理解'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'Android基礎',
            'description' => 'Activity/Fragment/Layout',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => ['基本アプリ構築', 'UI実装'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'Jetpack Compose',
            'description' => 'モダンUIフレームワーク',
            'sort_order' => 3,
            'estimated_hours' => 60,
            'deliverables' => ['Composeアプリ', 'State管理'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'データ永続化とAPI',
            'description' => 'Room/Retrofit/Coroutines',
            'sort_order' => 4,
            'estimated_hours' => 40,
            'deliverables' => ['ローカルDB', 'API連携'],
        ]);

        $milestone5 = $template->milestones()->create([
            'title' => 'Google Playリリース',
            'description' => 'テスト/最適化/公開',
            'sort_order' => 5,
            'estimated_hours' => 20,
            'deliverables' => ['アプリ公開', 'ストアリスティング'],
        ]);
    }

    private function createBusinessEnglishTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'ビジネス英語',
            'description' => '6ヶ月でビジネスシーンで使える英語力を習得',
            'category' => 'language',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 180,
            'tags' => ['english', 'business', 'communication'],
            'icon' => 'ic_language',
            'color' => '#4285F4',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'ビジネス英語基礎',
            'description' => 'ビジネスシーンの基本表現',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['自己紹介', 'メール作成'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'プレゼンテーション',
            'description' => '英語でのプレゼンスキル',
            'sort_order' => 2,
            'estimated_hours' => 40,
            'deliverables' => ['プレゼン実施', 'Q&A対応'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => '会議とネゴシエーション',
            'description' => '会議進行と交渉術',
            'sort_order' => 3,
            'estimated_hours' => 50,
            'deliverables' => ['会議参加', '意見表明'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'ビジネス文書',
            'description' => 'レポート、提案書作成',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['ビジネスメール', '提案書'],
        ]);
    }

    private function createDigitalMarketingTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'デジタルマーケティング',
            'description' => '4ヶ月でデジタルマーケターになる',
            'category' => 'business',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 160,
            'tags' => ['marketing', 'seo', 'sns', 'analytics'],
            'icon' => 'ic_marketing',
            'color' => '#EA4335',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'マーケティング基礎',
            'description' => 'デジタルマーケティングの基本',
            'sort_order' => 1,
            'estimated_hours' => 30,
            'deliverables' => ['マーケティング戦略', 'ターゲット設定'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'SEOとコンテンツマーケティング',
            'description' => '検索エンジン最適化',
            'sort_order' => 2,
            'estimated_hours' => 40,
            'deliverables' => ['SEO施策', 'コンテンツ作成'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'SNSマーケティング',
            'description' => 'Facebook/Instagram/Twitter広告',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => ['SNS広告運用', 'エンゲージメント向上'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'データ分析',
            'description' => 'Google Analytics/広告効果測定',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['レポート作成', 'ROI分析'],
        ]);
    }

    private function createMachineLearningTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => '機械学習入門',
            'description' => '4ヶ月で機械学習の基礎を習得',
            'category' => 'data_science',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 160,
            'tags' => ['machine-learning', 'python', 'ai', 'deep-learning'],
            'icon' => 'ic_machine',
            'color' => '#FF6F00',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => '機械学習基礎',
            'description' => '教師あり学習/教師なし学習',
            'sort_order' => 1,
            'estimated_hours' => 50,
            'deliverables' => ['回帰モデル', '分類モデル'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'ディープラーニング',
            'description' => 'ニューラルネットワーク/CNN/RNN',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => ['画像分類', 'テキスト分析'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'モデル最適化',
            'description' => 'ハイパーパラメータチューニング',
            'sort_order' => 3,
            'estimated_hours' => 30,
            'deliverables' => ['モデル改善', 'パフォーマンス向上'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'MLプロジェクト',
            'description' => 'エンドツーエンドMLプロジェクト',
            'sort_order' => 4,
            'estimated_hours' => 20,
            'deliverables' => ['MLアプリ', 'モデルデプロイ'],
        ]);
    }
}

