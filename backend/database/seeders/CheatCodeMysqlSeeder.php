<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeMysqlSeeder extends Seeder
{
    /**
     * Seed MySQL cheat code data from Kizamu
     * Reference: https://Kizamu.com/mysql
     */
    public function run(): void
    {
        // Create MySQL Language
        $mysqlLanguage = CheatCodeLanguage::create([
            'name' => 'mysql',
            'display_name' => 'MySQL',
            'slug' => 'mysql',
            'icon' => 'ic_mysql',
            'color' => '#4479A1',
            'description' => 'MySQLのリファレンス。最も一般的に使用されるSQLステートメントを参照できます。',
            'category' => 'database',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 14,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($mysqlLanguage, 'はじめに', 1, 'MySQLへの接続と基本操作', 'getting-started');

        $this->createExample($section1, $mysqlLanguage, 'MySQLへの接続', 1,
            "mysql -u <user> -p\n\nmysql [db_name]\n\nmysql -h <host> -P <port> -u <user> -p [db_name]\n\nmysql -h <host> -u <user> -p [db_name]",
            'MySQLへの接続方法',
            null,
            'easy'
        );

        $this->createExample($section1, $mysqlLanguage, 'データベース操作', 2,
            "CREATE DATABASE db ;  # Create database\nSHOW DATABASES;        # List databases\nUSE db;                # Switch to db\nCONNECT db ;           # Switch to db\nDROP DATABASE db;      # Delete db",
            'データベースの作成・一覧・切り替え・削除',
            null,
            'easy'
        );

        $this->createExample($section1, $mysqlLanguage, 'テーブル操作', 3,
            "SHOW TABLES;           # List tables for current db\nSHOW FIELDS FROM t;   # List fields for a table\nDESC t;                # Show table structure\nSHOW CREATE TABLE t;   # Show create table sql\nTRUNCATE TABLE t;      # Remove all data in a table\nDROP TABLE t;          # Delete table",
            'テーブルの一覧・構造確認・削除',
            null,
            'easy'
        );

        $this->createExample($section1, $mysqlLanguage, 'プロセス操作', 4,
            "show processlist;  # List processes\nkill pid;          # kill process",
            '実行中のプロセスの確認・終了',
            null,
            'easy'
        );

        $this->createExample($section1, $mysqlLanguage, 'バックアップの作成', 5,
            "# Create a backup\nmysqldump -u user -p db_name > db.sql\n\n# Export db without schema\nmysqldump -u user -p db_name --no-data=true --add-drop-table=false > db.sql\n\n# Restore a backup\nmysql -u user -p db_name < db.sql",
            'データベースのバックアップとリストア',
            null,
            'easy'
        );

        // Section 2: Managing Tables
        $section2 = $this->createSection($mysqlLanguage, 'テーブル管理', 2, 'テーブルの作成・変更・削除', 'managing-tables');

        $this->createExample($section2, $mysqlLanguage, 'テーブルの作成', 1,
            "CREATE TABLE t (\n     id    INT,\n     name  VARCHAR DEFAULT NOT NULL,\n     price INT DEFAULT 0\n     PRIMARY KEY(id)\n);",
            '3つのカラムを持つテーブルの作成',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'テーブルの削除', 2,
            "DROP TABLE t ;",
            'データベースからテーブルを削除',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'カラムの追加', 3,
            "ALTER TABLE t ADD column;",
            'テーブルに新しいカラムを追加',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'カラムの削除', 4,
            "ALTER TABLE t DROP COLUMN c ;",
            'テーブルからカラムを削除',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, '制約の追加', 5,
            "ALTER TABLE t ADD constraint;",
            'テーブルに制約を追加',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, '制約の削除', 6,
            "ALTER TABLE t DROP constraint;",
            'テーブルから制約を削除',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'テーブル名の変更', 7,
            "ALTER TABLE t1 RENAME TO t2;",
            'テーブル名をt1からt2に変更',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'カラム名の変更', 8,
            "ALTER TABLE t1 RENAME c1 TO c2 ;",
            'カラム名をc1からc2に変更',
            null,
            'easy'
        );

        $this->createExample($section2, $mysqlLanguage, 'テーブルデータの全削除', 9,
            "TRUNCATE TABLE t;",
            'テーブル内のすべてのデータを削除',
            null,
            'easy'
        );

        // Section 3: Querying Data
        $section3 = $this->createSection($mysqlLanguage, 'データの取得', 3, 'SELECT文とフィルタリング', 'querying-data');

        $this->createExample($section3, $mysqlLanguage, '基本的なSELECT', 1,
            "SELECT c1, c2 FROM t",
            'カラムc1、c2のデータを取得',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'すべてのカラムを取得', 2,
            "SELECT * FROM t",
            'すべての行とカラムを取得',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'WHERE句でフィルタリング', 3,
            "SELECT c1, c2 FROM t\nWHERE condition",
            '条件に一致する行をフィルタリング',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'DISTINCTで重複を除外', 4,
            "SELECT DISTINCT c1 FROM t\nWHERE condition",
            '重複のない行を取得',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'ORDER BYでソート', 5,
            "SELECT c1, c2 FROM t\nORDER BY c1 ASC [DESC]",
            '結果セットを昇順または降順でソート',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'LIMITとOFFSET', 6,
            "SELECT c1, c2 FROM t\nORDER BY c1 \nLIMIT n OFFSET offset",
            'offset行をスキップして次のn行を返す',
            null,
            'easy'
        );

        $this->createExample($section3, $mysqlLanguage, 'GROUP BYでグループ化', 7,
            "SELECT c1, aggregate(c2)\nFROM t\nGROUP BY c1",
            '集約関数を使用して行をグループ化',
            null,
            'medium'
        );

        $this->createExample($section3, $mysqlLanguage, 'HAVING句でグループをフィルタ', 8,
            "SELECT c1, aggregate(c2)\nFROM t\nGROUP BY c1\nHAVING condition",
            'HAVING句を使用してグループをフィルタリング',
            null,
            'medium'
        );

        // Section 4: Joins
        $section4 = $this->createSection($mysqlLanguage, '結合', 4, 'テーブル間の結合', 'joins');

        $this->createExample($section4, $mysqlLanguage, 'INNER JOIN', 1,
            "SELECT c1, c2 \nFROM t1\nINNER JOIN t2 ON condition",
            't1とt2の内部結合',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'LEFT JOIN', 2,
            "SELECT c1, c2 \nFROM t1\nLEFT JOIN t2 ON condition",
            't1とt2の左外部結合',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'RIGHT JOIN', 3,
            "SELECT c1, c2 \nFROM t1\nRIGHT JOIN t2 ON condition",
            't1とt2の右外部結合',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'FULL OUTER JOIN', 4,
            "SELECT c1, c2 \nFROM t1\nFULL OUTER JOIN t2 ON condition",
            '完全外部結合を実行',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'CROSS JOIN', 5,
            "SELECT c1, c2 \nFROM t1\nCROSS JOIN t2",
            'テーブルの行の直積を生成',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, '自己結合', 6,
            "SELECT c1, c2\nFROM t1 A\nINNER JOIN t1 B ON condition",
            'INNER JOIN句を使用してt1を自分自身に結合',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'UNION', 7,
            "SELECT c1, c2 FROM t1\nUNION [ALL]\nSELECT c1, c2 FROM t2",
            '2つのクエリの行を結合',
            null,
            'medium'
        );

        $this->createExample($section4, $mysqlLanguage, 'LIKEパターンマッチング', 8,
            "SELECT c1, c2 FROM t1\nWHERE c1 [NOT] LIKE pattern",
            'パターンマッチング%, _を使用して行をクエリ',
            null,
            'easy'
        );

        $this->createExample($section4, $mysqlLanguage, 'IN句', 9,
            "SELECT c1, c2 FROM t\nWHERE c1 [NOT] IN value_list",
            'リスト内の行をクエリ',
            null,
            'easy'
        );

        $this->createExample($section4, $mysqlLanguage, 'BETWEEN句', 10,
            "SELECT c1, c2 FROM t\nWHERE  c1 BETWEEN low AND high",
            '2つの値の間の行をクエリ',
            null,
            'easy'
        );

        $this->createExample($section4, $mysqlLanguage, 'NULLチェック', 11,
            "SELECT c1, c2 FROM t\nWHERE  c1 IS [NOT] NULL",
            'テーブル内の値がNULLかどうかをチェック',
            null,
            'easy'
        );

        // Section 5: Constraints
        $section5 = $this->createSection($mysqlLanguage, '制約', 5, 'SQL制約の使用', 'constraints');

        $this->createExample($section5, $mysqlLanguage, '主キー', 1,
            "CREATE TABLE t(\n    c1 INT, c2 INT, c3 VARCHAR,\n    PRIMARY KEY (c1,c2)\n);",
            'c1とc2を主キーに設定',
            null,
            'easy'
        );

        $this->createExample($section5, $mysqlLanguage, '外部キー', 2,
            "CREATE TABLE t1(\n    c1 INT PRIMARY KEY,  \n    c2 INT,\n    FOREIGN KEY (c2) REFERENCES t2(c2)\n);",
            'c2カラムを外部キーに設定',
            null,
            'medium'
        );

        $this->createExample($section5, $mysqlLanguage, 'UNIQUE制約', 3,
            "CREATE TABLE t(\n    c1 INT, c1 INT,\n    UNIQUE(c2,c3)\n);",
            'c1とc2の値を一意にする',
            null,
            'easy'
        );

        $this->createExample($section5, $mysqlLanguage, 'CHECK制約', 4,
            "CREATE TABLE t(\n  c1 INT, c2 INT,\n  CHECK(c1> 0 AND c1 >= c2)\n);",
            'c1 > 0かつc1 >= c2であることを保証',
            null,
            'medium'
        );

        $this->createExample($section5, $mysqlLanguage, 'NOT NULL制約', 5,
            "CREATE TABLE t(\n     c1 INT PRIMARY KEY,\n     c2 VARCHAR NOT NULL\n);",
            'c2カラムの値をNOT NULLに設定',
            null,
            'easy'
        );

        // Section 6: Modifying Data
        $section6 = $this->createSection($mysqlLanguage, 'データの変更', 6, 'INSERT、UPDATE、DELETE', 'modifying-data');

        $this->createExample($section6, $mysqlLanguage, '1行の挿入', 1,
            "INSERT INTO t(column_list)\nVALUES(value_list);",
            'テーブルに1行を挿入',
            null,
            'easy'
        );

        $this->createExample($section6, $mysqlLanguage, '複数行の挿入', 2,
            "INSERT INTO t(column_list)\nVALUES (value_list), \n       (value_list), …;",
            'テーブルに複数行を挿入',
            null,
            'easy'
        );

        $this->createExample($section6, $mysqlLanguage, 'SELECTからINSERT', 3,
            "INSERT INTO t1(column_list)\nSELECT column_list\nFROM t2;",
            't2からt1に行を挿入',
            null,
            'medium'
        );

        $this->createExample($section6, $mysqlLanguage, 'すべての行を更新', 4,
            "UPDATE t\nSET c1 = new_value;",
            'すべての行のカラムc1に新しい値を更新',
            null,
            'easy'
        );

        $this->createExample($section6, $mysqlLanguage, '条件付きUPDATE', 5,
            "UPDATE t\nSET c1 = new_value, \n        c2 = new_value\nWHERE condition;",
            '条件に一致するカラムc1、c2の値を更新',
            null,
            'easy'
        );

        $this->createExample($section6, $mysqlLanguage, 'すべてのデータを削除', 6,
            "DELETE FROM t;",
            'テーブル内のすべてのデータを削除',
            null,
            'easy'
        );

        $this->createExample($section6, $mysqlLanguage, '条件付きDELETE', 7,
            "DELETE FROM t\nWHERE condition;",
            'テーブル内の行のサブセットを削除',
            null,
            'easy'
        );

        // Section 7: Views
        $section7 = $this->createSection($mysqlLanguage, 'ビュー', 7, 'ビューの作成と管理', 'views');

        $this->createExample($section7, $mysqlLanguage, 'ビューの作成', 1,
            "CREATE VIEW v(c1,c2) \nAS\nSELECT c1, c2\nFROM t;",
            'c1とc2で構成される新しいビューを作成',
            null,
            'medium'
        );

        $this->createExample($section7, $mysqlLanguage, 'CHECK OPTION付きビュー', 2,
            "CREATE VIEW v(c1,c2) \nAS\nSELECT c1, c2\nFROM t;\nWITH [CASCADED | LOCAL] CHECK OPTION;",
            'チェックオプション付きの新しいビューを作成',
            null,
            'medium'
        );

        // Section 8: Data Types
        $section8 = $this->createSection($mysqlLanguage, 'データ型', 8, '文字列、日付、数値型', 'data-types');

        $this->createExample($section8, $mysqlLanguage, '文字列型', 1,
            "# CHAR       - String (0 - 255)\n# VARCHAR    - String (0 - 255)\n# TINYTEXT   - String (0 - 255)\n# TEXT       - String (0 - 65535)\n# BLOB       - String (0 - 65535)\n# MEDIUMTEXT - String (0 - 16777215)\n# MEDIUMBLOB - String (0 - 16777215)\n# LONGTEXT   - String (0 - 4294967295)\n# LONGBLOB   - String (0 - 4294967295)\n# ENUM       - One of preset options\n# SET        - Selection of preset options",
            'MySQLの文字列データ型',
            null,
            'easy'
        );

        $this->createExample($section8, $mysqlLanguage, '日付・時刻型', 2,
            "# DATE      - yyyy-MM-dd\n# TIME      - hh:mm:ss\n# DATETIME  - yyyy-MM-dd hh:mm:ss\n# TIMESTAMP - yyyy-MM-dd hh:mm:ss\n# YEAR      - yyyy",
            'MySQLの日付・時刻データ型',
            null,
            'easy'
        );

        $this->createExample($section8, $mysqlLanguage, '数値型', 3,
            "# TINYINT x   - Integer (-128 to 127)\n# SMALLINT x  - Integer (-32768 to 32767)\n# MEDIUMINT x - Integer (-8388608 to 8388607)\n# INT x       - Integer (-2147483648 to 2147483647)\n# BIGINT x    - Integer (-9223372036854775808 to 9223372036854775807)\n# FLOAT       - Decimal (precise to 23 digits)\n# DOUBLE      - Decimal (24 to 53 digits)\n# DECIMAL     - \"DOUBLE\" stored as string",
            'MySQLの数値データ型',
            null,
            'easy'
        );

        // Section 9: Functions
        $section9 = $this->createSection($mysqlLanguage, '関数と演算子', 9, 'MySQL関数の概要', 'functions');

        $this->createExample($section9, $mysqlLanguage, '文字列関数', 1,
            "# CONCAT(), SUBSTRING(), LENGTH(), UPPER(), LOWER()\n# REPLACE(), TRIM(), LEFT(), RIGHT(), MID()\n# CHAR_LENGTH(), LOCATE(), INSTR(), REVERSE()\n# LPAD(), RPAD(), LTRIM(), RTRIM()",
            '主要な文字列関数',
            null,
            'easy'
        );

        $this->createExample($section9, $mysqlLanguage, '日付・時刻関数', 2,
            "# NOW(), CURDATE(), CURTIME(), DATE(), TIME()\n# DATE_FORMAT(), DATE_ADD(), DATE_SUB()\n# DATEDIFF(), TIMEDIFF(), YEAR(), MONTH(), DAY()\n# HOUR(), MINUTE(), SECOND(), WEEK(), QUARTER()",
            '主要な日付・時刻関数',
            null,
            'easy'
        );

        $this->createExample($section9, $mysqlLanguage, '数値関数', 3,
            "# ABS(), CEIL(), FLOOR(), ROUND(), TRUNCATE()\n# MOD(), POW(), SQRT(), RAND()\n# SIN(), COS(), TAN(), LOG(), EXP()",
            '主要な数値関数',
            null,
            'easy'
        );

        $this->createExample($section9, $mysqlLanguage, '集約関数', 4,
            "# AVG(), COUNT(), SUM(), MAX(), MIN()\n# COUNT(DISTINCT), GROUP_CONCAT()\n# STD(), STDDEV(), VARIANCE()",
            '主要な集約関数',
            null,
            'easy'
        );

        $this->createExample($section9, $mysqlLanguage, 'JSON関数', 5,
            "# JSON_ARRAY(), JSON_OBJECT(), JSON_EXTRACT()\n# JSON_SET(), JSON_INSERT(), JSON_REPLACE()\n# JSON_REMOVE(), JSON_CONTAINS(), JSON_KEYS()\n# JSON_LENGTH(), JSON_TYPE(), JSON_VALID()",
            '主要なJSON関数',
            null,
            'medium'
        );

        $this->createExample($section9, $mysqlLanguage, '型変換関数', 6,
            "# CAST(), CONVERT(), BINARY",
            '型変換関数',
            null,
            'medium'
        );

        $this->createExample($section9, $mysqlLanguage, '制御フロー関数', 7,
            "# CASE, IF(), IFNULL(), NULLIF()",
            '制御フロー関数',
            null,
            'medium'
        );

        // Update language counts
        $this->updateLanguageCounts($mysqlLanguage);
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
        if (str_contains($titleLower, 'select') || str_contains($titleLower, 'query') || str_contains($descLower, '取得') || str_contains($descLower, 'クエリ')) {
            $tags[] = 'query';
        }
        if (str_contains($titleLower, 'join') || str_contains($descLower, '結合')) {
            $tags[] = 'join';
        }
        if (str_contains($titleLower, 'insert') || str_contains($descLower, '挿入')) {
            $tags[] = 'insert';
        }
        if (str_contains($titleLower, 'update') || str_contains($descLower, '更新')) {
            $tags[] = 'update';
        }
        if (str_contains($titleLower, 'delete') || str_contains($descLower, '削除')) {
            $tags[] = 'delete';
        }
        if (str_contains($titleLower, 'table') || str_contains($titleLower, 'create') || str_contains($titleLower, 'alter') || str_contains($descLower, 'テーブル')) {
            $tags[] = 'ddl';
        }
        if (str_contains($titleLower, 'constraint') || str_contains($titleLower, 'key') || str_contains($titleLower, '制約')) {
            $tags[] = 'constraint';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, '関数')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'type') || str_contains($titleLower, '型')) {
            $tags[] = 'data-type';
        }
        if (str_contains($titleLower, 'view') || str_contains($descLower, 'ビュー')) {
            $tags[] = 'view';
        }
        if (str_contains($titleLower, 'aggregate') || str_contains($titleLower, 'group') || str_contains($descLower, '集約')) {
            $tags[] = 'aggregate';
        }
        if (str_contains($titleLower, 'json')) {
            $tags[] = 'json';
        }

        // Add basic tags
        $tags[] = 'mysql';
        $tags[] = 'sql';
        $tags[] = 'database';
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

