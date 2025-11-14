<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeMysqlExerciseSeeder extends Seeder
{
    /**
     * Seed MySQL exercises
     * Note: These exercises focus on SQL query writing and syntax
     */
    public function run(): void
    {
        $mysqlLanguage = CheatCodeLanguage::where('slug', 'mysql')->first();

        if (!$mysqlLanguage) {
            $this->command->error('MySQL language not found. Please run CheatCodeMysqlSeeder first.');
            return;
        }

        // Exercise 1: Simple SELECT
        $this->createExercise(
            $mysqlLanguage,
            '全てのカラムを選択',
            'SELECT - 基本',
            'usersテーブルから全てのカラムを取得するクエリを書いてください。',
            "-- usersテーブルから全てのデータを取得\n",
            "SELECT * FROM users;",
            ['SELECT * で全カラム取得', 'FROM テーブル名'],
            'easy',
            10,
            ['mysql', 'select', 'basic'],
            1,
            [
                ['', 'SELECT * FROM users;', '全カラム取得', true, false, 1]
            ]
        );

        // Exercise 2: SELECT Specific Columns
        $this->createExercise(
            $mysqlLanguage,
            '特定のカラムを選択',
            'SELECT - カラム指定',
            'usersテーブルからnameとemailカラムのみを取得するクエリを書いてください。',
            "-- nameとemailを取得\n",
            "SELECT name, email FROM users;",
            ['SELECT カラム名1, カラム名2', 'カンマで複数指定'],
            'easy',
            10,
            ['mysql', 'select', 'columns'],
            2,
            [
                ['', 'SELECT name, email FROM users;', '特定カラム取得', true, false, 1]
            ]
        );

        // Exercise 3: WHERE Clause
        $this->createExercise(
            $mysqlLanguage,
            'WHERE句で条件指定',
            'WHERE - 条件',
            'usersテーブルからageが20より大きいユーザーを取得するクエリを書いてください。',
            "-- age > 20のユーザーを取得\n",
            "SELECT * FROM users WHERE age > 20;",
            ['WHERE 条件式', '> で大小比較'],
            'easy',
            15,
            ['mysql', 'where', 'condition'],
            3,
            [
                ['', 'SELECT * FROM users WHERE age > 20;', '条件指定', true, false, 1]
            ]
        );

        // Exercise 4: WHERE with AND
        $this->createExercise(
            $mysqlLanguage,
            '複数条件（AND）',
            'WHERE - AND',
            'usersテーブルからageが20以上かつcityが「Tokyo」のユーザーを取得するクエリを書いてください。',
            "-- age >= 20 AND city = 'Tokyo'\n",
            "SELECT * FROM users WHERE age >= 20 AND city = 'Tokyo';",
            ['WHERE 条件1 AND 条件2', '両方の条件を満たす'],
            'medium',
            15,
            ['mysql', 'where', 'and'],
            4,
            [
                ['', "SELECT * FROM users WHERE age >= 20 AND city = 'Tokyo';", 'AND条件', true, false, 1]
            ]
        );

        // Exercise 5: WHERE with OR
        $this->createExercise(
            $mysqlLanguage,
            '複数条件（OR）',
            'WHERE - OR',
            'usersテーブルからcityが「Tokyo」または「Osaka」のユーザーを取得するクエリを書いてください。',
            "-- city = 'Tokyo' OR city = 'Osaka'\n",
            "SELECT * FROM users WHERE city = 'Tokyo' OR city = 'Osaka';",
            ['WHERE 条件1 OR 条件2', 'いずれかの条件を満たす'],
            'medium',
            15,
            ['mysql', 'where', 'or'],
            5,
            [
                ['', "SELECT * FROM users WHERE city = 'Tokyo' OR city = 'Osaka';", 'OR条件', true, false, 1]
            ]
        );

        // Exercise 6: ORDER BY ASC
        $this->createExercise(
            $mysqlLanguage,
            '昇順で並べ替え',
            'ORDER BY - ASC',
            'usersテーブルのデータをageカラムで昇順に並べ替えるクエリを書いてください。',
            "-- ageで昇順ソート\n",
            "SELECT * FROM users ORDER BY age ASC;",
            ['ORDER BY カラム名 ASC', 'ASCは昇順（省略可）'],
            'easy',
            15,
            ['mysql', 'order-by', 'asc'],
            6,
            [
                ['', 'SELECT * FROM users ORDER BY age ASC;', '昇順ソート', true, false, 1]
            ]
        );

        // Exercise 7: ORDER BY DESC
        $this->createExercise(
            $mysqlLanguage,
            '降順で並べ替え',
            'ORDER BY - DESC',
            'usersテーブルのデータをcreated_atカラムで降順に並べ替えるクエリを書いてください。',
            "-- created_atで降順ソート\n",
            "SELECT * FROM users ORDER BY created_at DESC;",
            ['ORDER BY カラム名 DESC', 'DESCは降順'],
            'easy',
            15,
            ['mysql', 'order-by', 'desc'],
            7,
            [
                ['', 'SELECT * FROM users ORDER BY created_at DESC;', '降順ソート', true, false, 1]
            ]
        );

        // Exercise 8: LIMIT
        $this->createExercise(
            $mysqlLanguage,
            '取得件数を制限',
            'LIMIT',
            'usersテーブルから最初の10件のみを取得するクエリを書いてください。',
            "-- 最初の10件を取得\n",
            "SELECT * FROM users LIMIT 10;",
            ['LIMIT 件数', '指定件数のみ取得'],
            'easy',
            15,
            ['mysql', 'limit'],
            8,
            [
                ['', 'SELECT * FROM users LIMIT 10;', '件数制限', true, false, 1]
            ]
        );

        // Exercise 9: COUNT
        $this->createExercise(
            $mysqlLanguage,
            'レコード数をカウント',
            '集約関数 - COUNT',
            'usersテーブルのレコード数をカウントするクエリを書いてください。',
            "-- レコード数をカウント\n",
            "SELECT COUNT(*) FROM users;",
            ['COUNT(*)で全レコード数', '集約関数の一つ'],
            'easy',
            15,
            ['mysql', 'aggregate', 'count'],
            9,
            [
                ['', 'SELECT COUNT(*) FROM users;', 'レコード数', true, false, 1]
            ]
        );

        // Exercise 10: SUM
        $this->createExercise(
            $mysqlLanguage,
            '合計を計算',
            '集約関数 - SUM',
            'ordersテーブルのamountカラムの合計を計算するクエリを書いてください。',
            "-- amountの合計\n",
            "SELECT SUM(amount) FROM orders;",
            ['SUM(カラム名)で合計', '数値カラムに使用'],
            'easy',
            15,
            ['mysql', 'aggregate', 'sum'],
            10,
            [
                ['', 'SELECT SUM(amount) FROM orders;', '合計計算', true, false, 1]
            ]
        );

        // Exercise 11: AVG
        $this->createExercise(
            $mysqlLanguage,
            '平均を計算',
            '集約関数 - AVG',
            'usersテーブルのageカラムの平均を計算するクエリを書いてください。',
            "-- ageの平均\n",
            "SELECT AVG(age) FROM users;",
            ['AVG(カラム名)で平均', '数値の平均値を返す'],
            'easy',
            15,
            ['mysql', 'aggregate', 'avg'],
            11,
            [
                ['', 'SELECT AVG(age) FROM users;', '平均計算', true, false, 1]
            ]
        );

        // Exercise 12: MAX
        $this->createExercise(
            $mysqlLanguage,
            '最大値を取得',
            '集約関数 - MAX',
            'productsテーブルのpriceカラムの最大値を取得するクエリを書いてください。',
            "-- priceの最大値\n",
            "SELECT MAX(price) FROM products;",
            ['MAX(カラム名)で最大値'],
            'easy',
            15,
            ['mysql', 'aggregate', 'max'],
            12,
            [
                ['', 'SELECT MAX(price) FROM products;', '最大値取得', true, false, 1]
            ]
        );

        // Exercise 13: MIN
        $this->createExercise(
            $mysqlLanguage,
            '最小値を取得',
            '集約関数 - MIN',
            'productsテーブルのpriceカラムの最小値を取得するクエリを書いてください。',
            "-- priceの最小値\n",
            "SELECT MIN(price) FROM products;",
            ['MIN(カラム名)で最小値'],
            'easy',
            15,
            ['mysql', 'aggregate', 'min'],
            13,
            [
                ['', 'SELECT MIN(price) FROM products;', '最小値取得', true, false, 1]
            ]
        );

        // Exercise 14: GROUP BY
        $this->createExercise(
            $mysqlLanguage,
            'グループ化',
            'GROUP BY',
            'ordersテーブルをuser_idでグループ化し、各ユーザーの注文数をカウントするクエリを書いてください。',
            "-- user_idでグループ化してカウント\n",
            "SELECT user_id, COUNT(*) FROM orders GROUP BY user_id;",
            ['GROUP BY カラム名', '集約関数と組み合わせる'],
            'medium',
            20,
            ['mysql', 'group-by'],
            14,
            [
                ['', 'SELECT user_id, COUNT(*) FROM orders GROUP BY user_id;', 'グループ化', true, false, 1]
            ]
        );

        // Exercise 15: HAVING
        $this->createExercise(
            $mysqlLanguage,
            'グループ化後の条件',
            'HAVING',
            'ordersテーブルをuser_idでグループ化し、注文数が5以上のユーザーのみを取得するクエリを書いてください。',
            "-- 注文数が5以上のユーザー\n",
            "SELECT user_id, COUNT(*) FROM orders GROUP BY user_id HAVING COUNT(*) >= 5;",
            ['HAVING 集約関数の条件', 'GROUP BY後の条件指定'],
            'medium',
            25,
            ['mysql', 'having', 'group-by'],
            15,
            [
                ['', 'SELECT user_id, COUNT(*) FROM orders GROUP BY user_id HAVING COUNT(*) >= 5;', 'HAVING句', true, false, 1]
            ]
        );

        // Exercise 16: DISTINCT
        $this->createExercise(
            $mysqlLanguage,
            '重複を除外',
            'DISTINCT',
            'ordersテーブルからuser_idの重複を除外して取得するクエリを書いてください。',
            "-- 重複しないuser_id\n",
            "SELECT DISTINCT user_id FROM orders;",
            ['SELECT DISTINCT カラム名', '重複行を除外'],
            'easy',
            15,
            ['mysql', 'distinct'],
            16,
            [
                ['', 'SELECT DISTINCT user_id FROM orders;', '重複除外', true, false, 1]
            ]
        );

        // Exercise 17: LIKE Pattern
        $this->createExercise(
            $mysqlLanguage,
            'パターンマッチング',
            'LIKE',
            'usersテーブルからnameが「田中」で始まるユーザーを取得するクエリを書いてください。',
            "-- nameが「田中」で始まる\n",
            "SELECT * FROM users WHERE name LIKE '田中%';",
            ['LIKE \'パターン\'', '%は任意の文字列'],
            'medium',
            20,
            ['mysql', 'like', 'pattern'],
            17,
            [
                ['', "SELECT * FROM users WHERE name LIKE '田中%';", 'パターンマッチ', true, false, 1]
            ]
        );

        // Exercise 18: IN Operator
        $this->createExercise(
            $mysqlLanguage,
            'IN演算子',
            'IN',
            'usersテーブルからcityが「Tokyo」、「Osaka」、「Kyoto」のいずれかのユーザーを取得するクエリを書いてください。',
            "-- cityが3つの値のいずれか\n",
            "SELECT * FROM users WHERE city IN ('Tokyo', 'Osaka', 'Kyoto');",
            ['IN (値1, 値2, 値3)', '複数の値のいずれかに一致'],
            'medium',
            20,
            ['mysql', 'in', 'operator'],
            18,
            [
                ['', "SELECT * FROM users WHERE city IN ('Tokyo', 'Osaka', 'Kyoto');", 'IN演算子', true, false, 1]
            ]
        );

        // Exercise 19: BETWEEN
        $this->createExercise(
            $mysqlLanguage,
            '範囲指定',
            'BETWEEN',
            'usersテーブルからageが20から30の間のユーザーを取得するクエリを書いてください。',
            "-- age 20〜30\n",
            "SELECT * FROM users WHERE age BETWEEN 20 AND 30;",
            ['BETWEEN 値1 AND 値2', '範囲指定（境界値含む）'],
            'medium',
            20,
            ['mysql', 'between', 'range'],
            19,
            [
                ['', 'SELECT * FROM users WHERE age BETWEEN 20 AND 30;', '範囲指定', true, false, 1]
            ]
        );

        // Exercise 20: IS NULL
        $this->createExercise(
            $mysqlLanguage,
            'NULL判定',
            'IS NULL',
            'usersテーブルからemailがNULLのユーザーを取得するクエリを書いてください。',
            "-- emailがNULL\n",
            "SELECT * FROM users WHERE email IS NULL;",
            ['IS NULL でNULL判定', '= NULLは使えない'],
            'medium',
            20,
            ['mysql', 'null', 'is-null'],
            20,
            [
                ['', 'SELECT * FROM users WHERE email IS NULL;', 'NULL判定', true, false, 1]
            ]
        );

        // Exercise 21: IS NOT NULL
        $this->createExercise(
            $mysqlLanguage,
            'NOT NULL判定',
            'IS NOT NULL',
            'usersテーブルからemailがNULLでないユーザーを取得するクエリを書いてください。',
            "-- emailがNULLでない\n",
            "SELECT * FROM users WHERE email IS NOT NULL;",
            ['IS NOT NULL でNULLでない判定'],
            'easy',
            15,
            ['mysql', 'null', 'is-not-null'],
            21,
            [
                ['', 'SELECT * FROM users WHERE email IS NOT NULL;', 'NOT NULL判定', true, false, 1]
            ]
        );

        // Exercise 22: INNER JOIN
        $this->createExercise(
            $mysqlLanguage,
            '内部結合',
            'JOIN - INNER',
            'usersテーブルとordersテーブルをuser_idで内部結合するクエリを書いてください。',
            "-- usersとordersを結合\n",
            "SELECT * FROM users INNER JOIN orders ON users.id = orders.user_id;",
            ['INNER JOIN テーブル名 ON 条件', '両方に存在するデータのみ'],
            'medium',
            25,
            ['mysql', 'join', 'inner'],
            22,
            [
                ['', 'SELECT * FROM users INNER JOIN orders ON users.id = orders.user_id;', '内部結合', true, false, 1]
            ]
        );

        // Exercise 23: LEFT JOIN
        $this->createExercise(
            $mysqlLanguage,
            '左外部結合',
            'JOIN - LEFT',
            'usersテーブルとordersテーブルをuser_idで左外部結合するクエリを書いてください。',
            "-- 左外部結合\n",
            "SELECT * FROM users LEFT JOIN orders ON users.id = orders.user_id;",
            ['LEFT JOIN テーブル名 ON 条件', '左テーブルの全データを保持'],
            'medium',
            25,
            ['mysql', 'join', 'left'],
            23,
            [
                ['', 'SELECT * FROM users LEFT JOIN orders ON users.id = orders.user_id;', '左外部結合', true, false, 1]
            ]
        );

        // Exercise 24: RIGHT JOIN
        $this->createExercise(
            $mysqlLanguage,
            '右外部結合',
            'JOIN - RIGHT',
            'usersテーブルとordersテーブルをuser_idで右外部結合するクエリを書いてください。',
            "-- 右外部結合\n",
            "SELECT * FROM users RIGHT JOIN orders ON users.id = orders.user_id;",
            ['RIGHT JOIN テーブル名 ON 条件', '右テーブルの全データを保持'],
            'medium',
            25,
            ['mysql', 'join', 'right'],
            24,
            [
                ['', 'SELECT * FROM users RIGHT JOIN orders ON users.id = orders.user_id;', '右外部結合', true, false, 1]
            ]
        );

        // Exercise 25: INSERT
        $this->createExercise(
            $mysqlLanguage,
            'データの挿入',
            'INSERT',
            'usersテーブルにname=\'Taro\'、age=25のデータを挿入するクエリを書いてください。',
            "-- データを挿入\n",
            "INSERT INTO users (name, age) VALUES ('Taro', 25);",
            ['INSERT INTO テーブル名 (カラム) VALUES (値)'],
            'easy',
            20,
            ['mysql', 'insert'],
            25,
            [
                ['', "INSERT INTO users (name, age) VALUES ('Taro', 25);", 'データ挿入', true, false, 1]
            ]
        );

        // Exercise 26: INSERT Multiple
        $this->createExercise(
            $mysqlLanguage,
            '複数行の挿入',
            'INSERT - 複数行',
            'usersテーブルに2人のユーザー（name=\'Hanako\', age=30とname=\'Jiro\', age=22）を一度に挿入するクエリを書いてください。',
            "-- 複数行を挿入\n",
            "INSERT INTO users (name, age) VALUES ('Hanako', 30), ('Jiro', 22);",
            ['VALUES (値1), (値2)で複数行挿入'],
            'medium',
            25,
            ['mysql', 'insert', 'multiple'],
            26,
            [
                ['', "INSERT INTO users (name, age) VALUES ('Hanako', 30), ('Jiro', 22);", '複数行挿入', true, false, 1]
            ]
        );

        // Exercise 27: UPDATE
        $this->createExercise(
            $mysqlLanguage,
            'データの更新',
            'UPDATE',
            'usersテーブルでid=1のユーザーのageを30に更新するクエリを書いてください。',
            "-- id=1のageを更新\n",
            "UPDATE users SET age = 30 WHERE id = 1;",
            ['UPDATE テーブル名 SET カラム=値 WHERE 条件', 'WHERE忘れに注意'],
            'medium',
            20,
            ['mysql', 'update'],
            27,
            [
                ['', 'UPDATE users SET age = 30 WHERE id = 1;', 'データ更新', true, false, 1]
            ]
        );

        // Exercise 28: UPDATE Multiple Columns
        $this->createExercise(
            $mysqlLanguage,
            '複数カラムの更新',
            'UPDATE - 複数カラム',
            'usersテーブルでid=2のユーザーのnameを\'Yuki\'、ageを28に更新するクエリを書いてください。',
            "-- 複数カラムを更新\n",
            "UPDATE users SET name = 'Yuki', age = 28 WHERE id = 2;",
            ['SET カラム1=値1, カラム2=値2', 'カンマで複数指定'],
            'medium',
            25,
            ['mysql', 'update', 'multiple'],
            28,
            [
                ['', "UPDATE users SET name = 'Yuki', age = 28 WHERE id = 2;", '複数カラム更新', true, false, 1]
            ]
        );

        // Exercise 29: DELETE
        $this->createExercise(
            $mysqlLanguage,
            'データの削除',
            'DELETE',
            'usersテーブルからid=5のユーザーを削除するクエリを書いてください。',
            "-- id=5を削除\n",
            "DELETE FROM users WHERE id = 5;",
            ['DELETE FROM テーブル名 WHERE 条件', 'WHERE忘れに注意'],
            'medium',
            20,
            ['mysql', 'delete'],
            29,
            [
                ['', 'DELETE FROM users WHERE id = 5;', 'データ削除', true, false, 1]
            ]
        );

        // Exercise 30: CREATE TABLE
        $this->createExercise(
            $mysqlLanguage,
            'テーブルの作成',
            'CREATE TABLE',
            'id（INT、主キー、自動増分）、name（VARCHAR(100)）、age（INT）のカラムを持つusersテーブルを作成するクエリを書いてください。',
            "-- usersテーブルを作成\n",
            "CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100), age INT);",
            ['CREATE TABLE テーブル名 (カラム定義)'],
            'hard',
            30,
            ['mysql', 'create-table', 'ddl'],
            30,
            [
                ['', 'CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100), age INT);', 'テーブル作成', true, false, 1]
            ]
        );

        // Exercise 31: ALTER TABLE ADD
        $this->createExercise(
            $mysqlLanguage,
            'カラムの追加',
            'ALTER TABLE - ADD',
            'usersテーブルにemail（VARCHAR(255)）カラムを追加するクエリを書いてください。',
            "-- emailカラムを追加\n",
            "ALTER TABLE users ADD email VARCHAR(255);",
            ['ALTER TABLE テーブル名 ADD カラム定義'],
            'medium',
            25,
            ['mysql', 'alter-table', 'add'],
            31,
            [
                ['', 'ALTER TABLE users ADD email VARCHAR(255);', 'カラム追加', true, false, 1]
            ]
        );

        // Exercise 32: ALTER TABLE DROP
        $this->createExercise(
            $mysqlLanguage,
            'カラムの削除',
            'ALTER TABLE - DROP',
            'usersテーブルからageカラムを削除するクエリを書いてください。',
            "-- ageカラムを削除\n",
            "ALTER TABLE users DROP COLUMN age;",
            ['ALTER TABLE テーブル名 DROP COLUMN カラム名'],
            'medium',
            25,
            ['mysql', 'alter-table', 'drop'],
            32,
            [
                ['', 'ALTER TABLE users DROP COLUMN age;', 'カラム削除', true, false, 1]
            ]
        );

        // Exercise 33: Subquery
        $this->createExercise(
            $mysqlLanguage,
            'サブクエリ',
            'サブクエリ',
            'usersテーブルから、ordersテーブルで注文があるユーザーのみを取得するクエリをINとサブクエリで書いてください。',
            "-- 注文があるユーザーのみ\n",
            "SELECT * FROM users WHERE id IN (SELECT user_id FROM orders);",
            ['WHERE カラム IN (サブクエリ)', 'サブクエリは括弧で囲む'],
            'hard',
            30,
            ['mysql', 'subquery'],
            33,
            [
                ['', 'SELECT * FROM users WHERE id IN (SELECT user_id FROM orders);', 'サブクエリ', true, false, 1]
            ]
        );

        // Exercise 34: UNION
        $this->createExercise(
            $mysqlLanguage,
            'クエリの結合',
            'UNION',
            '2つのSELECT文（SELECT name FROM users WHERE age > 30とSELECT name FROM users WHERE city = \'Tokyo\'）をUNIONで結合するクエリを書いてください。',
            "-- 2つのクエリを結合\n",
            "SELECT name FROM users WHERE age > 30 UNION SELECT name FROM users WHERE city = 'Tokyo';",
            ['クエリ1 UNION クエリ2', '重複は自動削除'],
            'hard',
            30,
            ['mysql', 'union'],
            34,
            [
                ['', "SELECT name FROM users WHERE age > 30 UNION SELECT name FROM users WHERE city = 'Tokyo';", 'UNION', true, false, 1]
            ]
        );

        // Exercise 35: AS Alias
        $this->createExercise(
            $mysqlLanguage,
            '別名（エイリアス）',
            'AS',
            'usersテーブルのnameカラムを「user_name」という別名で取得するクエリを書いてください。',
            "-- nameに別名をつける\n",
            "SELECT name AS user_name FROM users;",
            ['カラム名 AS 別名', 'ASは省略可能'],
            'easy',
            15,
            ['mysql', 'alias', 'as'],
            35,
            [
                ['', 'SELECT name AS user_name FROM users;', '別名', true, false, 1]
            ]
        );

        // Exercise 36: CONCAT
        $this->createExercise(
            $mysqlLanguage,
            '文字列の結合',
            '文字列関数 - CONCAT',
            'usersテーブルのfirst_nameとlast_nameをスペースで結合してfull_nameとして取得するクエリを書いてください。',
            "-- 名前を結合\n",
            "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users;",
            ['CONCAT(文字列1, 文字列2, ...)', '複数の文字列を結合'],
            'medium',
            25,
            ['mysql', 'function', 'concat'],
            36,
            [
                ['', "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users;", '文字列結合', true, false, 1]
            ]
        );

        // Exercise 37: UPPER/LOWER
        $this->createExercise(
            $mysqlLanguage,
            '大文字小文字変換',
            '文字列関数 - UPPER',
            'usersテーブルのemailカラムを大文字に変換して取得するクエリを書いてください。',
            "-- emailを大文字に\n",
            "SELECT UPPER(email) FROM users;",
            ['UPPER(文字列)で大文字に', 'LOWER()で小文字に'],
            'easy',
            15,
            ['mysql', 'function', 'upper'],
            37,
            [
                ['', 'SELECT UPPER(email) FROM users;', '大文字変換', true, false, 1]
            ]
        );

        // Exercise 38: NOW()
        $this->createExercise(
            $mysqlLanguage,
            '現在日時の取得',
            '日付関数 - NOW',
            '現在の日時を取得するクエリを書いてください。',
            "-- 現在日時を取得\n",
            "SELECT NOW();",
            ['NOW()で現在日時', 'CURDATE()で現在日付のみ'],
            'easy',
            10,
            ['mysql', 'function', 'now', 'date'],
            38,
            [
                ['', 'SELECT NOW();', '現在日時', true, false, 1]
            ]
        );

        // Exercise 39: CASE Statement
        $this->createExercise(
            $mysqlLanguage,
            '条件分岐',
            'CASE',
            'usersテーブルのageが20未満なら「未成年」、20以上なら「成人」と表示するクエリを書いてください。',
            "-- ageで条件分岐\n",
            "SELECT name, CASE WHEN age < 20 THEN '未成年' ELSE '成人' END AS status FROM users;",
            ['CASE WHEN 条件 THEN 値 ELSE 値 END', '条件分岐の表現'],
            'hard',
            30,
            ['mysql', 'case', 'conditional'],
            39,
            [
                ['', "SELECT name, CASE WHEN age < 20 THEN '未成年' ELSE '成人' END AS status FROM users;", 'CASE文', true, false, 1]
            ]
        );

        // Exercise 40: CREATE INDEX
        $this->createExercise(
            $mysqlLanguage,
            'インデックスの作成',
            'INDEX',
            'usersテーブルのemailカラムにidx_emailという名前のインデックスを作成するクエリを書いてください。',
            "-- emailにインデックス作成\n",
            "CREATE INDEX idx_email ON users(email);",
            ['CREATE INDEX インデックス名 ON テーブル(カラム)', '検索速度の向上'],
            'medium',
            25,
            ['mysql', 'index', 'performance'],
            40,
            [
                ['', 'CREATE INDEX idx_email ON users(email);', 'インデックス作成', true, false, 1]
            ]
        );

        $this->command->info('MySQL exercises seeded successfully!');
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
        // Generate unique slug by combining language slug with sort order
        $slug = $language->slug . '-exercise-' . $sortOrder;

        $exercise = Exercise::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $category,
            'question' => $description,
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
