<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeJavaSeeder extends Seeder
{
    /**
     * Seed Java cheat code data from quickref.me
     * Reference: https://quickref.me/java
     */
    public function run(): void
    {
        // Create Java Language
        $javaLanguage = CheatCodeLanguage::create([
            'name' => 'java',
            'display_name' => 'Java',
            'slug' => 'java',
            'icon' => 'ic_java',
            'color' => '#007396',
            'description' => 'Javaは、移植性とクロスプラットフォーム開発のために設計されたクラスベースのオブジェクト指向プログラミング言語です。',
            'category' => 'programming',
            'popularity' => 90,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($javaLanguage, 'はじめに', 1, 'Javaプログラミングの基本', 'getting-started');

        $this->createExample($section1, $javaLanguage, 'Hello.java', 1,
            "public class Hello {\n  public static void main(String[] args) {\n    System.out.println(\"Hello, world!\");\n  }\n}",
            'mainメソッドとコンソール出力を含む基本的なJavaプログラム構造',
            "Hello, world!",
            'easy'
        );

        $this->createExample($section1, $javaLanguage, '変数', 2,
            "int num = 5;\nfloat floatNum = 5.99f;\nchar letter = 'D';\nboolean bool = true;\nString site = \"quickref.me\";",
            'プリミティブ型とStringの宣言と初期化',
            null,
            'easy'
        );

        $this->createExample($section1, $javaLanguage, 'プリミティブデータ型', 3,
            "// Data Type | Size    | Default | Range\n// byte      | 1 byte  | 0       | -128 to 127\n// short     | 2 bytes | 0       | -32,768 to 32,767\n// int       | 4 bytes | 0       | -2^31 to 2^31-1\n// long      | 8 bytes | 0L      | -2^63 to 2^63-1\n// float     | 4 bytes | 0.0f    | N/A\n// double    | 8 bytes | 0.0d    | N/A\n// char      | 2 bytes | '\\u0000'| 0 to 65,535\n// boolean   | N/A     | false   | true / false",
            'サイズ、デフォルト値、範囲を含むプリミティブ型のリファレンステーブル',
            null,
            'easy'
        );

        $this->createExample($section1, $javaLanguage, 'ループ', 4,
            "String word = \"QuickRef\";\nfor (char c: word.toCharArray()) {\n  System.out.print(c + \"-\");\n}",
            '文字配列を反復処理する拡張forループ',
            "Q-u-i-c-k-R-e-f-",
            'easy'
        );

        $this->createExample($section1, $javaLanguage, '配列', 5,
            "char[] chars = new char[10];\nString[] letters = {\"A\", \"B\", \"C\"};\nint[] mylist = {100, 200};",
            '配列の宣言と初期化方法',
            null,
            'easy'
        );

        $this->createExample($section1, $javaLanguage, '型キャスト', 6,
            "int i = 10;\nlong l = i;               // Widening casting\n\ndouble d = 10.02;\nlong l = (long)d;         // Narrowing casting\n\nString.valueOf(10);       // int to String\nInteger.parseInt(\"10\");   // String to int",
            '拡大/縮小変換とStringパース',
            null,
            'easy'
        );

        $this->createExample($section1, $javaLanguage, '条件分岐', 7,
            "int j = 10;\nif (j == 10) {\n  System.out.println(\"I get printed\");\n} else if (j > 10) {\n  System.out.println(\"I don't\");\n} else {\n  System.out.println(\"I also don't\");\n}",
            'If-else-if文の構造',
            "I get printed",
            'easy'
        );

        $this->createExample($section1, $javaLanguage, 'ユーザー入力', 8,
            "import java.util.Scanner;\n\nScanner in = new Scanner(System.in);\nString str = in.nextLine();\nint num = in.nextInt();",
            'Scannerによるユーザー入力の読み取り',
            null,
            'easy'
        );

        // Section 2: Java Strings
        $section2 = $this->createSection($javaLanguage, 'Java文字列', 2, 'Javaでの文字列操作', 'java-strings');

        $this->createExample($section2, $javaLanguage, '連結', 1,
            "String s = 3 + \"str\" + 3;     // 3str3\nString s = \"3\" + 3 + \"str\";   // 33str",
            '文字列と数値の連結例',
            null,
            'easy'
        );

        $this->createExample($section2, $javaLanguage, 'StringBuilder', 2,
            "StringBuilder sb = new StringBuilder(10);\nsb.append(\"Quick\");\nsb.append(\"!\");\n\nSystem.out.println(sb);\n\nsb.insert(5, \"Ref\");   // QuickRef!\nsb.delete(5, 8);       // Quick!",
            'StringBuilderのappend、delete、insert操作',
            "Quick!\nQuickRef!\nQuick!",
            'easy'
        );

        $this->createExample($section2, $javaLanguage, '比較', 3,
            "String s1 = new String(\"QuickRef\");\nString s2 = new String(\"QuickRef\");\n\ns1 == s2;                 // false (different references)\ns1.equals(s2);            // true (same content)\n\"AB\".equalsIgnoreCase(\"ab\");  // true",
            '参照比較と値比較、大文字小文字を区別しないマッチング',
            null,
            'easy'
        );

        $this->createExample($section2, $javaLanguage, '操作', 4,
            "String str = \"Abcd\";\n\nstr.toUpperCase();     // ABCD\nstr.toLowerCase();     // abcd\nstr.concat(\"#\");       // Abcd#\nstr.replace(\"b\", \"-\"); // A-cd\n\"  abc \".trim();      // abc",
            '文字列変換メソッド',
            null,
            'easy'
        );

        $this->createExample($section2, $javaLanguage, '情報取得', 5,
            "String str = \"Abcd\";\n\nstr.charAt(2);        // c\nstr.indexOf(\"a\");     // -1 (not found)\nstr.indexOf(\"A\");     // 0\nstr.length();         // 4\nstr.substring(2);     // cd\nstr.substring(2, 3);  // c\nstr.contains(\"c\");    // true\nstr.endsWith(\"d\");    // true\nstr.startsWith(\"A\"); // true\nstr.isEmpty();        // false",
            '検索機能付きの文字と部分文字列の抽出',
            null,
            'easy'
        );

        // Section 3: Java Arrays
        $section3 = $this->createSection($javaLanguage, 'Java配列', 3, 'Javaでの配列操作', 'java-arrays');

        $this->createExample($section3, $javaLanguage, '宣言', 1,
            "int[] a1;\nint[] a2 = {1, 2, 3};\nint[] a3 = new int[]{1, 2, 3};\nint[] a4 = new int[3];",
            '様々な配列宣言構文',
            null,
            'easy'
        );

        $this->createExample($section3, $javaLanguage, '変更', 2,
            "int[] a = {1, 2, 3};\nSystem.out.println(a[0]);  // 1\n\na[0] = 9;\nSystem.out.println(a[0]);  // 9\nSystem.out.println(a.length);  // 3",
            '要素の代入とlengthプロパティ',
            "1\n9\n3",
            'easy'
        );

        $this->createExample($section3, $javaLanguage, 'ループ（読み取りと変更）', 3,
            "int[] arr = {1, 2, 3};\nfor (int i=0; i < arr.length; i++) {\n    arr[i] = arr[i] * 2;\n    System.out.print(arr[i] + \" \");\n}",
            '要素を変更するためのインデックス付き反復処理',
            "2 4 6 ",
            'easy'
        );

        $this->createExample($section3, $javaLanguage, 'ループ（読み取り専用）', 4,
            "int[] arr = {1, 2, 3};\nfor (int element: arr) {\n    System.out.print(element + \" \");\n}",
            '読み取り専用反復処理用の拡張forループ',
            "1 2 3 ",
            'easy'
        );

        $this->createExample($section3, $javaLanguage, '多次元配列', 5,
            "int[][] matrix = { {1, 2, 3}, {4, 5} };\n\nint x = matrix[1][0];  // 4\n\n// [[1, 2, 3], [4, 5]]\nArrays.deepToString(matrix);",
            '2次元配列へのアクセスとString表現への変換',
            null,
            'easy'
        );

        $this->createExample($section3, $javaLanguage, 'ソート', 6,
            "char[] chars = {'b', 'a', 'c'};\nArrays.sort(chars);\n\n// [a, b, c]\nArrays.toString(chars);",
            '配列のソートと内容の表示',
            null,
            'easy'
        );

        // Section 4: Java Conditionals
        $section4 = $this->createSection($javaLanguage, 'Java条件分岐', 4, 'Javaでの条件文', 'java-conditionals');

        $this->createExample($section4, $javaLanguage, 'If文', 1,
            "int j = 10;\nif (j == 10) {\n  System.out.println(\"I get printed\");\n}",
            '基本的なif文',
            "I get printed",
            'easy'
        );

        $this->createExample($section4, $javaLanguage, 'If-Else文', 2,
            "if (j == 10) {\n  System.out.println(\"I get printed\");\n} else {\n  System.out.println(\"I don't\");\n}",
            'If-else分岐',
            null,
            'easy'
        );

        $this->createExample($section4, $javaLanguage, 'Switch文', 3,
            "int month = 3;\nString str;\n\nswitch (month) {\n  case 1:\n    str = \"January\";\n    break;\n  case 2:\n    str = \"February\";\n    break;\n  case 3:\n    str = \"March\";\n    break;\n  default:\n    str = \"Some other month\";\n    break;\n}",
            '離散値に基づく多方向分岐',
            null,
            'easy'
        );

        $this->createExample($section4, $javaLanguage, '三項演算子', 4,
            "int a = 10, b = 20;\nint max = (a > b) ? a : b;\n\nSystem.out.println(max);  // 20",
            '簡単な選択のためのコンパクトな条件式',
            "20",
            'easy'
        );

        // Section 5: Java Loops
        $section5 = $this->createSection($javaLanguage, 'Javaループ', 5, 'Javaでのループ構造', 'java-loops');

        $this->createExample($section5, $javaLanguage, 'Forループ', 1,
            "for (int i = 0; i < 10; i++) {\n  System.out.print(i);\n}\n// 0123456789",
            '標準的なインデックス付き反復処理',
            "0123456789",
            'easy'
        );

        $this->createExample($section5, $javaLanguage, '拡張Forループ', 2,
            "int[] numbers = {1, 2, 3};\nfor (int number: numbers) {\n  System.out.println(number);\n}",
            'コレクションと配列の簡略化された反復処理',
            "1\n2\n3",
            'easy'
        );

        $this->createExample($section5, $javaLanguage, 'Whileループ', 3,
            "int count = 0;\nwhile (count < 5) {\n  System.out.print(count);\n  count++;\n}\n// 01234",
            '事前テスト付きの条件付き繰り返し',
            "01234",
            'easy'
        );

        $this->createExample($section5, $javaLanguage, 'Do-Whileループ', 4,
            "int count = 0;\ndo {\n  System.out.print(count);\n  count++;\n} while (count < 5);\n// 01234",
            '少なくとも1回の実行を保証する事後テストループ',
            "01234",
            'easy'
        );

        $this->createExample($section5, $javaLanguage, 'Continue文', 5,
            "for (int i = 0; i < 5; i++) {\n  if (i == 3) {\n    continue;  // Skip rest when i is 3\n  }\n  System.out.print(i);\n}\n// 0124",
            '現在の反復をスキップして次に続く',
            "0124",
            'easy'
        );

        $this->createExample($section5, $javaLanguage, 'Break文', 6,
            "for (int i = 0; i < 5; i++) {\n  System.out.print(i);\n  if (i == 3) {\n    break;  // Exit loop when i is 3\n  }\n}\n// 0123",
            '条件が満たされたときにループを完全に終了',
            "0123",
            'easy'
        );

        // Section 6: Java Collections Framework
        $section6 = $this->createSection($javaLanguage, 'Javaコレクション', 6, 'Javaでのコレクションフレームワーク', 'java-collections');

        $this->createExample($section6, $javaLanguage, 'ArrayList', 1,
            "import java.util.ArrayList;\nimport java.util.List;\n\nList<Integer> nums = new ArrayList<>();\n\nnums.add(2);\nnums.add(5);\nnums.add(8);\n\nSystem.out.println(nums.get(0));  // 2\nnums.remove(0);                   // Remove first element\nSystem.out.println(nums.get(0));  // 5",
            '動的配列操作と反復処理',
            "2\n5",
            'easy'
        );

        $this->createExample($section6, $javaLanguage, 'ArrayList反復処理', 2,
            "import java.util.ArrayList;\n\nArrayList<String> cars = new ArrayList<>();\ncars.add(\"Volvo\");\ncars.add(\"BMW\");\n\nfor (String car : cars) {\n  System.out.println(car);\n}",
            '拡張forループによるArrayListの反復処理',
            "Volvo\nBMW",
            'easy'
        );

        $this->createExample($section6, $javaLanguage, 'HashMap', 3,
            "import java.util.HashMap;\nimport java.util.Map;\n\nMap<Integer, String> m = new HashMap<>();\n\nm.put(5, \"Five\");\nm.put(8, \"Eight\");\nm.put(6, \"Six\");\n\nSystem.out.println(m.get(6));   // Six\nSystem.out.println(m.get(3));   // null",
            'getとput操作によるキー・値ストレージ',
            "Six\nnull",
            'easy'
        );

        $this->createExample($section6, $javaLanguage, 'HashMap反復処理', 4,
            "Map<Integer, String> m = new HashMap<>();\nm.put(1, \"One\");\nm.put(2, \"Two\");\n\nm.forEach((key, value) -> {\n  String msg = key + \" = \" + value;\n  System.out.println(msg);\n});",
            'ラムダ式によるHashMapの反復処理',
            "1 = One\n2 = Two",
            'medium'
        );

        $this->createExample($section6, $javaLanguage, 'HashSet', 5,
            "import java.util.HashSet;\nimport java.util.Set;\n\nSet<String> set = new HashSet<>();\n\nset.add(\"dog\");\nset.add(\"cat\");\nset.add(\"dog\");  // duplicate, won't be added\n\nset.contains(\"cat\");  // true\nset.remove(\"cat\");\nset.contains(\"cat\");  // false",
            'メンバーシップテスト付きの一意要素コレクション',
            null,
            'easy'
        );

        $this->createExample($section6, $javaLanguage, 'ArrayDeque', 6,
            "import java.util.ArrayDeque;\nimport java.util.Deque;\n\nDeque<String> a = new ArrayDeque<>();\n\na.add(\"Dog\");\na.addFirst(\"Cat\");\na.addLast(\"Horse\");\n\nSystem.out.println(a.peek());     // Cat\nSystem.out.println(a.poll());     // Cat\nSystem.out.println(a.peek());     // Dog",
            '両端キュー操作（スタック/キュー）',
            "Cat\nCat\nDog",
            'medium'
        );

        // Section 7: Miscellaneous
        $section7 = $this->createSection($javaLanguage, 'その他', 7, 'エラーハンドリングとその他のJava機能', 'miscellaneous');

        $this->createExample($section7, $javaLanguage, 'Try-Catch-Finally', 1,
            "try {\n  int[] a = new int[5];\n  a[10] = 50;  // ArrayIndexOutOfBoundsException\n} catch (Exception e) {\n  System.out.println(\"Error: \" + e.getMessage());\n  e.printStackTrace();\n} finally {\n  System.out.println(\"Always printed!\");\n}",
            '確実なクリーンアップ付きの例外処理',
            null,
            'medium'
        );

        $this->createExample($section7, $javaLanguage, '正規表現', 2,
            "String text = \"I am learning Java\";\n\n// Remove whitespace\ntext.replaceAll(\"\\\\s+\", \"\");  // IamlearningJava\n\n// Split by space\ntext.split(\"\\\\s\");  // [\"I\", \"am\", \"learning\", \"Java\"]",
            '空白削除とString分割のための正規表現',
            null,
            'medium'
        );

        $this->createExample($section7, $javaLanguage, 'Mathメソッド', 3,
            "Math.max(5, 10);        // 10\nMath.min(5, 10);        // 5\nMath.abs(-4.7);         // 4.7\nMath.sqrt(64);          // 8.0\nMath.pow(2, 3);         // 8.0\nMath.round(4.6);        // 5\nMath.ceil(4.3);         // 5.0\nMath.floor(4.7);        // 4.0",
            '一般的な数学演算',
            null,
            'easy'
        );

        $this->createExample($section7, $javaLanguage, 'ラムダ式', 4,
            "import java.util.ArrayList;\n\nArrayList<Integer> numbers = new ArrayList<>();\nnumbers.add(5);\nnumbers.add(9);\nnumbers.add(8);\n\nnumbers.forEach((n) -> {\n  System.out.println(n);\n});",
            'forEach付きラムダ式（Java 8+）',
            "5\n9\n8",
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($javaLanguage);
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

        // Add tags based on title
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'object')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'loop') || str_contains($titleLower, 'iteration')) {
            $tags[] = 'loop';
        }
        if (str_contains($titleLower, 'collection') || str_contains($titleLower, 'list') || str_contains($titleLower, 'map') || str_contains($titleLower, 'set')) {
            $tags[] = 'collections';
        }
        if (str_contains($titleLower, 'exception') || str_contains($titleLower, 'error')) {
            $tags[] = 'error-handling';
        }
        if (str_contains($titleLower, 'lambda')) {
            $tags[] = 'functional';
            $tags[] = 'java8';
        }

        // Add basic tags
        $tags[] = 'java';
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
