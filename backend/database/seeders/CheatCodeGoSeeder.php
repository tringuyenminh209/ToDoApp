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
     * Seed Go cheat code data from quickref.me
     * Reference: https://quickref.me/go
     */
    public function run(): void
    {
        // Create Go Language
        $goLanguage = CheatCodeLanguage::create([
            'name' => 'go',
            'display_name' => 'Go',
            'slug' => 'go',
            'color' => '#00ADD8',
            'description' => 'Go is an open source programming language that makes it easy to build simple, reliable, and efficient software.',
            'category' => 'programming',
            'popularity' => 80,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($goLanguage, 'Getting Started', 1, 'Basics of Go programming');

        $this->createExample($section1, $goLanguage, 'hello.go', 1,
            "package main\n\nimport \"fmt\"\n\nfunc main() {\n    fmt.Println(\"Hello, world!\")\n}",
            'Basic Hello World program',
            "Hello, world!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'Run directly', 2,
            "$ go run hello.go\nHello, world!",
            'Running Go programs',
            "Hello, world!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'Variables', 3,
            "var s1 string\ns1 = \"Learn Go!\"\n\n// declare multiple variables at once\nvar b, c int = 1, 2\nvar d = true",
            'Variable declarations',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'Short declaration', 4,
            "s1 := \"Learn Go!\"        // string\nb, c := 1, 2             // int\nd := true                // bool",
            'Short variable declaration',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'Functions', 5,
            "package main\n\nimport \"fmt\"\n\n// The entry point of the programs\nfunc main() {\n    fmt.Println(\"Hello world!\")\n    say(\"Hello Go!\")\n}\n\nfunc say(message string) {\n    fmt.Println(\"You said: \", message)\n}",
            'Function definitions',
            "Hello world!\nYou said: Hello Go!",
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'Comments', 6,
            "// Single line comment\n\n/* Multi-\n line comment */",
            'Comment syntax',
            null,
            'easy'
        );

        $this->createExample($section1, $goLanguage, 'If statement', 7,
            "if true {\n    fmt.Println(\"Yes!\")\n}",
            'If statement syntax',
            "Yes!",
            'easy'
        );

        // Section 2: Go Basic types
        $section2 = $this->createSection($goLanguage, 'Go Basic types', 2, 'Go data types');

        $this->createExample($section2, $goLanguage, 'Strings', 1,
            "s1 := \"Hello\" + \"World\"\n\ns2 := `A \"raw\" string literal\ncan include line breaks.`\n\n// Outputs: 10\nfmt.Println(len(s1))\n\n// Outputs: Hello\nfmt.Println(string(s1[0:5]))",
            'String operations and concatenation',
            "10\nHello",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Numbers', 2,
            "num := 3         // int\nnum := 3.        // float64\nnum := 3 + 4i    // complex128\nnum := byte('a') // byte (alias: uint8)\n\nvar u uint = 7        // uint (unsigned)\nvar p float32 = 22.7  // 32-bit float",
            'Numeric types',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Operators', 3,
            "x := 5\nx++\nfmt.Println(\"x + 4 =\", x + 4)\nfmt.Println(\"x * 4 =\", x * 4)",
            'Arithmetic operators',
            "x + 4 = 6\nx * 4 = 24",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Booleans', 4,
            "isTrue   := true\nisFalse  := false",
            'Boolean type',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Boolean Operators', 5,
            "fmt.Println(true && true)   // true\nfmt.Println(true && false)  // false\nfmt.Println(true || true)   // true\nfmt.Println(true || false)  // true\nfmt.Println(!true)          // false",
            'Boolean operators',
            "true\nfalse\ntrue\ntrue\nfalse",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Arrays', 6,
            "primes := [...]int{2, 3, 5, 7, 11, 13}\nfmt.Println(len(primes)) // => 6\n\n// Outputs: [2 3 5 7 11 13]\nfmt.Println(primes)\n\n// Same as [:3], Outputs: [2 3 5]\nfmt.Println(primes[0:3])",
            'Array declaration and slicing',
            "6\n[2 3 5 7 11 13]\n[2 3 5]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Array declaration', 7,
            "var a [2]string\na[0] = \"Hello\"\na[1] = \"World\"\n\nfmt.Println(a[0], a[1]) //=> Hello World\nfmt.Println(a)   // => [Hello World]",
            'Array with explicit size',
            "Hello World\n[Hello World]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, '2d array', 8,
            "var twoDimension [2][3]int\nfor i := 0; i < 2; i++ {\n    for j := 0; j < 3; j++ {\n        twoDimension[i][j] = i + j\n    }\n}\n// => 2d:  [[0 1 2] [1 2 3]]\nfmt.Println(\"2d: \", twoDimension)",
            'Multi-dimensional arrays',
            "2d:  [[0 1 2] [1 2 3]]",
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'Pointers', 9,
            "func main () {\n  b := *getPointer()\n  fmt.Println(\"Value is\", b)\n}\n\nfunc getPointer () (myPointer *int) {\n  a := 234\n  return &a\n}",
            'Pointer basics',
            "Value is 234",
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'Pointers with new', 10,
            "a := new(int)\n*a = 234",
            'Creating pointers with new',
            null,
            'medium'
        );

        $this->createExample($section2, $goLanguage, 'Slices', 11,
            "s := make([]string, 3)\ns[0] = \"a\"\ns[1] = \"b\"\ns = append(s, \"d\")\ns = append(s, \"e\", \"f\")\n\nfmt.Println(s)\nfmt.Println(s[1])\nfmt.Println(len(s))\nfmt.Println(s[1:3])",
            'Slice operations',
            "[a b  d e f]\nb\n5\n[b ]",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Slice literal', 12,
            "slice := []int{2, 3, 4}",
            'Slice literal syntax',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Constants', 13,
            "const s string = \"constant\"\nconst Phi = 1.618\nconst n = 500000000\nconst d = 3e20 / n\nfmt.Println(d)",
            'Constant declarations',
            "6e+11",
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Type conversions', 14,
            "i := 90\nf := float64(i)\nu := uint(i)\n\n// Will be equal to the character Z\ns := string(i)",
            'Type conversions',
            null,
            'easy'
        );

        $this->createExample($section2, $goLanguage, 'Int to string', 15,
            "i := 90\n\n// need import \"strconv\"\ns := strconv.Itoa(i)\nfmt.Println(s) // Outputs: 90",
            'Converting int to string',
            "90",
            'easy'
        );

        // Section 3: Go Strings
        $section3 = $this->createSection($goLanguage, 'Go Strings', 3, 'String manipulation in Go');

        $this->createExample($section3, $goLanguage, 'Strings function', 1,
            "package main\n\nimport (\n\t\"fmt\"\n\ts \"strings\"\n)\n\nfunc main() {\n    /* Need to import strings as s */\n\tfmt.Println(s.Contains(\"test\", \"e\"))\n\n    /* Build in */\n    fmt.Println(len(\"hello\"))  // => 5\n    // Outputs: 101\n\tfmt.Println(\"hello\"[1])\n    // Outputs: e\n\tfmt.Println(string(\"hello\"[1]))\n}",
            'String functions',
            "true\n5\n101\ne",
            'easy'
        );

        $this->createExample($section3, $goLanguage, 'fmt.Printf', 2,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"os\"\n)\n\ntype point struct {\n\tx, y int\n}\n\nfunc main() {\n\tp := point{1, 2}\n\tfmt.Printf(\"%v\\n\", p)                        // => {1 2}\n\tfmt.Printf(\"%+v\\n\", p)                       // => {x:1 y:2}\n\tfmt.Printf(\"%#v\\n\", p)                       // => main.point{x:1, y:2}\n\tfmt.Printf(\"%T\\n\", p)                        // => main.point\n\tfmt.Printf(\"%t\\n\", true)                     // => TRUE\n\tfmt.Printf(\"%d\\n\", 123)                      // => 123\n\tfmt.Printf(\"%b\\n\", 14)                       // => 1110\n\tfmt.Printf(\"%c\\n\", 33)                       // => !\n\tfmt.Printf(\"%x\\n\", 456)                      // => 1c8\n\tfmt.Printf(\"%f\\n\", 78.9)                     // => 78.9\n\tfmt.Printf(\"%e\\n\", 123400000.0)              // => 1.23E+08\n\tfmt.Printf(\"%E\\n\", 123400000.0)              // => 1.23E+08\n\tfmt.Printf(\"%s\\n\", \"\\\"string\\\"\")             // => \"string\"\n\tfmt.Printf(\"%q\\n\", \"\\\"string\\\"\")             // => \"\\\"string\\\"\"\n\tfmt.Printf(\"%x\\n\", \"hex this\")               // => 6.86578E+15\n\tfmt.Printf(\"%p\\n\", &p)                       // => 0xc00002c040\n\tfmt.Printf(\"|%6d|%6d|\\n\", 12, 345)           // => |    12|   345|\n\tfmt.Printf(\"|%6.2f|%6.2f|\\n\", 1.2, 3.45)     // => |  1.20|  3.45|\n\tfmt.Printf(\"|%-6.2f|%-6.2f|\\n\", 1.2, 3.45)   // => |1.20  |3.45  |\n\tfmt.Printf(\"|%6s|%6s|\\n\", \"foo\", \"b\")        // => |   foo|     b|\n\tfmt.Printf(\"|%-6s|%-6s|\\n\", \"foo\", \"b\")      // => |foo   |b     |\n\n\ts := fmt.Sprintf(\"a %s\", \"string\")\n\tfmt.Println(s)\n\n\tfmt.Fprintf(os.Stderr, \"an %s\\n\", \"error\")\n}",
            'Formatted printing with fmt.Printf',
            "{1 2}\n{x:1 y:2}\nmain.point{x:1, y:2}\nmain.point\ntrue\n123\n1110\n!\n1c8\n78.9\n1.23E+08\n1.23E+08\n\"string\"\n\"\\\"string\\\"\"\n6865782074686973\n0xc00002c040\n|    12|   345|\n|  1.20|  3.45|\n|1.20  |3.45  |\n|   foo|     b|\n|foo   |b     |\na string\nan error",
            'medium'
        );

        // Section 4: Go Functions
        $section4 = $this->createSection($goLanguage, 'Go Functions', 4, 'Function definitions and usage');

        $this->createExample($section4, $goLanguage, 'Multiple return values', 1,
            "func swap(x, y string) (string, string) {\n    return y, x\n}\n\na, b := swap(\"hello\", \"world\")\nfmt.Println(a, b)",
            'Functions with multiple return values',
            "world hello",
            'easy'
        );

        $this->createExample($section4, $goLanguage, 'Named return values', 2,
            "func split(sum int) (x, y int) {\n    x = sum * 4 / 9\n    y = sum - x\n    return\n}",
            'Named return values',
            null,
            'medium'
        );

        $this->createExample($section4, $goLanguage, 'Variadic functions', 3,
            "func sum(nums ...int) int {\n    total := 0\n    for _, num := range nums {\n        total += num\n    }\n    return total\n}\n\nfmt.Println(sum(1, 2, 3))",
            'Variadic functions',
            "6",
            'medium'
        );

        $this->createExample($section4, $goLanguage, 'Functions as values', 4,
            "func main() {\n    // assign a function to a name\n    add := func(a, b int) int {\n        return a + b\n    }\n    // use the name to call the function\n    fmt.Println(add(3, 4)) // => 7\n}",
            'Functions as first-class values',
            "7",
            'medium'
        );

        $this->createExample($section4, $goLanguage, 'Closures', 5,
            "func scope() func() int{\n    outer_var := 2\n    foo := func() int {return outer_var}\n    return foo\n}\n\n// Outputs: 2\nfmt.Println(scope()())",
            'Closures example',
            "2",
            'medium'
        );

        // Section 5: Go Packages
        $section5 = $this->createSection($goLanguage, 'Go Packages', 5, 'Package management');

        $this->createExample($section5, $goLanguage, 'Importing', 1,
            "import \"fmt\"\nimport \"math/rand\"",
            'Basic imports',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, 'Import block', 2,
            "import (\n  \"fmt\"        // gives fmt.Println\n  \"math/rand\"  // gives rand.Intn\n)",
            'Import block syntax',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, 'Aliases', 3,
            "import r \"math/rand\"\n\nr.Intn()",
            'Import aliases',
            null,
            'easy'
        );

        $this->createExample($section5, $goLanguage, 'Exporting names', 4,
            "// Begin with a capital letter\nfunc Hello () {\n  ···\n}",
            'Exported names',
            null,
            'easy'
        );

        // Section 6: Go Concurrency
        $section6 = $this->createSection($goLanguage, 'Go Concurrency', 6, 'Goroutines and channels');

        $this->createExample($section6, $goLanguage, 'Goroutines', 1,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"time\"\n)\n\nfunc f(from string) {\n\tfor i := 0; i < 3; i++ {\n\t\tfmt.Println(from, \":\", i)\n\t}\n}\n\nfunc main() {\n\tf(\"direct\")\n\tgo f(\"goroutine\")\n\n\tgo func(msg string) {\n\t\tfmt.Println(msg)\n\t}(\"going\")\n\n\ttime.Sleep(time.Second)\n\tfmt.Println(\"done\")\n}",
            'Goroutines basics',
            "direct : 0\ndirect : 1\ndirect : 2\ngoing\ngoroutine : 0\ngoroutine : 1\ngoroutine : 2\ndone",
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'WaitGroup', 2,
            "package main\n\nimport (\n\t\"fmt\"\n\t\"sync\"\n\t\"time\"\n)\n\nfunc w(id int, wg *sync.WaitGroup) {\n\tdefer wg.Done()\n\tfmt.Printf(\"%d starting\\n\", id)\n\n\ttime.Sleep(time.Second)\n\tfmt.Printf(\"%d done\\n\", id)\n}\n\nfunc main() {\n\tvar wg sync.WaitGroup\n\tfor i := 1; i <= 5; i++ {\n\t\twg.Add(1)\n\t\tgo w(i, &wg)\n\t}\n\twg.Wait()\n}",
            'WaitGroup for synchronization',
            "1 starting\n2 starting\n3 starting\n4 starting\n5 starting\n1 done\n2 done\n3 done\n4 done\n5 done",
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'Closing channels', 3,
            "ch <- 1\nch <- 2\nch <- 3\nclose(ch) // Closes a channel\n\n// Iterate the channel until closed\nfor i := range ch {\n  ···\n}\n\n// Closed if `ok == false`\nv, ok := <- ch",
            'Channel closing and iteration',
            null,
            'medium'
        );

        $this->createExample($section6, $goLanguage, 'Buffered channels', 4,
            "ch := make(chan int, 2)\nch <- 1\nch <- 2\nch <- 3\n// fatal error:\n// all goroutines are asleep - deadlock",
            'Buffered channels',
            null,
            'medium'
        );

        // Section 7: Go Error control
        $section7 = $this->createSection($goLanguage, 'Go Error control', 7, 'Error handling');

        $this->createExample($section7, $goLanguage, 'Deferring functions', 1,
            "func main() {\n  defer func() {\n    fmt.Println(\"Done\")\n  }()\n  fmt.Println(\"Working...\")\n}",
            'Defer statement',
            "Working...\nDone",
            'easy'
        );

        $this->createExample($section7, $goLanguage, 'Defer', 2,
            "func main() {\n  defer fmt.Println(\"Done\")\n  fmt.Println(\"Working...\")\n}",
            'Simple defer',
            "Working...\nDone",
            'easy'
        );

        // Section 8: Go Methods
        $section8 = $this->createSection($goLanguage, 'Go Methods', 8, 'Methods and receivers');

        $this->createExample($section8, $goLanguage, 'Receivers', 1,
            "type Vertex struct {\n  X, Y float64\n}\n\nfunc (v Vertex) Abs() float64 {\n  return math.Sqrt(v.X * v.X + v.Y * v.Y)\n}\n\nv := Vertex{1, 2}\nv.Abs()",
            'Method with value receiver',
            null,
            'medium'
        );

        $this->createExample($section8, $goLanguage, 'Mutation', 2,
            "func (v *Vertex) Scale(f float64) {\n  v.X = v.X * f\n  v.Y = v.Y * f\n}\n\nv := Vertex{6, 12}\nv.Scale(0.5)\n// `v` is updated",
            'Method with pointer receiver',
            null,
            'medium'
        );

        // Section 9: Go Interfaces
        $section9 = $this->createSection($goLanguage, 'Go Interfaces', 9, 'Interface implementation');

        $this->createExample($section9, $goLanguage, 'A basic interface', 1,
            "type Shape interface {\n  Area() float64\n  Perimeter() float64\n}",
            'Interface definition',
            null,
            'medium'
        );

        $this->createExample($section9, $goLanguage, 'Struct', 2,
            "type Rectangle struct {\n  Length, Width float64\n}",
            'Struct definition',
            null,
            'easy'
        );

        $this->createExample($section9, $goLanguage, 'Methods', 3,
            "func (r Rectangle) Area() float64 {\n  return r.Length * r.Width\n}\n\nfunc (r Rectangle) Perimeter() float64 {\n  return 2 * (r.Length + r.Width)\n}",
            'Implementing interface methods',
            null,
            'medium'
        );

        $this->createExample($section9, $goLanguage, 'Interface example', 4,
            "func main() {\n  var r Shape = Rectangle{Length: 3, Width: 4}\n  fmt.Printf(\"Type of r: %T, Area: %v, Perimeter: %v.\", r, r.Area(), r.Perimeter())\n}",
            'Using interface',
            "Type of r: main.Rectangle, Area: 12, Perimeter: 14.",
            'medium'
        );

        // Section 10: Miscellaneous
        $section10 = $this->createSection($goLanguage, 'Miscellaneous', 10, 'Keywords and operators');

        $this->createExample($section10, $goLanguage, 'Keywords', 1,
            "break default func interface select\ncase defer go map struct\nchan else goto package switch\nconst fallthrough if range type\ncontinue for import return var",
            'Go keywords',
            null,
            'easy'
        );

        // Update counts
        $this->updateLanguageCounts($goLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
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

