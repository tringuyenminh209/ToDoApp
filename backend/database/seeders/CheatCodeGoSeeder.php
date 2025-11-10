<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeGoSeeder extends Seeder
{
    /**
     * Seed Go cheat code data from doleaf
     * Reference: https://doleaf.com/go
     */
    public function run(): void
    {
        // Create Go Language
        $goLanguage = CheatCodeLanguage::create([
            'name' => 'go',
            'display_name' => 'Go',
            'slug' => 'go',
            'icon' => 'ic_go',
            'color' => '#00ADD8',
            'description' => 'Goは、シンプルで信頼性が高く効率的なソフトウェアを簡単に構築できるオープンソースのプログラミング言語です。',
            'category' => 'programming',
            'popularity' => 80,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($goLanguage, 'はじめに', 1, 'Goプログラミングの基本', 'getting-started');

        $this->createExample($section1, $goLanguage, 'hello.go', 1,
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n    fmt.Println(\"Hello, world!\")\n}",
            '基本的なHello Worldプログラム',
            "Hello, world!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, '直接実行', 2,
            "$ go run hello.go\nHello, world!",
            'Goプログラムの実行',
            "Hello, world!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, '変数', 3,
            "var s1 string\ns1 = \"Learn Go!\"\n\n// declare multiple variables at once\nvar b, c int = 1, 2\nvar d = true",
            '変数の宣言',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, '短縮宣言', 4,
            "s1 := \"Learn Go!\"        // string\nb, c := 1, 2             // int\nd := true                // bool",
            '短縮変数宣言',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, '関数', 5,
            "package main\n\nimport \"fmt\"\n\n// The entry point of the programs\nfunc main() {\n    fmt.Println(\"Hello world!\")\n    say(\"Hello Go!\")\n}\n\nfunc say(message string) {\n    fmt.Println(\"You said: \", message)\n}",
            '関数の定義',
            "Hello world!\nYou said: Hello Go!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'コメント', 6,
            "// Single line comment\n\n/* Multi-\n line comment */",
            'コメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'If文', 7,
            "if true {\n    fmt.Println(\"Yes!\")\n}",
            'If文の構文',
            "Yes!",
            'easy'
        );

        // Section 2: Go Basic types
        $section2 = $this->createSection($goLanguage, 'Go基本型', 2, 'Goのデータ型', 'go-basic-types');

        $this->createExample($section2, $goLanguage, '文字列', 1,
            "s1 := \"Hello\" + \"World\"\n\ns2 := `A \"raw\" string literal\ncan include line breaks.`\n\n// Outputs: 10\nfmt.Println(len(s1))\n\n// Outputs: Hello\nfmt.Println(string(s1[0:5]))",
            '文字列操作と連結',
            "10\nHello",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '数値', 2,
            "num := 3         // int\nnum := 3.        // float64\nnum := 3 + 4i    // complex128\nnum := byte('a') // byte (alias: uint8)\n\nvar u uint = 7        // uint (unsigned)\nvar p float32 = 22.7  // 32-bit float",
            '数値型',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, '演算子', 3,
            "x := 5\nx++\nfmt.Println(\"x + 4 =\", x + 4)\nfmt.Println(\"x * 4 =\", x * 4)",
            '算術演算子',
            "x + 4 = 6\nx * 4 = 24",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'ブール値', 4,
            "isTrue   := true\nisFalse  := false",
            'ブール型',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'ブール演算子', 5,
            "fmt.Println(true && true)   // true\nfmt.Println(true && false)  // false\nfmt.Println(true || true)   // true\nfmt.Println(true || false)  // true\nfmt.Println(!true)          // false",
            'ブール演算子',
            "true\nfalse\ntrue\ntrue\nfalse",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '配列', 6,
            "primes := [...]int{2, 3, 5, 7, 11, 13}\nfmt.Println(len(primes)) // => 6\n\n// Outputs: [2 3 5 7 11 13]\nfmt.Println(primes)\n\n// Same as [:3], Outputs: [2 3 5]\nfmt.Println(primes[0:3])",
            '配列の宣言とスライス',
            "6\n[2 3 5 7 11 13]\n[2 3 5]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '配列の宣言', 7,
            "var a [2]string\na[0] = \"Hello\"\na[1] = \"World\"\n\nfmt.Println(a[0], a[1]) //=> Hello World\nfmt.Println(a)   // => [Hello World]",
            '明示的なサイズの配列',
            "Hello World\n[Hello World]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '2次元配列', 8,
            "var twoDimension [2][3]int\nfor i := 0; i < 2; i++ {\n    for j := 0; j < 3; j++ {\n        twoDimension[i][j] = i + j\n    }\n}\n// => 2d:  [[0 1 2] [1 2 3]]\nfmt.Println(\"2d: \", twoDimension)",
            '多次元配列',
            "2d:  [[0 1 2] [1 2 3]]",
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'ポインタ', 9,
            "func main () {\n  b := *getPointer()\n  fmt.Println(\"Value is\", b)\n}\n\nfunc getPointer () (myPointer *int) {\n  a := 234\n  return &a\n}",
            'ポインタの基本',
            "Value is 234",
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'newによるポインタ', 10,
            "a := new(int)\n*a = 234",
            'newによるポインタの作成',
            null,
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'スライス', 11,
            "s := make([]string, 3)\ns[0] = \"a\"\ns[1] = \"b\"\ns = append(s, \"d\")\ns = append(s, \"e\", \"f\")\n\nfmt.Println(s)\nfmt.Println(s[1])\nfmt.Println(len(s))\nfmt.Println(s[1:3])",
            'スライス操作',
            "[a b  d e f]\nb\n5\n[b ]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'スライスリテラル', 12,
            "slice := []int{2, 3, 4}",
            'スライスリテラル構文',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, '定数', 13,
            "const s string = \"constant\"\nconst Phi = 1.618\nconst n = 500000000\nconst d = 3e20 / n\nfmt.Println(d)",
            '定数の宣言',
            "6e+11",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '型変換', 14,
            "i := 90\nf := float64(i)\nu := uint(i)\n\n// Will be equal to the character Z\ns := string(i)",
            '型変換',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Intから文字列へ', 15,
            "i := 90\n\n// need import \"strconv\"\ns := strconv.Itoa(i)\nfmt.Println(s) // Outputs: 90",
            'intから文字列への変換',
            "90",
            'easy'
        );

        // Section 3: Go Strings
        $section3 = $this->createSection($goLanguage, 'Go文字列', 3, 'Goでの文字列操作', 'go-strings');

        $this->createExample($section3, $goLanguage, '文字列関数', 1,
            "package main\n\nimport (\n\t\"fmt\"\n\ts \"strings\"\n)\n\nfunc main() {\n    /* Need to import strings as s */\n\tfmt.Println(s.Contains(\"test\", \"e\"))\n\n    /* Build in */\n    fmt.Println(len(\"hello\"))  // => 5\n    // Outputs: 101\n\tfmt.Println(\"hello\"[1])\n    // Outputs: e\n\tfmt.Println(string(\"hello\"[1]))\n}",
            '文字列関数',
            "true\n5\n101\ne",
            'easy'
        );

        $this->createExample($section3, $goLanguage, 'fmt.Printf', 2,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"os\"\n)\n\ntype point struct {\n\tx, y int\n}\n\nfunc main() {\n\tp := point{1, 2}\n\tfmt.Printf(\"%v\\n\", p)                        // => {1 2}\n\tfmt.Printf(\"%+v\\n\", p)                       // => {x:1 y:2}\n\tfmt.Printf(\"%#v\\n\", p)                       // => main.point{x:1, y:2}\n\tfmt.Printf(\"%T\\n\", p)                        // => main.point\n\tfmt.Printf(\"%t\\n\", true)                     // => TRUE\n\tfmt.Printf(\"%d\\n\", 123)                      // => 123\n\tfmt.Printf(\"%b\\n\", 14)                       // => 1110\n\tfmt.Printf(\"%c\\n\", 33)                       // => !\n\tfmt.Printf(\"%x\\n\", 456)                      // => 1c8\n\tfmt.Printf(\"%f\\n\", 78.9)                     // => 78.9\n\tfmt.Printf(\"%e\\n\", 123400000.0)              // => 1.23E+08\n\tfmt.Printf(\"%E\\n\", 123400000.0)              // => 1.23E+08\n\tfmt.Printf(\"%s\\n\", \"\\\"string\\\"\")             // => \"string\"\n\tfmt.Printf(\"%q\\n\", \"\\\"string\\\"\")             // => \"\\\"string\\\"\"\n\tfmt.Printf(\"%x\\n\", \"hex this\")               // => 6.86578E+15\n\tfmt.Printf(\"%p\\n\", &p)                       // => 0xc00002c040\n\tfmt.Printf(\"|%6d|%6d|\\n\", 12, 345)           // => |    12|   345|\n\tfmt.Printf(\"|%6.2f|%6.2f|\\n\", 1.2, 3.45)     // => |  1.20|  3.45|\n\tfmt.Printf(\"|%-6.2f|%-6.2f|\\n\", 1.2, 3.45)   // => |1.20  |3.45  |\n\tfmt.Printf(\"|%6s|%6s|\\n\", \"foo\", \"b\")        // => |   foo|     b|\n\tfmt.Printf(\"|%-6s|%-6s|\\n\", \"foo\", \"b\")      // => |foo   |b     |\n\n\ts := fmt.Sprintf(\"a %s\", \"string\")\n\tfmt.Println(s)\n\n\tfmt.Fprintf(os.Stderr, \"an %s\\n\", \"error\")\n}",
            'fmt.Printfによるフォーマット出力',
            "{1 2}\n{x:1 y:2}\nmain.point{x:1, y:2}\nmain.point\ntrue\n123\n1110\n!\n1c8\n78.9\n1.23E+08\n1.23E+08\n\"string\"\n\"\\\"string\\\"\"\n6865782074686973\n0xc00002c040\n|    12|   345|\n|  1.20|  3.45|\n|1.20  |3.45  |\n|   foo|     b|\n|foo   |b     |\na string\nan error",
            'medium'
        );

        // Section 4: Go Functions
        $section4 = $this->createSection($goLanguage, 'Go関数', 4, '関数の定義と使用', 'go-functions');

        $this->createExample($section4, $goLanguage, '複数の戻り値', 1,
            "func swap(x, y string) (string, string) {\n    return y, x\n}\n\na, b := swap(\"hello\", \"world\")\nfmt.Println(a, b)",
            '複数の戻り値を持つ関数',
            "world hello",
            'easy'
        );

        $this->createExample($section4, $goLanguage, '名前付き戻り値', 2,
            "func split(sum int) (x, y int) {\n    x = sum * 4 / 9\n    y = sum - x\n    return\n}",
            '名前付き戻り値',
            null,
            'medium'
        );

        $this->createExample($section4, $goLanguage, '可変長引数関数', 3,
            "func sum(nums ...int) int {\n    total := 0\n    for _, num := range nums {\n        total += num\n    }\n    return total\n}\n\nfmt.Println(sum(1, 2, 3))",
            '可変長引数関数',
            "6",
            'medium'
        );

        $this->createExample($section4, $goLanguage, '関数を値として', 4,
            "func main() {\n    // assign a function to a name\n    add := func(a, b int) int {\n        return a + b\n    }\n    // use the name to call the function\n    fmt.Println(add(3, 4)) // => 7\n}",
            '第一級関数としての関数',
            "7",
            'medium'
        );

        $this->createExample($section4, $goLanguage, 'クロージャ', 5,
            "func scope() func() int{\n    outer_var := 2\n    foo := func() int {return outer_var}\n    return foo\n}\n\n// Outputs: 2\nfmt.Println(scope()())",
            'クロージャの例',
            "2",
            'medium'
        );

        // Section 5: Go Packages
        $section5 = $this->createSection($goLanguage, 'Goパッケージ', 5, 'パッケージ管理', 'go-packages');

        $this->createExample($section5, $goLanguage, 'インポート', 1,
            "import \"fmt\"\nimport \"math/rand\"",
            '基本的なインポート',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, 'インポートブロック', 2,
            "import (\n  \"fmt\"        // gives fmt.Println\n  \"math/rand\"  // gives rand.Intn\n)",
            'インポートブロック構文',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, 'エイリアス', 3,
            "import r \"math/rand\"\n\nr.Intn()",
            'インポートエイリアス',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, '名前のエクスポート', 4,
            "// Begin with a capital letter\nfunc Hello () {\n  ···\n}",
            'エクスポートされた名前',
            null,
            'easy'
        );

        // Section 6: Go Concurrency
        $section6 = $this->createSection($goLanguage, 'Go並行処理', 6, 'Goroutineとチャネル', 'go-concurrency');

        $this->createExample($section6, $goLanguage, 'Goroutine', 1,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"time\"\n)\n\nfunc f(from string) {\n\tfor i := 0; i < 3; i++ {\n\t\tfmt.Println(from, \":\", i)\n\t}\n}\n\nfunc main() {\n\tf(\"direct\")\n\tgo f(\"goroutine\")\n\n\tgo func(msg string) {\n\t\tfmt.Println(msg)\n\t}(\"going\")\n\n\ttime.Sleep(time.Second)\n\tfmt.Println(\"done\")\n}",
            'Goroutineの基本',
            "direct : 0\ndirect : 1\ndirect : 2\ngoing\ngoroutine : 0\ngoroutine : 1\ngoroutine : 2\ndone",
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'WaitGroup', 2,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"sync\"\n\t\"time\"\n)\n\nfunc w(id int, wg *sync.WaitGroup) {\n\tdefer wg.Done()\n\tfmt.Printf(\"%d starting\\n\", id)\n\n\ttime.Sleep(time.Second)\n\tfmt.Printf(\"%d done\\n\", id)\n}\n\nfunc main() {\n\tvar wg sync.WaitGroup\n\tfor i := 1; i <= 5; i++ {\n\t\twg.Add(1)\n\t\tgo w(i, &wg)\n\t}\n\twg.Wait()\n}",
            '同期のためのWaitGroup',
            "1 starting\n2 starting\n3 starting\n4 starting\n5 starting\n1 done\n2 done\n3 done\n4 done\n5 done",
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'チャネルのクローズ', 3,
            "ch <- 1\nch <- 2\nch <- 3\nclose(ch) // Closes a channel\n\n// Iterate the channel until closed\nfor i := range ch {\n  ···\n}\n\n// Closed if `ok == false`\nv, ok := <- ch",
            'チャネルのクローズとイテレーション',
            null,
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'バッファ付きチャネル', 4,
            "ch := make(chan int, 2)\nch <- 1\nch <- 2\nch <- 3\n// fatal error:\n// all goroutines are asleep - deadlock",
            'バッファ付きチャネル',
            null,
            'medium'
        );

        // Section 7: Go Error control
        $section7 = $this->createSection($goLanguage, 'Goエラー制御', 7, 'エラーハンドリング', 'go-error-control');

        $this->createExample($section7, $goLanguage, '関数の遅延実行', 1,
            "func main() {\n  defer func() {\n    fmt.Println(\"Done\")\n  }()\n  fmt.Println(\"Working...\")\n}",
            'Defer文',
            "Working...\nDone",
            'easy'
        );

        $this->createExample($section7, $goLanguage, 'Defer', 2,
            "func main() {\n  defer fmt.Println(\"Done\")\n  fmt.Println(\"Working...\")\n}",
            'シンプルなdefer',
            "Working...\nDone",
            'easy'
        );

        // Section 8: Go Methods
        $section8 = $this->createSection($goLanguage, 'Goメソッド', 8, 'メソッドとレシーバ', 'go-methods');

        $this->createExample($section8, $goLanguage, 'レシーバ', 1,
            "type Vertex struct {\n  X, Y float64\n}\n\nfunc (v Vertex) Abs() float64 {\n  return math.Sqrt(v.X * v.X + v.Y * v.Y)\n}\n\nv := Vertex{1, 2}\nv.Abs()",
            '値レシーバを持つメソッド',
            null,
            'medium'
        );

        $this->createExample($section8, $goLanguage, '変更', 2,
            "func (v *Vertex) Scale(f float64) {\n  v.X = v.X * f\n  v.Y = v.Y * f\n}\n\nv := Vertex{6, 12}\nv.Scale(0.5)\n// `v` is updated",
            'ポインタレシーバを持つメソッド',
            null,
            'medium'
        );

        // Section 9: Go Interfaces
        $section9 = $this->createSection($goLanguage, 'Goインターフェース', 9, 'インターフェースの実装', 'go-interfaces');

        $this->createExample($section9, $goLanguage, '基本的なインターフェース', 1,
            "type Shape interface {\n  Area() float64\n  Perimeter() float64\n}",
            'インターフェースの定義',
            null,
            'medium'
        );

        $this->createExample($section9, $goLanguage, '構造体', 2,
            "type Rectangle struct {\n  Length, Width float64\n}",
            '構造体の定義',
            null,
            'easy'
        );

        $this->createExample($section9, $goLanguage, 'メソッド', 3,
            "func (r Rectangle) Area() float64 {\n  return r.Length * r.Width\n}\n\nfunc (r Rectangle) Perimeter() float64 {\n  return 2 * (r.Length + r.Width)\n}",
            'インターフェースメソッドの実装',
            null,
            'medium'
        );

        $this->createExample($section9, $goLanguage, 'インターフェースの例', 4,
            "func main() {\n  var r Shape = Rectangle{Length: 3, Width: 4}\n  fmt.Printf(\"Type of r: %T, Area: %v, Perimeter: %v.\", r, r.Area(), r.Perimeter())\n}",
            'インターフェースの使用',
            "Type of r: main.Rectangle, Area: 12, Perimeter: 14.",
            'medium'
        );

        // Section 10: Miscellaneous
        $section10 = $this->createSection($goLanguage, 'その他', 10, 'キーワードと演算子', 'miscellaneous');

        $this->createExample($section10, $goLanguage, 'キーワード', 1,
            "break default func interface select\ncase defer go map struct\nchan else goto package switch\nconst fallthrough if range type\ncontinue for import return var",
            'Goキーワード',
            null,
            'easy'
        );

        // Update counts
        $this->updateLanguageCounts($goLanguage);
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
        if (str_contains($titleLower, 'struct') || str_contains($titleLower, 'interface') || str_contains($titleLower, 'method')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array') || str_contains($titleLower, 'slice')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'func')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'goroutine') || str_contains($titleLower, 'channel') || str_contains($titleLower, 'concurrency')) {
            $tags[] = 'concurrency';
        }
        if (str_contains($titleLower, 'error') || str_contains($titleLower, 'defer')) {
            $tags[] = 'error-handling';
        }
        if (str_contains($titleLower, 'pointer')) {
            $tags[] = 'pointer';
        }

        // Add basic tag
        $tags[] = 'go';
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

