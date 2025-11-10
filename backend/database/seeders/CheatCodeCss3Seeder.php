<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeCss3Seeder extends Seeder
{
    /**
     * Seed CSS3 cheat code data from doleaf
     * Reference: https://doleaf.com/css3
     */
    public function run(): void
    {
        // Create CSS3 Language
        $cssLanguage = CheatCodeLanguage::create([
            'name' => 'css3',
            'display_name' => 'CSS3',
            'slug' => 'css3',
            'icon' => 'ic_css',
            'color' => '#1572B6',
            'description' => 'セレクタ構文、プロパティ、単位、その他の有用な情報をリストアップしたCSSのリファレンスチートシート。',
            'category' => 'markup',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 7,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($cssLanguage, 'はじめに', 1, 'CSS3の基本と導入', 'getting-started');

        $this->createExample($section1, $cssLanguage, '外部スタイルシート', 1,
            "<link href=\"./path/to/stylesheet/style.css\" rel=\"stylesheet\" type=\"text/css\">",
            '外部CSSファイルのリンク',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, '内部スタイルシート', 2,
            "<style>\nbody {\n    background-color: linen;\n}\n</style>",
            'HTML内の内部CSS',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'インラインスタイル', 3,
            "<h2 style=\"text-align: center;\">Centered text</h2>\n\n<p style=\"color: blue; font-size: 18px;\">Blue, 18-point text</p>",
            'インラインCSSスタイル',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'クラスの追加', 4,
            "<div class=\"classname\"></div>\n<div class=\"class1 ... classn\"></div>",
            'HTML要素へのCSSクラスの追加',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, '!important', 5,
            ".post-title {\n    color: blue !important;\n}",
            'スタイルを上書きするための!importantの使用',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'セレクタ', 6,
            "h1 { } \n#job-title { }\ndiv.hero { }\ndiv > p { }",
            '基本的なCSSセレクタ',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'テキストの色', 7,
            "color: #2a2aff;\ncolor: green;\ncolor: rgb(34, 12, 64, 0.6);\ncolor: hsla(30 100% 50% / 0.6);",
            'テキストの色の値',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, '背景', 8,
            "background-color: blue;\nbackground-image: url(\"nyan-cat.gif\");\nbackground-image: url(\"../image.png\");",
            '背景プロパティ',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'フォント', 9,
            ".page-title {\n    font-weight: bold;\n    font-size: 30px;\n    font-family: \"Courier New\";\n}",
            'フォントプロパティ',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, '位置', 10,
            ".box {\n    position: relative;\n    top: 20px;\n    left: 20px;\n}",
            '位置プロパティ',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'アニメーション', 11,
            "animation: 300ms linear 0s infinite;\n\nanimation: bounce 300ms linear infinite;",
            'CSSアニメーションの短縮記法',
            null,
            'medium'
        );

        $this->createExample($section1, $cssLanguage, 'コメント', 12,
            "/* This is a single line comment */\n\n/* This is a \n   multi-line comment */",
            'CSSコメント構文',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'Flexレイアウト', 13,
            "div {\n    display: flex;\n    justify-content: center;\n}\ndiv {\n    display: flex;\n    justify-content: flex-start;\n}",
            'Flexboxレイアウト',
            null,
            'easy'
        );

        $this->createExample($section1, $cssLanguage, 'Gridレイアウト', 14,
            "#container {\n  display: grid;\n  grid: repeat(2, 60px) / auto-flow 80px;\n}\n\n#container > div {\n  background-color: #8ca0ff;\n  width: 50px;\n  height: 50px;\n}",
            'CSS Gridレイアウト',
            null,
            'medium'
        );

        $this->createExample($section1, $cssLanguage, '変数とカウンター', 15,
            "counter-set: subsection;\ncounter-increment: subsection;\ncounter-reset: subsection 0;\n\n:root {\n  --bg-color: brown;\n}\nelement {\n  background-color: var(--bg-color);\n}",
            'CSS変数とカウンター',
            null,
            'medium'
        );

        // Section 2: CSS Selectors
        $section2 = $this->createSection($cssLanguage, 'CSSセレクタ', 2, 'CSSセレクタ構文', 'css-selectors');

        $this->createExample($section2, $cssLanguage, 'グループセレクタ', 1,
            "h1, h2 {\n    color: red;\n}",
            'セレクタのグループ化',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, 'チェーンセレクタ', 2,
            "h3.section-heading {\n    color: blue;\n}",
            'セレクタのチェーン',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '属性セレクタ', 3,
            "div[attribute=\"SomeValue\"] {\n    background-color: red;\n}",
            '属性セレクタ',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '最初の子要素セレクタ', 4,
            "p:first-child {\n    font-weight: bold;\n}",
            '最初の子要素疑似クラス',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '子要素なしセレクタ', 5,
            ".box:empty {\n  background: lime;\n  height: 80px;\n  width: 80px;\n}",
            '空の疑似クラス',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '基本セレクタの表', 6,
            "| *         | All elements                |\n| ---------- | --------------------------- |\n| div        | All div tags                |\n| .classname | All elements with class     |\n| #idname    | Element with ID             |\n| div,p      | All divs and paragraphs     |\n| #idname *  | All elements inside #idname |",
            '基本セレクタの種類',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '結合子の表', 7,
            "| Selector      | Description                       |\n| ------------- | --------------------------------- |\n| div.classname | Div with certain classname        |\n| div#idname    | Div with certain ID               |\n| div p         | Paragraphs inside divs            |\n| div > p       | All p tags one level deep in div  |\n| div + p       | P tags immediately after div      |\n| div ~ p       | P tags preceded by div            |",
            '結合子セレクタ',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '属性セレクタの表', 8,
            "| a[target]           | With a target attribute |\n| --------------------- | ----------------------- |\n| a[target=\"_blank\"] | Open in new tab         |\n| a[href^=\"/index\"]   | Starts with /index      |\n| [class|=\"chair\"]   | Starts with chair       |\n| [class*=\"chair\"]   | containing chair        |\n| [title~=\"chair\"]   | Contains the word chair |\n| a[href$=\".doc\"]     | Ends with .doc          |\n| [type=\"button\"]     | Specified type          |",
            '属性セレクタ構文',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, 'ユーザーアクション疑似クラス', 9,
            "a:link    /* Link in normal state    */\na:active  /* Link in clicked state   */\na:hover   /* Link with mouse over it */\na:visited /* Visited link            */",
            'リンク疑似クラス',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '疑似クラスの表', 10,
            "| p::after        | Add content after p                                    |\n| p::before       | Add content before p                                   |\n| p::first-letter | First letter in p                                      |\n| p::first-line   | First line in p                                        |\n| ::selection     | Selected by user                                       |\n| ::placeholder   | Placeholder attribute                                 |\n| :root           | Documents root element                                |\n| :target         | Highlight active anchor                                |\n| div:empty       | Element with no children                              |\n| p:lang(en)      | P with en language attribute                          |\n| :not(span)      | Element that's not a span                             |",
            '疑似クラスと疑似要素',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '入力疑似クラス', 11,
            "input:checked       /* Checked inputs                                    */\ninput:disabled      /* Disabled inputs                                     */\ninput:enabled       /* Enabled inputs                                      */\ninput:focus         /* Input has focus                                      */\ninput:in-range      /* Value in range                                       */\ninput:out-of-range  /* Input value out of range                              */\ninput:valid         /* Input with valid value                                */\ninput:invalid       /* Input with invalid value                              */\ninput:optional      /* No required attribute                                 */\ninput:required      /* Input with required attribute                          */\ninput:read-only     /* With readonly attribute                                */\ninput:read-write    /* No readonly attribute                                 */\ninput:indeterminate /* With indeterminate state                               */",
            '入力疑似クラス',
            null,
            'easy'
        );

        $this->createExample($section2, $cssLanguage, '構造的疑似クラス', 12,
            "p:first-child         /* First child                */\np:last-child          /* Last child                 */\np:first-of-type       /* First of some type         */\np:last-of-type        /* Last of some type          */\np:nth-child(2)        /* Second child of its parent */\np:nth-child(3n+2)     /* Nth-child (an + b) formula */\np:nth-last-child(2)   /* Second child from behind   */\np:nth-of-type(2)      /* Second p of its parent     */\np:nth-last-of-type(2) /* Second p from behind      */",
            '構造的疑似クラス',
            null,
            'medium'
        );

        // Section 3: CSS Animation
        $section3 = $this->createSection($cssLanguage, 'CSSアニメーション', 3, 'CSSアニメーションとキーフレーム', 'css-animations');

        $this->createExample($section3, $cssLanguage, 'アニメーションプロパティ', 1,
            "animation:                 /* (shorthand)                                          */\nanimation-name:            /* <name>                                                 */\nanimation-duration:        /* <time>ms                                               */\nanimation-timing-function: /* ease / linear / ease-in / ease-out / ease-in-out       */\nanimation-delay:           /* <time>ms                                               */\nanimation-iteration-count: /* infinite / <number>                                    */\nanimation-direction:       /* normal / reverse / alternate / alternate-reverse       */\nanimation-fill-mode:       /* none / forwards / backwards / both / initial / inherit */\nanimation-play-state:      /* running / paused                                        */",
            'アニメーションプロパティ',
            null,
            'medium'
        );

        $this->createExample($section3, $cssLanguage, 'アニメーションの例', 2,
            "/* @keyframes duration | timing-function | delay |\n   iteration-count | direction | fill-mode | play-state | name */\nanimation: 3s ease-in 1s 2 reverse both paused slidein;\n\n/* @keyframes duration | timing-function | delay | name */\nanimation: 3s linear 1s slidein;\n\n/* @keyframes duration | name */\nanimation: 3s slidein;\n\nanimation: 4s linear 0s infinite alternate move_eye;\nanimation: bounce 300ms linear 0s infinite normal;\nanimation: bounce 300ms linear infinite;\nanimation: bounce 300ms linear infinite alternate-reverse;\nanimation: bounce 300ms linear 2s infinite alternate-reverse forwards normal;",
            'アニメーションの短縮記法の例',
            null,
            'medium'
        );

        $this->createExample($section3, $cssLanguage, 'JavaScriptイベント', 3,
            ".one('webkitAnimationEnd oanimationend msAnimationEnd animationend')",
            'JavaScriptアニメーション終了イベント',
            null,
            'medium'
        );

        // Section 4: CSS Flexbox
        $section4 = $this->createSection($cssLanguage, 'CSS Flexbox', 4, 'Flexboxレイアウトシステム', 'css-flexbox');

        $this->createExample($section4, $cssLanguage, '簡単な例', 1,
            ".container {\n  display: flex;\n}\n\n.container > div {\n  flex: 1 1 auto;\n}",
            '基本的なflexbox設定',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, 'コンテナ - Display', 2,
            ".container {\n  display: flex;\n  display: inline-flex;\n}",
            'Flexbox displayの値',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, 'コンテナ - flex-direction', 3,
            ".container {\n  flex-direction: row;            /* ltr - default */\n  flex-direction: row-reverse;    /* rtl */\n  flex-direction: column;         /* top-bottom */\n  flex-direction: column-reverse; /* bottom-top */\n}",
            'Flex directionのオプション',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, 'コンテナ - flex-wrap', 4,
            ".container {\n  flex-wrap: nowrap; /* one-line */\n  flex-wrap: wrap;   /* multi-line */\n}",
            'Flex wrapのオプション',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, 'コンテナ - align-items', 5,
            ".container {\n  align-items: flex-start; /* vertical-align to top */\n  align-items: flex-end;   /* vertical-align to bottom */\n  align-items: center;     /* vertical-align to center */\n  align-items: stretch;    /* same height on all (default) */\n}",
            'Align itemsのオプション',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, 'コンテナ - justify-content', 6,
            ".container {\n  justify-content: flex-start;    /* [xxx        ] */\n  justify-content: center;        /* [    xxx    ] */\n  justify-content: flex-end;      /* [        xxx] */\n  justify-content: space-between; /* [x    x    x] */\n  justify-content: space-around;  /* [ x   x   x ] */\n  justify-content: space-evenly;  /* [  x  x  x  ] */\n}",
            'Justify contentのオプション',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, '子要素 - flex', 7,
            ".container > div {\n  /* This: */\n  flex: 1 0 auto;\n\n  /* Is equivalent to this: */\n  flex-grow: 1;\n  flex-shrink: 0;\n  flex-basis: auto;\n}",
            'Flex短縮プロパティ',
            null,
            'medium'
        );

        $this->createExample($section4, $cssLanguage, '子要素 - order', 8,
            ".container > div {\n  order: 1;\n}",
            'Orderプロパティ',
            null,
            'easy'
        );

        $this->createExample($section4, $cssLanguage, '子要素 - align-self', 9,
            ".container > div {\n  align-self: flex-start;  /* left */\n  margin-left: auto;       /* right */\n}",
            'Align selfプロパティ',
            null,
            'easy'
        );

        // Section 5: CSS Flexbox Tricks
        $section5 = $this->createSection($cssLanguage, 'CSS Flexboxのテクニック', 5, 'Flexboxのヒントとテクニック', 'css-flexbox-tricks');

        $this->createExample($section5, $cssLanguage, '垂直中央揃え', 1,
            ".container {\n  display: flex;\n}\n\n.container > div {\n  width: 100px;\n  height: 100px;\n  margin: auto;\n}",
            'margin autoによる中央揃え',
            null,
            'easy'
        );

        $this->createExample($section5, $cssLanguage, '垂直中央揃え（2）', 2,
            ".container {\n  display: flex;\n\n  /* vertical */\n  align-items: center; \n\n  /* horizontal */\n  justify-content: center;\n}",
            'align-itemsとjustify-contentによる中央揃え',
            null,
            'easy'
        );

        $this->createExample($section5, $cssLanguage, '並び替え', 3,
            ".container > .top {\n order: 1;\n}\n\n.container > .bottom {\n order: 2;\n}",
            'Flexアイテムの並び替え',
            null,
            'easy'
        );

        $this->createExample($section5, $cssLanguage, 'モバイルレイアウト', 4,
            ".container {\n  display: flex;\n  flex-direction: column;\n}\n\n.container > .top {\n  flex: 0 0 100px;\n}\n\n.container > .content {\n  flex: 1 0 auto;\n}",
            'モバイル対応のflexレイアウト',
            null,
            'medium'
        );

        $this->createExample($section5, $cssLanguage, 'テーブル風', 5,
            ".container {\n  display: flex;\n}\n\n/* the 'px' values here are just suggested percentages */\n.container > .checkbox { flex: 1 0 20px; }\n.container > .subject  { flex: 1 0 400px; }\n.container > .date     { flex: 1 0 120px; }",
            'Flexboxによるテーブル風レイアウト',
            null,
            'medium'
        );

        $this->createExample($section5, $cssLanguage, '垂直', 6,
            ".container {\n  align-items: center;\n}",
            'すべてのアイテムを垂直中央揃え',
            null,
            'easy'
        );

        $this->createExample($section5, $cssLanguage, '左と右', 7,
            ".menu > .left  { align-self: flex-start; }\n.menu > .right { align-self: flex-end; }",
            '左と右の配置',
            null,
            'easy'
        );

        // Section 6: CSS Grid Layout
        $section6 = $this->createSection($cssLanguage, 'CSS Gridレイアウト', 6, 'CSS Gridシステム', 'css-grid-layout');

        $this->createExample($section6, $cssLanguage, 'Grid Template Columns', 1,
            "#grid-container {\n    display: grid;\n    width: 100px;\n    grid-template-columns: 20px 20% 60%;\n}",
            'Grid template columns',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'fr相対単位', 2,
            ".grid {\n    display: grid;\n    width: 100px;\n    grid-template-columns: 1fr 60px 1fr;\n}",
            '分数単位（fr）',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'Grid Gap', 3,
            "/*The distance between rows is 20px*/\n/*The distance between columns is 10px*/\n#grid-container {\n    display: grid;\n    grid-gap: 20px 10px;\n}",
            'Grid gapプロパティ',
            null,
            'easy'
        );

        $this->createExample($section6, $cssLanguage, 'CSSブロックレベルGrid', 4,
            "#grid-container {\n    display: block;\n}",
            'ブロックレベルgrid',
            null,
            'easy'
        );

        $this->createExample($section6, $cssLanguage, 'grid-row', 5,
            ".item {\n    grid-row: 1 / span 2;\n}",
            'Grid rowプロパティ',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'CSSインラインレベルGrid', 6,
            "#grid-container {\n    display: inline-grid;\n}",
            'インラインレベルgrid',
            null,
            'easy'
        );

        $this->createExample($section6, $cssLanguage, 'minmax()関数', 7,
            ".grid {\n    display: grid;\n    grid-template-columns: 100px minmax(100px, 500px) 100px; \n}",
            'Minmax関数',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'grid-row-startとgrid-row-end', 8,
            "grid-row-start: 2;\ngrid-row-end: span 2;",
            'Grid row startとend',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'CSS grid-row-gap', 9,
            "grid-row-gap: length;",
            'Grid row gap',
            null,
            'easy'
        );

        $this->createExample($section6, $cssLanguage, 'CSS grid-area', 10,
            ".item1 {\n    grid-area: 2 / 1 / span 2 / span 3;\n}",
            'Grid area短縮記法',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'Justify Items', 11,
            "#container {\n    display: grid;\n    justify-items: center;\n    grid-template-columns: 1fr;\n    grid-template-rows: 1fr 1fr 1fr;\n    grid-gap: 10px;\n}",
            'Justify itemsプロパティ',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'CSS grid-template-areas', 12,
            ".item {\n    grid-area: nav;\n}\n.grid-container {\n    display: grid;\n    grid-template-areas:\n    'nav nav . .'\n    'nav nav . .';\n}",
            'Grid template areas',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'Justify Self', 13,
            "#grid-container {\n    display: grid;\n    justify-items: start;\n}\n\n.grid-items {\n    justify-self: end;\n}",
            'Justify selfプロパティ',
            null,
            'medium'
        );

        $this->createExample($section6, $cssLanguage, 'Align Items', 14,
            "#container {\n    display: grid;\n    align-items: start;\n    grid-template-columns: 1fr;\n    grid-template-rows: 1fr 1fr 1fr;\n    grid-gap: 10px;\n}",
            'Align itemsプロパティ',
            null,
            'medium'
        );

        // Section 7: CSS Dynamic Content
        $section7 = $this->createSection($cssLanguage, 'CSS動的コンテンツ', 7, 'CSS変数とカウンター', 'css-dynamic-content');

        $this->createExample($section7, $cssLanguage, '変数 - 定義', 1,
            ":root {\n  --first-color: #16f;\n  --second-color: #ff7;\n}",
            'CSS変数の定義',
            null,
            'easy'
        );

        $this->createExample($section7, $cssLanguage, '変数 - 使用', 2,
            "#firstParagraph {\n  background-color: var(--first-color);\n  color: var(--second-color);\n}",
            'CSS変数の使用',
            null,
            'easy'
        );

        $this->createExample($section7, $cssLanguage, 'カウンター - 設定', 3,
            "/* Set \"my-counter\" to 0 */\ncounter-set: my-counter;",
            'CSSカウンターの設定',
            null,
            'medium'
        );

        $this->createExample($section7, $cssLanguage, 'カウンター - 増加', 4,
            "/* Increment \"my-counter\" by 1 */\ncounter-increment: my-counter;",
            'CSSカウンターの増加',
            null,
            'medium'
        );

        $this->createExample($section7, $cssLanguage, 'カウンター - 減少', 5,
            "/* Decrement \"my-counter\" by 1 */\ncounter-increment: my-counter -1;",
            'CSSカウンターの減少',
            null,
            'medium'
        );

        $this->createExample($section7, $cssLanguage, 'カウンター - リセット', 6,
            "/* Reset \"my-counter\" to 0 */\ncounter-reset: my-counter;",
            'CSSカウンターのリセット',
            null,
            'medium'
        );

        $this->createExample($section7, $cssLanguage, 'カウンターの使用 - 基本', 7,
            "body { counter-reset: section; }\n\nh3::before {\n  counter-increment: section; \n  content: \"Section.\" counter(section);\n}",
            '基本的なカウンターの使用',
            null,
            'medium'
        );

        $this->createExample($section7, $cssLanguage, 'カウンターの使用 - ネスト', 8,
            "ol {\n  counter-reset: section;   \n  list-marker-type: none;\n}\n\nli::before {\n  counter-increment: section;\n  content: counters(section, \".\") \" \"; \n}",
            'ネストされたカウンター',
            null,
            'medium'
        );

        // Section 8: CSS 3 tricks
        $section8 = $this->createSection($cssLanguage, 'CSS 3のテクニック', 8, 'CSSのヒントとテクニック', 'css-tricks');

        $this->createExample($section8, $cssLanguage, 'スクロールバースムーズ', 1,
            "html {\n    scroll-behavior: smooth;\n}",
            'スムーズなスクロール動作',
            null,
            'easy'
        );

        // Update counts
        $this->updateLanguageCounts($cssLanguage);
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
        if (str_contains($titleLower, 'flexbox') || str_contains($titleLower, 'flex')) {
            $tags[] = 'flexbox';
        }
        if (str_contains($titleLower, 'grid')) {
            $tags[] = 'grid';
        }
        if (str_contains($titleLower, 'selector') || str_contains($titleLower, 'pseudo')) {
            $tags[] = 'selector';
        }
        if (str_contains($titleLower, 'animation') || str_contains($titleLower, 'keyframe')) {
            $tags[] = 'animation';
        }
        if (str_contains($titleLower, 'variable') || str_contains($titleLower, 'counter')) {
            $tags[] = 'dynamic';
        }
        if (str_contains($titleLower, 'position') || str_contains($titleLower, 'layout')) {
            $tags[] = 'layout';
        }
        if (str_contains($titleLower, 'color') || str_contains($titleLower, 'background') || str_contains($titleLower, 'font')) {
            $tags[] = 'styling';
        }
        if (str_contains($titleLower, 'responsive') || str_contains($titleLower, 'mobile')) {
            $tags[] = 'responsive';
        }

        // Add basic tags
        $tags[] = 'css';
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

