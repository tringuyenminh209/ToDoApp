# CheatCode HTML Seeder - Detailed Documentation

## 📋 Overview

**File:** `CheatCodeHtmlSeeder.php`
**Source:** https://quickref.me/html
**Language:** HTML (HyperText Markup Language)
**Category:** Markup Language
**Total Sections:** 6
**Total Examples:** 35
**Difficulty:** 85% Easy, 15% Medium

---

## 📦 Language Configuration

```php
CheatCodeLanguage::create([
    'name' => 'html',
    'display_name' => 'HTML',
    'slug' => 'html',
    'color' => '#E34F26',  // HTML5 official color
    'description' => 'HTML (HyperText Markup Language) is the standard markup language for creating web pages and web applications.',
    'category' => 'markup',  // Not 'programming'
    'popularity' => 95,
    'is_active' => true,
    'sort_order' => 10,
]);
```

---

## 📚 Sections & Examples

### 1. Getting Started (11 examples)

Basic HTML tags and structure fundamentals.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Hello HTML | Basic HTML5 boilerplate structure | Easy |
| 2 | Comment | Single and multi-line comment syntax | Easy |
| 3 | Paragraph | Text paragraph element | Easy |
| 4 | HTML Link | Various link types (web, email, phone, SMS) | Easy |
| 5 | Image Tag | Image with lazy loading and attributes | Easy |
| 6 | Text Formatting | Bold, italic, underline, mark, code, etc. | Easy |
| 7 | Headings | Six heading levels (H1-H6) | Easy |
| 8 | Section Divisions | div, span, p, br, hr elements | Easy |
| 9 | Inline Frame | iframe for embedded content | Easy |
| 10 | JavaScript in HTML | Embedded script tag | Easy |
| 11 | CSS in HTML | Embedded style tag | Easy |

**Example Code:**
```html
<!-- Hello HTML - Full Boilerplate -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML5 Boilerplate</title>
</head>
<body>
    <h1>Hello world, hello QuickRef.ME!</h1>
</body>
</html>
```

---

### 2. HTML5 Tags (7 examples)

Semantic HTML5 elements for modern web development.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Document Structure | header, nav, main, footer | Easy |
| 2 | Header Navigation | Navigation menu structure | Easy |
| 3 | HTML5 Video | Video element with controls | Easy |
| 4 | HTML5 Audio | Audio element with controls | Easy |
| 5 | HTML5 Ruby | Ruby annotation for phonetics | Medium |
| 6 | HTML5 Progress | Progress bar element | Easy |
| 7 | HTML5 Mark | Highlighted text element | Easy |

**Example Code:**
```html
<!-- Semantic Document Structure -->
<body>
  <header>
    <nav>...</nav>
  </header>
  <main>
    <h1>QuickRef.ME</h1>
  </main>
  <footer>
    <p>©2023 QuickRef.ME</p>
  </footer>
</body>
```

---

### 3. HTML Tables (1 example)

Table structure with thead and tbody.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Table Example | Basic table with header and body | Easy |

**Example Code:**
```html
<table>
    <thead>
        <tr>
            <td>name</td>
            <td>age</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Roberta</td>
            <td>39</td>
        </tr>
        <tr>
            <td>Oliver</td>
            <td>25</td>
        </tr>
    </tbody>
</table>
```

---

### 4. HTML Lists (3 examples)

Different list types for organizing content.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Unordered List | Bulleted list (ul/li) | Easy |
| 2 | Ordered List | Numbered list (ol/li) | Easy |
| 3 | Definition List | Term/definition pairs (dl/dt/dd) | Easy |

**Example Code:**
```html
<!-- Unordered List -->
<ul>
    <li>I'm an item</li>
    <li>I'm another item</li>
</ul>

<!-- Ordered List -->
<ol>
    <li>I'm the first item</li>
    <li>I'm the second item</li>
</ol>

<!-- Definition List -->
<dl>
    <dt>A Term</dt>
    <dd>Definition of a term</dd>
</dl>
```

---

### 5. HTML Forms (9 examples)

Comprehensive form elements and input types.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Form Tags | Complete form with email, password, checkbox | Easy |
| 2 | Label Tags | Nested and referenced label patterns | Easy |
| 3 | Textarea | Multi-line text input | Easy |
| 4 | Radio Buttons | Single selection from group | Easy |
| 5 | Checkboxes | Multiple selections allowed | Easy |
| 6 | Select Tags | Dropdown list options | Easy |
| 7 | Fieldset Tags | Grouped form elements with legend | Easy |
| 8 | Datalist Tags | HTML5 autocomplete input | Medium |
| 9 | Submit and Reset Buttons | Form submission controls | Easy |

**Example Code:**
```html
<!-- Complete Login Form -->
<form method="POST" action="api/login">
  <label for="mail">Email: </label>
  <input type="email" id="mail" name="mail">
  <br/>
  <label for="pw">Password: </label>
  <input type="password" id="pw" name="pw">
  <br/>
  <input type="submit" value="Login">
  <br/>
  <input type="checkbox" id="ck" name="ck">
  <label for="ck">Remember me</label>
</form>
```

---

### 6. HTML Meta Tags (4 examples)

SEO and metadata for social media sharing.

| # | Title | Description | Difficulty |
|---|-------|-------------|------------|
| 1 | Meta Tags | Essential metadata (charset, title, description) | Medium |
| 2 | Open Graph | Facebook, Instagram, Pinterest, LinkedIn | Medium |
| 3 | Twitter Cards | Twitter-specific metadata | Medium |
| 4 | Geotagging | Geographic location metadata | Medium |

