<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class SqlCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * SQL/データベース基礎コース - 12週間の実践コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'SQL/データベース基礎コース',
            'description' => '初心者向けSQL/データベース完全コース。12週間でデータベースの基礎から、SQL操作、データベース設計、最適化まで実践的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 96,
            'tags' => ['sql', 'database', 'mysql', 'データベース', '初心者', 'RDBMS'],
            'icon' => 'ic_database',
            'color' => '#4479A1',
            'is_featured' => true,
        ]);

        // Milestone 1: SQL基礎とCRUD操作 (第1週～第3週)
        $milestone1 = $template->milestones()->create([
            'title' => 'SQL基礎とCRUD操作',
            'description' => 'データベースの概念、テーブル作成、基本的なCRUD操作（作成、読取、更新、削除）',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'データベースとテーブルを作成',
                'INSERT文でデータを挿入',
                'SELECT文でデータを取得',
                'UPDATE/DELETE文でデータを更新・削除'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：データベース基礎とセットアップ',
                'description' => 'データベースの概念、RDBMS、MySQLのインストールと基本操作',
                'sort_order' => 1,
                'estimated_minutes' => 270,
                'priority' => 5,
                'resources' => ['MySQL公式ドキュメント', 'SQL Tutorial'],
                'subtasks' => [
                    ['title' => 'MySQLをインストール', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'データベースの概念を理解', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '最初のデータベースを作成', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'データベースとは？',
                        'content' => "# データベースとは？\n\n**データベース**は、データを整理して保存・管理するシステムです。\n\n## データベースの種類\n\n### 1. リレーショナルデータベース（RDBMS）\n- **MySQL**: 最も人気、オープンソース\n- **PostgreSQL**: 高機能、エンタープライズ向け\n- **SQLite**: 軽量、モバイルアプリ向け\n- **Oracle**: エンタープライズ向け、有料\n- **SQL Server**: Microsoft製\n\n### 2. NoSQL\n- **MongoDB**: ドキュメント型\n- **Redis**: キーバリュー型\n- **Cassandra**: カラム型\n\n## RDBMSの特徴\n1. **テーブル**: 行と列でデータを管理\n2. **SQL**: 標準的なクエリ言語\n3. **ACID特性**: トランザクションの信頼性\n4. **リレーション**: テーブル間の関連性\n\n## 基本用語\n- **データベース（Database）**: データの集合\n- **テーブル（Table）**: データを格納する表\n- **行（Row/Record）**: 1件のデータ\n- **列（Column/Field）**: データの属性\n- **主キー（Primary Key）**: 各行を一意に識別\n- **外部キー（Foreign Key）**: 他テーブルとの関連",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'MySQLの基本操作',
                        'content' => "-- MySQLに接続\nmysql -u root -p\n\n-- データベース一覧を表示\nSHOW DATABASES;\n\n-- 新しいデータベースを作成\nCREATE DATABASE my_database;\nCREATE DATABASE IF NOT EXISTS school;\n\n-- データベースを選択\nUSE my_database;\n\n-- 現在のデータベースを確認\nSELECT DATABASE();\n\n-- データベースを削除\nDROP DATABASE my_database;\nDROP DATABASE IF EXISTS old_database;\n\n-- 文字コードを指定してデータベース作成\nCREATE DATABASE my_database\n  CHARACTER SET utf8mb4\n  COLLATE utf8mb4_unicode_ci;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：テーブル作成とデータ型',
                'description' => 'CREATE TABLE、データ型、制約、INSERT文',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['MySQL Data Types'],
                'subtasks' => [
                    ['title' => 'データ型を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'テーブルを作成', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'INSERT文を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'SQLのデータ型',
                        'content' => "# SQLのデータ型\n\n## 数値型\n- **INT**: 整数（-2,147,483,648 〜 2,147,483,647）\n- **BIGINT**: 大きい整数\n- **DECIMAL(M,D)**: 固定小数点数（例: DECIMAL(10,2)）\n- **FLOAT, DOUBLE**: 浮動小数点数\n\n## 文字列型\n- **VARCHAR(n)**: 可変長文字列（最大65,535文字）\n- **CHAR(n)**: 固定長文字列\n- **TEXT**: 長い文字列（最大65,535文字）\n- **LONGTEXT**: 非常に長い文字列（最大4GB）\n\n## 日付・時刻型\n- **DATE**: 日付（YYYY-MM-DD）\n- **TIME**: 時刻（HH:MM:SS）\n- **DATETIME**: 日時（YYYY-MM-DD HH:MM:SS）\n- **TIMESTAMP**: タイムスタンプ（自動更新可能）\n- **YEAR**: 年（YYYY）\n\n## その他\n- **BOOLEAN/BOOL**: 真偽値（0 or 1）\n- **ENUM**: 列挙型（例: ENUM('small', 'medium', 'large')）\n- **JSON**: JSON形式のデータ",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'テーブル作成',
                        'content' => "-- 基本的なテーブル作成\nCREATE TABLE users (\n    id INT,\n    name VARCHAR(100),\n    email VARCHAR(255),\n    age INT,\n    created_at DATETIME\n);\n\n-- 制約付きテーブル作成\nCREATE TABLE students (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    name VARCHAR(100) NOT NULL,\n    email VARCHAR(255) UNIQUE NOT NULL,\n    age INT CHECK (age >= 18),\n    grade ENUM('A', 'B', 'C', 'D', 'F'),\n    gpa DECIMAL(3, 2) DEFAULT 0.00,\n    is_active BOOLEAN DEFAULT TRUE,\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n);\n\n-- テーブル一覧を表示\nSHOW TABLES;\n\n-- テーブル構造を表示\nDESCRIBE students;\nDESC students;\nSHOW COLUMNS FROM students;\n\n-- テーブル作成SQL文を表示\nSHOW CREATE TABLE students;\n\n-- テーブルを削除\nDROP TABLE users;\nDROP TABLE IF EXISTS old_table;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'INSERT文 - データ挿入',
                        'content' => "-- 基本的な挿入\nINSERT INTO students (name, email, age, grade)\nVALUES ('太郎', 'taro@example.com', 20, 'A');\n\n-- 複数行を一度に挿入\nINSERT INTO students (name, email, age, grade) VALUES\n    ('花子', 'hanako@example.com', 21, 'B'),\n    ('次郎', 'jiro@example.com', 19, 'A'),\n    ('三郎', 'saburo@example.com', 22, 'C');\n\n-- 全ての列に挿入（列名省略可能）\nINSERT INTO students\nVALUES (5, '四郎', 'shiro@example.com', 20, 'B', 3.5, TRUE, NOW(), NOW());\n\n-- 一部の列のみ挿入（他はDEFAULT値）\nINSERT INTO students (name, email)\nVALUES ('五郎', 'goro@example.com');\n\n-- 挿入された行のIDを取得\nINSERT INTO students (name, email, age)\nVALUES ('六郎', 'rokuro@example.com', 21);\nSELECT LAST_INSERT_ID();",
                        'code_language' => 'sql',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：SELECT、UPDATE、DELETE',
                'description' => 'データの取得、更新、削除の基本操作',
                'sort_order' => 3,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['SQL CRUD Operations'],
                'subtasks' => [
                    ['title' => 'SELECT文を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'UPDATE文を学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'DELETE文を学習', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'SELECT文 - データ取得',
                        'content' => "-- 全ての列を取得\nSELECT * FROM students;\n\n-- 特定の列のみ取得\nSELECT name, email, age FROM students;\n\n-- エイリアスを使用\nSELECT \n    name AS 名前,\n    email AS メールアドレス,\n    age AS 年齢\nFROM students;\n\n-- 計算結果を取得\nSELECT \n    name,\n    age,\n    age + 5 AS five_years_later\nFROM students;\n\n-- 重複を除外\nSELECT DISTINCT grade FROM students;\n\n-- 件数を制限\nSELECT * FROM students LIMIT 5;\nSELECT * FROM students LIMIT 5 OFFSET 10;  -- 11件目から5件\nSELECT * FROM students LIMIT 10, 5;  -- 同上",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'UPDATE文 - データ更新',
                        'content' => "-- 基本的な更新\nUPDATE students\nSET grade = 'A'\nWHERE id = 1;\n\n-- 複数列を更新\nUPDATE students\nSET \n    grade = 'B',\n    gpa = 3.5,\n    updated_at = NOW()\nWHERE id = 2;\n\n-- 条件に合う全ての行を更新\nUPDATE students\nSET is_active = FALSE\nWHERE age < 18;\n\n-- 計算結果で更新\nUPDATE students\nSET age = age + 1\nWHERE id = 3;\n\n-- ⚠️ WHERE句を忘れると全てのレコードが更新される\n-- UPDATE students SET grade = 'F';  -- 危険！\n\n-- 安全な更新（影響を受ける行数を確認）\nSELECT COUNT(*) FROM students WHERE age < 18;\nUPDATE students SET is_active = FALSE WHERE age < 18;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'DELETE文 - データ削除',
                        'content' => "-- 基本的な削除\nDELETE FROM students\nWHERE id = 1;\n\n-- 条件に合う全ての行を削除\nDELETE FROM students\nWHERE grade = 'F';\n\n-- 複数条件で削除\nDELETE FROM students\nWHERE age < 18 AND is_active = FALSE;\n\n-- ⚠️ WHERE句を忘れると全てのレコードが削除される\n-- DELETE FROM students;  -- 危険！全削除\n\n-- 全てのデータを削除（TRUNCATEの方が高速）\nTRUNCATE TABLE students;  -- AUTO_INCREMENTもリセット\n\n-- 削除前に確認\nSELECT COUNT(*) FROM students WHERE age < 18;\nDELETE FROM students WHERE age < 18;\n\n-- 論理削除（実際には削除せず、フラグを立てる）\nUPDATE students\nSET is_active = FALSE, deleted_at = NOW()\nWHERE id = 5;",
                        'code_language' => 'sql',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 2: データ取得とJOIN (第4週～第6週)
        $milestone2 = $template->milestones()->create([
            'title' => 'データ取得とJOIN',
            'description' => 'WHERE句、ORDER BY、GROUP BY、集計関数、JOINによるテーブル結合',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                'WHERE句で条件指定',
                'ORDER BY、GROUP BYでデータを整理',
                '集計関数で統計を取得',
                'JOINで複数テーブルを結合'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 4
            [
                'title' => '第4週：WHERE句と条件指定',
                'description' => 'WHERE句、比較演算子、論理演算子、LIKE、IN、BETWEEN',
                'sort_order' => 4,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['SQL WHERE Clause'],
                'subtasks' => [
                    ['title' => 'WHERE句の基本を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'LIKE、IN、BETWEENを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'NULL値の扱いを学習', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'WHERE句 - 条件指定',
                        'content' => "-- 比較演算子\nSELECT * FROM students WHERE age = 20;  -- 等しい\nSELECT * FROM students WHERE age != 20;  -- 等しくない\nSELECT * FROM students WHERE age <> 20;  -- 等しくない（別の書き方）\nSELECT * FROM students WHERE age > 20;   -- より大きい\nSELECT * FROM students WHERE age >= 20;  -- 以上\nSELECT * FROM students WHERE age < 20;   -- より小さい\nSELECT * FROM students WHERE age <= 20;  -- 以下\n\n-- 論理演算子\nSELECT * FROM students\nWHERE age >= 18 AND age <= 25;\n\nSELECT * FROM students\nWHERE grade = 'A' OR grade = 'B';\n\nSELECT * FROM students\nWHERE NOT (age < 18);\n\n-- 複数条件の組み合わせ\nSELECT * FROM students\nWHERE (grade = 'A' OR grade = 'B') AND age >= 20;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'LIKE、IN、BETWEEN',
                        'content' => "-- LIKE（パターンマッチング）\nSELECT * FROM students WHERE name LIKE '太%';  -- 太で始まる\nSELECT * FROM students WHERE name LIKE '%郎';  -- 郎で終わる\nSELECT * FROM students WHERE name LIKE '%田%';  -- 田を含む\nSELECT * FROM students WHERE email LIKE '%@gmail.com';  -- Gmail\nSELECT * FROM students WHERE name LIKE '___';  -- 3文字（_は1文字）\n\n-- IN（複数の値）\nSELECT * FROM students WHERE grade IN ('A', 'B');\nSELECT * FROM students WHERE id IN (1, 3, 5, 7);\nSELECT * FROM students WHERE grade NOT IN ('F');\n\n-- BETWEEN（範囲指定）\nSELECT * FROM students WHERE age BETWEEN 18 AND 25;  -- 18以上25以下\nSELECT * FROM students WHERE gpa BETWEEN 3.0 AND 4.0;\nSELECT * FROM students \nWHERE created_at BETWEEN '2024-01-01' AND '2024-12-31';\n\n-- NULL値のチェック\nSELECT * FROM students WHERE email IS NULL;\nSELECT * FROM students WHERE email IS NOT NULL;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 5
            [
                'title' => '第5週：ORDER BY、GROUP BY、集計関数',
                'description' => 'データの並び替え、グループ化、COUNT/SUM/AVG/MAX/MIN',
                'sort_order' => 5,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['SQL Aggregate Functions'],
                'subtasks' => [
                    ['title' => 'ORDER BYを学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => '集計関数を学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'GROUP BYとHAVINGを学習', 'estimated_minutes' => 150, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ORDER BY - ソート',
                        'content' => "-- 昇順（ASC: 小さい順）\nSELECT * FROM students ORDER BY age ASC;\nSELECT * FROM students ORDER BY age;  -- ASCは省略可能\n\n-- 降順（DESC: 大きい順）\nSELECT * FROM students ORDER BY age DESC;\nSELECT * FROM students ORDER BY gpa DESC;\n\n-- 複数列でソート\nSELECT * FROM students\nORDER BY grade ASC, gpa DESC;\n\n-- 計算結果でソート\nSELECT name, age, age * 2 AS double_age\nFROM students\nORDER BY double_age DESC;\n\n-- 列番号でソート（非推奨だが可能）\nSELECT name, age, grade FROM students\nORDER BY 3, 2 DESC;  -- 3列目（grade）、2列目（age）降順\n\n-- NULL値の扱い\nSELECT * FROM students ORDER BY email;  -- NULLは最初\nSELECT * FROM students ORDER BY email DESC;  -- NULLは最後",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '集計関数',
                        'content' => "-- COUNT（件数）\nSELECT COUNT(*) FROM students;  -- 全行数\nSELECT COUNT(email) FROM students;  -- NULLを除く\nSELECT COUNT(DISTINCT grade) FROM students;  -- 重複除外\n\n-- SUM（合計）\nSELECT SUM(age) FROM students;\nSELECT SUM(gpa) FROM students;\n\n-- AVG（平均）\nSELECT AVG(age) FROM students;\nSELECT AVG(gpa) AS average_gpa FROM students;\nSELECT ROUND(AVG(age), 2) AS avg_age FROM students;  -- 小数点2桁\n\n-- MAX/MIN（最大/最小）\nSELECT MAX(age) FROM students;\nSELECT MIN(age) FROM students;\nSELECT MAX(gpa), MIN(gpa) FROM students;\n\n-- 複数の集計を同時に\nSELECT \n    COUNT(*) AS total_students,\n    AVG(age) AS average_age,\n    MAX(gpa) AS highest_gpa,\n    MIN(gpa) AS lowest_gpa\nFROM students;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'GROUP BY と HAVING',
                        'content' => "-- GROUP BY（グループ化）\nSELECT grade, COUNT(*) AS student_count\nFROM students\nGROUP BY grade;\n\nSELECT grade, AVG(age) AS average_age\nFROM students\nGROUP BY grade;\n\n-- 複数列でグループ化\nSELECT grade, is_active, COUNT(*) AS count\nFROM students\nGROUP BY grade, is_active;\n\n-- HAVING（グループ化後のフィルタ）\nSELECT grade, COUNT(*) AS student_count\nFROM students\nGROUP BY grade\nHAVING COUNT(*) >= 5;  -- 5人以上のgradeのみ\n\nSELECT grade, AVG(gpa) AS avg_gpa\nFROM students\nGROUP BY grade\nHAVING AVG(gpa) >= 3.0;\n\n-- WHERE と HAVING の違い\n-- WHERE: グループ化前のフィルタ（個別行）\n-- HAVING: グループ化後のフィルタ（集計結果）\n\nSELECT grade, COUNT(*) AS count\nFROM students\nWHERE age >= 18  -- 個別の条件\nGROUP BY grade\nHAVING COUNT(*) >= 3  -- グループの条件\nORDER BY count DESC;",
                        'code_language' => 'sql',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：JOIN（テーブル結合）',
                'description' => 'INNER JOIN、LEFT JOIN、RIGHT JOIN、CROSS JOIN、自己結合',
                'sort_order' => 6,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['SQL JOIN Tutorial'],
                'subtasks' => [
                    ['title' => 'INNER JOINを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'LEFT/RIGHT JOINを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '複数テーブルのJOINを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'JOINの種類',
                        'content' => "# JOINの種類\n\n## INNER JOIN（内部結合）\n両方のテーブルに一致するデータのみ取得\n```\nA: [1,2,3]    B: [2,3,4]\n結果: [2,3]\n```\n\n## LEFT JOIN（左外部結合）\n左テーブルの全データ + 右テーブルの一致データ\n```\nA: [1,2,3]    B: [2,3,4]\n結果: [1,2,3]  (1はBがNULL)\n```\n\n## RIGHT JOIN（右外部結合）\n右テーブルの全データ + 左テーブルの一致データ\n```\nA: [1,2,3]    B: [2,3,4]\n結果: [2,3,4]  (4はAがNULL)\n```\n\n## FULL OUTER JOIN（完全外部結合）\n両方のテーブルの全データ（MySQLは未サポート）\n```\nA: [1,2,3]    B: [2,3,4]\n結果: [1,2,3,4]\n```\n\n## CROSS JOIN（交差結合）\n全ての組み合わせ\n```\nA: [1,2]    B: [a,b]\n結果: [(1,a), (1,b), (2,a), (2,b)]\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'INNER JOIN',
                        'content' => "-- サンプルテーブル\nCREATE TABLE courses (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    course_name VARCHAR(100)\n);\n\nCREATE TABLE enrollments (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    student_id INT,\n    course_id INT,\n    enrollment_date DATE\n);\n\n-- INNER JOIN（基本）\nSELECT \n    students.name,\n    courses.course_name,\n    enrollments.enrollment_date\nFROM enrollments\nINNER JOIN students ON enrollments.student_id = students.id\nINNER JOIN courses ON enrollments.course_id = courses.id;\n\n-- テーブルエイリアスを使用（推奨）\nSELECT \n    s.name AS student_name,\n    c.course_name,\n    e.enrollment_date\nFROM enrollments e\nINNER JOIN students s ON e.student_id = s.id\nINNER JOIN courses c ON e.course_id = c.id\nWHERE s.grade = 'A'\nORDER BY e.enrollment_date DESC;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'LEFT JOIN と RIGHT JOIN',
                        'content' => "-- LEFT JOIN（左外部結合）\n-- 全ての学生と、履修があればコース情報を表示\nSELECT \n    s.name,\n    c.course_name\nFROM students s\nLEFT JOIN enrollments e ON s.id = e.student_id\nLEFT JOIN courses c ON e.course_id = c.id;\n\n-- 履修していない学生を検索\nSELECT s.name\nFROM students s\nLEFT JOIN enrollments e ON s.id = e.student_id\nWHERE e.id IS NULL;\n\n-- RIGHT JOIN（右外部結合）\n-- 全てのコースと、履修者がいれば学生情報を表示\nSELECT \n    c.course_name,\n    s.name\nFROM enrollments e\nRIGHT JOIN courses c ON e.course_id = c.id\nLEFT JOIN students s ON e.student_id = s.id;\n\n-- 履修者がいないコースを検索\nSELECT c.course_name\nFROM courses c\nLEFT JOIN enrollments e ON c.id = e.course_id\nWHERE e.id IS NULL;",
                        'code_language' => 'sql',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'CROSS JOIN と自己結合',
                        'content' => "-- CROSS JOIN（交差結合）\n-- 全ての組み合わせを生成\nSELECT \n    s.name AS student,\n    c.course_name\nFROM students s\nCROSS JOIN courses c;\n\n-- 自己結合（同じテーブル同士を結合）\n-- 例: 同じgradeの学生ペアを見つける\nSELECT \n    s1.name AS student1,\n    s2.name AS student2,\n    s1.grade\nFROM students s1\nINNER JOIN students s2 ON s1.grade = s2.grade\nWHERE s1.id < s2.id;  -- 重複ペアを避ける\n\n-- 階層構造（従業員と上司）\nCREATE TABLE employees (\n    id INT PRIMARY KEY,\n    name VARCHAR(100),\n    manager_id INT\n);\n\nSELECT \n    e.name AS employee,\n    m.name AS manager\nFROM employees e\nLEFT JOIN employees m ON e.manager_id = m.id;",
                        'code_language' => 'sql',
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 3: 高度なSQL (第7週～第9週)
        $milestone3 = $template->milestones()->create([
            'title' => '高度なSQL',
            'description' => 'サブクエリ、CTE、ウィンドウ関数、トランザクション、インデックス',
            'sort_order' => 3,
            'estimated_hours' => 24,
            'deliverables' => [
                'サブクエリで複雑な条件を記述',
                'CTEで読みやすいクエリを作成',
                'ウィンドウ関数で分析',
                'トランザクションでデータ整合性を保証'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 7
            [
                'title' => '第7週：サブクエリとCTE',
                'description' => 'サブクエリ（副問い合わせ）、WITH句（CTE）',
                'sort_order' => 7,
                'estimated_minutes' => 390,
                'priority' => 4,
                'resources' => ['SQL Subqueries', 'Common Table Expressions'],
                'subtasks' => [
                    ['title' => 'サブクエリの基本を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => '相関サブクエリを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'CTEを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'サブクエリ',
                        'content' => "-- WHERE句内のサブクエリ\nSELECT name, age\nFROM students\nWHERE age > (SELECT AVG(age) FROM students);\n\n-- IN を使ったサブクエリ\nSELECT name\nFROM students\nWHERE id IN (\n    SELECT student_id\n    FROM enrollments\n    WHERE course_id = 1\n);\n\n-- EXISTS を使ったサブクエリ\nSELECT name\nFROM students s\nWHERE EXISTS (\n    SELECT 1\n    FROM enrollments e\n    WHERE e.student_id = s.id\n);\n\n-- FROM句内のサブクエリ（派生テーブル）\nSELECT grade, avg_gpa\nFROM (\n    SELECT grade, AVG(gpa) AS avg_gpa\n    FROM students\n    GROUP BY grade\n) AS grade_stats\nWHERE avg_gpa >= 3.0;\n\n-- SELECT句内のサブクエリ（スカラーサブクエリ）\nSELECT \n    name,\n    age,\n    (SELECT AVG(age) FROM students) AS avg_age,\n    age - (SELECT AVG(age) FROM students) AS age_diff\nFROM students;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'CTE（Common Table Expression）',
                        'content' => "-- 基本的なCTE\nWITH grade_stats AS (\n    SELECT \n        grade,\n        COUNT(*) AS student_count,\n        AVG(gpa) AS avg_gpa\n    FROM students\n    GROUP BY grade\n)\nSELECT *\nFROM grade_stats\nWHERE avg_gpa >= 3.0;\n\n-- 複数のCTE\nWITH \nhigh_performers AS (\n    SELECT * FROM students WHERE gpa >= 3.5\n),\nactive_students AS (\n    SELECT * FROM students WHERE is_active = TRUE\n)\nSELECT h.*\nFROM high_performers h\nINNER JOIN active_students a ON h.id = a.id;\n\n-- 再帰的CTE（階層構造）\nWITH RECURSIVE number_series AS (\n    SELECT 1 AS n\n    UNION ALL\n    SELECT n + 1\n    FROM number_series\n    WHERE n < 10\n)\nSELECT * FROM number_series;\n\n-- 組織図（従業員と部下）\nWITH RECURSIVE employee_hierarchy AS (\n    -- 最上位（社長）\n    SELECT id, name, manager_id, 1 AS level\n    FROM employees\n    WHERE manager_id IS NULL\n    \n    UNION ALL\n    \n    -- 部下\n    SELECT e.id, e.name, e.manager_id, eh.level + 1\n    FROM employees e\n    INNER JOIN employee_hierarchy eh ON e.manager_id = eh.id\n)\nSELECT * FROM employee_hierarchy\nORDER BY level, id;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 8
            [
                'title' => '第8週：ウィンドウ関数と高度な集計',
                'description' => 'ROW_NUMBER、RANK、LEAD/LAG、累積集計',
                'sort_order' => 8,
                'estimated_minutes' => 390,
                'priority' => 4,
                'resources' => ['SQL Window Functions'],
                'subtasks' => [
                    ['title' => 'ROW_NUMBER、RANKを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'LEAD/LAGを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '累積集計を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ウィンドウ関数の基本',
                        'content' => "-- ROW_NUMBER（連番）\nSELECT \n    name,\n    gpa,\n    ROW_NUMBER() OVER (ORDER BY gpa DESC) AS row_num\nFROM students;\n\n-- RANK（順位、同率は同順位）\nSELECT \n    name,\n    gpa,\n    RANK() OVER (ORDER BY gpa DESC) AS rank\nFROM students;\n\n-- DENSE_RANK（順位、隙間なし）\nSELECT \n    name,\n    gpa,\n    DENSE_RANK() OVER (ORDER BY gpa DESC) AS dense_rank\nFROM students;\n\n-- PARTITION BY（グループ内での順位）\nSELECT \n    grade,\n    name,\n    gpa,\n    ROW_NUMBER() OVER (PARTITION BY grade ORDER BY gpa DESC) AS rank_in_grade\nFROM students;\n\n-- 各gradeでトップ3を取得\nWITH ranked_students AS (\n    SELECT \n        grade,\n        name,\n        gpa,\n        ROW_NUMBER() OVER (PARTITION BY grade ORDER BY gpa DESC) AS rn\n    FROM students\n)\nSELECT grade, name, gpa\nFROM ranked_students\nWHERE rn <= 3;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'LEAD、LAG、集計ウィンドウ',
                        'content' => "-- LEAD（次の行の値を取得）\nSELECT \n    name,\n    age,\n    LEAD(age, 1) OVER (ORDER BY age) AS next_age\nFROM students;\n\n-- LAG（前の行の値を取得）\nSELECT \n    name,\n    age,\n    LAG(age, 1) OVER (ORDER BY age) AS prev_age,\n    age - LAG(age, 1) OVER (ORDER BY age) AS age_diff\nFROM students;\n\n-- 累積合計（SUM OVER）\nSELECT \n    name,\n    age,\n    SUM(age) OVER (ORDER BY id) AS cumulative_age\nFROM students;\n\n-- 移動平均\nSELECT \n    name,\n    gpa,\n    AVG(gpa) OVER (\n        ORDER BY id\n        ROWS BETWEEN 2 PRECEDING AND CURRENT ROW\n    ) AS moving_avg_3\nFROM students;\n\n-- FIRST_VALUE、LAST_VALUE\nSELECT \n    grade,\n    name,\n    gpa,\n    FIRST_VALUE(name) OVER (PARTITION BY grade ORDER BY gpa DESC) AS top_student,\n    LAST_VALUE(name) OVER (\n        PARTITION BY grade \n        ORDER BY gpa DESC\n        ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING\n    ) AS bottom_student\nFROM students;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 9
            [
                'title' => '第9週：トランザクションとインデックス',
                'description' => 'ACID特性、COMMIT/ROLLBACK、インデックスの作成と最適化',
                'sort_order' => 9,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => ['SQL Transactions', 'Database Indexing'],
                'subtasks' => [
                    ['title' => 'トランザクションを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'インデックスを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'クエリ最適化を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'トランザクション',
                        'content' => "-- トランザクションの基本\nSTART TRANSACTION;  -- または BEGIN;\n\nUPDATE accounts SET balance = balance - 100 WHERE id = 1;\nUPDATE accounts SET balance = balance + 100 WHERE id = 2;\n\nCOMMIT;  -- 確定\n-- ROLLBACK;  -- 取り消し\n\n-- エラーハンドリング付きトランザクション\nSTART TRANSACTION;\n\nUPDATE students SET gpa = 4.0 WHERE id = 1;\n\n-- エラーが発生した場合\nIF error THEN\n    ROLLBACK;\nELSE\n    COMMIT;\nEND IF;\n\n-- セーブポイント\nSTART TRANSACTION;\n\nINSERT INTO students (name, email) VALUES ('Test1', 'test1@example.com');\nSAVEPOINT sp1;\n\nINSERT INTO students (name, email) VALUES ('Test2', 'test2@example.com');\nSAVEPOINT sp2;\n\nINSERT INTO students (name, email) VALUES ('Test3', 'test3@example.com');\n\nROLLBACK TO sp2;  -- sp2まで戻る（Test3のみ取り消し）\n\nCOMMIT;\n\n-- トランザクション分離レベル\nSET TRANSACTION ISOLATION LEVEL READ COMMITTED;\nSET TRANSACTION ISOLATION LEVEL REPEATABLE READ;\nSET TRANSACTION ISOLATION LEVEL SERIALIZABLE;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'インデックス',
                        'content' => "-- インデックス作成\nCREATE INDEX idx_email ON students(email);\nCREATE INDEX idx_grade_gpa ON students(grade, gpa);\n\n-- ユニークインデックス\nCREATE UNIQUE INDEX idx_unique_email ON students(email);\n\n-- フルテキストインデックス\nCREATE FULLTEXT INDEX idx_fulltext_name ON students(name);\n\n-- インデックス一覧\nSHOW INDEX FROM students;\n\n-- インデックス削除\nDROP INDEX idx_email ON students;\n\n-- クエリの実行計画を確認\nEXPLAIN SELECT * FROM students WHERE email = 'test@example.com';\nEXPLAIN SELECT * FROM students WHERE grade = 'A' AND gpa > 3.5;\n\n-- インデックスの効果\n-- インデックスなし: フルスキャン（全行を読む）\n-- インデックスあり: 高速検索\n\n-- インデックスが効果的な場合\n-- - WHERE句で頻繁に使われる列\n-- - JOIN句で使われる列\n-- - ORDER BY句で使われる列\n\n-- インデックスが不要な場合\n-- - 小さいテーブル\n-- - 頻繁に更新される列\n-- - カーディナリティが低い列（値の種類が少ない）",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'クエリ最適化のヒント',
                        'content' => "# クエリ最適化のヒント\n\n## 1. SELECT * を避ける\n```sql\n-- 悪い例\nSELECT * FROM students;\n\n-- 良い例\nSELECT id, name, email FROM students;\n```\n\n## 2. WHERE句にインデックス列を使う\n```sql\n-- インデックスが効く\nSELECT * FROM students WHERE id = 100;\n\n-- インデックスが効かない（関数）\nSELECT * FROM students WHERE UPPER(name) = 'TARO';\n```\n\n## 3. LIMIT を使う\n```sql\nSELECT * FROM students ORDER BY gpa DESC LIMIT 10;\n```\n\n## 4. JOINよりEXISTSを使う場合も\n```sql\n-- EXISTS の方が速い場合がある\nSELECT * FROM students s\nWHERE EXISTS (\n    SELECT 1 FROM enrollments e WHERE e.student_id = s.id\n);\n```\n\n## 5. UNIONよりUNION ALLを使う\n```sql\n-- UNION: 重複削除あり（遅い）\n-- UNION ALL: 重複削除なし（速い）\n```\n\n## 6. サブクエリよりJOINを使う\n```sql\n-- 遅い\nSELECT * FROM students\nWHERE id IN (SELECT student_id FROM enrollments);\n\n-- 速い\nSELECT DISTINCT s.*\nFROM students s\nINNER JOIN enrollments e ON s.id = e.student_id;\n```\n\n## 7. EXPLAIN で実行計画を確認\n```sql\nEXPLAIN SELECT * FROM students WHERE grade = 'A';\n```",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 4: データベース設計と最適化 (第10週～第12週)
        $milestone4 = $template->milestones()->create([
            'title' => 'データベース設計と最適化',
            'description' => '正規化、制約、ビュー、ストアドプロシージャ、トリガー',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                '正規化されたデータベースを設計',
                'ビューで複雑なクエリを簡素化',
                'ストアドプロシージャで処理を再利用',
                'トリガーで自動処理を実装'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 10
            [
                'title' => '第10週：データベース設計と正規化',
                'description' => 'ER図、正規化（第1～第3正規形）、制約',
                'sort_order' => 10,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Database Normalization', 'ER Diagram'],
                'subtasks' => [
                    ['title' => 'ER図を学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => '正規化を学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => '制約を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'データベース正規化',
                        'content' => "# データベース正規化\n\n正規化は、データの重複を減らし、整合性を保つための手法です。\n\n## 第1正規形（1NF）\n- **繰り返しグループを排除**\n- 各列は単一の値を持つ\n\n悪い例:\n```\nstudents\n| id | name  | courses           |\n| 1  | Taro  | Math, Science     |\n```\n\n良い例:\n```\nstudents          enrollments\n| id | name  |    | student_id | course |\n| 1  | Taro  |    | 1          | Math   |\n                  | 1          | Science|\n```\n\n## 第2正規形（2NF）\n- 1NFを満たす\n- **部分関数従属を排除**\n- 主キー全体に依存する\n\n## 第3正規形（3NF）\n- 2NFを満たす\n- **推移的関数従属を排除**\n- 非キー列同士の依存をなくす\n\n悪い例:\n```\n| student_id | city | prefecture |\n| 1          | 横浜 | 神奈川     |\n```\n（横浜→神奈川は推移的従属）\n\n良い例:\n```\nstudents          cities\n| id | city_id |  | id | name | prefecture |\n| 1  | 10      |  | 10 | 横浜 | 神奈川     |\n```\n\n## 正規化のメリット・デメリット\n✅ データの重複が減る\n✅ 更新異常を防ぐ\n❌ JOINが増えて遅くなる場合も\n\n→ 適度な**非正規化**も必要",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '制約（Constraints）',
                        'content' => "-- PRIMARY KEY（主キー）\nCREATE TABLE students (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    name VARCHAR(100)\n);\n\n-- 複合主キー\nCREATE TABLE enrollments (\n    student_id INT,\n    course_id INT,\n    PRIMARY KEY (student_id, course_id)\n);\n\n-- FOREIGN KEY（外部キー）\nCREATE TABLE enrollments (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    student_id INT,\n    course_id INT,\n    FOREIGN KEY (student_id) REFERENCES students(id)\n        ON DELETE CASCADE\n        ON UPDATE CASCADE,\n    FOREIGN KEY (course_id) REFERENCES courses(id)\n        ON DELETE RESTRICT\n);\n\n-- UNIQUE（一意制約）\nCREATE TABLE users (\n    id INT PRIMARY KEY,\n    email VARCHAR(255) UNIQUE,\n    username VARCHAR(50) UNIQUE\n);\n\n-- CHECK（チェック制約）\nCREATE TABLE students (\n    id INT PRIMARY KEY,\n    age INT CHECK (age >= 18 AND age <= 100),\n    grade ENUM('A', 'B', 'C', 'D', 'F'),\n    gpa DECIMAL(3,2) CHECK (gpa >= 0.0 AND gpa <= 4.0)\n);\n\n-- NOT NULL（非NULL制約）\nCREATE TABLE products (\n    id INT PRIMARY KEY,\n    name VARCHAR(100) NOT NULL,\n    price DECIMAL(10,2) NOT NULL\n);\n\n-- DEFAULT（デフォルト値）\nCREATE TABLE posts (\n    id INT PRIMARY KEY,\n    title VARCHAR(200),\n    views INT DEFAULT 0,\n    is_published BOOLEAN DEFAULT FALSE,\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 11
            [
                'title' => '第11週：ビューとストアドプロシージャ',
                'description' => 'VIEW、STORED PROCEDURE、FUNCTION',
                'sort_order' => 11,
                'estimated_minutes' => 390,
                'priority' => 4,
                'resources' => ['SQL Views', 'Stored Procedures'],
                'subtasks' => [
                    ['title' => 'VIEWを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'STORED PROCEDUREを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'FUNCTIONを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'VIEW（ビュー）',
                        'content' => "-- ビュー作成\nCREATE VIEW student_summary AS\nSELECT \n    s.id,\n    s.name,\n    s.grade,\n    COUNT(e.id) AS course_count\nFROM students s\nLEFT JOIN enrollments e ON s.id = e.student_id\nGROUP BY s.id, s.name, s.grade;\n\n-- ビューを使用\nSELECT * FROM student_summary;\nSELECT * FROM student_summary WHERE grade = 'A';\n\n-- ビュー一覧\nSHOW FULL TABLES WHERE TABLE_TYPE = 'VIEW';\n\n-- ビューの定義を確認\nSHOW CREATE VIEW student_summary;\n\n-- ビューの更新\nCREATE OR REPLACE VIEW student_summary AS\nSELECT \n    s.id,\n    s.name,\n    s.grade,\n    s.gpa,\n    COUNT(e.id) AS course_count\nFROM students s\nLEFT JOIN enrollments e ON s.id = e.student_id\nGROUP BY s.id, s.name, s.grade, s.gpa;\n\n-- ビュー削除\nDROP VIEW student_summary;\n\n-- 更新可能なビュー\nCREATE VIEW active_students AS\nSELECT id, name, email, grade\nFROM students\nWHERE is_active = TRUE;\n\n-- ビュー経由でデータ更新\nUPDATE active_students SET grade = 'A' WHERE id = 1;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'STORED PROCEDURE（ストアドプロシージャ）',
                        'content' => "-- 基本的なストアドプロシージャ\nDELIMITER //\n\nCREATE PROCEDURE GetStudentsByGrade(IN p_grade CHAR(1))\nBEGIN\n    SELECT id, name, email, gpa\n    FROM students\n    WHERE grade = p_grade\n    ORDER BY gpa DESC;\nEND //\n\nDELIMITER ;\n\n-- 実行\nCALL GetStudentsByGrade('A');\n\n-- OUT パラメータ\nDELIMITER //\n\nCREATE PROCEDURE GetStudentCount(\n    IN p_grade CHAR(1),\n    OUT p_count INT\n)\nBEGIN\n    SELECT COUNT(*) INTO p_count\n    FROM students\n    WHERE grade = p_grade;\nEND //\n\nDELIMITER ;\n\n-- 実行\nCALL GetStudentCount('A', @count);\nSELECT @count;\n\n-- 条件分岐とループ\nDELIMITER //\n\nCREATE PROCEDURE UpdateGradeByGPA()\nBEGIN\n    DECLARE done INT DEFAULT FALSE;\n    DECLARE v_id INT;\n    DECLARE v_gpa DECIMAL(3,2);\n    DECLARE cur CURSOR FOR SELECT id, gpa FROM students;\n    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;\n    \n    OPEN cur;\n    \n    read_loop: LOOP\n        FETCH cur INTO v_id, v_gpa;\n        IF done THEN\n            LEAVE read_loop;\n        END IF;\n        \n        IF v_gpa >= 3.5 THEN\n            UPDATE students SET grade = 'A' WHERE id = v_id;\n        ELSEIF v_gpa >= 3.0 THEN\n            UPDATE students SET grade = 'B' WHERE id = v_id;\n        ELSEIF v_gpa >= 2.5 THEN\n            UPDATE students SET grade = 'C' WHERE id = v_id;\n        ELSE\n            UPDATE students SET grade = 'D' WHERE id = v_id;\n        END IF;\n    END LOOP;\n    \n    CLOSE cur;\nEND //\n\nDELIMITER ;\n\n-- プロシージャ一覧\nSHOW PROCEDURE STATUS WHERE Db = 'my_database';\n\n-- プロシージャ削除\nDROP PROCEDURE IF EXISTS GetStudentsByGrade;",
                        'code_language' => 'sql',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 12
            [
                'title' => '第12週：トリガーとベストプラクティス',
                'description' => 'TRIGGER、バックアップ、セキュリティ、パフォーマンスチューニング',
                'sort_order' => 12,
                'estimated_minutes' => 390,
                'priority' => 4,
                'resources' => ['SQL Triggers', 'Database Security'],
                'subtasks' => [
                    ['title' => 'TRIGGERを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'バックアップとセキュリティを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'ベストプラクティスを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'TRIGGER（トリガー）',
                        'content' => "-- BEFORE INSERT トリガー\nDELIMITER //\n\nCREATE TRIGGER before_student_insert\nBEFORE INSERT ON students\nFOR EACH ROW\nBEGIN\n    -- メールアドレスを小文字に変換\n    SET NEW.email = LOWER(NEW.email);\n    \n    -- 作成日時を設定\n    IF NEW.created_at IS NULL THEN\n        SET NEW.created_at = NOW();\n    END IF;\nEND //\n\nDELIMITER ;\n\n-- AFTER UPDATE トリガー（監査ログ）\nCREATE TABLE student_audit (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    student_id INT,\n    old_grade CHAR(1),\n    new_grade CHAR(1),\n    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);\n\nDELIMITER //\n\nCREATE TRIGGER after_student_update\nAFTER UPDATE ON students\nFOR EACH ROW\nBEGIN\n    IF OLD.grade != NEW.grade THEN\n        INSERT INTO student_audit (student_id, old_grade, new_grade)\n        VALUES (NEW.id, OLD.grade, NEW.grade);\n    END IF;\nEND //\n\nDELIMITER ;\n\n-- BEFORE DELETE トリガー\nDELIMITER //\n\nCREATE TRIGGER before_student_delete\nBEFORE DELETE ON students\nFOR EACH ROW\nBEGIN\n    -- 削除前にアーカイブ\n    INSERT INTO students_archive\n    SELECT * FROM students WHERE id = OLD.id;\nEND //\n\nDELIMITER ;\n\n-- トリガー一覧\nSHOW TRIGGERS;\n\n-- トリガー削除\nDROP TRIGGER IF EXISTS before_student_insert;",
                        'code_language' => 'sql',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'バックアップとリストア',
                        'content' => "-- データベース全体をバックアップ\nmysqldump -u root -p my_database > backup.sql\n\n-- 特定のテーブルのみバックアップ\nmysqldump -u root -p my_database students > students_backup.sql\n\n-- 構造のみバックアップ（データなし）\nmysqldump -u root -p --no-data my_database > structure.sql\n\n-- データのみバックアップ（構造なし）\nmysqldump -u root -p --no-create-info my_database > data.sql\n\n-- 圧縮してバックアップ\nmysqldump -u root -p my_database | gzip > backup.sql.gz\n\n-- リストア\nmysql -u root -p my_database < backup.sql\n\n-- 圧縮ファイルからリストア\ngunzip < backup.sql.gz | mysql -u root -p my_database\n\n-- データベースを削除して再作成\nDROP DATABASE IF EXISTS my_database;\nCREATE DATABASE my_database;\nmysql -u root -p my_database < backup.sql",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'SQLベストプラクティス',
                        'content' => "# SQLベストプラクティス\n\n## 1. 命名規則\n- **テーブル名**: 複数形、小文字、スネークケース（`students`, `course_enrollments`）\n- **列名**: 小文字、スネークケース（`first_name`, `created_at`）\n- **主キー**: `id`\n- **外部キー**: `テーブル名_id`（`student_id`, `course_id`）\n\n## 2. データ型の選択\n- 適切なサイズを選ぶ（`INT` vs `BIGINT`）\n- 文字列は `VARCHAR` > `CHAR`\n- 日時は `DATETIME` or `TIMESTAMP`\n- 金額は `DECIMAL`（`FLOAT`は避ける）\n\n## 3. インデックス\n- 主キーは自動的にインデックス\n- 外部キーにインデックスを作成\n- WHERE句で頻繁に使う列にインデックス\n- 過度なインデックスは避ける（更新が遅くなる）\n\n## 4. クエリの書き方\n- `SELECT *` を避ける\n- `LIMIT` を使う\n- 複雑なサブクエリはCTEで読みやすく\n- `EXPLAIN` で実行計画を確認\n\n## 5. セキュリティ\n- **SQLインジェクション対策**: プリペアドステートメントを使う\n- **最小権限の原則**: 必要な権限のみ付与\n- **パスワードのハッシュ化**: 平文保存は絶対NG\n\n```sql\n-- SQLインジェクション（危険）\nSELECT * FROM users WHERE username = '\$username';\n\n-- プリペアドステートメント（安全）\nPREPARE stmt FROM 'SELECT * FROM users WHERE username = ?';\nEXECUTE stmt USING @username;\n```\n\n## 6. トランザクション\n- 複数の操作は1つのトランザクションに\n- できるだけ短く保つ\n- デッドロックに注意\n\n## 7. バックアップ\n- 定期的なバックアップ\n- リストアのテスト\n- 本番と開発環境を分ける\n\n## 8. パフォーマンス\n- N+1問題を避ける（JOIN を使う）\n- ページネーション実装\n- キャッシュ活用\n- スロークエリログを監視",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "SQL/Database Course Seeder completed successfully!\n";
    }
}
