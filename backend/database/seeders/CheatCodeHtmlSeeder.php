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
     * Seed HTML cheat code data from quickref.me
     * Reference: https://quickref.me/html
     */
    public function run(): void
    {
        // Create HTML Language
        $htmlLanguage = CheatCodeLanguage::create([
            'name' => 'html',
            'display_name' => 'HTML',
            'slug' => 'html',
            'color' => '#E34F26',
            'description' => 'HTML (HyperText Markup Language) is the standard markup language for creating web pages and web applications.',
            'category' => 'markup',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($htmlLanguage, 'Getting Started', 1, 'HTML basics and fundamental tags');

        $this->createExample($section1, $htmlLanguage, 'Hello HTML', 1,
            "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>HTML5 Boilerplate</title>\n</head>\n<body>\n    <h1>Hello world, hello QuickRef.ME!</h1>\n</body>\n</html>",
            'Basic HTML5 boilerplate structure',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Comment', 2,
            "<!-- this is a comment -->\n\n<!--\n    Or you can comment out a\n    large number of lines.\n-->",
            'Single and multi-line comment syntax',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Paragraph', 3,
            "<p>I'm from QuickRef.ME</p>\n<p>Share quick reference cheat sheet.</p>",
            'Text paragraph element',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'HTML Link', 4,
            "<a href=\"https://quickref.me\">QuickRef</a>\n<a href=\"mailto:email@example.com\">Email</a>\n<a href=\"tel:+12345678\">Call</a>\n<a href=\"sms:+12345678&body=ha%20ha\">Msg</a>",
            'Various link types including email, phone, and SMS',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Image Tag', 5,
            "<img loading=\"lazy\" src=\"https://xxx.png\" alt=\"Describe image here\" width=\"400\" height=\"400\">",
            'Image element with lazy loading and descriptive attributes',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Text Formatting', 6,
            "<b>Bold Text</b>\n<strong>This text is important</strong>\n<i>Italic Text</i>\n<em>This text is emphasized</em>\n<u>Underline Text</u>\n<pre>Pre-formatted Text</pre>\n<code>Source code</code>\n<del>Deleted text</del>\n<mark>Highlighted text (HTML5)</mark>\n<ins>Inserted text</ins>\n<sup>Makes text superscripted</sup>\n<sub>Makes text subscripted</sub>\n<small>Makes text smaller</small>\n<kbd>Ctrl</kbd>\n<blockquote>Text Block Quote</blockquote>",
            'Various text emphasis and formatting tags',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Headings', 7,
            "<h1> This is Heading 1 </h1>\n<h2> This is Heading 2 </h2>\n<h3> This is Heading 3 </h3>\n<h4> This is Heading 4 </h4>\n<h5> This is Heading 5 </h5>\n<h6> This is Heading 6 </h6>",
            'Six levels of heading hierarchy (H1-H6)',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Section Divisions', 8,
            "<div>Division or Section</div>\n<span>Section of text within content</span>\n<p>Paragraph of Text</p>\n<br>\n<hr>",
            'Container elements for organizing page content: div, span, p, br, hr',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'Inline Frame', 9,
            "<iframe title=\"New York\"\n    width=\"342\"\n    height=\"306\"\n    id=\"gmap_canvas\"\n    src=\"https://maps.google.com/maps?q=2880%20Broadway,%20New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed\"\n    scrolling=\"no\">\n</iframe>",
            'Embedded content within a webpage',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'JavaScript in HTML', 10,
            "<script type=\"text/javascript\">\n    let text = \"Hello QuickRef.ME\";\n    alert(text);\n</script>",
            'Embedded script tag for JavaScript code',
            null,
            'easy'
        );

        $this->createExample($section1, $htmlLanguage, 'CSS in HTML', 11,
            "<style type=\"text/css\">\n    h1 {\n        color: purple;\n    }\n</style>",
            'Embedded stylesheet for CSS styling',
            null,
            'easy'
        );

        // Section 2: HTML5 Tags
        $section2 = $this->createSection($htmlLanguage, 'HTML5 Tags', 2, 'Semantic HTML5 elements');

        $this->createExample($section2, $htmlLanguage, 'Document Structure', 1,
            "<body>\n  <header>\n    <nav>...</nav>\n  </header>\n  <main>\n    <h1>QuickRef.ME</h1>\n  </main>\n  <footer>\n    <p>©2023 QuickRef.ME</p>\n  </footer>\n</body>",
            'Semantic page layout elements: header, nav, main, footer',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'Header Navigation', 2,
            "<header>\n  <nav>\n    <ul>\n      <li><a href=\"#\">Edit Page</a></li>\n      <li><a href=\"#\">Twitter</a></li>\n      <li><a href=\"#\">Facebook</a></li>\n    </ul>\n  </nav>\n</header>",
            'Navigation menu within header element',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Video', 3,
            "<video controls=\"\" width=\"100%\">\n    <source src=\"https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4\" type=\"video/mp4\">\n    Sorry, your browser doesn't support embedded videos.\n</video>",
            'Embeds video with controls and fallback message',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Audio', 4,
            "<audio controls\n    src=\"https://interactive-examples.mdn.mozilla.net/media/cc0-audio/t-rex-roar.mp3\">\n    Your browser does not support the audio element.\n</audio>",
            'Embeds sound or audio stream with controls',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Ruby', 5,
            "<ruby>\n  汉 <rp>(</rp><rt>hàn</rt><rp>)</rp>\n  字 <rp>(</rp><rt>zì</rt><rp>)</rp>\n</ruby>",
            'Ruby annotation for phonetic guides (e.g., Chinese pronunciation)',
            null,
            'medium'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Progress', 6,
            "<progress value=\"50\" max=\"100\"></progress>",
            'Progress bar visualization element',
            null,
            'easy'
        );

        $this->createExample($section2, $htmlLanguage, 'HTML5 Mark', 7,
            "<p>I Love <mark>QuickRef.ME</mark></p>",
            'Highlighted text element for emphasis',
            null,
            'easy'
        );

        // Section 3: HTML Tables
        $section3 = $this->createSection($htmlLanguage, 'HTML Tables', 3, 'Table structures and organization');

        $this->createExample($section3, $htmlLanguage, 'Table Example', 1,
            "<table>\n    <thead>\n        <tr>\n            <td>name</td>\n            <td>age</td>\n        </tr>\n    </thead>\n    <tbody>\n        <tr>\n            <td>Roberta</td>\n            <td>39</td>\n        </tr>\n        <tr>\n            <td>Oliver</td>\n            <td>25</td>\n        </tr>\n    </tbody>\n</table>",
            'Basic table with header and body sections',
            null,
            'easy'
        );

        // Section 4: HTML Lists
        $section4 = $this->createSection($htmlLanguage, 'HTML Lists', 4, 'Ordered, unordered, and definition lists');

        $this->createExample($section4, $htmlLanguage, 'Unordered List', 1,
            "<ul>\n    <li>I'm an item</li>\n    <li>I'm another item</li>\n    <li>I'm another item</li>\n</ul>",
            'Bulleted list of items',
            null,
            'easy'
        );

        $this->createExample($section4, $htmlLanguage, 'Ordered List', 2,
            "<ol>\n    <li>I'm the first item</li>\n    <li>I'm the second item</li>\n    <li>I'm the third item</li>\n</ol>",
            'Numbered list of items',
            null,
            'easy'
        );

        $this->createExample($section4, $htmlLanguage, 'Definition List', 3,
            "<dl>\n    <dt>A Term</dt>\n    <dd>Definition of a term</dd>\n    <dt>Another Term</dt>\n    <dd>Definition of another term</dd>\n</dl>",
            'Terms with their definitions',
            null,
            'easy'
        );

        // Section 5: HTML Forms
        $section5 = $this->createSection($htmlLanguage, 'HTML Forms', 5, 'Form elements and input types');

        $this->createExample($section5, $htmlLanguage, 'Form Tags', 1,
            "<form method=\"POST\" action=\"api/login\">\n  <label for=\"mail\">Email: </label>\n  <input type=\"email\" id=\"mail\" name=\"mail\">\n  <br/>\n  <label for=\"pw\">Password: </label>\n  <input type=\"password\" id=\"pw\" name=\"pw\">\n  <br/>\n  <input type=\"submit\" value=\"Login\">\n  <br/>\n  <input type=\"checkbox\" id=\"ck\" name=\"ck\">\n  <label for=\"ck\">Remember me</label>\n</form>",
            'Complete form collecting email, password, and checkbox inputs',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Label Tags', 2,
            "<!-- Nested label -->\n<label>Click me \n<input type=\"text\" id=\"user\" name=\"name\"/>\n</label>\n\n<!-- 'for' attribute -->\n<label for=\"user\">Click me</label>\n<input id=\"user\" type=\"text\" name=\"name\"/>",
            'Nested and referenced label patterns for form inputs',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Textarea', 3,
            "<textarea rows=\"2\" cols=\"30\" name=\"address\" id=\"address\"></textarea>",
            'Multi-line text input control',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Radio Buttons', 4,
            "<input type=\"radio\" name=\"gender\" id=\"m\">\n<label for=\"m\">Male</label>\n<input type=\"radio\" name=\"gender\" id=\"f\">\n<label for=\"f\">Female</label>",
            'Allows user to select exactly one option from a group',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Checkboxes', 5,
            "<input type=\"checkbox\" name=\"s\" id=\"soc\">\n<label for=\"soc\">Soccer</label>\n<input type=\"checkbox\" name=\"s\" id=\"bas\">\n<label for=\"bas\">Baseball</label>",
            'Allows user to select one or more options',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Select Tags', 6,
            "<label for=\"city\">City:</label>\n<select name=\"city\" id=\"city\">\n    <option value=\"1\">Sydney</option>\n    <option value=\"2\">Melbourne</option>\n    <option value=\"3\">Cromwell</option>\n</select>",
            'Dropdown list of options',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Fieldset Tags', 7,
            "<fieldset>\n    <legend>Your favorite monster</legend>\n    <input type=\"radio\" id=\"kra\" name=\"m\">\n    <label for=\"kraken\">Kraken</label><br/>\n    <input type=\"radio\" id=\"sas\" name=\"m\">\n    <label for=\"sas\">Sasquatch</label>\n</fieldset>",
            'Grouped form elements with legend title',
            null,
            'easy'
        );

        $this->createExample($section5, $htmlLanguage, 'Datalist Tags', 8,
            "<label for=\"b\">Choose a browser: </label>\n<input list=\"list\" id=\"b\" name=\"browser\"/>\n<datalist id=\"list\">\n  <option value=\"Chrome\">\n  <option value=\"Firefox\">\n  <option value=\"Internet Explorer\">\n  <option value=\"Opera\">\n  <option value=\"Safari\">\n  <option value=\"Microsoft Edge\">\n</datalist>",
            'HTML5 pre-defined options for input field with autocomplete',
            null,
            'medium'
        );

        $this->createExample($section5, $htmlLanguage, 'Submit and Reset Buttons', 9,
            "<form action=\"register.php\" method=\"post\">\n  <label for=\"foo\">Name:</label>\n  <input type=\"text\" name=\"name\" id=\"foo\">\n  <input type=\"submit\" value=\"Submit\">\n  <input type=\"reset\" value=\"Reset\">\n</form>",
            'Form submission and reset controls',
            null,
            'easy'
        );

        // Section 6: HTML Meta Tags
        $section6 = $this->createSection($htmlLanguage, 'HTML Meta Tags', 6, 'Metadata and SEO tags');

        $this->createExample($section6, $htmlLanguage, 'Meta Tags', 1,
            "<meta charset=\"utf-8\">\n<title>···</title>\n<meta property=\"og:title\"  content=\"···\">\n<link rel=\"canonical\" href=\"https://···\">\n<meta name=\"description\" content=\"···\">\n<meta property=\"og:image\" content=\"https://···\">",
            'Essential metadata including charset, title, URL, description, and image',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'Open Graph', 2,
            "<meta property=\"og:type\" content=\"website\">\n<meta property=\"og:locale\" content=\"en_CA\">\n<meta property=\"og:title\" content=\"HTML cheatsheet\">\n<meta property=\"og:url\" content=\"https://quickref.me/html\">\n<meta property=\"og:image\" content=\"https://xxx.com/image.jpg\">\n<meta property=\"og:site_name\" content=\"Name of your website\">\n<meta property=\"og:description\" content=\"Description of this page\">",
            'Social media metadata for Facebook, Instagram, Pinterest, and LinkedIn',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'Twitter Cards', 3,
            "<meta name=\"twitter:card\" content=\"summary\">\n<meta name=\"twitter:site\" content=\"@FechinLi\">\n<meta name=\"twitter:title\" content=\"HTML cheatsheet\">\n<meta name=\"twitter:url\" content=\"https://quickref.me/html\">\n<meta name=\"twitter:description\" content=\"Description of this page\">\n<meta name=\"twitter:image\" content=\"https://xxx.com/image.jpg\">",
            'Twitter-specific metadata for shared content cards',
            null,
            'medium'
        );

        $this->createExample($section6, $htmlLanguage, 'Geotagging', 4,
            "<meta name=\"ICBM\" content=\"45.416667,-75.7\">\n<meta name=\"geo.position\" content=\"45.416667;-75.7\">\n<meta name=\"geo.region\" content=\"ca-on\">\n<meta name=\"geo.placename\" content=\"Ottawa\">",
            'Geographic location metadata for content',
            null,
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($htmlLanguage);
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
