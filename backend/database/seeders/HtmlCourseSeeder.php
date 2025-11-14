<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class HtmlCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * HTML基礎演習 - 15週の実践的な課題を通じて、HTMLの基本からセマンティックHTML、マルチメディアまで段階的に学習
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'HTML基礎演習',
            'description' => '初心者向けHTML基礎コース。15週の実践的な課題を通じて、HTMLの基本構造からセマンティックHTML、フォーム、マルチメディアまで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['html', 'html5', 'web', '基礎', '演習', '初心者', 'マークアップ'],
            'icon' => 'ic_html',
            'color' => '#E34F26',
            'is_featured' => true,
        ]);

        // Milestone 1: HTML基礎 (第1週～第3週)
        $milestone1 = $template->milestones()->create([
            'title' => 'HTML基礎',
            'description' => 'HTMLの基本構造とテキスト要素の学習',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'HTMLの基本構造を理解',
                'テキスト要素を使える',
                'リストと画像を扱える'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第1週：HTML入門・基本構造',
                'description' => 'HTMLとは、基本構造、DOCTYPE、head、body',
                'sort_order' => 1,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'HTMLとは', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'HTML文書の基本構造', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '最初のHTMLページを作成', 'estimated_minutes' => 180, 'sort_order' => 3],
                    ['title' => '文字コードとメタタグ', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'HTMLとは',
                        'content' => "# HTMLとは

**HTML（HyperText Markup Language）**は、Webページの構造を記述するためのマークアップ言語です。

## HTMLの役割

1. **コンテンツの構造化**: 見出し、段落、リストなどの構造を定義
2. **リンクの作成**: ページ間のハイパーリンクを実現
3. **画像や動画の埋め込み**: マルチメディアコンテンツを表示
4. **フォームの作成**: ユーザー入力を受け付ける

## HTMLの特徴

- **タグで囲む**: `<tag>内容</tag>` の形式
- **階層構造**: 要素をネストして構造を作る
- **属性**: タグに追加情報を付与できる
- **大文字小文字を区別しない**: `<HTML>` も `<html>` も同じ（小文字推奨）

## HTML、CSS、JavaScriptの関係

```
HTML       → 構造（骨組み）
CSS        → デザイン（見た目）
JavaScript → 動き（インタラクション）
```

HTMLはWebページの**土台**となる技術です。",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'HTML文書の基本構造',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>ページのタイトル</title>
</head>
<body>
    <h1>こんにちは、HTML！</h1>
    <p>これは最初のHTMLページです。</p>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'HTML文書の各部分の説明',
                        'content' => "# HTML文書の各部分

## DOCTYPE宣言

```html
<!DOCTYPE html>
```

- HTML5の文書であることを宣言
- ブラウザに正しくレンダリングさせるために必須
- 必ず文書の先頭に記述

## html要素

```html
<html lang=\"ja\">
```

- HTML文書のルート要素
- `lang`属性で言語を指定（日本語は\"ja\"、英語は\"en\"）

## head要素

文書のメタ情報を記述する領域。ページには表示されない。

```html
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>ページタイトル</title>
</head>
```

### 主な子要素

- `<meta charset=\"UTF-8\">`: 文字コード指定（UTF-8推奨）
- `<meta name=\"viewport\" ...>`: モバイル対応の設定
- `<title>`: ブラウザのタブに表示されるタイトル
- `<link>`: 外部CSSファイルの読み込み
- `<style>`: CSS記述
- `<script>`: JavaScriptファイルの読み込み

## body要素

```html
<body>
    <!-- ここにページのコンテンツを記述 -->
</body>
```

- ページに表示される内容を記述
- 見出し、段落、画像、リンクなど",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'メタタグの例',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <!-- 必須のメタタグ -->
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

    <!-- ページ情報 -->
    <title>私のWebサイト</title>
    <meta name=\"description\" content=\"HTMLを学習するためのサンプルページです\">
    <meta name=\"keywords\" content=\"HTML, 学習, Web開発\">
    <meta name=\"author\" content=\"田中太郎\">

    <!-- OGP（SNSシェア用） -->
    <meta property=\"og:title\" content=\"私のWebサイト\">
    <meta property=\"og:description\" content=\"HTMLを学習するページ\">
    <meta property=\"og:image\" content=\"https://example.com/image.jpg\">

    <!-- ファビコン -->
    <link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\">

    <!-- 外部CSS読み込み -->
    <link rel=\"stylesheet\" href=\"style.css\">
</head>
<body>
    <h1>ようこそ</h1>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'HTMLのコメント',
                        'content' => "# HTMLのコメント

コメントは、コード内にメモを残すための記法です。ブラウザでは表示されません。

## 基本的な書き方

```html
<!-- これはコメントです -->

<!--
複数行の
コメントも
書けます
-->
```

## 使用例

```html
<!-- ヘッダーセクション -->
<header>
    <h1>サイトタイトル</h1>
</header>

<!-- メインコンテンツ -->
<main>
    <!-- この機能は後で実装
    <div class=\"feature\">
        未実装の機能
    </div>
    -->

    <p>現在のコンテンツ</p>
</main>

<!-- フッターセクション -->
<footer>
    <p>&copy; 2024 My Website</p>
</footer>
```

## コメントの用途

1. **説明**: コードの意図を説明
2. **一時的な無効化**: コードを削除せずに無効化
3. **セクション分け**: コードの構造を整理
4. **TODOメモ**: 後で実装する機能をメモ

## 注意点

- コメント内にHTMLタグを書いても実行されない
- ソースコードを見れば誰でも読めるので、機密情報は書かない",
                        'sort_order' => 5
                    ],
                ],
            ],
            [
                'title' => '第2週：テキスト要素とリンク',
                'description' => '見出し、段落、強調、リンクの使い方',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '見出しと段落', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'テキスト装飾要素', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'リンクの作成', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'ページ内リンク', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '見出しタグ（h1～h6）',
                        'content' => "# 見出しタグ

HTMLには6段階の見出しタグがあります。

## 見出しの階層

```html
<h1>最も重要な見出し</h1>
<h2>2番目に重要な見出し</h2>
<h3>3番目に重要な見出し</h3>
<h4>4番目に重要な見出し</h4>
<h5>5番目に重要な見出し</h5>
<h6>6番目に重要な見出し</h6>
```

## 使用ルール

### 1. h1は1ページに1つ

```html
<!-- 良い例 ✓ -->
<h1>ページのメインタイトル</h1>
<h2>セクション1</h2>
<h3>サブセクション1-1</h3>
<h2>セクション2</h2>

<!-- 悪い例 ✗ -->
<h1>タイトル1</h1>
<h1>タイトル2</h1> <!-- h1は1つだけ -->
```

### 2. 階層を飛ばさない

```html
<!-- 良い例 ✓ -->
<h1>タイトル</h1>
<h2>セクション</h2>
<h3>サブセクション</h3>

<!-- 悪い例 ✗ -->
<h1>タイトル</h1>
<h3>セクション</h3> <!-- h2を飛ばしている -->
```

### 3. 見た目の調整にはCSSを使う

```html
<!-- 悪い例 ✗ -->
<h5>大きく見せたいテキスト</h5> <!-- 見た目だけでh5を選んでいる -->

<!-- 良い例 ✓ -->
<h2 class=\"small-heading\">適切な階層の見出し</h2>
<!-- CSSで見た目を調整 -->
```

## 実例

```html
<article>
    <h1>HTMLの学習ガイド</h1>

    <h2>第1章：HTMLとは</h2>
    <p>HTMLの基本概念を学びます。</p>

    <h3>1.1 HTMLの歴史</h3>
    <p>HTMLの発展の歴史...</p>

    <h3>1.2 HTMLの役割</h3>
    <p>Webページにおける役割...</p>

    <h2>第2章：基本構造</h2>
    <p>HTML文書の構造を学びます。</p>
</article>
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '段落とテキスト要素',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>テキスト要素</title>
</head>
<body>
    <h1>テキスト要素のサンプル</h1>

    <!-- 段落 -->
    <p>これは段落です。pタグで囲まれたテキストは、1つのまとまった文章として表示されます。</p>

    <p>段落と段落の間には、自動的に余白が入ります。</p>

    <!-- 改行 -->
    <p>
        この文章には<br>
        改行が<br>
        含まれています。
    </p>

    <!-- 強調 -->
    <p>これは<strong>重要なテキスト</strong>です（太字で表示）。</p>
    <p>これは<em>強調されたテキスト</em>です（斜体で表示）。</p>

    <!-- その他のテキスト装飾 -->
    <p><b>太字のテキスト</b>（意味的な強調ではない）</p>
    <p><i>イタリック体のテキスト</i></p>
    <p><u>下線付きテキスト</u></p>
    <p><mark>ハイライト表示</mark></p>
    <p><small>小さいテキスト</small></p>
    <p><del>削除されたテキスト</del></p>
    <p><ins>挿入されたテキスト</ins></p>
    <p>H<sub>2</sub>O（下付き文字）</p>
    <p>x<sup>2</sup>（上付き文字）</p>

    <!-- 水平線 -->
    <hr>

    <!-- 整形済みテキスト -->
    <pre>
        この中では
        改行や    スペースが
            そのまま表示されます
    </pre>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'strongとem、bとiの違い',
                        'content' => "# 意味的な強調 vs 見た目の装飾

## strong と b の違い

### `<strong>` - 意味的に重要

```html
<p><strong>警告:</strong> この操作は取り消せません。</p>
```

- **意味**: 重要、緊急、警告
- **スクリーンリーダー**: 強調して読み上げる
- **SEO**: 検索エンジンも重要と認識
- **用途**: 本当に重要な内容

### `<b>` - 単なる太字

```html
<p><b>製品名</b>: スマートフォン</p>
```

- **意味**: なし（単に目立たせるだけ）
- **スクリーンリーダー**: 通常通り読み上げ
- **SEO**: 特別な意味なし
- **用途**: 製品名、キーワードなど

## em と i の違い

### `<em>` - 意味的な強調

```html
<p>私は<em>絶対に</em>行きます。</p>
```

- **意味**: 強調、アクセント
- **スクリーンリーダー**: イントネーションを変えて読み上げ
- **用途**: 文脈上の強調

### `<i>` - 単なる斜体

```html
<p><i>E. coli</i>（大腸菌）</p>
```

- **意味**: なし
- **用途**: 専門用語、外国語、思考など

## どちらを使うべきか？

```html
<!-- 良い例 ✓ -->
<p><strong>重要:</strong> 期限は明日です。</p>
<p>本を<em>絶対に</em>読みたい。</p>

<!-- 悪い例 ✗ -->
<p><b>重要:</b> 期限は明日です。</p> <!-- 意味的な重要性を表現すべき -->

<!-- どちらでもOK -->
<p><b>商品A</b>: 1,000円</p> <!-- 単に目立たせるだけ -->
<p><strong>商品A</strong>: 1,000円</p> <!-- より意味的 -->
```

**原則**: 意味がある場合は`strong`/`em`、単なる見た目なら`b`/`i`またはCSSを使用",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'リンクの作成',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>リンクのサンプル</title>
</head>
<body>
    <h1>リンクの種類</h1>

    <!-- 基本的なリンク -->
    <p><a href=\"https://www.example.com\">外部サイトへのリンク</a></p>

    <!-- 新しいタブで開く -->
    <p><a href=\"https://www.example.com\" target=\"_blank\">新しいタブで開く</a></p>

    <!-- 相対パスでのリンク -->
    <p><a href=\"about.html\">同じフォルダのページ</a></p>
    <p><a href=\"pages/contact.html\">サブフォルダのページ</a></p>
    <p><a href=\"../index.html\">親フォルダのページ</a></p>

    <!-- メールリンク -->
    <p><a href=\"mailto:info@example.com\">メールを送る</a></p>
    <p><a href=\"mailto:info@example.com?subject=お問い合わせ&body=本文\">件名・本文付きメール</a></p>

    <!-- 電話リンク（スマホで有効） -->
    <p><a href=\"tel:0312345678\">電話をかける</a></p>

    <!-- ページ内リンク -->
    <p><a href=\"#section1\">セクション1へ移動</a></p>

    <!-- ダウンロードリンク -->
    <p><a href=\"document.pdf\" download>PDFをダウンロード</a></p>

    <!-- 画像リンク -->
    <a href=\"https://www.example.com\">
        <img src=\"logo.png\" alt=\"ロゴ\" width=\"200\">
    </a>

    <!-- ボタン風リンク（CSSで装飾） -->
    <p><a href=\"signup.html\" class=\"button\">今すぐ登録</a></p>

    <hr>

    <!-- ページ内リンクの目的地 -->
    <h2 id=\"section1\">セクション1</h2>
    <p>ここがセクション1です。</p>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'リンクのベストプラクティス',
                        'content' => "# リンクのベストプラクティス

## 1. わかりやすいリンクテキスト

```html
<!-- 悪い例 ✗ -->
<p>詳細は<a href=\"about.html\">こちら</a>をクリック。</p>
<p><a href=\"download.pdf\">ここ</a>からダウンロード。</p>

<!-- 良い例 ✓ -->
<p><a href=\"about.html\">会社概要</a>をご覧ください。</p>
<p><a href=\"download.pdf\">製品カタログ（PDF）をダウンロード</a></p>
```

リンクテキストだけで内容がわかるようにする。

## 2. 外部リンクには target=\"_blank\"

```html
<a href=\"https://external-site.com\" target=\"_blank\" rel=\"noopener noreferrer\">
    外部サイト
</a>
```

- `target=\"_blank\"`: 新しいタブで開く
- `rel=\"noopener noreferrer\"`: セキュリティ対策（必須）

## 3. メールリンクは慎重に

```html
<!-- スパム対策として、直接メールアドレスを書かない場合も -->
<a href=\"#\" onclick=\"location.href='mailto:' + 'info' + '@' + 'example.com'\">
    お問い合わせ
</a>
```

## 4. ページ内リンクにはスムーススクロール

```html
<style>
html {
    scroll-behavior: smooth;
}
</style>

<a href=\"#top\">ページトップへ</a>
```

## 5. リンク切れをチェック

定期的にリンクが有効か確認する。

## 6. アクセシビリティ

```html
<!-- 画像リンクには必ずalt属性 -->
<a href=\"home.html\">
    <img src=\"home-icon.png\" alt=\"ホームへ戻る\">
</a>

<!-- アイコンのみのリンクにはaria-label -->
<a href=\"search.html\" aria-label=\"検索ページへ\">
    <span class=\"icon-search\"></span>
</a>
```",
                        'sort_order' => 5
                    ],
                ],
            ],
            [
                'title' => '第3週：リストと画像',
                'description' => '順序付きリスト、順序なしリスト、画像の埋め込み',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '順序なしリスト（ul）', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '順序付きリスト（ol）', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '画像の埋め込み', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => '画像の最適化とレスポンシブ対応', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'リストの種類',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>リストのサンプル</title>
</head>
<body>
    <h1>リストの種類</h1>

    <!-- 順序なしリスト -->
    <h2>買い物リスト</h2>
    <ul>
        <li>牛乳</li>
        <li>パン</li>
        <li>卵</li>
        <li>バナナ</li>
    </ul>

    <!-- 順序付きリスト -->
    <h2>手順</h2>
    <ol>
        <li>材料を準備する</li>
        <li>野菜を切る</li>
        <li>鍋で煮る</li>
        <li>味を調える</li>
    </ol>

    <!-- 定義リスト -->
    <h2>用語集</h2>
    <dl>
        <dt>HTML</dt>
        <dd>HyperText Markup Language - Webページの構造を記述</dd>

        <dt>CSS</dt>
        <dd>Cascading Style Sheets - Webページの見た目を装飾</dd>

        <dt>JavaScript</dt>
        <dd>プログラミング言語 - Webページに動きを追加</dd>
    </dl>

    <!-- ネストされたリスト -->
    <h2>階層構造</h2>
    <ul>
        <li>フルーツ
            <ul>
                <li>りんご</li>
                <li>バナナ</li>
                <li>オレンジ</li>
            </ul>
        </li>
        <li>野菜
            <ul>
                <li>にんじん</li>
                <li>トマト</li>
            </ul>
        </li>
    </ul>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'リストの使い分け',
                        'content' => "# リストの使い分け

## 順序なしリスト（ul）

**用途**: 順序が重要でない項目のリスト

```html
<ul>
    <li>項目1</li>
    <li>項目2</li>
    <li>項目3</li>
</ul>
```

**使用例**:
- ナビゲーションメニュー
- 特徴や利点のリスト
- 買い物リスト
- タグやカテゴリー

**デフォルトの表示**: 黒丸（•）マーカー

**CSSでマーカーを変更**:
```css
ul {
    list-style-type: circle;  /* ○ */
    list-style-type: square;  /* ■ */
    list-style-type: none;    /* なし */
}
```

## 順序付きリスト（ol）

**用途**: 順序が重要な項目のリスト

```html
<ol>
    <li>最初のステップ</li>
    <li>次のステップ</li>
    <li>最後のステップ</li>
</ol>
```

**使用例**:
- 手順や手続き
- ランキング
- 目次
- レシピの作り方

**デフォルトの表示**: 数字（1, 2, 3...）

**開始番号の変更**:
```html
<ol start=\"5\">
    <li>5番目の項目</li>
    <li>6番目の項目</li>
</ol>
```

**番号の種類を変更**:
```html
<ol type=\"A\">  <!-- A, B, C... -->
<ol type=\"a\">  <!-- a, b, c... -->
<ol type=\"I\">  <!-- I, II, III... -->
<ol type=\"i\">  <!-- i, ii, iii... -->
<ol type=\"1\">  <!-- 1, 2, 3...（デフォルト） -->
```

**逆順**:
```html
<ol reversed>
    <li>3位</li>
    <li>2位</li>
    <li>1位</li>
</ol>
```

## 定義リスト（dl）

**用途**: 用語と説明のペア

```html
<dl>
    <dt>用語</dt>
    <dd>説明</dd>
</dl>
```

**使用例**:
- 用語集
- FAQ
- メタデータの表示
- 製品仕様

## ネストされたリスト

```html
<ul>
    <li>親項目1
        <ul>
            <li>子項目1-1</li>
            <li>子項目1-2</li>
        </ul>
    </li>
    <li>親項目2
        <ol>
            <li>子項目2-1</li>
            <li>子項目2-2</li>
        </ol>
    </li>
</ul>
```

**注意**: ulとolは混在できる",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '画像の基本',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>画像の埋め込み</title>
</head>
<body>
    <h1>画像の基本</h1>

    <!-- 基本的な画像 -->
    <img src=\"photo.jpg\" alt=\"写真の説明\">

    <!-- サイズ指定 -->
    <img src=\"logo.png\" alt=\"ロゴ\" width=\"200\" height=\"100\">

    <!-- 相対パス -->
    <img src=\"images/banner.jpg\" alt=\"バナー\">

    <!-- 絶対パス（外部サイト） -->
    <img src=\"https://example.com/photo.jpg\" alt=\"外部の画像\">

    <!-- 画像とキャプション -->
    <figure>
        <img src=\"landscape.jpg\" alt=\"風景写真\" width=\"600\">
        <figcaption>美しい山の風景</figcaption>
    </figure>

    <!-- 画像リンク -->
    <a href=\"https://www.example.com\">
        <img src=\"button.png\" alt=\"サイトへ移動\" width=\"200\">
    </a>

    <!-- 代替画像（画像が読み込めない場合） -->
    <img src=\"image.jpg\" alt=\"画像の説明\"
         onerror=\"this.src='placeholder.png'\">

    <!-- レスポンシブ画像（CSSで制御） -->
    <img src=\"photo.jpg\" alt=\"写真\" style=\"max-width: 100%; height: auto;\">

    <!-- loading属性（遅延読み込み） -->
    <img src=\"large-image.jpg\" alt=\"大きい画像\" loading=\"lazy\">
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'alt属性の重要性',
                        'content' => "# alt属性の重要性

`alt`属性（代替テキスト）は**必須**です。

## alt属性の役割

### 1. アクセシビリティ

```html
<img src=\"dog.jpg\" alt=\"茶色の犬が公園で遊んでいる\">
```

- **スクリーンリーダー**: 視覚障害者向けに画像を音声で説明
- 画像の内容を言葉で伝える

### 2. SEO（検索エンジン最適化）

- 検索エンジンが画像の内容を理解
- 画像検索の対象になる

### 3. 画像が表示できない場合

- ネットワークエラー
- ファイルパスの間違い
- 画像フォーマットが未対応

→ alt属性のテキストが代わりに表示される

## 良いalt属性の書き方

### 具体的に説明

```html
<!-- 悪い例 ✗ -->
<img src=\"photo.jpg\" alt=\"写真\">
<img src=\"icon.png\" alt=\"画像\">

<!-- 良い例 ✓ -->
<img src=\"photo.jpg\" alt=\"青空の下でサッカーをする子供たち\">
<img src=\"icon.png\" alt=\"ホームアイコン\">
```

### 装飾的な画像は空にする

```html
<!-- 装飾のみの画像 -->
<img src=\"decoration.png\" alt=\"\">
```

- CSSで追加すべき装飾画像には `alt=\"\"`
- **altを省略してはいけない**（`alt=\"\"`と書く）

### 文脈に合わせる

```html
<!-- ニュース記事の画像 -->
<img src=\"event.jpg\" alt=\"市長が新施設の除幕式でテープカット\">

<!-- 商品画像 -->
<img src=\"product.jpg\" alt=\"iPhone 15 Pro - スペースブラック 256GB\">

<!-- アイコン -->
<img src=\"search.png\" alt=\"検索\">
```

### リンク画像は行き先を説明

```html
<!-- 悪い例 ✗ -->
<a href=\"home.html\">
    <img src=\"home.png\" alt=\"画像\">
</a>

<!-- 良い例 ✓ -->
<a href=\"home.html\">
    <img src=\"home.png\" alt=\"ホームページへ戻る\">
</a>
```

## title属性との違い

```html
<img src=\"photo.jpg\"
     alt=\"風景写真\"
     title=\"2024年春の富士山\">
```

- **alt**: 画像の内容を説明（必須）
- **title**: 補足情報（オプション、ホバー時に表示）",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => '画像フォーマットの選択',
                        'content' => "# 画像フォーマットの選択

## JPEG（.jpg, .jpeg）

**特徴**:
- 写真に最適
- 圧縮率が高い
- 透過非対応

**用途**:
- 写真
- 複雑なグラデーション
- 背景画像

**メリット**:
- ファイルサイズが小さい
- 多くの色を表現できる

**デメリット**:
- 透過できない
- 圧縮すると劣化する

```html
<img src=\"photo.jpg\" alt=\"風景写真\">
```

## PNG（.png）

**特徴**:
- 透過対応
- ロスレス圧縮
- イラストに最適

**用途**:
- ロゴ
- アイコン
- 透過が必要な画像
- スクリーンショット

**メリット**:
- 透過できる
- 圧縮しても劣化しない
- くっきりした線に強い

**デメリット**:
- 写真だとファイルサイズが大きい

```html
<img src=\"logo.png\" alt=\"会社ロゴ\">
```

## GIF（.gif）

**特徴**:
- アニメーション対応
- 256色まで
- 透過対応（1bit）

**用途**:
- 簡単なアニメーション
- シンプルなアイコン

**メリット**:
- アニメーションが作れる
- ファイルサイズが小さい

**デメリット**:
- 色数が少ない（256色）
- 写真には不向き

```html
<img src=\"animation.gif\" alt=\"読み込み中のアニメーション\">
```

## SVG（.svg）

**特徴**:
- ベクター形式
- 拡大しても綺麗
- XMLベース

**用途**:
- ロゴ
- アイコン
- 図形
- グラフ

**メリット**:
- 拡大縮小しても劣化しない
- ファイルサイズが小さい
- CSSで色を変更できる

**デメリット**:
- 複雑な画像には不向き

```html
<img src=\"icon.svg\" alt=\"メニューアイコン\">
<!-- または -->
<svg>...</svg>
```

## WebP（.webp）

**特徴**:
- 次世代フォーマット
- JPEG/PNGより軽量
- 透過/アニメーション対応

**用途**:
- 写真
- イラスト
- 全般的に使える

**メリット**:
- ファイルサイズが小さい
- 画質が良い

**デメリット**:
- 古いブラウザで未対応

```html
<picture>
    <source srcset=\"image.webp\" type=\"image/webp\">
    <img src=\"image.jpg\" alt=\"画像\">
</picture>
```

## AVIF（.avif）

**特徴**:
- 最新フォーマット
- WebPよりさらに軽量

**用途**:
- 最新Webサイト

**メリット**:
- 最高の圧縮率

**デメリット**:
- ブラウザ対応がまだ限定的

## 選択のガイドライン

```
写真           → JPEG または WebP
ロゴ/アイコン   → PNG または SVG
アニメーション  → GIF または WebP
透過が必要     → PNG または WebP
拡大縮小が必要 → SVG
```",
                        'sort_order' => 5
                    ],
                ],
            ],
        ]);

        // Milestone 2: フォームとテーブル (第4週～第6週)
        $milestone2 = $template->milestones()->create([
            'title' => 'フォームとテーブル',
            'description' => 'テーブルとフォームの作成方法を学習',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                'テーブルを作成できる',
                'フォームを作成できる',
                '各種入力要素を使える'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第4週：テーブルの基本',
                'description' => 'table、tr、td、th、thead、tbody、tfootの使い方',
                'sort_order' => 4,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'テーブルの基本構造', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'テーブルのグループ化', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'セルの結合', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'テーブルのスタイリング', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'テーブルの基本構造',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>テーブルの基本</title>
</head>
<body>
    <h1>基本的なテーブル</h1>

    <table border=\"1\">
        <!-- ヘッダー行 -->
        <tr>
            <th>名前</th>
            <th>年齢</th>
            <th>職業</th>
        </tr>

        <!-- データ行 -->
        <tr>
            <td>田中太郎</td>
            <td>25</td>
            <td>エンジニア</td>
        </tr>

        <tr>
            <td>佐藤花子</td>
            <td>30</td>
            <td>デザイナー</td>
        </tr>

        <tr>
            <td>鈴木一郎</td>
            <td>28</td>
            <td>マーケター</td>
        </tr>
    </table>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'テーブルの要素',
                        'content' => "# テーブルの要素

## 基本要素

### `<table>`
テーブル全体を囲む親要素

```html
<table>
    <!-- テーブルの内容 -->
</table>
```

### `<tr>` (Table Row)
テーブルの**行**を表す

```html
<tr>
    <td>セル1</td>
    <td>セル2</td>
</tr>
```

### `<td>` (Table Data)
データ**セル**を表す

```html
<td>データ</td>
```

### `<th>` (Table Header)
**見出しセル**を表す（太字・中央揃えで表示）

```html
<th>見出し</th>
```

## テーブルの構造化

### `<thead>` (Table Head)
テーブルのヘッダー部分をグループ化

```html
<thead>
    <tr>
        <th>列1</th>
        <th>列2</th>
    </tr>
</thead>
```

### `<tbody>` (Table Body)
テーブルのメインデータ部分をグループ化

```html
<tbody>
    <tr>
        <td>データ1</td>
        <td>データ2</td>
    </tr>
</tbody>
```

### `<tfoot>` (Table Footer)
テーブルのフッター部分をグループ化（合計行など）

```html
<tfoot>
    <tr>
        <td>合計</td>
        <td>100</td>
    </tr>
</tfoot>
```

## 完全な構造の例

```html
<table>
    <thead>
        <tr>
            <th>商品名</th>
            <th>価格</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>りんご</td>
            <td>100円</td>
        </tr>
        <tr>
            <td>バナナ</td>
            <td>150円</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>合計</th>
            <td>250円</td>
        </tr>
    </tfoot>
</table>
```

## その他の要素

### `<caption>`
テーブルのキャプション（タイトル）

```html
<table>
    <caption>2024年売上データ</caption>
    <!-- ... -->
</table>
```

### `<col>` と `<colgroup>`
列のグループ化とスタイル適用

```html
<table>
    <colgroup>
        <col style=\"background-color: #f0f0f0;\">
        <col span=\"2\" style=\"background-color: #e0e0e0;\">
    </colgroup>
    <!-- ... -->
</table>
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'セルの結合',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>セルの結合</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>セルの結合</h1>

    <!-- colspan: 横方向の結合 -->
    <h2>横方向の結合（colspan）</h2>
    <table>
        <tr>
            <th colspan=\"3\">2024年 第1四半期売上</th>
        </tr>
        <tr>
            <th>1月</th>
            <th>2月</th>
            <th>3月</th>
        </tr>
        <tr>
            <td>100万円</td>
            <td>120万円</td>
            <td>110万円</td>
        </tr>
    </table>

    <!-- rowspan: 縦方向の結合 -->
    <h2>縦方向の結合（rowspan）</h2>
    <table>
        <tr>
            <th rowspan=\"2\">製品</th>
            <th colspan=\"2\">販売数</th>
        </tr>
        <tr>
            <th>国内</th>
            <th>海外</th>
        </tr>
        <tr>
            <td>製品A</td>
            <td>1,000</td>
            <td>500</td>
        </tr>
        <tr>
            <td>製品B</td>
            <td>800</td>
            <td>300</td>
        </tr>
    </table>

    <!-- 複雑な結合 -->
    <h2>複雑な結合</h2>
    <table>
        <tr>
            <th rowspan=\"2\">部門</th>
            <th rowspan=\"2\">社員名</th>
            <th colspan=\"2\">売上（万円）</th>
        </tr>
        <tr>
            <th>前期</th>
            <th>今期</th>
        </tr>
        <tr>
            <td rowspan=\"2\">営業部</td>
            <td>田中</td>
            <td>100</td>
            <td>120</td>
        </tr>
        <tr>
            <td>佐藤</td>
            <td>90</td>
            <td>110</td>
        </tr>
    </table>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'セル結合の属性',
                        'content' => "# セル結合の属性

## colspan（列の結合）

**横方向**に複数のセルを結合します。

```html
<table border=\"1\">
    <tr>
        <!-- 3列分のセルを結合 -->
        <th colspan=\"3\">見出し</th>
    </tr>
    <tr>
        <td>A</td>
        <td>B</td>
        <td>C</td>
    </tr>
</table>
```

**結果**:
```
+---------------+
|   見出し       |
+-----+-----+-----+
|  A  |  B  |  C  |
+-----+-----+-----+
```

### 注意点
- colspan の値は結合するセルの数
- 結合した分、その行の他の`<td>`は削除する

## rowspan（行の結合）

**縦方向**に複数のセルを結合します。

```html
<table border=\"1\">
    <tr>
        <!-- 2行分のセルを結合 -->
        <th rowspan=\"2\">部門</th>
        <td>田中</td>
    </tr>
    <tr>
        <!-- ここには部門のセルは不要 -->
        <td>佐藤</td>
    </tr>
</table>
```

**結果**:
```
+------+------+
| 部門 | 田中 |
|      +------+
|      | 佐藤 |
+------+------+
```

### 注意点
- rowspan の値は結合する行の数
- 結合した分、次の行の`<td>`を減らす

## colspan と rowspan の組み合わせ

```html
<table border=\"1\">
    <tr>
        <th rowspan=\"2\" colspan=\"2\">左上</th>
        <th>右上1</th>
    </tr>
    <tr>
        <th>右上2</th>
    </tr>
    <tr>
        <td>A</td>
        <td>B</td>
        <td>C</td>
    </tr>
</table>
```

**結果**:
```
+------------+------+
|            |右上1 |
|    左上     +------+
|            |右上2 |
+------+-----+------+
|  A   |  B  |  C   |
+------+-----+------+
```

## よくある間違い

### ❌ 間違い: セルの数が合わない

```html
<table border=\"1\">
    <tr>
        <th colspan=\"3\">見出し</th>
    </tr>
    <tr>
        <td>A</td>
        <td>B</td>
        <!-- Cが足りない！ -->
    </tr>
</table>
```

### ✅ 正しい

```html
<table border=\"1\">
    <tr>
        <th colspan=\"3\">見出し</th>
    </tr>
    <tr>
        <td>A</td>
        <td>B</td>
        <td>C</td>
    </tr>
</table>
```",
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第5週：フォームの基本',
                'description' => 'form、input、label、buttonの基本',
                'sort_order' => 5,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'フォームの基本構造', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'テキスト入力', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'ボタンとsubmit', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'labelとの関連付け', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'フォームの基本構造',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>フォームの基本</title>
</head>
<body>
    <h1>お問い合わせフォーム</h1>

    <!-- 基本的なフォーム -->
    <form action=\"/submit\" method=\"POST\">
        <!-- テキスト入力 -->
        <label for=\"name\">お名前：</label>
        <input type=\"text\" id=\"name\" name=\"name\" required>
        <br><br>

        <!-- メールアドレス -->
        <label for=\"email\">メールアドレス：</label>
        <input type=\"email\" id=\"email\" name=\"email\" required>
        <br><br>

        <!-- パスワード -->
        <label for=\"password\">パスワード：</label>
        <input type=\"password\" id=\"password\" name=\"password\" required>
        <br><br>

        <!-- 送信ボタン -->
        <button type=\"submit\">送信</button>
        <button type=\"reset\">リセット</button>
    </form>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'form要素の属性',
                        'content' => "# form要素の属性

## 基本構文

```html
<form action=\"送信先URL\" method=\"送信方法\">
    <!-- フォームの内容 -->
</form>
```

## 主な属性

### action

フォームデータの送信先URLを指定

```html
<form action=\"/contact\" method=\"POST\">
    <!-- ... -->
</form>
```

- サーバー側のエンドポイントを指定
- 省略すると現在のページに送信

### method

データの送信方法を指定

```html
<!-- GETメソッド（デフォルト） -->
<form action=\"/search\" method=\"GET\">
    <input type=\"text\" name=\"q\">
    <button type=\"submit\">検索</button>
</form>
<!-- URL: /search?q=検索キーワード -->

<!-- POSTメソッド -->
<form action=\"/login\" method=\"POST\">
    <input type=\"email\" name=\"email\">
    <input type=\"password\" name=\"password\">
    <button type=\"submit\">ログイン</button>
</form>
```

**GET vs POST**:
- **GET**: URLにデータが表示される、ブックマーク可能、検索フォーム向き
- **POST**: URLに表示されない、セキュア、ログインや登録向き

### name

フォーム全体に名前を付ける（任意）

```html
<form name=\"contactForm\" action=\"/submit\" method=\"POST\">
    <!-- ... -->
</form>
```

### autocomplete

自動補完の有効/無効

```html
<!-- 自動補完を有効 -->
<form autocomplete=\"on\">

<!-- 自動補完を無効 -->
<form autocomplete=\"off\">
```

### novalidate

HTML5のバリデーションを無効化

```html
<form novalidate>
    <input type=\"email\" required>
    <!-- ブラウザの検証が行われない -->
</form>
```

### enctype

ファイルアップロード時に必要

```html
<form action=\"/upload\" method=\"POST\" enctype=\"multipart/form-data\">
    <input type=\"file\" name=\"document\">
    <button type=\"submit\">アップロード</button>
</form>
```

**enctypeの種類**:
- `application/x-www-form-urlencoded`: デフォルト
- `multipart/form-data`: ファイルアップロード時
- `text/plain`: テキストのみ（メール送信時など）

### target

送信結果の表示先

```html
<!-- 新しいタブで開く -->
<form action=\"/submit\" method=\"POST\" target=\"_blank\">

<!-- iframe内で開く -->
<form action=\"/submit\" method=\"POST\" target=\"myIframe\">
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'input要素の種類',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>様々なinput要素</title>
</head>
<body>
    <h1>入力フォーム</h1>

    <form>
        <!-- テキスト -->
        <label>名前: <input type=\"text\" name=\"name\" placeholder=\"山田太郎\"></label><br><br>

        <!-- メール -->
        <label>メール: <input type=\"email\" name=\"email\" placeholder=\"example@mail.com\"></label><br><br>

        <!-- パスワード -->
        <label>パスワード: <input type=\"password\" name=\"password\"></label><br><br>

        <!-- 電話番号 -->
        <label>電話: <input type=\"tel\" name=\"phone\" placeholder=\"090-1234-5678\"></label><br><br>

        <!-- URL -->
        <label>Website: <input type=\"url\" name=\"website\" placeholder=\"https://example.com\"></label><br><br>

        <!-- 数値 -->
        <label>年齢: <input type=\"number\" name=\"age\" min=\"0\" max=\"120\"></label><br><br>

        <!-- 日付 -->
        <label>生年月日: <input type=\"date\" name=\"birthday\"></label><br><br>

        <!-- 時刻 -->
        <label>時刻: <input type=\"time\" name=\"time\"></label><br><br>

        <!-- 日時 -->
        <label>予約日時: <input type=\"datetime-local\" name=\"appointment\"></label><br><br>

        <!-- 色 -->
        <label>好きな色: <input type=\"color\" name=\"color\"></label><br><br>

        <!-- 範囲 -->
        <label>満足度: <input type=\"range\" name=\"satisfaction\" min=\"1\" max=\"5\"></label><br><br>

        <!-- 検索 -->
        <label>検索: <input type=\"search\" name=\"query\" placeholder=\"検索キーワード\"></label><br><br>

        <!-- ファイル -->
        <label>ファイル: <input type=\"file\" name=\"document\"></label><br><br>

        <!-- 非表示 -->
        <input type=\"hidden\" name=\"user_id\" value=\"12345\">

        <button type=\"submit\">送信</button>
    </form>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'label要素とアクセシビリティ',
                        'content' => "# label要素とアクセシビリティ

`<label>`は入力フィールドのラベルを表し、アクセシビリティとUXを向上させます。

## labelの書き方

### 方法1: for属性で関連付け

```html
<label for=\"username\">ユーザー名：</label>
<input type=\"text\" id=\"username\" name=\"username\">
```

- `for`属性とinputの`id`を一致させる
- labelとinputが離れていてもOK

### 方法2: labelで囲む

```html
<label>
    ユーザー名：
    <input type=\"text\" name=\"username\">
</label>
```

- inputをlabelで囲む
- `for`と`id`は不要

## labelを使うメリット

### 1. クリック領域が広がる

```html
<label for=\"agree\">
    <input type=\"checkbox\" id=\"agree\" name=\"agree\">
    利用規約に同意する
</label>
```

- テキスト部分をクリックしてもチェックボックスが反応
- 特にスマホで押しやすくなる

### 2. スクリーンリーダー対応

```html
<label for=\"email\">メールアドレス</label>
<input type=\"email\" id=\"email\" name=\"email\">
```

- 視覚障害者向けにフィールドの説明を読み上げ
- アクセシビリティが向上

### 3. フォーカス管理

labelをクリックすると、関連するinputにフォーカスが移動します。

## 複数のinputがある場合

```html
<fieldset>
    <legend>お名前</legend>

    <label for=\"lastName\">姓：</label>
    <input type=\"text\" id=\"lastName\" name=\"lastName\">

    <label for=\"firstName\">名：</label>
    <input type=\"text\" id=\"firstName\" name=\"firstName\">
</fieldset>
```

## ラジオボタンとチェックボックス

```html
<!-- ラジオボタン -->
<fieldset>
    <legend>性別</legend>

    <label>
        <input type=\"radio\" name=\"gender\" value=\"male\">
        男性
    </label>

    <label>
        <input type=\"radio\" name=\"gender\" value=\"female\">
        女性
    </label>
</fieldset>

<!-- チェックボックス -->
<fieldset>
    <legend>趣味</legend>

    <label>
        <input type=\"checkbox\" name=\"hobby\" value=\"sports\">
        スポーツ
    </label>

    <label>
        <input type=\"checkbox\" name=\"hobby\" value=\"music\">
        音楽
    </label>
</fieldset>
```

## ベストプラクティス

```html
<!-- 良い例 ✓ -->
<label for=\"email\">メールアドレス</label>
<input type=\"email\" id=\"email\" name=\"email\" required>

<!-- 悪い例 ✗ -->
<span>メールアドレス</span>
<input type=\"email\" name=\"email\"> <!-- labelがない -->
```

**必ずlabelを使う理由**:
- アクセシビリティの向上
- UXの改善
- SEO効果",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ボタンの種類',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>ボタンの種類</title>
</head>
<body>
    <h1>ボタンの種類</h1>

    <form action=\"/submit\" method=\"POST\">
        <input type=\"text\" name=\"username\" placeholder=\"ユーザー名\">

        <!-- submit: フォームを送信 -->
        <button type=\"submit\">送信する</button>

        <!-- reset: フォームをリセット -->
        <button type=\"reset\">リセット</button>

        <!-- button: 何もしない（JavaScriptで制御） -->
        <button type=\"button\" onclick=\"alert('クリックされました')\">
            クリック
        </button>
    </form>

    <hr>

    <!-- inputタグのボタン -->
    <form action=\"/login\" method=\"POST\">
        <input type=\"text\" name=\"email\" placeholder=\"メールアドレス\">

        <!-- input type=\"submit\" -->
        <input type=\"submit\" value=\"ログイン\">

        <!-- input type=\"reset\" -->
        <input type=\"reset\" value=\"クリア\">

        <!-- input type=\"button\" -->
        <input type=\"button\" value=\"キャンセル\" onclick=\"history.back()\">
    </form>

    <hr>

    <!-- 画像ボタン -->
    <form action=\"/search\" method=\"GET\">
        <input type=\"text\" name=\"q\" placeholder=\"検索キーワード\">
        <input type=\"image\" src=\"search-icon.png\" alt=\"検索\">
    </form>

    <hr>

    <!-- disabledボタン -->
    <form>
        <button type=\"submit\" disabled>送信不可</button>
        <button type=\"submit\">送信可能</button>
    </form>

    <hr>

    <!-- カスタムボタン（CSSでスタイリング） -->
    <style>
        .primary-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .primary-btn:hover {
            background-color: #0056b3;
        }
    </style>

    <form>
        <button type=\"submit\" class=\"primary-btn\">登録する</button>
    </form>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 5
                    ],
                ],
            ],
            [
                'title' => '第6週：フォーム要素応用',
                'description' => 'select、checkbox、radio、textareaなどの入力要素',
                'sort_order' => 6,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'セレクトボックス', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'チェックボックスとラジオボタン', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'テキストエリア', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'フォームのバリデーション', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
        ]);

        // Milestone 3: セマンティックHTML (第7週～第9週)
        $milestone3 = $template->milestones()->create([
            'title' => 'セマンティックHTML',
            'description' => 'セマンティック要素とHTML5の新機能を学習',
            'sort_order' => 3,
            'estimated_hours' => 24,
            'deliverables' => [
                'セマンティック要素を理解',
                'アクセシビリティを考慮できる',
                'HTML5の新要素を使える'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '第7週：セマンティック要素',
                'description' => 'header、nav、main、article、section、aside、footer',
                'sort_order' => 7,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'セマンティックHTMLとは', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'ページ構造要素', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'コンテンツ構造要素', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'セマンティックHTMLとは',
                        'content' => "# セマンティックHTML

**セマンティックHTML**は、HTMLタグに**意味**を持たせるアプローチです。

## なぜセマンティックHTMLが重要か

### 1. アクセシビリティの向上

スクリーンリーダーなどの支援技術が、ページ構造を正しく理解できます。

```html
<!-- 悪い例 ✗ -->
<div class=\"header\">ヘッダー</div>
<div class=\"nav\">ナビゲーション</div>
<div class=\"content\">コンテンツ</div>

<!-- 良い例 ✓ -->
<header>ヘッダー</header>
<nav>ナビゲーション</nav>
<main>コンテンツ</main>
```

### 2. SEO効果

検索エンジンがページの構造と内容を理解しやすくなります。

### 3. コードの可読性

開発者が見て、各部分の役割がすぐわかります。

### 4. メンテナンス性

意味のあるタグを使うことで、後から修正しやすくなります。

## div vs セマンティック要素

### divを使った構造（非セマンティック）

```html
<div class=\"page\">
    <div class=\"header\">
        <div class=\"logo\">サイト名</div>
        <div class=\"menu\">メニュー</div>
    </div>
    <div class=\"main-content\">
        <div class=\"article\">記事</div>
        <div class=\"sidebar\">サイドバー</div>
    </div>
    <div class=\"footer\">フッター</div>
</div>
```

### セマンティック要素を使った構造

```html
<div class=\"page\">
    <header>
        <h1>サイト名</h1>
        <nav>メニュー</nav>
    </header>
    <main>
        <article>記事</article>
        <aside>サイドバー</aside>
    </main>
    <footer>フッター</footer>
</div>
```

## 主なセマンティック要素

- `<header>`: ヘッダー（ページまたはセクションの導入部）
- `<nav>`: ナビゲーション
- `<main>`: メインコンテンツ
- `<article>`: 独立したコンテンツ
- `<section>`: セクション（関連するコンテンツのまとまり）
- `<aside>`: 補足情報（サイドバーなど）
- `<footer>`: フッター（ページまたはセクションの末尾）

## いつdivを使うか

divは**意味がない**汎用的なコンテナです。

```html
<!-- divを使うべき場合 -->
<div class=\"container\">  <!-- レイアウト用のラッパー -->
<div class=\"card\">       <!-- スタイリング用のグループ -->
<div class=\"flex-box\">   <!-- CSSレイアウト用 -->
```

**原則**: 適切なセマンティック要素があればそれを使い、なければdivを使う",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ページ構造の基本',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>セマンティックHTML</title>
    <style>
        header { background: #333; color: white; padding: 20px; }
        nav { background: #666; padding: 10px; }
        nav a { color: white; margin: 0 10px; }
        main { display: flex; padding: 20px; }
        article { flex: 3; padding: 20px; background: #f9f9f9; }
        aside { flex: 1; padding: 20px; background: #e9e9e9; margin-left: 20px; }
        footer { background: #333; color: white; padding: 20px; text-align: center; }
    </style>
</head>
<body>
    <!-- ヘッダー：サイト全体の導入部 -->
    <header>
        <h1>私のWebサイト</h1>
        <p>セマンティックHTMLのサンプル</p>
    </header>

    <!-- ナビゲーション：サイト内リンク -->
    <nav>
        <a href=\"#home\">ホーム</a>
        <a href=\"#about\">概要</a>
        <a href=\"#services\">サービス</a>
        <a href=\"#contact\">お問い合わせ</a>
    </nav>

    <!-- メインコンテンツ：ページの主要部分 -->
    <main>
        <!-- 記事：独立したコンテンツ -->
        <article>
            <h2>記事のタイトル</h2>
            <p>公開日: <time datetime=\"2024-01-15\">2024年1月15日</time></p>
            <p>これは記事の本文です。articleタグは、それ単体で意味をなすコンテンツに使用します。</p>

            <!-- 記事内のセクション -->
            <section>
                <h3>セクション1</h3>
                <p>記事内の関連するコンテンツをグループ化します。</p>
            </section>

            <section>
                <h3>セクション2</h3>
                <p>見出しと内容のセットで使用します。</p>
            </section>
        </article>

        <!-- サイドバー：補足情報 -->
        <aside>
            <h3>関連情報</h3>
            <ul>
                <li>関連記事1</li>
                <li>関連記事2</li>
                <li>人気記事</li>
            </ul>
        </aside>
    </main>

    <!-- フッター：サイト全体の末尾 -->
    <footer>
        <p>&copy; 2024 私のWebサイト. All rights reserved.</p>
        <p>お問い合わせ: info@example.com</p>
    </footer>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'セマンティック要素の使い分け',
                        'content' => "# セマンティック要素の使い分け

## header

ページやセクションの**導入部**を表します。

```html
<!-- ページ全体のheader -->
<header>
    <h1>サイト名</h1>
    <nav><!-- ナビゲーション --></nav>
</header>

<!-- article内のheader -->
<article>
    <header>
        <h2>記事タイトル</h2>
        <p>著者: 田中太郎</p>
        <time datetime=\"2024-01-15\">2024年1月15日</time>
    </header>
    <p>記事の本文...</p>
</article>
```

**用途**: ロゴ、サイト名、ナビゲーション、記事のメタ情報

## nav

**ナビゲーションリンク**のセクション。

```html
<nav>
    <ul>
        <li><a href=\"/\">ホーム</a></li>
        <li><a href=\"/about\">会社概要</a></li>
        <li><a href=\"/products\">製品</a></li>
        <li><a href=\"/contact\">お問い合わせ</a></li>
    </ul>
</nav>
```

**注意**: すべてのリンク集がnavではない。主要なナビゲーションのみに使用。

```html
<!-- 良い例 ✓ -->
<nav><!-- メインメニュー --></nav>

<!-- 悪い例 ✗ -->
<nav><!-- フッターのSNSリンク --></nav>  <!-- navほど重要でないリンク集 -->
```

## main

ページの**メインコンテンツ**。1ページに1つだけ。

```html
<body>
    <header><!-- ヘッダー --></header>
    <nav><!-- ナビゲーション --></nav>

    <main>
        <!-- ページの主要コンテンツ -->
        <h1>ページタイトル</h1>
        <p>メインコンテンツ...</p>
    </main>

    <footer><!-- フッター --></footer>
</body>
```

**ルール**:
- 1ページに1つのみ
- `<header>`, `<nav>`, `<footer>`, `<aside>`の中には入れない

## article

**独立したコンテンツ**。それ単体で意味をなす。

```html
<!-- ブログ記事 -->
<article>
    <h2>HTMLの基礎</h2>
    <p>HTMLについて...</p>
</article>

<!-- フォーラムの投稿 -->
<article>
    <h3>投稿タイトル</h3>
    <p>投稿者: 田中</p>
    <p>投稿内容...</p>
</article>

<!-- 商品カード -->
<article class=\"product\">
    <h3>商品A</h3>
    <p>価格: 1,000円</p>
</article>
```

**判断基準**: RSSフィードに含められるか？ 単体で配信できるか？

## section

**関連するコンテンツのまとまり**。見出しを持つべき。

```html
<article>
    <h1>HTML完全ガイド</h1>

    <section>
        <h2>第1章: HTMLとは</h2>
        <p>HTMLの基礎...</p>
    </section>

    <section>
        <h2>第2章: タグの使い方</h2>
        <p>タグについて...</p>
    </section>
</article>
```

**section vs div**:
- section: 論理的なセクション（見出しがある）
- div: スタイリング用のグループ（見出し不要）

## aside

**補足情報**や**関連コンテンツ**。

```html
<!-- サイドバー -->
<aside>
    <h3>関連記事</h3>
    <ul>
        <li><a href=\"/article1\">記事1</a></li>
        <li><a href=\"/article2\">記事2</a></li>
    </ul>
</aside>

<!-- 記事内の補足 -->
<article>
    <p>メインの内容...</p>

    <aside>
        <h4>豆知識</h4>
        <p>補足情報...</p>
    </aside>
</article>
```

**用途**: サイドバー、広告、関連リンク、注釈

## footer

ページやセクションの**末尾**。

```html
<!-- ページ全体のfooter -->
<footer>
    <p>&copy; 2024 My Company</p>
    <nav>
        <a href=\"/privacy\">プライバシーポリシー</a>
        <a href=\"/terms\">利用規約</a>
    </nav>
</footer>

<!-- article内のfooter -->
<article>
    <h2>記事タイトル</h2>
    <p>記事本文...</p>

    <footer>
        <p>著者: 田中太郎</p>
        <p>カテゴリ: Web開発</p>
    </footer>
</article>
```

**用途**: 著作権表示、連絡先、関連リンク、著者情報",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践的なページ構造',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>ブログサイト</title>
</head>
<body>
    <!-- サイト全体のヘッダー -->
    <header>
        <h1>テックブログ</h1>
        <p>Web開発の最新情報</p>

        <!-- メインナビゲーション -->
        <nav aria-label=\"メインメニュー\">
            <ul>
                <li><a href=\"/\">ホーム</a></li>
                <li><a href=\"/articles\">記事一覧</a></li>
                <li><a href=\"/about\">このブログについて</a></li>
                <li><a href=\"/contact\">お問い合わせ</a></li>
            </ul>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main>
        <!-- ブログ記事 -->
        <article>
            <!-- 記事のヘッダー -->
            <header>
                <h2>HTMLセマンティック要素の使い方</h2>
                <p>
                    投稿日: <time datetime=\"2024-01-15\">2024年1月15日</time> |
                    著者: 田中太郎 |
                    カテゴリ: <a href=\"/category/html\">HTML</a>
                </p>
            </header>

            <!-- 記事本文 -->
            <section>
                <h3>はじめに</h3>
                <p>この記事では、HTMLのセマンティック要素について解説します。</p>
            </section>

            <section>
                <h3>セマンティック要素とは</h3>
                <p>セマンティック要素は、タグに意味を持たせる...</p>

                <!-- セクション内の補足 -->
                <aside>
                    <h4>💡 ポイント</h4>
                    <p>divよりもセマンティック要素を優先しましょう。</p>
                </aside>
            </section>

            <section>
                <h3>まとめ</h3>
                <p>セマンティックHTMLを使うことで...</p>
            </section>

            <!-- 記事のフッター -->
            <footer>
                <p>タグ: <a href=\"/tag/html\">#HTML</a> <a href=\"/tag/semantic\">#セマンティック</a></p>
                <p>この記事が役に立ちましたか？</p>
            </footer>
        </article>

        <!-- 別の記事 -->
        <article>
            <header>
                <h2>CSSグリッドレイアウト入門</h2>
                <p>投稿日: <time datetime=\"2024-01-10\">2024年1月10日</time></p>
            </header>
            <p>CSSグリッドの基本的な使い方を紹介します...</p>
            <a href=\"/articles/css-grid\">続きを読む</a>
        </article>

        <!-- サイドバー（補足情報） -->
        <aside>
            <section>
                <h3>人気記事</h3>
                <ul>
                    <li><a href=\"/articles/1\">JavaScriptの基礎</a></li>
                    <li><a href=\"/articles/2\">Reactを始めよう</a></li>
                    <li><a href=\"/articles/3\">レスポンシブデザイン</a></li>
                </ul>
            </section>

            <section>
                <h3>カテゴリ</h3>
                <nav aria-label=\"カテゴリー\">
                    <ul>
                        <li><a href=\"/category/html\">HTML</a></li>
                        <li><a href=\"/category/css\">CSS</a></li>
                        <li><a href=\"/category/javascript\">JavaScript</a></li>
                    </ul>
                </nav>
            </section>
        </aside>
    </main>

    <!-- サイト全体のフッター -->
    <footer>
        <section>
            <h3>このサイトについて</h3>
            <p>Web開発に関する情報を発信しています。</p>
        </section>

        <section>
            <h3>リンク</h3>
            <nav aria-label=\"フッターリンク\">
                <ul>
                    <li><a href=\"/privacy\">プライバシーポリシー</a></li>
                    <li><a href=\"/terms\">利用規約</a></li>
                    <li><a href=\"/sitemap\">サイトマップ</a></li>
                </ul>
            </nav>
        </section>

        <p><small>&copy; 2024 テックブログ. All rights reserved.</small></p>
    </footer>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第8週：HTML5の新要素',
                'description' => 'figure、time、mark、detailsなどHTML5で追加された要素',
                'sort_order' => 8,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'figure と figcaption', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'time、mark、progress', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'details と summary', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'その他のHTML5要素', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第9週：アクセシビリティ',
                'description' => 'WAI-ARIA、alt属性、アクセシブルなHTML',
                'sort_order' => 9,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'アクセシビリティの重要性', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'ARIAロールと属性', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'キーボード操作とフォーカス', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
        ]);

        // Milestone 4: マルチメディアと高度な機能 (第10週～第12週)
        $milestone4 = $template->milestones()->create([
            'title' => 'マルチメディアと高度な機能',
            'description' => '画像、音声、動画、Canvas、SVGの使い方',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                'マルチメディア要素を使える',
                'レスポンシブ画像を実装できる',
                'Canvas/SVGの基本を理解'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第10週：画像とメディア',
                'description' => 'レスポンシブ画像、picture要素、srcset',
                'sort_order' => 10,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'レスポンシブ画像の基本', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'picture要素とsrcset', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => '画像フォーマットの選択', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第11週：Audio と Video',
                'description' => 'audio、video要素、メディアコントロール',
                'sort_order' => 11,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'audio要素の基本', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'video要素の基本', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'メディアの制御', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => '字幕とトラック', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第12週：Canvas と SVG',
                'description' => 'Canvas APIの基礎、SVGとの違い',
                'sort_order' => 12,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'Canvas要素の基本', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'SVGの基本', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'CanvasとSVGの使い分け', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'アイコンとグラフィックス', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
        ]);

        // Milestone 5: 総合課題 (第13週～第15週)
        $milestone5 = $template->milestones()->create([
            'title' => '総合課題',
            'description' => 'これまでの学習を活かした総合的なWebページ制作',
            'sort_order' => 5,
            'estimated_hours' => 24,
            'deliverables' => [
                'レスポンシブなWebページを作成',
                'セマンティックなHTMLを書ける',
                '総合的なWebサイトを完成'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => '第13週：レスポンシブHTML',
                'description' => 'メディアクエリ、フレキシブルレイアウト',
                'sort_order' => 13,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'レスポンシブデザインの概念', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'ビューポートとメディアクエリ', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'モバイルファーストの考え方', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第14週：総合課題①',
                'description' => 'ポートフォリオサイトまたはブログサイトの作成',
                'sort_order' => 14,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'サイト設計', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'HTML構造の実装', 'estimated_minutes' => 240, 'sort_order' => 2],
                    ['title' => 'コンテンツの追加', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第15週：総合課題②（最終発表）',
                'description' => '最終調整、バリデーション、発表準備',
                'sort_order' => 15,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'HTMLバリデーション', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'アクセシビリティチェック', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'SEO最適化', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => '発表準備とドキュメント作成', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
        ]);
    }
}
