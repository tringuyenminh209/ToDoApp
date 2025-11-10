<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeBashSeeder extends Seeder
{
    /**
     * Seed Bash cheat code data from doleaf
     * Reference: https://doleaf.com/bash
     */
    public function run(): void
    {
        // Create Bash Language
        $bashLanguage = CheatCodeLanguage::create([
            'name' => 'bash',
            'display_name' => 'Bash',
            'slug' => 'bash',
            'icon' => 'ic_bash',
            'color' => '#4EAA25',
            'description' => 'Bashシェルスクリプトのリファレンス。Linux/Unix環境でのシェルスクリプティングの基礎から応用まで。',
            'category' => 'shell',
            'popularity' => 85,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($bashLanguage, 'はじめに', 1, 'Bashスクリプトの基本と導入', 'getting-started');

        $this->createExample($section1, $bashLanguage, 'Hello World', 1,
            "#!/bin/bash\n\nVAR=\"world\"\necho \"Hello \$VAR!\" # => Hello world!",
            '基本的なBashスクリプトの例',
            "Hello world!",
            'easy'
        );

        $this->createExample($section1, $bashLanguage, 'スクリプトの実行', 2,
            "$ bash hello.sh",
            'Bashスクリプトの実行方法',
            null,
            'easy'
        );

        $this->createExample($section1, $bashLanguage, '変数', 3,
            "NAME=\"John\"\n\necho \${NAME}    # => John (Variables)\necho \$NAME      # => John (Variables)\necho \"\$NAME\"    # => John (Variables)\necho '\$NAME'    # => \$NAME (Exact string)\necho \"\${NAME}!\" # => John! (Variables)\n\nNAME = \"John\"   # => Error (about space)",
            '変数の宣言と使用方法',
            "John\nJohn\nJohn\n\$NAME\nJohn!",
            'easy'
        );

        $this->createExample($section1, $bashLanguage, 'コメント', 4,
            "# This is an inline Bash comment.\n\n: '\nThis is a\nvery neat comment\nin bash\n'",
            'インラインコメントと複数行コメント',
            null,
            'easy'
        );

        $this->createExample($section1, $bashLanguage, '引数', 5,
            "# \$1 … \$9    - Parameter 1 ... 9\n# \$0         - Name of the script itself\n# \$1         - First argument\n# \${10}      - Positional parameter 10\n# \$#         - Number of arguments\n# \$\*        - All arguments\n# \$@         - All arguments, starting from first\n# \$-         - Current options\n# \$_         - Last argument of the previous command",
            'スクリプト引数の特殊変数',
            null,
            'easy'
        );

        $this->createExample($section1, $bashLanguage, '関数', 6,
            "get_name() {\n    echo \"John\"\n}\n\necho \"You are \$(get_name)\"",
            '関数の定義と呼び出し',
            "You are John",
            'easy'
        );

        $this->createExample($section1, $bashLanguage, '条件分岐', 7,
            "if [[ -z \"\$string\" ]]; then\n    echo \"String is empty\"\nelif [[ -n \"\$string\" ]]; then\n    echo \"String is not empty\"\nfi",
            'if-elif-else文の基本構文',
            null,
            'easy'
        );

        $this->createExample($section1, $bashLanguage, 'ブレース展開', 8,
            "echo {A,B}.js\n# => A.js B.js\n\necho {1..5}\n# => 1 2 3 4 5",
            'ブレース展開の使用例',
            "A.js B.js\n1 2 3 4 5",
            'easy'
        );

        $this->createExample($section1, $bashLanguage, 'コマンド置換', 9,
            "# => I'm in /path/of/current\necho \"I'm in \$(PWD)\"\n\n# Same as:\necho \"I'm in `pwd`\"",
            'コマンド置換の2つの方法',
            null,
            'easy'
        );

        // Section 2: Parameter Expansions
        $section2 = $this->createSection($bashLanguage, 'パラメータ展開', 2, '変数の展開と操作', 'parameter-expansions');

        $this->createExample($section2, $bashLanguage, '接頭辞・接尾辞の削除', 1,
            "STR=\"/path/to/foo.cpp\"\necho \${STR%.cpp}    # /path/to/foo\necho \${STR%.cpp}.o  # /path/to/foo.o\necho \${STR%/*}      # /path/to\n\necho \${STR##*.}     # cpp (extension)\necho \${STR##*/}     # foo.cpp (basepath)\n\necho \${STR#*/}      # path/to/foo.cpp\necho \${STR##*/}     # foo.cpp",
            'パラメータ展開による文字列操作',
            "/path/to/foo\n/path/to/foo.o\n/path/to\ncpp\nfoo.cpp\npath/to/foo.cpp\nfoo.cpp",
            'medium'
        );

        $this->createExample($section2, $bashLanguage, '文字列置換', 2,
            "STR=\"/path/to/foo.cpp\"\necho \${STR/foo/bar} # /path/to/bar.cpp",
            '文字列の置換',
            "/path/to/bar.cpp",
            'easy'
        );

        $this->createExample($section2, $bashLanguage, '部分文字列', 3,
            "name=\"John\"\necho \${name}           # => John\necho \${name:0:2}       # => Jo\necho \${name::2}        # => Jo\necho \${name::-1}       # => Joh\necho \${name:(-1)}      # => n\necho \${name:(-2)}      # => hn\necho \${name:(-2):2}    # => hn",
            '部分文字列の取得',
            "John\nJo\nJo\nJoh\nn\nhn\nhn",
            'medium'
        );

        $this->createExample($section2, $bashLanguage, '文字列の長さ', 4,
            "FOO=\"Hello\"\necho \${#FOO}    # => 5",
            '文字列の長さを取得',
            "5",
            'easy'
        );

        $this->createExample($section2, $bashLanguage, 'デフォルト値', 5,
            "echo \${food:-Cake}  #=> \$food or \"Cake\"",
            '変数が未設定の場合のデフォルト値',
            "Cake",
            'easy'
        );

        $this->createExample($section2, $bashLanguage, '大文字・小文字変換', 6,
            "STR=\"HELLO WORLD!\"\necho \${STR,}   # => hELLO WORLD!\necho \${STR,,}  # => hello world!\n\nSTR=\"hello world!\"\necho \${STR^}   # => Hello world!\necho \${STR^^} # => HELLO WORLD!",
            '文字列の大文字・小文字変換',
            "hELLO WORLD!\nhello world!\nHello world!\nHELLO WORLD!",
            'medium'
        );

        // Section 3: Arrays
        $section3 = $this->createSection($bashLanguage, '配列', 3, '配列の定義と操作', 'arrays');

        $this->createExample($section3, $bashLanguage, '配列の定義', 1,
            "Fruits=('Apple' 'Banana' 'Orange')\n\nFruits[0]=\"Apple\"\nFruits[1]=\"Banana\"\nFruits[2]=\"Orange\"\n\nARRAY1=(foo{1..2}) # => foo1 foo2\nARRAY2=({A..D})    # => A B C D",
            '配列の定義方法',
            null,
            'easy'
        );

        $this->createExample($section3, $bashLanguage, '配列のインデックス', 2,
            "Fruits=('Apple' 'Banana' 'Orange')\n\necho \${Fruits[0]}     # First element\necho \${Fruits[-1]}    # Last element\necho \${Fruits[@]}     # All elements\necho \${#Fruits[@]}    # Number of all\necho \${#Fruits}       # Length of 1st\necho \${Fruits[@]:3:2} # Range",
            '配列要素へのアクセス',
            "Apple\nOrange\nApple Banana Orange\n3\n5",
            'easy'
        );

        $this->createExample($section3, $bashLanguage, '配列の反復処理', 3,
            "Fruits=('Apple' 'Banana' 'Orange')\n\nfor e in \"\${Fruits[@]}\"; do\n    echo \$e\ndone",
            '配列の全要素を反復処理',
            "Apple\nBanana\nOrange",
            'easy'
        );

        $this->createExample($section3, $bashLanguage, 'インデックス付き反復処理', 4,
            "for i in \"\${!Fruits[@]}\"; do\n  printf \"%s\\t%s\\n\" \"\$i\" \"\${Fruits[\$i]}\"\ndone",
            'インデックスと値を同時に取得',
            "0\tApple\n1\tBanana\n2\tOrange",
            'medium'
        );

        $this->createExample($section3, $bashLanguage, '配列操作', 5,
            "Fruits=(\"\${Fruits[@]}\" \"Watermelon\")     # Push\nFruits+=('Watermelon')                   # Also Push\nFruits=( \${Fruits[@]/Ap*/} )             # Remove by regex match\nunset Fruits[2]                          # Remove one item\nFruits=(\"\${Fruits[@]}\")                  # Duplicate",
            '配列への要素追加と削除',
            null,
            'medium'
        );

        // Section 4: Dictionaries
        $section4 = $this->createSection($bashLanguage, '連想配列', 4, '連想配列（辞書）の操作', 'dictionaries');

        $this->createExample($section4, $bashLanguage, '連想配列の定義', 1,
            "declare -A sounds\n\nsounds[dog]=\"bark\"\nsounds[cow]=\"moo\"\nsounds[bird]=\"tweet\"\nsounds[wolf]=\"howl\"",
            '連想配列の宣言と初期化',
            null,
            'easy'
        );

        $this->createExample($section4, $bashLanguage, '連想配列の操作', 2,
            "echo \${sounds[dog]} # Dog's sound\necho \${sounds[@]}   # All values\necho \${!sounds[@]}  # All keys\necho \${#sounds[@]}  # Number of elements\nunset sounds[dog]   # Delete dog",
            '連想配列の値とキーの取得',
            "bark\nbark moo tweet howl\ndog cow bird wolf\n4",
            'easy'
        );

        $this->createExample($section4, $bashLanguage, '連想配列の反復処理', 3,
            "for val in \"\${sounds[@]}\"; do\n    echo \$val\ndone\n\nfor key in \"\${!sounds[@]}\"; do\n    echo \$key\ndone",
            '値とキーの反復処理',
            "bark\nmoo\ntweet\nhowl\ndog\ncow\nbird\nwolf",
            'easy'
        );

        // Section 5: Conditionals
        $section5 = $this->createSection($bashLanguage, '条件分岐', 5, '条件文と比較演算子', 'conditionals');

        $this->createExample($section5, $bashLanguage, '整数の比較', 1,
            "[[ \$a -eq \$b ]]  # Equal\n[[ \$a -ne \$b ]]  # Not equal\n[[ \$a -lt \$b ]]  # Less than\n[[ \$a -le \$b ]]  # Less or equal\n[[ \$a -gt \$b ]]  # Greater than\n[[ \$a -ge \$b ]]  # Greater or equal",
            '整数比較演算子',
            null,
            'easy'
        );

        $this->createExample($section5, $bashLanguage, '文字列の比較', 2,
            "[[ \$a == \$b ]]   # Equal\n[[ \$a != \$b ]]   # Not equal\n[[ -z \$a ]]       # Is empty\n[[ -n \$a ]]       # Is not empty\n[[ \$a < \$b ]]     # Less than (lexicographically)\n[[ \$a > \$b ]]     # Greater than (lexicographically)",
            '文字列比較演算子',
            null,
            'easy'
        );

        $this->createExample($section5, $bashLanguage, 'ファイル条件', 3,
            "[[ -e \$file ]]    # Exists\n[[ -r \$file ]]    # Readable\n[[ -h \$file ]]    # Symlink\n[[ -d \$file ]]    # Directory\n[[ -w \$file ]]    # Writable\n[[ -s \$file ]]    # Size is > 0 bytes\n[[ -f \$file ]]    # File\n[[ -x \$file ]]    # Executable",
            'ファイル・ディレクトリの条件チェック',
            null,
            'easy'
        );

        $this->createExample($section5, $bashLanguage, '論理演算子', 4,
            "[[ \$a && \$b ]]   # Logical AND\n[[ \$a || \$b ]]   # Logical OR\n[[ ! \$a ]]        # Logical NOT",
            '論理演算子',
            null,
            'easy'
        );

        // Section 6: Loops
        $section6 = $this->createSection($bashLanguage, 'ループ', 6, 'for、while、untilループ', 'loops');

        $this->createExample($section6, $bashLanguage, 'Forループ', 1,
            "for i in /etc/rc.*; do\n  echo \$i\ndone",
            'ファイルパターンでのforループ',
            null,
            'easy'
        );

        $this->createExample($section6, $bashLanguage, 'Cスタイルforループ', 2,
            "for ((i = 0 ; i < 100 ; i++)); do\n  echo \$i\ndone",
            'Cスタイルのforループ',
            null,
            'easy'
        );

        $this->createExample($section6, $bashLanguage, '範囲forループ', 3,
            "for i in {1..5}; do\n    echo \"Welcome \$i times\"\ndone",
            '数値範囲でのforループ',
            "Welcome 1 times\nWelcome 2 times\nWelcome 3 times\nWelcome 4 times\nWelcome 5 times",
            'easy'
        );

        $this->createExample($section6, $bashLanguage, 'Whileループ', 4,
            "count=0\nwhile [ \$count -lt 5 ]; do\n    echo \"Count: \$count\"\n    ((count++))\ndone",
            'whileループの基本',
            "Count: 0\nCount: 1\nCount: 2\nCount: 3\nCount: 4",
            'easy'
        );

        $this->createExample($section6, $bashLanguage, 'Untilループ', 5,
            "count=0\nuntil [ \$count -gt 5 ]; do\n    echo \"Count: \$count\"\n    ((count++))\ndone",
            'untilループ（条件がfalseの間実行）',
            "Count: 0\nCount: 1\nCount: 2\nCount: 3\nCount: 4\nCount: 5",
            'easy'
        );

        $this->createExample($section6, $bashLanguage, 'BreakとContinue', 6,
            "for i in {1..10}; do\n    if [[ \$i -eq 5 ]]; then\n        break\n    fi\n    echo \$i\ndone",
            'break文でループを終了',
            "1\n2\n3\n4",
            'easy'
        );

        // Section 7: Functions
        $section7 = $this->createSection($bashLanguage, '関数', 7, '関数の定義と使用', 'functions');

        $this->createExample($section7, $bashLanguage, '関数の定義', 1,
            "get_name() {\n    echo \"John\"\n}\n\necho \"You are \$(get_name)\"",
            '基本的な関数定義',
            "You are John",
            'easy'
        );

        $this->createExample($section7, $bashLanguage, '関数の引数', 2,
            "greet() {\n    echo \"Hello, \$1\"\n}\n\ngreet \"World\"  # => Hello, World",
            '関数への引数渡し',
            "Hello, World",
            'easy'
        );

        $this->createExample($section7, $bashLanguage, '戻り値', 3,
            "myfunc() {\n    return 1\n}\n\nif myfunc; then\n    echo \"success\"\nelse\n    echo \"failure\"\nfi",
            '関数の戻り値（終了コード）',
            "failure",
            'medium'
        );

        // Section 8: Options & Settings
        $section8 = $this->createSection($bashLanguage, 'オプションと設定', 8, 'Bashオプションとグロブ設定', 'options-settings');

        $this->createExample($section8, $bashLanguage, 'Bashオプション', 1,
            "# Avoid overlay files\nset -o noclobber\n\n# Used to exit upon error\nset -o errexit\n\n# Unveils hidden failures\nset -o pipefail\n\n# Exposes unset variables\nset -o nounset",
            '重要なBashオプション設定',
            null,
            'medium'
        );

        $this->createExample($section8, $bashLanguage, 'グロブオプション', 2,
            "# Non-matching globs are removed\nshopt -s nullglob\n\n# Non-matching globs throw errors\nshopt -s failglob\n\n# Case insensitive globs\nshopt -s nocaseglob\n\n# Wildcards match dotfiles\nshopt -s dotglob\n\n# Allow ** for recursive matches\nshopt -s globstar",
            'グロブ（ワイルドカード）オプション',
            null,
            'medium'
        );

        // Section 9: History
        $section9 = $this->createSection($bashLanguage, '履歴', 9, 'コマンド履歴の操作', 'history');

        $this->createExample($section9, $bashLanguage, '履歴コマンド', 1,
            "history              # Show history\nsudo !!             # Run the previous command with sudo\nshopt -s histverify # Don't execute expanded result immediately",
            'コマンド履歴の基本操作',
            null,
            'easy'
        );

        $this->createExample($section9, $bashLanguage, '履歴展開', 2,
            "!!                 # Execute last command again\n!!:s/<FROM>/<TO>/  # Replace first occurrence\n!!:gs/<FROM>/<TO>/ # Replace all occurrences\n!$                 # Last parameter of most recent command\n!-n                # Expand nth most recent command",
            '履歴展開の使用例',
            null,
            'medium'
        );

        // Section 10: Miscellaneous
        $section10 = $this->createSection($bashLanguage, 'その他', 10, 'その他の便利な機能', 'miscellaneous');

        $this->createExample($section10, $bashLanguage, '数値計算', 1,
            "\$((a + 200))      # Add 200 to \$a\n\n\$((\$RANDOM%200))  # Random number 0..199",
            '算術展開による計算',
            null,
            'easy'
        );

        $this->createExample($section10, $bashLanguage, 'サブシェル', 2,
            "(cd somedir; echo \"I'm now in \$PWD\")\npwd # still in first directory",
            'サブシェルの使用（現在のシェルに影響しない）',
            null,
            'medium'
        );

        $this->createExample($section10, $bashLanguage, 'リダイレクション', 3,
            "python hello.py > output.txt   # stdout to (file)\npython hello.py >> output.txt  # stdout to (file), append\npython hello.py 2> error.log   # stderr to (file)\npython hello.py 2>&1           # stderr to stdout\npython hello.py 2>/dev/null    # stderr to (null)\npython hello.py &>/dev/null    # stdout and stderr to (null)\n\npython hello.py < foo.txt      # feed foo.txt to stdin",
            '入出力リダイレクション',
            null,
            'easy'
        );

        $this->createExample($section10, $bashLanguage, 'Case/Switch', 4,
            "case \"\$1\" in\n    start | up)\n    vagrant up\n    ;;\n\n    *)\n    echo \"Usage: \$0 {start|stop|ssh}\"\n    ;;\nesac",
            'case文による分岐処理',
            null,
            'easy'
        );

        $this->createExample($section10, $bashLanguage, 'エラートラップ', 5,
            "trap 'echo Error at about \$LINENO' ERR",
            'エラー発生時のトラップ設定',
            null,
            'medium'
        );

        $this->createExample($section10, $bashLanguage, 'printf', 6,
            "printf \"Hello %s, I'm %s\" Sven Olga\n#=> \"Hello Sven, I'm Olga\"\n\nprintf \"1 + 1 = %d\" 2\n#=> \"1 + 1 = 2\"\n\nprintf \"Print a float: %f\" 2\n#=> \"Print a float: 2.000000\"",
            'printfによるフォーマット出力',
            "Hello Sven, I'm Olga\n1 + 1 = 2\nPrint a float: 2.000000",
            'easy'
        );

        $this->createExample($section10, $bashLanguage, 'オプション解析', 7,
            "while [[ \"\$1\" =~ ^- && ! \"\$1\" == \"--\" ]]; do case \$1 in\n    -V | --version )\n    echo \$version\n    exit\n    ;;\n    -s | --string )\n    shift; string=\$1\n    ;;\n    -f | --flag )\n    flag=1\n    ;;\nesac; shift; done",
            'コマンドラインオプションの解析',
            null,
            'medium'
        );

        $this->createExample($section10, $bashLanguage, '条件実行', 8,
            "git commit && git push\ngit commit || echo \"Commit failed\"",
            '条件付きコマンド実行',
            null,
            'easy'
        );

        $this->createExample($section10, $bashLanguage, '厳密モード', 9,
            "set -euo pipefail\nIFS=\$'\\n\\t'",
            'Bash厳密モードの設定',
            null,
            'medium'
        );

        // Update language counts
        $this->updateLanguageCounts($bashLanguage);
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
        if (str_contains($titleLower, 'array') || str_contains($descLower, 'array') || str_contains($descLower, '配列')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string') || str_contains($descLower, 'string') || str_contains($descLower, '文字列')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function') || str_contains($descLower, 'function') || str_contains($descLower, '関数')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'loop') || str_contains($titleLower, 'for') || str_contains($titleLower, 'while') || str_contains($descLower, 'ループ')) {
            $tags[] = 'loop';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'case') || str_contains($descLower, '条件')) {
            $tags[] = 'conditional';
        }
        if (str_contains($titleLower, 'variable') || str_contains($titleLower, 'var') || str_contains($descLower, '変数')) {
            $tags[] = 'variable';
        }
        if (str_contains($titleLower, 'file') || str_contains($titleLower, 'directory') || str_contains($descLower, 'ファイル')) {
            $tags[] = 'file';
        }
        if (str_contains($titleLower, 'history') || str_contains($descLower, '履歴')) {
            $tags[] = 'history';
        }
        if (str_contains($titleLower, 'redirect') || str_contains($descLower, 'リダイレクション')) {
            $tags[] = 'io';
        }
        if (str_contains($titleLower, 'dictionary') || str_contains($titleLower, 'associative') || str_contains($descLower, '連想')) {
            $tags[] = 'dictionary';
        }
        if (str_contains($titleLower, 'parameter') || str_contains($titleLower, 'expansion') || str_contains($descLower, '展開')) {
            $tags[] = 'parameter-expansion';
        }

        // Add basic tags
        $tags[] = 'bash';
        $tags[] = 'shell';
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