**Example Code:**
```html
<!-- Essential Meta Tags -->
<meta charset="utf-8">
<title>HTML Cheatsheet</title>
<meta property="og:title" content="HTML Cheatsheet">
<link rel="canonical" href="https://quickref.me/html">
<meta name="description" content="Quick reference for HTML">
<meta property="og:image" content="https://xxx.com/image.jpg">

<!-- Open Graph for Social Media -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_CA">
<meta property="og:title" content="HTML cheatsheet">
<meta property="og:url" content="https://quickref.me/html">
<meta property="og:site_name" content="QuickRef.ME">
```

---

## 🏷️ Auto-Generated Tags

The seeder automatically generates relevant tags based on content:

| Tag | Applied When | Purpose |
|-----|--------------|---------|
| `html` | All examples | Base language tag |
| `web` | All examples | Web development context |
| `form` | Form-related examples | Form inputs, labels |
| `table` | Table examples | Table structures |
| `list` | List examples | ul, ol, dl elements |
| `seo` | Meta tag examples | SEO and metadata |
| `metadata` | Meta tag examples | Page metadata |
| `html5` | HTML5-specific features | video, audio, semantic tags |
| `semantic` | Semantic HTML | header, nav, main, footer |
| `text` | Text formatting | Headings, paragraphs |
| `media` | Media elements | Images, links |

---

## 📊 Statistics

```
Total Sections:     6
Total Examples:     35
Total Lines:        ~450

Difficulty Breakdown:
├── Easy:          30 examples (85.7%)
└── Medium:         5 examples (14.3%)

Examples by Section:
├── Getting Started:    11 (31.4%)
├── HTML5 Tags:          7 (20.0%)
├── HTML Tables:         1 (2.9%)
├── HTML Lists:          3 (8.6%)
├── HTML Forms:          9 (25.7%)
└── HTML Meta Tags:      4 (11.4%)
```

---

## 🎯 Key Features

### 1. **Semantic HTML Focus**
Emphasizes modern HTML5 semantic elements:
- `<header>`, `<nav>`, `<main>`, `<footer>`
- `<article>`, `<section>`, `<aside>`
- Better accessibility and SEO

### 2. **Comprehensive Form Coverage**
All major form input types:
- Text, email, password
- Radio buttons, checkboxes
- Select dropdowns
- Datalist (HTML5 autocomplete)
- Fieldset grouping

### 3. **SEO & Metadata**
Complete metadata examples:
- Essential meta tags
- Open Graph for social media
- Twitter Cards
- Geotagging

### 4. **Modern HTML5 Features**
- Video and audio elements
- Progress bars
- Ruby annotations
- Mark (highlighting)

---

## 🚀 Usage Examples

### Run HTML Seeder Only
```bash
cd backend
php artisan db:seed --class=CheatCodeHtmlSeeder
```

### Verify Data
```sql
-- Check HTML language created
SELECT * FROM cheat_code_languages WHERE name = 'html';

-- Check sections
SELECT * FROM cheat_code_sections WHERE language_id = (
    SELECT id FROM cheat_code_languages WHERE name = 'html'
);

-- Check example count
SELECT COUNT(*) FROM code_examples WHERE language_id = (
    SELECT id FROM cheat_code_languages WHERE name = 'html'
);
```

### Expected Output
```
Language: HTML (#E34F26)
├── Getting Started (11 examples)
├── HTML5 Tags (7 examples)
├── HTML Tables (1 example)
├── HTML Lists (3 examples)
├── HTML Forms (9 examples)
└── HTML Meta Tags (4 examples)

Total: 6 sections, 35 examples
```

---

## 🔍 Sample Data Verification

After running the seeder, you can verify with:

```php
// In tinker: php artisan tinker

$html = \App\Models\CheatCodeLanguage::where('name', 'html')->first();

echo "Language: {$html->display_name}\n";
echo "Sections: {$html->sections_count}\n";
echo "Examples: {$html->examples_count}\n";
echo "Category: {$html->category}\n";

// Get all section titles
$html->sections->pluck('title');
// ["Getting Started", "HTML5 Tags", "HTML Tables", ...]

// Get form examples
$formSection = $html->sections()->where('slug', 'html-forms')->first();
$formSection->examples->pluck('title');
// ["Form Tags", "Label Tags", "Textarea", ...]
```

---

## 🎨 Visual Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickRef HTML Examples</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#forms">Forms</a></li>
                <li><a href="#lists">Lists</a></li>
                <li><a href="#tables">Tables</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>HTML Cheat Sheet</h1>
        <p>Quick reference for <mark>HTML elements</mark></p>

        <section id="forms">
            <h2>Forms</h2>
            <form>
                <input type="email" placeholder="Email">
                <input type="submit" value="Submit">
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 QuickRef.ME</p>
    </footer>
</body>
</html>
```

---

## 🔮 Future Enhancements

### Potential Additions:
- [ ] HTML accessibility (ARIA attributes)
- [ ] Canvas examples
- [ ] SVG basics
- [ ] Web components
- [ ] HTML templates
- [ ] Picture element (responsive images)
- [ ] Details/Summary (disclosure widgets)

---

## 📚 References

- **Official:** https://html.spec.whatwg.org/
- **MDN Docs:** https://developer.mozilla.org/en-US/docs/Web/HTML
- **Source:** https://quickref.me/html
- **HTML5 Spec:** https://www.w3.org/TR/html52/

---

**Created:** 2025-01-06
**Language:** HTML
**Version:** 1.0
**Maintainer:** DOL LEAF Development Team
