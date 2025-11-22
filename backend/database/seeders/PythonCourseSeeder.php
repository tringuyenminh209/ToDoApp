<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class PythonCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Python基礎コース - 15週間の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Python基礎コース',
            'description' => '初心者向けPythonプログラミング基礎コース。15週間の実践的な課題を通じて、Pythonの基本構文からオブジェクト指向プログラミング、データ分析まで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['python', '基礎', '初心者', 'プログラミング', 'データ分析', 'AI'],
            'icon' => 'ic_python',
            'color' => '#3776AB',
            'is_featured' => true,
        ]);

        // Milestone 1: Python基礎 (第1週～第4週)
        $milestone1 = $template->milestones()->create([
            'title' => 'Python基礎',
            'description' => '開発環境のセットアップから、変数、データ型、演算子、入力処理まで学習',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'Python開発環境をセットアップ完了',
                'Hello Worldプログラムを作成',
                '変数と型を使ったプログラム',
                'ユーザー入力を受け取るプログラム'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：環境設定とHello World',
                'description' => 'Python開発環境のセットアップとprint()を使った画面出力',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python公式サイト', 'Visual Studio Code + Python拡張機能'],
                'subtasks' => [
                    ['title' => 'Pythonをインストール', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'VS Codeをセットアップ', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'Hello Worldプログラムを作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Pythonとは？',
                        'content' => "# Pythonとは？\n\n**Python**は、1991年にGuido van Rossumによって開発されたプログラミング言語です。\n\n## Pythonの特徴\n1. **読みやすい**: シンプルで直感的な構文\n2. **汎用性が高い**: Web開発、データ分析、AI、自動化など幅広い用途\n3. **豊富なライブラリ**: NumPy、pandas、TensorFlowなど\n4. **インタープリタ言語**: コンパイル不要で即実行可能\n5. **動的型付け**: 型宣言不要\n\n## Pythonの用途\n- Web開発（Django、Flask）\n- データ分析・可視化\n- 機械学習・AI\n- 自動化スクリプト\n- ゲーム開発",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Hello World',
                        'content' => "# これがPythonの最初のプログラム！\nprint(\"Hello, World!\")\n\n# 複数行の出力\nprint(\"Welcome to Python\")\nprint(\"Let's start coding!\")",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：変数とデータ型',
                'description' => 'Pythonの変数宣言、基本データ型、型変換について学習',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python公式ドキュメント - Data Types'],
                'subtasks' => [
                    ['title' => '変数の宣言方法を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => '基本データ型を理解', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '型変換を学習', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '変数とデータ型',
                        'content' => "# 変数とデータ型\n\n## 基本データ型\n- **int**: 整数（例: 42, -10）\n- **float**: 浮動小数点数（例: 3.14, -0.5）\n- **str**: 文字列（例: \"Hello\", 'Python'）\n- **bool**: ブール値（True, False）\n\n## 変数の宣言\nPythonでは型宣言が不要です。\n```python\nname = \"Alice\"  # str\nage = 25  # int\nheight = 165.5  # float\nis_student = True  # bool\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '変数とデータ型の例',
                        'content' => "# 変数の宣言\nname = \"太郎\"\nage = 20\nheight = 175.5\nis_student = True\n\n# 出力\nprint(\"名前:\", name)\nprint(\"年齢:\", age)\nprint(\"身長:\", height)\nprint(\"学生？:\", is_student)\n\n# 型の確認\nprint(type(name))  # <class 'str'>\nprint(type(age))   # <class 'int'>",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '型変換',
                        'content' => "# 型変換の例\nage_str = \"25\"\nage_int = int(age_str)  # strからintへ\nprint(age_int + 5)  # 30\n\nscore = 95\nscore_str = str(score)  # intからstrへ\nprint(\"スコア: \" + score_str)\n\nprice = \"1500.5\"\nprice_float = float(price)  # strからfloatへ\nprint(price_float * 1.1)  # 1650.55",
                        'code_language' => 'python',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：演算子と文字列操作',
                'description' => '算術演算子、比較演算子、論理演算子、文字列操作について学習',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Python Operators Documentation'],
                'subtasks' => [
                    ['title' => '算術演算子を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => '文字列操作を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '算術演算子',
                        'content' => "# 算術演算子\na, b = 10, 3\n\nprint(a + b)   # 13 (加算)\nprint(a - b)   # 7 (減算)\nprint(a * b)   # 30 (乗算)\nprint(a / b)   # 3.333... (除算)\nprint(a // b)  # 3 (整数除算)\nprint(a % b)   # 1 (剰余)\nprint(a ** b)  # 1000 (累乗)",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '文字列操作',
                        'content' => "# 文字列の連結\nfirst_name = \"太郎\"\nlast_name = \"山田\"\nfull_name = last_name + first_name\nprint(full_name)  # 山田太郎\n\n# 文字列の繰り返し\nprint(\"Python\" * 3)  # PythonPythonPython\n\n# 文字列のインデックス\ntext = \"Hello\"\nprint(text[0])    # H\nprint(text[-1])   # o\n\n# 文字列のスライス\nprint(text[1:4])  # ell\n\n# 文字列メソッド\nprint(text.upper())      # HELLO\nprint(text.lower())      # hello\nprint(text.replace(\"H\", \"J\"))  # Jello",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 4
            [
                'title' => '第4週：ユーザー入力とフォーマット',
                'description' => 'input()を使った入力とf-stringを使ったフォーマット出力',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Python Input/Output'],
                'subtasks' => [
                    ['title' => 'ユーザー入力を受け取る', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'フォーマット出力を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ユーザー入力',
                        'content' => "# input()を使った入力\nname = input(\"名前を入力してください: \")\nage = int(input(\"年齢を入力してください: \"))\n\nprint(f\"こんにちは、{name}さん！\")\nprint(f\"あなたは{age}歳ですね。\")\n\n# 計算例\nnum1 = float(input(\"1つ目の数値: \"))\nnum2 = float(input(\"2つ目の数値: \"))\ntotal = num1 + num2\nprint(f\"合計: {total}\")",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'フォーマット出力',
                        'content' => "name = \"太郎\"\nage = 20\nscore = 95.5\n\n# f-string（推奨）\nprint(f\"名前: {name}, 年齢: {age}歳\")\nprint(f\"スコア: {score:.1f}点\")  # 小数点1桁\n\n# format()メソッド\nprint(\"名前: {}, 年齢: {}歳\".format(name, age))\n\n# %演算子（旧式）\nprint(\"名前: %s, 年齢: %d歳\" % (name, age))\n\n# 数値フォーマット\npi = 3.14159\nprint(f\"{pi:.2f}\")  # 3.14\nprint(f\"{1000:,}\")   # 1,000",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 2: 制御フローと関数 (第5週～第7週)
        $milestone2 = $template->milestones()->create([
            'title' => '制御フローと関数',
            'description' => 'if/elif/else、while、for、関数の定義と使用',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                '条件分岐を使ったプログラム',
                'ループを使ったプログラム',
                '関数を定義したプログラム'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 5
            [
                'title' => '第5週：条件分岐（if/elif/else）',
                'description' => '条件分岐の基本とネストされた条件文',
                'sort_order' => 5,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Control Flow'],
                'subtasks' => [
                    ['title' => 'if/elif/else文を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '比較演算子と論理演算子を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'If/Elif/Else',
                        'content' => "# 基本的なif文\nage = 20\n\nif age >= 18:\n    print(\"成人です\")\nelse:\n    print(\"未成年です\")\n\n# elif（else if）\nscore = 85\n\nif score >= 90:\n    print(\"A\")\nelif score >= 80:\n    print(\"B\")\nelif score >= 70:\n    print(\"C\")\nelse:\n    print(\"D\")\n\n# 論理演算子\nage = 25\nhas_license = True\n\nif age >= 18 and has_license:\n    print(\"運転できます\")\nelif age >= 18 or has_license:\n    print(\"条件を満たしていません\")\nelse:\n    print(\"運転できません\")",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：ループ（while, for）',
                'description' => 'whileループとforループ、range()の使い方',
                'sort_order' => 6,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Loops'],
                'subtasks' => [
                    ['title' => 'while文を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'for文とrange()を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Whileループ',
                        'content' => "# whileループ\ncount = 0\nwhile count < 5:\n    print(f\"カウント: {count}\")\n    count += 1\n\n# 無限ループの制御\nwhile True:\n    answer = input(\"続けますか？ (yes/no): \")\n    if answer.lower() == \"no\":\n        break  # ループを抜ける\n    print(\"続行中...\")",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Forループ',
                        'content' => "# forループとrange()\nfor i in range(5):  # 0, 1, 2, 3, 4\n    print(i)\n\n# 開始、終了、ステップ\nfor i in range(1, 11, 2):  # 1, 3, 5, 7, 9\n    print(i)\n\n# リストのループ\nfruits = [\"りんご\", \"バナナ\", \"オレンジ\"]\nfor fruit in fruits:\n    print(f\"好きな果物: {fruit}\")\n\n# enumerate()でインデックスと値を取得\nfor index, fruit in enumerate(fruits):\n    print(f\"{index}: {fruit}\")\n\n# break, continue\nfor i in range(10):\n    if i == 3:\n        continue  # 3をスキップ\n    if i == 8:\n        break  # 8で終了\n    print(i)",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 7
            [
                'title' => '第7週：関数',
                'description' => '関数の定義、引数、戻り値、デフォルト引数、可変長引数',
                'sort_order' => 7,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Functions'],
                'subtasks' => [
                    ['title' => '基本的な関数を定義', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'デフォルト引数を学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '関数の定義',
                        'content' => "# 基本的な関数\ndef greet(name):\n    print(f\"こんにちは、{name}さん！\")\n\ngreet(\"太郎\")  # こんにちは、太郎さん！\n\n# 戻り値あり\ndef add(a, b):\n    return a + b\n\nresult = add(3, 5)\nprint(result)  # 8\n\n# 複数の戻り値\ndef calculate(a, b):\n    sum_val = a + b\n    diff = a - b\n    product = a * b\n    return sum_val, diff, product\n\ns, d, p = calculate(10, 3)\nprint(f\"和: {s}, 差: {d}, 積: {p}\")",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'デフォルト引数と可変長引数',
                        'content' => "# デフォルト引数\ndef greet(name, greeting=\"こんにちは\"):\n    print(f\"{greeting}、{name}さん！\")\n\ngreet(\"太郎\")  # こんにちは、太郎さん！\ngreet(\"花子\", \"おはよう\")  # おはよう、花子さん！\n\n# 可変長引数 (*args)\ndef sum_all(*numbers):\n    total = 0\n    for num in numbers:\n        total += num\n    return total\n\nprint(sum_all(1, 2, 3))  # 6\nprint(sum_all(1, 2, 3, 4, 5))  # 15\n\n# キーワード引数 (**kwargs)\ndef print_info(**info):\n    for key, value in info.items():\n        print(f\"{key}: {value}\")\n\nprint_info(name=\"太郎\", age=20, city=\"東京\")",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 3: データ構造 (第8週～第10週)
        $milestone3 = $template->milestones()->create([
            'title' => 'データ構造',
            'description' => 'リスト、タプル、辞書、セットの使い方とリスト内包表記',
            'sort_order' => 3,
            'estimated_hours' => 24,
            'deliverables' => [
                'リストを使ったプログラム',
                '辞書を使ったプログラム',
                'リスト内包表記を使ったプログラム'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 8
            [
                'title' => '第8週：リストとタプル',
                'description' => 'リストとタプルの操作、メソッド、違い',
                'sort_order' => 8,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Lists and Tuples'],
                'subtasks' => [
                    ['title' => 'リストを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'タプルを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'リスト',
                        'content' => "# リストの作成\nfruits = [\"りんご\", \"バナナ\", \"オレンジ\"]\nnumbers = [1, 2, 3, 4, 5]\nmixed = [1, \"Hello\", 3.14, True]\n\n# インデックスアクセス\nprint(fruits[0])  # りんご\nprint(fruits[-1])  # オレンジ\n\n# スライス\nprint(numbers[1:4])  # [2, 3, 4]\n\n# リストの操作\nfruits.append(\"ぶどう\")  # 末尾に追加\nfruits.insert(1, \"いちご\")  # 指定位置に挿入\nfruits.remove(\"バナナ\")  # 要素を削除\npopped = fruits.pop()  # 末尾を削除して返す\n\n# その他のメソッド\nprint(len(fruits))  # 長さ\nprint(\"りんご\" in fruits)  # 存在チェック\nfruits.sort()  # ソート\nfruits.reverse()  # 反転",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'タプル',
                        'content' => "# タプル（変更不可能なリスト）\ncoordinates = (10, 20)\ncolors = (\"赤\", \"緑\", \"青\")\n\n# アクセス\nprint(coordinates[0])  # 10\n\n# アンパック\nx, y = coordinates\nprint(f\"x={x}, y={y}\")\n\n# タプルは変更不可\n# coordinates[0] = 15  # エラー！\n\n# リストとタプルの違い\n# - リスト: 変更可能（mutable）- [ ]\n# - タプル: 変更不可（immutable）- ( )\n# タプルの方が高速でメモリ効率が良い",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 9
            [
                'title' => '第9週：辞書とセット',
                'description' => '辞書（dict）とセット（set）の操作',
                'sort_order' => 9,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Dictionaries and Sets'],
                'subtasks' => [
                    ['title' => '辞書を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'セットを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '辞書（Dictionary）',
                        'content' => "# 辞書の作成\nstudent = {\n    \"name\": \"太郎\",\n    \"age\": 20,\n    \"grade\": \"A\"\n}\n\n# アクセス\nprint(student[\"name\"])  # 太郎\nprint(student.get(\"age\"))  # 20\nprint(student.get(\"city\", \"不明\"))  # デフォルト値\n\n# 追加・更新\nstudent[\"city\"] = \"東京\"\nstudent[\"age\"] = 21\n\n# 削除\ndel student[\"grade\"]\nage = student.pop(\"age\")  # 削除して返す\n\n# ループ\nfor key in student:\n    print(f\"{key}: {student[key]}\")\n\nfor key, value in student.items():\n    print(f\"{key}: {value}\")\n\n# メソッド\nprint(student.keys())    # キー一覧\nprint(student.values())  # 値一覧\nprint(student.items())   # (key, value)のペア",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'セット（Set）',
                        'content' => "# セット（重複のない集合）\nfruits = {\"りんご\", \"バナナ\", \"オレンジ\"}\nnumbers = {1, 2, 3, 3, 4, 4, 5}  # {1, 2, 3, 4, 5}\n\n# 追加・削除\nfruits.add(\"ぶどう\")\nfruits.remove(\"バナナ\")\nfruits.discard(\"メロン\")  # なくてもエラーにならない\n\n# 集合演算\na = {1, 2, 3, 4}\nb = {3, 4, 5, 6}\n\nprint(a | b)  # 和集合: {1, 2, 3, 4, 5, 6}\nprint(a & b)  # 積集合: {3, 4}\nprint(a - b)  # 差集合: {1, 2}\nprint(a ^ b)  # 対称差: {1, 2, 5, 6}\n\n# 重複削除に便利\nduplicates = [1, 2, 2, 3, 3, 3, 4]\nunique = list(set(duplicates))  # [1, 2, 3, 4]",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 10
            [
                'title' => '第10週：リスト内包表記とイテレータ',
                'description' => 'リスト内包表記、辞書内包表記、ジェネレータ',
                'sort_order' => 10,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Comprehensions'],
                'subtasks' => [
                    ['title' => 'リスト内包表記を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'ジェネレータを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'リスト内包表記',
                        'content' => "# 通常のループ\nsquares = []\nfor i in range(10):\n    squares.append(i ** 2)\n\n# リスト内包表記（推奨）\nsquares = [i ** 2 for i in range(10)]\nprint(squares)  # [0, 1, 4, 9, 16, 25, 36, 49, 64, 81]\n\n# 条件付き\neven_squares = [i ** 2 for i in range(10) if i % 2 == 0]\nprint(even_squares)  # [0, 4, 16, 36, 64]\n\n# if-else\nresults = [\"偶数\" if i % 2 == 0 else \"奇数\" for i in range(5)]\nprint(results)  # ['偶数', '奇数', '偶数', '奇数', '偶数']\n\n# ネストされたループ\nmatrix = [[i * j for j in range(3)] for i in range(3)]\nprint(matrix)  # [[0, 0, 0], [0, 1, 2], [0, 2, 4]]",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '辞書・セット内包表記',
                        'content' => "# 辞書内包表記\nsquares_dict = {i: i ** 2 for i in range(5)}\nprint(squares_dict)  # {0: 0, 1: 1, 2: 4, 3: 9, 4: 16}\n\n# セット内包表記\neven_set = {i for i in range(10) if i % 2 == 0}\nprint(even_set)  # {0, 2, 4, 6, 8}\n\n# ジェネレータ式（メモリ効率的）\ngen = (i ** 2 for i in range(10))\nfor value in gen:\n    print(value)\n\n# ジェネレータ関数\ndef countdown(n):\n    while n > 0:\n        yield n  # yieldで値を返す\n        n -= 1\n\nfor i in countdown(5):\n    print(i)  # 5, 4, 3, 2, 1",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 4: オブジェクト指向プログラミング (第11週～第13週)
        $milestone4 = $template->milestones()->create([
            'title' => 'オブジェクト指向プログラミング',
            'description' => 'クラス、オブジェクト、継承、カプセル化、ポリモーフィズム',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                'クラスを定義したプログラム',
                '継承を使ったプログラム',
                'カプセル化を実装したプログラム'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 11
            [
                'title' => '第11週：クラスとオブジェクト',
                'description' => 'クラスの定義、コンストラクタ、インスタンス変数とメソッド',
                'sort_order' => 11,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Classes and Objects'],
                'subtasks' => [
                    ['title' => 'クラスを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'インスタンス変数とメソッドを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'クラスの定義',
                        'content' => "# クラスの定義\nclass Person:\n    # コンストラクタ\n    def __init__(self, name, age):\n        self.name = name  # インスタンス変数\n        self.age = age\n    \n    # メソッド\n    def greet(self):\n        print(f\"こんにちは、{self.name}です。\")\n    \n    def celebrate_birthday(self):\n        self.age += 1\n        print(f\"誕生日おめでとう！{self.age}歳になりました。\")\n\n# オブジェクトの作成\nperson1 = Person(\"太郎\", 20)\nperson2 = Person(\"花子\", 25)\n\n# メソッド呼び出し\nperson1.greet()  # こんにちは、太郎です。\nprint(person1.age)  # 20\nperson1.celebrate_birthday()  # 21歳になりました。",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'クラス変数とクラスメソッド',
                        'content' => "class Dog:\n    # クラス変数（全インスタンスで共有）\n    species = \"Canis familiaris\"\n    count = 0\n    \n    def __init__(self, name, age):\n        self.name = name\n        self.age = age\n        Dog.count += 1  # インスタンス数をカウント\n    \n    def description(self):\n        return f\"{self.name}は{self.age}歳です。\"\n    \n    @classmethod\n    def get_count(cls):\n        return f\"犬は{cls.count}匹います。\"\n    \n    @staticmethod\n    def is_adult(age):\n        return age >= 2\n\n# 使用例\ndog1 = Dog(\"ポチ\", 3)\ndog2 = Dog(\"シロ\", 1)\n\nprint(Dog.species)  # Canis familiaris\nprint(Dog.get_count())  # 犬は2匹います。\nprint(Dog.is_adult(3))  # True",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 12
            [
                'title' => '第12週：継承とポリモーフィズム',
                'description' => 'クラスの継承、メソッドのオーバーライド、super()',
                'sort_order' => 12,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Inheritance'],
                'subtasks' => [
                    ['title' => '継承を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'ポリモーフィズムを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '継承',
                        'content' => "# 基底クラス\nclass Animal:\n    def __init__(self, name):\n        self.name = name\n    \n    def speak(self):\n        return \"何か音を出す\"\n\n# 派生クラス\nclass Dog(Animal):\n    def __init__(self, name, breed):\n        super().__init__(name)  # 親クラスのコンストラクタを呼ぶ\n        self.breed = breed\n    \n    def speak(self):  # メソッドのオーバーライド\n        return \"ワンワン！\"\n\nclass Cat(Animal):\n    def speak(self):\n        return \"ニャー！\"\n\n# 使用例\ndog = Dog(\"ポチ\", \"柴犬\")\ncat = Cat(\"タマ\")\n\nprint(dog.name)    # ポチ\nprint(dog.breed)   # 柴犬\nprint(dog.speak())  # ワンワン！\nprint(cat.speak())  # ニャー！",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ポリモーフィズム',
                        'content' => "# ポリモーフィズム（多態性）\ndef animal_sound(animal):\n    print(f\"{animal.name}の鳴き声: {animal.speak()}\")\n\nanimals = [\n    Dog(\"ポチ\", \"柴犬\"),\n    Cat(\"タマ\"),\n    Dog(\"シロ\", \"秋田犬\")\n]\n\n# 同じメソッド呼び出しで異なる動作\nfor animal in animals:\n    animal_sound(animal)\n# 出力:\n# ポチの鳴き声: ワンワン！\n# タマの鳴き声: ニャー！\n# シロの鳴き声: ワンワン！\n\n# isinstance()で型チェック\ndog = Dog(\"ポチ\", \"柴犬\")\nprint(isinstance(dog, Dog))     # True\nprint(isinstance(dog, Animal))  # True\nprint(isinstance(dog, Cat))     # False",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 13
            [
                'title' => '第13週：カプセル化と特殊メソッド',
                'description' => 'プライベート変数、プロパティ、特殊メソッド（__str__, __repr__など）',
                'sort_order' => 13,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Encapsulation'],
                'subtasks' => [
                    ['title' => 'カプセル化を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '特殊メソッドを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'カプセル化',
                        'content' => "class BankAccount:\n    def __init__(self, owner, balance):\n        self.owner = owner\n        self.__balance = balance  # プライベート変数（__で始まる）\n    \n    def deposit(self, amount):\n        if amount > 0:\n            self.__balance += amount\n            print(f\"{amount}円を入金しました。\")\n    \n    def withdraw(self, amount):\n        if 0 < amount <= self.__balance:\n            self.__balance -= amount\n            print(f\"{amount}円を出金しました。\")\n        else:\n            print(\"残高不足です。\")\n    \n    @property\n    def balance(self):  # ゲッター\n        return self.__balance\n\n# 使用例\naccount = BankAccount(\"太郎\", 10000)\naccount.deposit(5000)\nprint(account.balance)  # 15000\n# account.__balance  # エラー（直接アクセス不可）",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '特殊メソッド',
                        'content' => "class Point:\n    def __init__(self, x, y):\n        self.x = x\n        self.y = y\n    \n    def __str__(self):  # print()で使われる\n        return f\"Point({self.x}, {self.y})\"\n    \n    def __repr__(self):  # デバッグ用\n        return f\"Point(x={self.x}, y={self.y})\"\n    \n    def __add__(self, other):  # + 演算子\n        return Point(self.x + other.x, self.y + other.y)\n    \n    def __eq__(self, other):  # == 演算子\n        return self.x == other.x and self.y == other.y\n    \n    def __len__(self):  # len()で使われる\n        return abs(self.x) + abs(self.y)\n\n# 使用例\np1 = Point(1, 2)\np2 = Point(3, 4)\n\nprint(p1)  # Point(1, 2)\np3 = p1 + p2  # Point(4, 6)\nprint(p1 == p2)  # False\nprint(len(p1))  # 3",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 5: モジュールとファイル操作 (第14週～第15週)
        $milestone5 = $template->milestones()->create([
            'title' => 'モジュールとファイル操作',
            'description' => 'モジュールのインポート、ファイル操作、例外処理、標準ライブラリ',
            'sort_order' => 5,
            'estimated_hours' => 24,
            'deliverables' => [
                'モジュールを作成・インポート',
                'ファイルの読み書きプログラム',
                '例外処理を実装したプログラム'
            ],
        ]);

        $milestone5->tasks()->createMany([
            // Week 14
            [
                'title' => '第14週：モジュールと例外処理',
                'description' => 'モジュールのインポート、作成、例外処理',
                'sort_order' => 14,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Python Modules and Exceptions'],
                'subtasks' => [
                    ['title' => 'モジュールを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '例外処理を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'モジュール',
                        'content' => "# 標準ライブラリのインポート\nimport math\nfrom datetime import datetime\nimport random as rnd  # エイリアス\n\n# 使用例\nprint(math.sqrt(16))  # 4.0\nprint(math.pi)  # 3.141592653589793\n\nnow = datetime.now()\nprint(now)\n\nprint(rnd.randint(1, 100))  # 1-100のランダムな整数\n\n# 自作モジュールの作成\n# mymodule.py\n# def greet(name):\n#     return f\"Hello, {name}!\"\n# \n# PI = 3.14159\n\n# メインプログラム\n# import mymodule\n# print(mymodule.greet(\"太郎\"))\n# print(mymodule.PI)",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '例外処理',
                        'content' => "# try-except\ntry:\n    num = int(input(\"数値を入力: \"))\n    result = 100 / num\n    print(f\"結果: {result}\")\nexcept ValueError:\n    print(\"数値を入力してください。\")\nexcept ZeroDivisionError:\n    print(\"0では割れません。\")\nexcept Exception as e:\n    print(f\"エラー: {e}\")\nelse:\n    print(\"正常に処理されました。\")\nfinally:\n    print(\"処理完了。\")\n\n# 例外を発生させる\ndef divide(a, b):\n    if b == 0:\n        raise ValueError(\"0で割ることはできません。\")\n    return a / b\n\ntry:\n    result = divide(10, 0)\nexcept ValueError as e:\n    print(e)  # 0で割ることはできません。",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 15
            [
                'title' => '第15週：ファイル操作と実践プロジェクト',
                'description' => 'ファイルの読み書き、CSVファイル、JSONファイル',
                'sort_order' => 15,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Python File I/O'],
                'subtasks' => [
                    ['title' => 'ファイルの読み書きを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'CSV・JSONファイルを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '実践プロジェクトを作成', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ファイル操作',
                        'content' => "# ファイルの書き込み\nwith open(\"sample.txt\", \"w\", encoding=\"utf-8\") as f:\n    f.write(\"こんにちは、Python！\\n\")\n    f.write(\"ファイル操作を学習中です。\\n\")\n\n# ファイルの読み込み\nwith open(\"sample.txt\", \"r\", encoding=\"utf-8\") as f:\n    content = f.read()  # 全体を読む\n    print(content)\n\n# 行ごとに読む\nwith open(\"sample.txt\", \"r\", encoding=\"utf-8\") as f:\n    for line in f:\n        print(line.strip())\n\n# 追記モード\nwith open(\"sample.txt\", \"a\", encoding=\"utf-8\") as f:\n    f.write(\"追記します。\\n\")\n\n# ファイルの存在チェック\nimport os\nif os.path.exists(\"sample.txt\"):\n    print(\"ファイルが存在します。\")",
                        'code_language' => 'python',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'CSV・JSONファイル',
                        'content' => "# CSVファイルの読み書き\nimport csv\n\n# CSV書き込み\nwith open(\"students.csv\", \"w\", newline=\"\", encoding=\"utf-8\") as f:\n    writer = csv.writer(f)\n    writer.writerow([\"名前\", \"年齢\", \"成績\"])\n    writer.writerow([\"太郎\", 20, \"A\"])\n    writer.writerow([\"花子\", 22, \"B\"])\n\n# CSV読み込み\nwith open(\"students.csv\", \"r\", encoding=\"utf-8\") as f:\n    reader = csv.reader(f)\n    for row in reader:\n        print(row)\n\n# JSONファイル\nimport json\n\n# JSON書き込み\ndata = {\n    \"name\": \"太郎\",\n    \"age\": 20,\n    \"skills\": [\"Python\", \"JavaScript\"]\n}\n\nwith open(\"data.json\", \"w\", encoding=\"utf-8\") as f:\n    json.dump(data, f, ensure_ascii=False, indent=2)\n\n# JSON読み込み\nwith open(\"data.json\", \"r\", encoding=\"utf-8\") as f:\n    loaded_data = json.load(f)\n    print(loaded_data)",
                        'code_language' => 'python',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Pythonのベストプラクティス',
                        'content' => "# Pythonのベストプラクティス\n\n## PEP 8（コーディング規約）\n1. **インデント**: スペース4つ\n2. **命名規則**:\n   - 変数・関数: snake_case\n   - クラス: PascalCase\n   - 定数: UPPER_CASE\n3. **行の長さ**: 最大79文字\n4. **import**: ファイルの先頭にまとめる\n\n## 便利な標準ライブラリ\n- **datetime**: 日付・時刻処理\n- **os, pathlib**: ファイル・ディレクトリ操作\n- **re**: 正規表現\n- **collections**: 便利なデータ構造\n- **itertools**: イテレータツール\n\n## 仮想環境\n```bash\n# 仮想環境の作成\npython -m venv myenv\n\n# アクティベート（Windows）\nmyenv\\Scripts\\activate\n\n# アクティベート（Mac/Linux）\nsource myenv/bin/activate\n\n# パッケージのインストール\npip install requests pandas\n```\n\n## プロジェクト構造\n```\nmyproject/\n├── main.py\n├── requirements.txt\n├── modules/\n│   ├── __init__.py\n│   └── utils.py\n└── tests/\n    └── test_utils.py\n```",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "Python Course Seeder completed successfully!\n";
    }
}
