<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeYamlSeeder extends Seeder
{
    /**
     * Seed YAML cheat code data from doleaf
     * Reference: https://doleaf.com/yaml
     */
    public function run(): void
    {
        // Create YAML Language
        $yamlLanguage = CheatCodeLanguage::create([
            'name' => 'yaml',
            'display_name' => 'YAML',
            'slug' => 'yaml',
            'icon' => 'ic_yaml',
            'color' => '#CB171E',
            'description' => 'YAML形式の設定ファイルを理解し、記述するためのリファレンス。',
            'category' => 'markup',
            'popularity' => 80,
            'is_active' => true,
            'sort_order' => 13,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($yamlLanguage, 'はじめに', 1, 'YAMLの基本と導入', 'getting-started');

        $this->createExample($section1, $yamlLanguage, 'YAMLの特徴', 1,
            "# YAML is a data serialisation language designed to be directly writable and readable by humans\n\n# YAML does not allow the use of tabs\n# Must be space between the element parts\n# YAML is CASE sensitive\n# End your YAML file with the .yaml or .yml extension\n# YAML is a superset of JSON\n# Ansible playbooks are YAML files",
            'YAMLの基本的な特徴とルール',
            null,
            'easy'
        );

        $this->createExample($section1, $yamlLanguage, 'スカラー型', 2,
            "n1: 1            # integer          \nn2: 1.234        # float      \n\ns1: 'abc'        # string        \ns2: \"abc\"        # string           \ns3: abc          # string           \n\nb: false         # boolean type \n\nd: 2015-04-05    # date type",
            'YAMLの基本的なデータ型',
            null,
            'easy'
        );

        $this->createExample($section1, $yamlLanguage, '変数', 3,
            "some_thing: &VAR_NAME foobar\nother_thing: *VAR_NAME",
            'アンカーとエイリアスを使用した変数',
            null,
            'medium'
        );

        $this->createExample($section1, $yamlLanguage, 'コメント', 4,
            "# A single line comment example\n\n# block level comment example\n# comment line 1\n# comment line 2\n# comment line 3",
            'YAMLでのコメントの書き方',
            null,
            'easy'
        );

        $this->createExample($section1, $yamlLanguage, '複数行文字列', 5,
            "description: |\n  hello\n  world",
            '複数行文字列（改行を保持）',
            "hello\nworld\n",
            'easy'
        );

        $this->createExample($section1, $yamlLanguage, '継承', 6,
            "parent: &defaults\n  a: 2\n  b: 3\n\nchild:\n  <<: *defaults\n  b: 4",
            'マッピングの継承とマージ',
            null,
            'medium'
        );

        $this->createExample($section1, $yamlLanguage, '参照', 7,
            "values: &ref\n  - Will be\n  - reused below\n  \nother_values:\n  i_am_ref: *ref",
            'アンカーを使用した値の参照',
            null,
            'medium'
        );

        $this->createExample($section1, $yamlLanguage, '折りたたみ文字列', 8,
            "description: >\n  hello\n  world",
            '複数行文字列（改行をスペースに変換）',
            "hello world\n",
            'easy'
        );

        $this->createExample($section1, $yamlLanguage, '複数ドキュメント', 9,
            "---\ndocument: this is doc 1\n---\ndocument: this is doc 2",
            'YAMLファイル内の複数ドキュメント',
            null,
            'easy'
        );

        // Section 2: YAML Collections
        $section2 = $this->createSection($yamlLanguage, 'YAMLコレクション', 2, '配列、マッピング、ネスト構造', 'collections');

        $this->createExample($section2, $yamlLanguage, 'シーケンス（配列）', 1,
            "- Mark McGwire\n- Sammy Sosa\n- Ken Griffey",
            '基本的な配列（シーケンス）',
            null,
            'easy'
        );

        $this->createExample($section2, $yamlLanguage, 'マッピング（辞書）', 2,
            "hr:  65       # Home runs\navg: 0.278    # Batting average\nrbi: 147      # Runs Batted In",
            '基本的なマッピング（キー・値のペア）',
            null,
            'easy'
        );

        $this->createExample($section2, $yamlLanguage, 'マッピングからシーケンス', 3,
            "attributes:\n  - a1\n  - a2\nmethods: [getter, setter]",
            'マッピング内のシーケンス',
            null,
            'easy'
        );

        $this->createExample($section2, $yamlLanguage, 'シーケンスのマッピング', 4,
            "children:\n  - name: Jimmy Smith\n    age: 15\n  - name: Jimmy Smith\n    age: 15\n  -\n    name: Sammy Sosa\n    age: 12",
            'シーケンス内のマッピング',
            null,
            'easy'
        );

        $this->createExample($section2, $yamlLanguage, 'シーケンスのシーケンス', 5,
            "my_sequences:\n  - [1, 2, 3]\n  - [4, 5, 6]\n  -  \n    - 7\n    - 8\n    - 9\n    - 0",
            'ネストされたシーケンス',
            null,
            'medium'
        );

        $this->createExample($section2, $yamlLanguage, 'マッピングのマッピング', 6,
            "Mark McGwire: {hr: 65, avg: 0.278}\nSammy Sosa: {\n    hr: 63,\n    avg: 0.288\n  }",
            'ネストされたマッピング',
            null,
            'easy'
        );

        $this->createExample($section2, $yamlLanguage, 'ネストされたコレクション', 7,
            "Jack:\n  id: 1\n  name: Franc\n  salary: 25000\n  hobby:\n    - a\n    - b\n  location: {country: \"A\", city: \"A-A\"}",
            '複雑なネスト構造',
            null,
            'medium'
        );

        $this->createExample($section2, $yamlLanguage, '順序なしセット', 8,
            "set1: !!set\n  ? one\n  ? two\nset2: !!set {'one', \"two\"}",
            'セット（重複なしコレクション）',
            null,
            'medium'
        );

        $this->createExample($section2, $yamlLanguage, '順序付きマッピング', 9,
            "ordered: !!omap\n- Mark McGwire: 65\n- Sammy Sosa: 63\n- Ken Griffy: 58",
            '順序を保持するマッピング',
            null,
            'medium'
        );

        // Section 3: YAML Reference
        $section3 = $this->createSection($yamlLanguage, 'YAMLリファレンス', 3, '記号、エスケープ、型', 'reference');

        $this->createExample($section3, $yamlLanguage, '用語', 1,
            "# Sequences aka arrays or lists\n# Scalars aka strings or numbers\n# Mappings aka hashes or dictionaries",
            'YAMLの基本用語',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, 'ドキュメント指示子', 2,
            "# %    - Directive indicator\n# ---  - Document header\n# ...  - Document terminator",
            'ドキュメントの区切り記号',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, 'コレクション指示子', 3,
            "# ?    - Key indicator\n# :    - Value indicator\n# -    - Nested series entry indicator\n# ,    - Separate in-line branch entries\n# []   - Surround in-line series branch\n# {}   - Surround in-line keyed branch",
            'コレクションの記号',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, 'エイリアス指示子', 4,
            "# &  - Anchor property\n# *  - Alias indicator",
            'アンカーとエイリアス',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, '特殊キー', 5,
            "# =  - Default \"value\" mapping key\n# << - Merge keys from another mapping",
            '特殊なマッピングキー',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, 'スカラー指示子', 6,
            "# ''  - Surround in-line unescaped scalar\n# \"   - Surround in-line escaped scalar\n# |   - Block scalar indicator\n# >   - Folded scalar indicator\n# -   - Strip chomp modifier (|- or >-)\n# +   - Keep chomp modifier (|+ or >+)\n# 1-9 - Explicit indentation modifier",
            'スカラー値の記号',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, 'タグプロパティ', 7,
            "# none   - Unspecified tag (automatically resolved)\n# !      - Non-specific tag (by default, !!map/!!seq/!!str)\n# !foo   - Primary (by convention, means a local !foo tag)\n# !!foo  - Secondary (by convention, means tag:yaml.org,2002:foo)\n# !h!foo - Requires %TAG !h! <prefix>\n# !<foo> - Verbatim tag (always means foo)",
            'YAMLタグの種類',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, 'コア型', 8,
            "# !!map - {Hash table, dictionary, mapping}\n# !!seq - {List, array, tuple, vector, sequence}\n# !!str - Unicode string",
            'YAMLのコアデータ型',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, 'エスケープコード - 数値', 9,
            "# \\x12       (8-bit)\n# \\u1234     (16-bit)\n# \\U00102030 (32-bit)",
            '数値エスケープコード',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, 'エスケープコード - 保護', 10,
            "# \\\\     (\\\\)\n# \\\"     (\")\n# \\      ( )\n# \\<TAB> (TAB)",
            '保護用エスケープコード',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, 'エスケープコード - Cスタイル', 11,
            "# \\0  (NUL)\n# \\a  (BEL)\n# \\b  (BS)\n# \\f  (FF)\n# \\n  (LF)\n# \\r  (CR)\n# \\t  (TAB)\n# \\v  (VTAB)",
            'Cスタイルのエスケープコード',
            null,
            'easy'
        );

        $this->createExample($section3, $yamlLanguage, '追加のエスケープコード', 12,
            "# \\e  (ESC)\n# \\_  (NBSP)\n# \\N  (NEL)\n# \\L  (LS)\n# \\P  (PS)",
            '追加のエスケープコード',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, 'その他の型', 13,
            "# !!set  - {cherries, plums, apples}\n# !!omap - [one: 1, two: 2]",
            'セットと順序付きマッピング',
            null,
            'medium'
        );

        $this->createExample($section3, $yamlLanguage, '言語独立スカラー型', 14,
            "# {~, null}              - Null (no value)\n# [1234, 0x4D2, 02333]     - [Decimal int, Hexadecimal int, Octal int]\n# [1_230.15, 12.3015e+02]  - [Fixed float, Exponential float]\n# [.inf, -.Inf, .NAN]      - [Infinity, Negative, Not a number]\n# {Y, true, Yes, ON}       - Boolean true\n# {n, FALSE, No, off}      - Boolean false",
            '言語独立のスカラー型表現',
            null,
            'medium'
        );

        // Update language counts
        $this->updateLanguageCounts($yamlLanguage);
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
        if (str_contains($titleLower, 'sequence') || str_contains($titleLower, 'array') || str_contains($descLower, '配列') || str_contains($descLower, 'シーケンス')) {
            $tags[] = 'sequence';
        }
        if (str_contains($titleLower, 'mapping') || str_contains($titleLower, 'dictionary') || str_contains($descLower, 'マッピング') || str_contains($descLower, '辞書')) {
            $tags[] = 'mapping';
        }
        if (str_contains($titleLower, 'scalar') || str_contains($titleLower, 'string') || str_contains($descLower, 'スカラー') || str_contains($descLower, '文字列')) {
            $tags[] = 'scalar';
        }
        if (str_contains($titleLower, 'variable') || str_contains($titleLower, 'anchor') || str_contains($titleLower, 'alias') || str_contains($descLower, '変数') || str_contains($descLower, '参照')) {
            $tags[] = 'variable';
        }
        if (str_contains($titleLower, 'nested') || str_contains($titleLower, 'ネスト')) {
            $tags[] = 'nested';
        }
        if (str_contains($titleLower, 'escape') || str_contains($titleLower, 'エスケープ')) {
            $tags[] = 'escape';
        }
        if (str_contains($titleLower, 'tag') || str_contains($titleLower, 'タグ')) {
            $tags[] = 'tag';
        }
        if (str_contains($titleLower, 'set') || str_contains($descLower, 'セット')) {
            $tags[] = 'set';
        }
        if (str_contains($titleLower, 'inherit') || str_contains($titleLower, '継承') || str_contains($titleLower, 'merge')) {
            $tags[] = 'inheritance';
        }
        if (str_contains($titleLower, 'collection') || str_contains($descLower, 'コレクション')) {
            $tags[] = 'collection';
        }

        // Add basic tags
        $tags[] = 'yaml';
        $tags[] = 'markup';
        $tags[] = 'configuration';
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

