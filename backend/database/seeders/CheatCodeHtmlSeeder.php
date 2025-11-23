<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeHtmlSeeder extends Seeder
{
    /**
     * Seed HTML cheat code data from Kizamu
     * Reference: https://Kizamu.com/html
     */
    public function run(): void
    {
        // Create HTML Language
        $htmlLanguage = CheatCodeLanguage::create([
            'name' => 'html',
            'display_name' => 'HTML',
            'slug' => 'html',
            'icon' => 'ic_html',
            'color' => '#E34F26',
            'description' => 'HTML（HyperText Markup Language）は、WebページとWebアプリケーションを作成するための標準的なマークアップ言語です。',
            'category' => 'markup',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($htmlLanguage, 'はじめに', 1, 'HTMLの基本と基本的なタグ', 'getting-started');

        $this->createExample($section1, $htmlLanguage, 'Hello HTML', 1,
            "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>HTML5 Boilerplate</title>\n</head>\n<body>\n    <h1>Hello world, hello Kizamu!</h1>\n</body>\n</html>",
            '基本的なHTML5ボイラープレート構造',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'コメント', 2,
            "<!-- this is a comment -->\n\n<!--\n    Or you can comment out a\n    large number of lines.\n-->",
            '単一行と複数行のコメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, '段落', 3,
            "<p>I'm from Kizamu</p>\n<p>Share reference cheat sheet.</p>",
            'テキスト段落要素',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'HTMLリンク', 4,
            "<a href=\"https://Kizamu.com\">Kizamu</a>\n<a href=\"mailto:email@example.com\">Email</a>\n<a href=\"tel:+12345678\">Call</a>\n<a href=\"sms:+12345678&body=ha%20ha\">Msg</a>",
            'メール、電話、SMSを含む様々なリンクタイプ',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, '画像タグ', 5,
            "<img loading=\"lazy\" src=\"https://xxx.png\" alt=\"Describe image here\" width=\"400\" height=\"400\">",
            '遅延読み込みと説明属性を持つ画像要素',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'テキストフォーマット', 6,
            "<b>Bold Text</b>\n<strong>This text is important</strong>\n<i>Italic Text</i>\n<em>This text is emphasized</em>\n<u>Underline Text</u>\n<pre>Pre-formatted Text</pre>\n<code>Source code</code>\n<del>Deleted text</del>\n<mark>Highlighted text (HTML5)</mark>\n<ins>Inserted text</ins>\n<sup>Makes text superscripted</sup>\n<sub>Makes text subscripted</sub>\n<small>Makes text smaller</small>\n<kbd>Ctrl</kbd>\n<blockquote>Text Block Quote</blockquote>",
            '様々なテキスト強調とフォーマットタグ',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, '見出し', 7,
            "<h1> This is Heading 1 </h1>\n<h2> This is Heading 2 </h2>\n<h3> This is Heading 3 </h3>\n<h4> This is Heading 4 </h4>\n<h5> This is Heading 5 </h5>\n<h6> This is Heading 6 </h6>",
            '6レベルの見出し階層（H1-H6）',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'セクション分割', 8,
            "<div>Division or Section</div>\n<span>Section of text within content</span>\n<p>Paragraph of Text</p>\n<br>\n<hr>",
            'ページコンテンツを整理するためのコンテナ要素：div、span、p、br、hr',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'インラインフレーム', 9,
            "<iframe title=\"New York\"\n    width=\"342\"\n    height=\"306\"\n    id=\"gmap_canvas\"\n    src=\"https://maps.google.com/maps?q=2880%20Broadway,%20New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed\"\n    scrolling=\"no\">\n</iframe>",
            'Webページ内に埋め込まれたコンテンツ',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'HTML内のJavaScript', 10,
            "<script type=\"text/javascript\">\n    let text = \"Hello Kizamu\";\n    alert(text);\n</script>",
            'JavaScriptコード用の埋め込みscriptタグ',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'HTML内のCSS', 11,
            "<style type=\"text/css\">\n    h1 {\n        color: purple;\n    }\n</style>",
            'CSSスタイリング用の埋め込みスタイルシート',
            null,
            'easy'
        );

        // Section 2: HTML5 Tags
        $section2 = $this->createSection($htmlLanguage, 'HTML5タグ', 2, 'セマンティックHTML5要素', 'html5-tags');

        $this->createExample($section2, $htmlLanguage, 'ドキュメント構造', 1,
            "<body>\n  <header>\n    <nav>...</nav>\n  </header>\n  <main>\n    <h1>Kizamu</h1>\n  </main>\n  <footer>\n    <p>©2023 Kizamu</p>\n  </footer>\n</body>",
            'セマンティックなページレイアウト要素：header、nav、main、footer',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'ヘッダーナビゲーション', 2,
            "<header>\n  <nav>\n    <ul>\n      <li><a href=\"#\">Edit Page</a></li>\n      <li><a href=\"#\">Twitter</a></li>\n      <li><a href=\"#\">Facebook</a></li>\n    </ul>\n  </nav>\n</header>",
            'header要素内のナビゲーションメニュー',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5動画', 3,
            "<video controls=\"\" width=\"100%\">\n    <source src=\"https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4\" type=\"video/mp4\">\n    Sorry, your browser doesn't support embedded videos.\n</video>",
            'コントロールとフォールバックメッセージ付きの動画埋め込み',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5音声', 4,
            "<audio controls\n    src=\"https://interactive-examples.mdn.mozilla.net/media/cc0-audio/t-rex-roar.mp3\">\n    Your browser does not support the audio element.\n</audio>",
            'コントロール付きの音声またはオーディオストリームの埋め込み',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Ruby', 5,
            "<ruby>\n  汉 <rp>(</rp><rt>hàn</rt><rp>)</rp>\n  字 <rp>(</rp><rt>zì</rt><rp>)</rp>\n</ruby>",
            '音声ガイド用のルビ注釈（例：中国語の発音）',
            null,
            'medium'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Progress', 6,
            "<progress value=\"50\" max=\"100\"></progress>",
            'プログレスバー可視化要素',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Mark', 7,
            "<p>I Love <mark>Kizamu</mark></p>",
            '強調のためのハイライトテキスト要素',
            null,
            'easy'
        );

        // Section 3: HTML Tables
        $section3 = $this->createSection($htmlLanguage, 'HTMLテーブル', 3, 'テーブル構造と整理', 'html-tables');

        $this->createExample($section3, $htmlLanguage, 'テーブルの例', 1,
            "<table>\n    <thead>\n        <tr>\n            <td>name</td>\n            <td>age</td>\n        </tr>\n    </thead>\n    <tbody>\n        <tr>\n            <td>Roberta</td>\n            <td>39</td>\n        </tr>\n        <tr>\n            <td>Oliver</td>\n            <td>25</td>\n        </tr>\n    </tbody>\n</table>",
            'ヘッダーと本文セクションを持つ基本的なテーブル',
            null,
            'easy'
        );

        // Section 4: HTML Lists
        $section4 = $this->createSection($htmlLanguage, 'HTMLリスト', 4, '順序付き、順序なし、定義リスト', 'html-lists');

        $this->createExample($section4, $htmlLanguage, '順序なしリスト', 1,
            "<ul>\n    <li>I'm an item</li>\n    <li>I'm another item</li>\n    <li>I'm another item</li>\n</ul>",
            '箇条書きリスト',
            null,
            'easy'
        );

        $this->createExample($section4, $htmlLanguage, '順序付きリスト', 2,
            "<ol>\n    <li>I'm the first item</li>\n    <li>I'm the second item</li>\n    <li>I'm the third item</li>\n</ol>",
            '番号付きリスト',
            null,
            'easy'
        );

        $this->createExample($section4, $htmlLanguage, '定義リスト', 3,
            "<dl>\n    <dt>A Term</dt>\n    <dd>Definition of a term</dd>\n    <dt>Another Term</dt>\n    <dd>Definition of another term</dd>\n</dl>",
            '用語とその定義',
            null,
            'easy'
        );

        // Section 5: HTML Forms
        $section5 = $this->createSection($htmlLanguage, 'HTMLフォーム', 5, 'フォーム要素と入力タイプ', 'html-forms');

        $this->createExample($section5, $htmlLanguage, 'フォームタグ', 1,
            "<form method=\"POST\" action=\"api/login\">\n  <label for=\"mail\">Email: </label>\n  <input type=\"email\" id=\"mail\" name=\"mail\">\n  <br/>\n  <label for=\"pw\">Password: </label>\n  <input type=\"password\" id=\"pw\" name=\"pw\">\n  <br/>\n  <input type=\"submit\" value=\"Login\">\n  <br/>\n  <input type=\"checkbox\" id=\"ck\" name=\"ck\">\n  <label for=\"ck\">Remember me</label>\n</form>",
            'メール、パスワード、チェックボックス入力を収集する完全なフォーム',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'ラベルタグ', 2,
            "<!-- Nested label -->\n<label>Click me \n<input type=\"text\" id=\"user\" name=\"name\"/>\n</label>\n\n<!-- 'for' attribute -->\n<label for=\"user\">Click me</label>\n<input id=\"user\" type=\"text\" name=\"name\"/>",
            'フォーム入力用のネストされたラベルと参照ラベルパターン',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Textarea', 3,
            "<textarea rows=\"2\" cols=\"30\" name=\"address\" id=\"address\"></textarea>",
            '複数行テキスト入力コントロール',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'ラジオボタン', 4,
            "<input type=\"radio\" name=\"gender\" id=\"m\">\n<label for=\"m\">Male</label>\n<input type=\"radio\" name=\"gender\" id=\"f\">\n<label for=\"f\">Female</label>",
            'グループから正確に1つのオプションを選択できる',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'チェックボックス', 5,
            "<input type=\"checkbox\" name=\"s\" id=\"soc\">\n<label for=\"soc\">Soccer</label>\n<input type=\"checkbox\" name=\"s\" id=\"bas\">\n<label for=\"bas\">Baseball</label>",
            '1つ以上のオプションを選択できる',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Selectタグ', 6,
            "<label for=\"city\">City:</label>\n<select name=\"city\" id=\"city\">\n    <option value=\"1\">Sydney</option>\n    <option value=\"2\">Melbourne</option>\n    <option value=\"3\">Cromwell</option>\n</select>",
            'オプションのドロップダウンリスト',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Fieldsetタグ', 7,
            "<fieldset>\n    <legend>Your favorite monster</legend>\n    <input type=\"radio\" id=\"kra\" name=\"m\">\n    <label for=\"kraken\">Kraken</label><br/>\n    <input type=\"radio\" id=\"sas\" name=\"m\">\n    <label for=\"sas\">Sasquatch</label>\n</fieldset>",
            '凡例タイトル付きのグループ化されたフォーム要素',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Datalistタグ', 8,
            "<label for=\"b\">Choose a browser: </label>\n<input list=\"list\" id=\"b\" name=\"browser\"/>\n<datalist id=\"list\">\n  <option value=\"Chrome\">\n  <option value=\"Firefox\">\n  <option value=\"Internet Explorer\">\n  <option value=\"Opera\">\n  <option value=\"Safari\">\n  <option value=\"Microsoft Edge\">\n</datalist>",
            'オートコンプリート付き入力フィールド用のHTML5事前定義オプション',
            null,
            'medium'
        );

        $this->createExample($section5, $htmlLanguage, '送信とリセットボタン', 9,
            "<form action=\"register.php\" method=\"post\">\n  <label for=\"foo\">Name:</label>\n  <input type=\"text\" name=\"name\" id=\"foo\">\n  <input type=\"submit\" value=\"Submit\">\n  <input type=\"reset\" value=\"Reset\">\n</form>",
            'フォーム送信とリセットコントロール',
            null,
            'easy'
        );

        // Section 6: HTML Meta Tags
        $section6 = $this->createSection($htmlLanguage, 'HTMLメタタグ', 6, 'メタデータとSEOタグ', 'html-meta-tags');

        $this->createExample($section6, $htmlLanguage, 'メタタグ', 1,
            "<meta charset=\"utf-8\">\n<title>···</title>\n<meta property=\"og:title\"  content=\"···\">\n<link rel=\"canonical\" href=\"https://···\">\n<meta name=\"description\" content=\"···\">\n<meta property=\"og:image\" content=\"https://···\">",
            '文字セット、タイトル、URL、説明、画像を含む基本的なメタデータ',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'Open Graph', 2,
            "<meta property=\"og:type\" content=\"website\">\n<meta property=\"og:locale\" content=\"en_CA\">\n<meta property=\"og:title\" content=\"HTML cheatsheet\">\n<meta property=\"og:url\" content=\"https://Kizamu.com/html\">\n<meta property=\"og:image\" content=\"https://xxx.com/image.jpg\">\n<meta property=\"og:site_name\" content=\"Name of your website\">\n<meta property=\"og:description\" content=\"Description of this page\">",
            'Facebook、Instagram、Pinterest、LinkedIn用のソーシャルメディアメタデータ',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'Twitter Cards', 3,
            "<meta name=\"twitter:card\" content=\"summary\">\n<meta name=\"twitter:site\" content=\"@FechinLi\">\n<meta name=\"twitter:title\" content=\"HTML cheatsheet\">\n<meta name=\"twitter:url\" content=\"https://Kizamu.com/html\">\n<meta name=\"twitter:description\" content=\"Description of this page\">\n<meta name=\"twitter:image\" content=\"https://xxx.com/image.jpg\">",
            '共有コンテンツカード用のTwitter固有のメタデータ',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'ジオタギング', 4,
            "<meta name=\"ICBM\" content=\"45.416667,-75.7\">\n<meta name=\"geo.position\" content=\"45.416667;-75.7\">\n<meta name=\"geo.region\" content=\"ca-on\">\n<meta name=\"geo.placename\" content=\"Ottawa\">",
            'コンテンツ用の地理的位置メタデータ',
            null,
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($htmlLanguage);
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
        $descLower = strtolower($description ?? '');

        // Add tags based on title and description
        if (str_contains($titleLower, 'form') || str_contains($titleLower, 'input') || str_contains($descLower, 'form')) {
            $tags[] = 'form';
        }
        if (str_contains($titleLower, 'table')) {
            $tags[] = 'table';
        }
        if (str_contains($titleLower, 'list')) {
            $tags[] = 'list';
        }
        if (str_contains($titleLower, 'meta') || str_contains($titleLower, 'seo') || str_contains($descLower, 'metadata')) {
            $tags[] = 'seo';
            $tags[] = 'metadata';
        }
        if (str_contains($titleLower, 'html5') || str_contains($titleLower, 'video') || str_contains($titleLower, 'audio')) {
            $tags[] = 'html5';
        }
        if (str_contains($titleLower, 'semantic') || str_contains($descLower, 'semantic')) {
            $tags[] = 'semantic';
        }
        if (str_contains($titleLower, 'text') || str_contains($titleLower, 'heading') || str_contains($titleLower, 'paragraph')) {
            $tags[] = 'text';
        }
        if (str_contains($titleLower, 'link') || str_contains($titleLower, 'image')) {
            $tags[] = 'media';
        }

        // Add basic tags
        $tags[] = 'html';
        $tags[] = 'web';

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
