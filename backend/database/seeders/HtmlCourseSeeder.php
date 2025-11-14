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
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'セレクトボックス（select）',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>セレクトボックス</title>
</head>
<body>
    <h1>セレクトボックスの使い方</h1>

    <form action=\"/submit\" method=\"POST\">
        <!-- 基本的なセレクトボックス -->
        <label for=\"country\">国を選択：</label>
        <select id=\"country\" name=\"country\" required>
            <option value=\"\">-- 選択してください --</option>
            <option value=\"jp\">日本</option>
            <option value=\"us\">アメリカ</option>
            <option value=\"uk\">イギリス</option>
            <option value=\"cn\">中国</option>
        </select>
        <br><br>

        <!-- デフォルト選択 -->
        <label for=\"language\">言語：</label>
        <select id=\"language\" name=\"language\">
            <option value=\"ja\" selected>日本語</option>
            <option value=\"en\">English</option>
            <option value=\"zh\">中文</option>
        </select>
        <br><br>

        <!-- optgroupでグループ化 -->
        <label for=\"food\">好きな食べ物：</label>
        <select id=\"food\" name=\"food\">
            <optgroup label=\"和食\">
                <option value=\"sushi\">寿司</option>
                <option value=\"ramen\">ラーメン</option>
                <option value=\"tempura\">天ぷら</option>
            </optgroup>
            <optgroup label=\"洋食\">
                <option value=\"pasta\">パスタ</option>
                <option value=\"pizza\">ピザ</option>
                <option value=\"steak\">ステーキ</option>
            </optgroup>
            <optgroup label=\"中華\">
                <option value=\"mapo\">麻婆豆腐</option>
                <option value=\"gyoza\">餃子</option>
            </optgroup>
        </select>
        <br><br>

        <!-- 複数選択可能 -->
        <label for=\"skills\">スキル（複数選択可）：</label>
        <select id=\"skills\" name=\"skills[]\" multiple size=\"5\">
            <option value=\"html\">HTML</option>
            <option value=\"css\">CSS</option>
            <option value=\"js\">JavaScript</option>
            <option value=\"php\">PHP</option>
            <option value=\"python\">Python</option>
            <option value=\"java\">Java</option>
        </select>
        <br>
        <small>Ctrl/Cmd + クリックで複数選択</small>
        <br><br>

        <!-- 無効化されたオプション -->
        <label for=\"plan\">プラン：</label>
        <select id=\"plan\" name=\"plan\">
            <option value=\"free\">無料プラン</option>
            <option value=\"basic\">ベーシック</option>
            <option value=\"pro\">プロ</option>
            <option value=\"enterprise\" disabled>エンタープライズ（準備中）</option>
        </select>
        <br><br>

        <button type=\"submit\">送信</button>
    </form>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'チェックボックスとラジオボタン',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>チェックボックスとラジオボタン</title>
    <style>
        fieldset {
            margin-bottom: 20px;
            padding: 15px;
            border: 2px solid #ddd;
        }
        legend {
            font-weight: bold;
            padding: 0 10px;
        }
        label {
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>チェックボックスとラジオボタン</h1>

    <form action=\"/submit\" method=\"POST\">
        <!-- ラジオボタン（単一選択） -->
        <fieldset>
            <legend>性別（必須）</legend>
            <label>
                <input type=\"radio\" name=\"gender\" value=\"male\" required>
                男性
            </label>
            <label>
                <input type=\"radio\" name=\"gender\" value=\"female\" required>
                女性
            </label>
            <label>
                <input type=\"radio\" name=\"gender\" value=\"other\" required>
                その他
            </label>
        </fieldset>

        <!-- デフォルト選択されたラジオボタン -->
        <fieldset>
            <legend>メール受信設定</legend>
            <label>
                <input type=\"radio\" name=\"email_opt\" value=\"yes\" checked>
                受け取る
            </label>
            <label>
                <input type=\"radio\" name=\"email_opt\" value=\"no\">
                受け取らない
            </label>
        </fieldset>

        <!-- チェックボックス（複数選択可） -->
        <fieldset>
            <legend>趣味（複数選択可）</legend>
            <label>
                <input type=\"checkbox\" name=\"hobby[]\" value=\"sports\">
                スポーツ
            </label>
            <label>
                <input type=\"checkbox\" name=\"hobby[]\" value=\"music\">
                音楽
            </label>
            <label>
                <input type=\"checkbox\" name=\"hobby[]\" value=\"reading\" checked>
                読書
            </label>
            <label>
                <input type=\"checkbox\" name=\"hobby[]\" value=\"travel\">
                旅行
            </label>
            <label>
                <input type=\"checkbox\" name=\"hobby[]\" value=\"cooking\">
                料理
            </label>
        </fieldset>

        <!-- 単一のチェックボックス（同意確認） -->
        <fieldset>
            <legend>確認事項</legend>
            <label>
                <input type=\"checkbox\" name=\"terms\" value=\"agreed\" required>
                利用規約に同意する（必須）
            </label>
            <label>
                <input type=\"checkbox\" name=\"newsletter\" value=\"yes\">
                ニュースレターを受け取る（任意）
            </label>
        </fieldset>

        <!-- 無効化されたチェックボックス -->
        <fieldset>
            <legend>プレミアム機能（アップグレード必要）</legend>
            <label>
                <input type=\"checkbox\" name=\"premium1\" value=\"feature1\" disabled>
                高度な分析機能（プレミアム限定）
            </label>
            <label>
                <input type=\"checkbox\" name=\"premium2\" value=\"feature2\" disabled>
                優先サポート（プレミアム限定）
            </label>
        </fieldset>

        <button type=\"submit\">送信</button>
        <button type=\"reset\">リセット</button>
    </form>

    <hr>

    <h2>ポイント</h2>
    <ul>
        <li><strong>ラジオボタン</strong>: 同じ name 属性のグループから1つだけ選択</li>
        <li><strong>チェックボックス</strong>: 複数選択可能。配列として送信する場合は name=\"hobby[]\"</li>
        <li><strong>checked</strong>: デフォルトで選択状態にする</li>
        <li><strong>required</strong>: 必須項目にする</li>
        <li><strong>disabled</strong>: 選択不可にする</li>
    </ul>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'テキストエリア（textarea）',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>テキストエリア</title>
    <style>
        textarea {
            font-family: sans-serif;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .char-counter {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>テキストエリアの使い方</h1>

    <form action=\"/submit\" method=\"POST\">
        <!-- 基本的なテキストエリア -->
        <label for=\"message\">メッセージ：</label><br>
        <textarea id=\"message\" name=\"message\" rows=\"5\" cols=\"50\" required>
        </textarea>
        <br><br>

        <!-- デフォルトテキスト入り -->
        <label for=\"bio\">自己紹介：</label><br>
        <textarea id=\"bio\" name=\"bio\" rows=\"4\" cols=\"60\">
ここに自己紹介を入力してください。
趣味や特技などを自由に書いてください。
        </textarea>
        <br><br>

        <!-- 最大文字数制限 -->
        <label for=\"review\">商品レビュー（最大200文字）：</label><br>
        <textarea
            id=\"review\"
            name=\"review\"
            rows=\"6\"
            cols=\"60\"
            maxlength=\"200\"
            placeholder=\"商品の感想を入力してください...\"
        ></textarea>
        <div class=\"char-counter\">残り文字数: 200</div>
        <br>

        <!-- 最小文字数制限 -->
        <label for=\"feedback\">フィードバック（最低50文字）：</label><br>
        <textarea
            id=\"feedback\"
            name=\"feedback\"
            rows=\"5\"
            cols=\"60\"
            minlength=\"50\"
            required
            placeholder=\"詳しいフィードバックをお願いします（50文字以上）\"
        ></textarea>
        <br><br>

        <!-- リサイズ不可 -->
        <label for=\"comment\">コメント：</label><br>
        <textarea
            id=\"comment\"
            name=\"comment\"
            rows=\"3\"
            cols=\"60\"
            style=\"resize: none;\"
            placeholder=\"リサイズできません\"
        ></textarea>
        <br><br>

        <!-- 横リサイズのみ -->
        <label for=\"note\">メモ：</label><br>
        <textarea
            id=\"note\"
            name=\"note\"
            rows=\"4\"
            cols=\"60\"
            style=\"resize: horizontal;\"
            placeholder=\"横方向のみリサイズ可能\"
        ></textarea>
        <br><br>

        <!-- 無効化されたテキストエリア -->
        <label for=\"disabled_text\">編集不可：</label><br>
        <textarea
            id=\"disabled_text\"
            name=\"disabled_text\"
            rows=\"3\"
            cols=\"60\"
            disabled
        >このテキストエリアは編集できません。</textarea>
        <br><br>

        <!-- 読み取り専用 -->
        <label for=\"readonly_text\">読み取り専用：</label><br>
        <textarea
            id=\"readonly_text\"
            name=\"readonly_text\"
            rows=\"3\"
            cols=\"60\"
            readonly
        >このテキストエリアは読み取り専用です。フォーム送信時にデータは送られます。</textarea>
        <br><br>

        <button type=\"submit\">送信</button>
    </form>

    <hr>

    <h2>属性の説明</h2>
    <ul>
        <li><strong>rows</strong>: 表示行数</li>
        <li><strong>cols</strong>: 表示列数（文字数）</li>
        <li><strong>maxlength</strong>: 最大文字数</li>
        <li><strong>minlength</strong>: 最小文字数</li>
        <li><strong>placeholder</strong>: プレースホルダーテキスト</li>
        <li><strong>required</strong>: 必須項目</li>
        <li><strong>disabled</strong>: 無効化（送信されない）</li>
        <li><strong>readonly</strong>: 読み取り専用（送信される）</li>
    </ul>

    <h2>CSSでのリサイズ制御</h2>
    <ul>
        <li><code>resize: none;</code> - リサイズ不可</li>
        <li><code>resize: vertical;</code> - 縦方向のみ</li>
        <li><code>resize: horizontal;</code> - 横方向のみ</li>
        <li><code>resize: both;</code> - 両方向（デフォルト）</li>
    </ul>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'フォームのバリデーション',
                        'content' => "# フォームのバリデーション

HTMLには、フォーム入力を検証するための**組み込みバリデーション機能**があります。

## 1. 必須入力（required）

```html
<input type=\"text\" name=\"username\" required>
<textarea name=\"message\" required></textarea>
<select name=\"country\" required>
    <option value=\"\">選択してください</option>
    <option value=\"jp\">日本</option>
</select>
```

**効果**: 未入力の場合、送信時にブラウザがエラーメッセージを表示

---

## 2. 文字数制限

### minlength / maxlength

```html
<!-- 最小5文字、最大20文字 -->
<input type=\"text\" name=\"username\" minlength=\"5\" maxlength=\"20\" required>

<!-- パスワードは8文字以上 -->
<input type=\"password\" name=\"password\" minlength=\"8\" required>

<!-- テキストエリアの文字数制限 -->
<textarea name=\"bio\" minlength=\"50\" maxlength=\"500\"></textarea>
```

---

## 3. 数値の範囲指定（min / max）

```html
<!-- 年齢: 18歳以上、100歳以下 -->
<input type=\"number\" name=\"age\" min=\"18\" max=\"100\" required>

<!-- 評価: 1～5の範囲 -->
<input type=\"number\" name=\"rating\" min=\"1\" max=\"5\" step=\"1\" required>

<!-- 価格: 0以上、小数点2桁まで -->
<input type=\"number\" name=\"price\" min=\"0\" step=\"0.01\">

<!-- 日付: 今日以降のみ選択可能 -->
<input type=\"date\" name=\"reservation\" min=\"2025-11-14\" required>
```

---

## 4. パターン検証（pattern）

正規表現を使ってカスタム検証ができます。

```html
<!-- 郵便番号（日本）: 123-4567 -->
<input
    type=\"text\"
    name=\"zipcode\"
    pattern=\"\\d{3}-\\d{4}\"
    placeholder=\"123-4567\"
    title=\"郵便番号は 123-4567 の形式で入力してください\"
    required
>

<!-- 電話番号: 090-1234-5678 または 03-1234-5678 -->
<input
    type=\"tel\"
    name=\"phone\"
    pattern=\"\\d{2,4}-\\d{3,4}-\\d{4}\"
    placeholder=\"090-1234-5678\"
    title=\"電話番号は 090-1234-5678 の形式で入力してください\"
    required
>

<!-- ユーザー名: 半角英数字とアンダースコアのみ、3～15文字 -->
<input
    type=\"text\"
    name=\"username\"
    pattern=\"[A-Za-z0-9_]{3,15}\"
    placeholder=\"user_name\"
    title=\"ユーザー名は半角英数字とアンダースコアで3～15文字\"
    required
>

<!-- パスワード: 最低8文字、英大文字・小文字・数字を含む -->
<input
    type=\"password\"
    name=\"password\"
    pattern=\"(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).{8,}\"
    title=\"8文字以上、英大文字・小文字・数字を含む\"
    required
>

<!-- URL: https:// で始まる -->
<input
    type=\"url\"
    name=\"website\"
    pattern=\"https://.*\"
    placeholder=\"https://example.com\"
    title=\"URLは https:// で始まる必要があります\"
>
```

### title 属性の重要性
- `pattern` を使う場合、**必ず title 属性でルールを説明**する
- ユーザーがエラーの理由を理解できる

---

## 5. 入力タイプによる自動バリデーション

### email
```html
<input type=\"email\" name=\"email\" required>
```
- メール形式（`user@example.com`）を自動検証
- スマホでは @ キーボードが表示される

### url
```html
<input type=\"url\" name=\"website\" required>
```
- URL形式（`https://example.com`）を自動検証

### tel
```html
<input type=\"tel\" name=\"phone\">
```
- スマホで数字キーボードが表示される
- 形式の自動検証はないため、`pattern` との併用推奨

### number
```html
<input type=\"number\" name=\"quantity\" min=\"1\" max=\"100\">
```
- 数値のみ入力可能
- `min`, `max`, `step` で範囲指定

---

## 6. カスタムエラーメッセージ（JavaScript）

```html
<form id=\"myForm\">
    <input type=\"text\" id=\"username\" name=\"username\" required minlength=\"5\">
    <button type=\"submit\">送信</button>
</form>

<script>
const input = document.getElementById('username');

input.addEventListener('invalid', function(e) {
    if (input.validity.valueMissing) {
        input.setCustomValidity('ユーザー名を入力してください');
    } else if (input.validity.tooShort) {
        input.setCustomValidity('ユーザー名は5文字以上で入力してください');
    }
});

input.addEventListener('input', function(e) {
    input.setCustomValidity(''); // エラーをリセット
});
</script>
```

---

## 7. novalidate 属性

フォームのHTML5バリデーションを**無効化**します。

```html
<!-- バリデーションを無効化（JavaScriptで独自検証する場合） -->
<form action=\"/submit\" method=\"POST\" novalidate>
    <input type=\"email\" name=\"email\" required>
    <button type=\"submit\">送信</button>
</form>
```

---

## 8. 実践例: 会員登録フォーム

```html
<form action=\"/register\" method=\"POST\">
    <h2>会員登録</h2>

    <!-- ユーザー名 -->
    <label for=\"username\">ユーザー名：</label>
    <input
        type=\"text\"
        id=\"username\"
        name=\"username\"
        pattern=\"[A-Za-z0-9_]{3,15}\"
        title=\"半角英数字とアンダースコアで3～15文字\"
        required
    >
    <br><br>

    <!-- メールアドレス -->
    <label for=\"email\">メールアドレス：</label>
    <input
        type=\"email\"
        id=\"email\"
        name=\"email\"
        required
    >
    <br><br>

    <!-- パスワード -->
    <label for=\"password\">パスワード：</label>
    <input
        type=\"password\"
        id=\"password\"
        name=\"password\"
        minlength=\"8\"
        pattern=\"(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).{8,}\"
        title=\"8文字以上、英大文字・小文字・数字を含む\"
        required
    >
    <br><br>

    <!-- 年齢 -->
    <label for=\"age\">年齢：</label>
    <input
        type=\"number\"
        id=\"age\"
        name=\"age\"
        min=\"18\"
        max=\"120\"
        required
    >
    <br><br>

    <!-- 利用規約 -->
    <label>
        <input type=\"checkbox\" name=\"terms\" value=\"agreed\" required>
        利用規約に同意する
    </label>
    <br><br>

    <button type=\"submit\">登録</button>
</form>
```

---

## まとめ

| 属性 | 用途 |
|------|------|
| `required` | 必須入力 |
| `minlength` / `maxlength` | 文字数制限 |
| `min` / `max` | 数値・日付の範囲 |
| `pattern` | 正規表現による検証 |
| `type=\"email\"` | メール形式の検証 |
| `type=\"url\"` | URL形式の検証 |
| `title` | エラーメッセージのヒント |

**ベストプラクティス**:
- HTML5バリデーションは**クライアント側の検証**なので、必ず**サーバー側でも検証**する
- `title` 属性でユーザーに分かりやすいメッセージを提供する
- モバイルユーザーのために適切な `type` を使う（`email`, `tel`, `number` など）",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践: お問い合わせフォーム',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>お問い合わせフォーム</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Hiragino Sans', 'Yu Gothic', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .required {
            color: red;
            margin-left: 5px;
        }
        input[type=\"text\"],
        input[type=\"email\"],
        input[type=\"tel\"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        input:invalid,
        select:invalid,
        textarea:invalid {
            border-color: #ff6b6b;
        }
        .radio-group,
        .checkbox-group {
            margin-top: 10px;
        }
        .radio-group label,
        .checkbox-group label {
            display: inline-block;
            margin-right: 15px;
            font-weight: normal;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .hint {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <h1>お問い合わせフォーム</h1>

        <form action=\"/submit\" method=\"POST\">
            <!-- お名前 -->
            <div class=\"form-group\">
                <label for=\"name\">
                    お名前<span class=\"required\">*</span>
                </label>
                <input
                    type=\"text\"
                    id=\"name\"
                    name=\"name\"
                    placeholder=\"山田 太郎\"
                    minlength=\"2\"
                    maxlength=\"50\"
                    required
                >
            </div>

            <!-- メールアドレス -->
            <div class=\"form-group\">
                <label for=\"email\">
                    メールアドレス<span class=\"required\">*</span>
                </label>
                <input
                    type=\"email\"
                    id=\"email\"
                    name=\"email\"
                    placeholder=\"example@example.com\"
                    required
                >
            </div>

            <!-- 電話番号 -->
            <div class=\"form-group\">
                <label for=\"phone\">電話番号</label>
                <input
                    type=\"tel\"
                    id=\"phone\"
                    name=\"phone\"
                    pattern=\"\\d{2,4}-\\d{3,4}-\\d{4}\"
                    placeholder=\"090-1234-5678\"
                    title=\"電話番号は 090-1234-5678 の形式で入力してください\"
                >
                <div class=\"hint\">例: 090-1234-5678</div>
            </div>

            <!-- お問い合わせ種別 -->
            <div class=\"form-group\">
                <label for=\"category\">
                    お問い合わせ種別<span class=\"required\">*</span>
                </label>
                <select id=\"category\" name=\"category\" required>
                    <option value=\"\">-- 選択してください --</option>
                    <option value=\"product\">商品について</option>
                    <option value=\"service\">サービスについて</option>
                    <option value=\"technical\">技術的な質問</option>
                    <option value=\"billing\">料金・請求について</option>
                    <option value=\"other\">その他</option>
                </select>
            </div>

            <!-- 優先度 -->
            <div class=\"form-group\">
                <label>
                    優先度<span class=\"required\">*</span>
                </label>
                <div class=\"radio-group\">
                    <label>
                        <input type=\"radio\" name=\"priority\" value=\"low\" required>
                        低
                    </label>
                    <label>
                        <input type=\"radio\" name=\"priority\" value=\"medium\" checked>
                        中
                    </label>
                    <label>
                        <input type=\"radio\" name=\"priority\" value=\"high\">
                        高
                    </label>
                </div>
            </div>

            <!-- お問い合わせ内容 -->
            <div class=\"form-group\">
                <label for=\"message\">
                    お問い合わせ内容<span class=\"required\">*</span>
                </label>
                <textarea
                    id=\"message\"
                    name=\"message\"
                    rows=\"6\"
                    minlength=\"10\"
                    maxlength=\"1000\"
                    placeholder=\"お問い合わせ内容を詳しくご記入ください（10文字以上）\"
                    required
                ></textarea>
                <div class=\"hint\">10～1000文字で入力してください</div>
            </div>

            <!-- 希望する連絡方法 -->
            <div class=\"form-group\">
                <label>希望する連絡方法（複数選択可）</label>
                <div class=\"checkbox-group\">
                    <label>
                        <input type=\"checkbox\" name=\"contact_method[]\" value=\"email\" checked>
                        メール
                    </label>
                    <label>
                        <input type=\"checkbox\" name=\"contact_method[]\" value=\"phone\">
                        電話
                    </label>
                    <label>
                        <input type=\"checkbox\" name=\"contact_method[]\" value=\"chat\">
                        チャット
                    </label>
                </div>
            </div>

            <!-- 個人情報の取り扱い -->
            <div class=\"form-group\">
                <div class=\"checkbox-group\">
                    <label>
                        <input type=\"checkbox\" name=\"privacy\" value=\"agreed\" required>
                        <a href=\"/privacy\" target=\"_blank\">個人情報の取り扱い</a>に同意する<span class=\"required\">*</span>
                    </label>
                </div>
            </div>

            <!-- 送信ボタン -->
            <button type=\"submit\" class=\"btn-submit\">送信する</button>
        </form>
    </div>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 5
                    ],
                ],
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
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'figure と figcaption',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>figure と figcaption</title>
    <style>
        figure {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        figcaption {
            margin-top: 10px;
            font-style: italic;
            color: #666;
            text-align: center;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    <h1>figure と figcaption の使い方</h1>

    <!-- 基本的な使い方：画像 + キャプション -->
    <figure>
        <img src=\"sunset.jpg\" alt=\"美しい夕焼けの風景\">
        <figcaption>図1: 海に沈む夕日（2024年1月撮影）</figcaption>
    </figure>

    <!-- コードスニペット -->
    <figure>
        <pre><code>
function greet(name) {
    return `Hello, ${name}!`;
}
        </code></pre>
        <figcaption>リスト1: JavaScriptの挨拶関数</figcaption>
    </figure>

    <!-- 引用文 -->
    <figure>
        <blockquote>
            <p>「シンプルであることは究極の洗練である。」</p>
        </blockquote>
        <figcaption>— レオナルド・ダ・ヴィンチ</figcaption>
    </figure>

    <!-- 複数の画像 -->
    <figure>
        <img src=\"photo1.jpg\" alt=\"写真1\">
        <img src=\"photo2.jpg\" alt=\"写真2\">
        <img src=\"photo3.jpg\" alt=\"写真3\">
        <figcaption>図2: プロジェクトの進行状況（3枚組）</figcaption>
    </figure>

    <!-- 表 -->
    <figure>
        <table border=\"1\">
            <thead>
                <tr>
                    <th>年度</th>
                    <th>売上</th>
                    <th>利益</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2022</td>
                    <td>1000万円</td>
                    <td>200万円</td>
                </tr>
                <tr>
                    <td>2023</td>
                    <td>1500万円</td>
                    <td>350万円</td>
                </tr>
            </tbody>
        </table>
        <figcaption>表1: 年度別売上と利益の推移</figcaption>
    </figure>

    <!-- SVGグラフ -->
    <figure>
        <svg width=\"200\" height=\"100\">
            <rect x=\"10\" y=\"10\" width=\"40\" height=\"80\" fill=\"#4CAF50\" />
            <rect x=\"60\" y=\"30\" width=\"40\" height=\"60\" fill=\"#2196F3\" />
            <rect x=\"110\" y=\"20\" width=\"40\" height=\"70\" fill=\"#FF9800\" />
        </svg>
        <figcaption>図3: 四半期別売上グラフ</figcaption>
    </figure>

    <hr>

    <h2>ポイント</h2>
    <ul>
        <li><strong>figure</strong>: 自己完結型のコンテンツ（画像、図表、コード、引用など）</li>
        <li><strong>figcaption</strong>: figureの説明文（キャプション）</li>
        <li>figcaptionは figure の最初または最後に配置</li>
        <li>figcaption は省略可能だが、あると分かりやすい</li>
    </ul>

    <h2>いつ使うか</h2>
    <ul>
        <li>画像に説明が必要な場合</li>
        <li>コードスニペットに番号やタイトルを付ける場合</li>
        <li>引用文の出典を明記する場合</li>
        <li>図表に番号やキャプションを付ける場合</li>
    </ul>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'time、mark、progress',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>time、mark、progress</title>
    <style>
        mark {
            background-color: yellow;
            padding: 2px 4px;
        }
        .highlight-blue {
            background-color: lightblue;
        }
        progress {
            width: 300px;
            height: 30px;
        }
        .progress-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>HTML5の新要素：time、mark、progress</h1>

    <!-- ========== time 要素 ========== -->
    <h2>1. time 要素</h2>
    <p>日付や時刻を機械可読形式でマークアップします。</p>

    <h3>基本的な使い方</h3>

    <!-- 日付 -->
    <p>公開日: <time datetime=\"2024-01-15\">2024年1月15日</time></p>

    <!-- 時刻 -->
    <p>イベント開始: <time datetime=\"14:30\">14:30</time></p>

    <!-- 日付 + 時刻 -->
    <p>次回ミーティング: <time datetime=\"2024-02-20T10:00\">2024年2月20日 10:00</time></p>

    <!-- タイムゾーン付き -->
    <p>
        グローバル会議:
        <time datetime=\"2024-03-01T09:00:00+09:00\">
            2024年3月1日 9:00（日本時間）
        </time>
    </p>

    <!-- 期間 -->
    <p>セール期間: <time datetime=\"2024-01-01\">1月1日</time> ～ <time datetime=\"2024-01-31\">1月31日</time></p>

    <!-- 年 -->
    <p><time datetime=\"2024\">2024年</time>の目標</p>

    <!-- 月 -->
    <p><time datetime=\"2024-06\">2024年6月</time>にリリース予定</p>

    <h3>実用例</h3>
    <article>
        <h4>ブログ記事タイトル</h4>
        <p>
            投稿: <time datetime=\"2024-01-10T15:30:00+09:00\" pubdate>2024年1月10日 15:30</time>
            <br>
            更新: <time datetime=\"2024-01-12T09:00:00+09:00\">2024年1月12日 9:00</time>
        </p>
    </article>

    <hr>

    <!-- ========== mark 要素 ========== -->
    <h2>2. mark 要素</h2>
    <p>テキストをハイライト（強調表示）します。</p>

    <h3>基本的な使い方</h3>

    <!-- デフォルトのハイライト -->
    <p>検索結果: HTMLの<mark>セマンティック要素</mark>について解説します。</p>

    <!-- 検索キーワードのハイライト -->
    <p>「<mark>JavaScript</mark>」の検索結果を3件見つけました。</p>

    <!-- 重要な部分の強調 -->
    <p>
        注意: <mark>この機能は2024年12月31日でサポート終了</mark>となります。
    </p>

    <!-- カスタムスタイル -->
    <p>
        お知らせ:
        <mark class=\"highlight-blue\">新機能が追加されました！</mark>
    </p>

    <h3>markとstrongの違い</h3>
    <ul>
        <li><code>&lt;mark&gt;</code>: <mark>視覚的なハイライト</mark>（検索結果など）</li>
        <li><code>&lt;strong&gt;</code>: <strong>重要性の強調</strong>（意味的に重要）</li>
    </ul>

    <hr>

    <!-- ========== progress 要素 ========== -->
    <h2>3. progress 要素</h2>
    <p>タスクの進行状況を表示します。</p>

    <h3>基本的な使い方</h3>

    <!-- 確定的な進行状況 -->
    <div class=\"progress-container\">
        <label for=\"file-progress\">ファイルアップロード:</label><br>
        <progress id=\"file-progress\" value=\"70\" max=\"100\">70%</progress>
        <span>70%</span>
    </div>

    <!-- 0%（開始前） -->
    <div class=\"progress-container\">
        <label>ダウンロード待機中:</label><br>
        <progress value=\"0\" max=\"100\">0%</progress>
        <span>0%</span>
    </div>

    <!-- 50%（半分完了） -->
    <div class=\"progress-container\">
        <label>インストール中:</label><br>
        <progress value=\"50\" max=\"100\">50%</progress>
        <span>50%</span>
    </div>

    <!-- 100%（完了） -->
    <div class=\"progress-container\">
        <label>アップロード完了:</label><br>
        <progress value=\"100\" max=\"100\">100%</progress>
        <span>100%</span>
    </div>

    <!-- 不確定な進行状況（value属性なし） -->
    <div class=\"progress-container\">
        <label>処理中（時間不明）:</label><br>
        <progress max=\"100\">処理中...</progress>
    </div>

    <h3>JavaScriptで動的に更新</h3>
    <div class=\"progress-container\">
        <label>ダウンロード進行状況:</label><br>
        <progress id=\"dynamic-progress\" value=\"0\" max=\"100\">0%</progress>
        <span id=\"progress-text\">0%</span>
        <br><br>
        <button onclick=\"startProgress()\">開始</button>
        <button onclick=\"stopProgress()\">停止</button>
        <button onclick=\"resetProgress()\">リセット</button>
    </div>

    <script>
        let progressValue = 0;
        let interval;

        function startProgress() {
            interval = setInterval(() => {
                if (progressValue < 100) {
                    progressValue += 5;
                    document.getElementById('dynamic-progress').value = progressValue;
                    document.getElementById('progress-text').textContent = progressValue + '%';
                } else {
                    clearInterval(interval);
                }
            }, 200);
        }

        function stopProgress() {
            clearInterval(interval);
        }

        function resetProgress() {
            clearInterval(interval);
            progressValue = 0;
            document.getElementById('dynamic-progress').value = 0;
            document.getElementById('progress-text').textContent = '0%';
        }
    </script>

    <hr>

    <h2>まとめ</h2>
    <table border=\"1\" style=\"width: 100%; border-collapse: collapse;\">
        <thead>
            <tr>
                <th>要素</th>
                <th>用途</th>
                <th>主な属性</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>&lt;time&gt;</code></td>
                <td>日付・時刻のマークアップ</td>
                <td><code>datetime</code></td>
            </tr>
            <tr>
                <td><code>&lt;mark&gt;</code></td>
                <td>テキストのハイライト</td>
                <td>なし（CSSでスタイリング）</td>
            </tr>
            <tr>
                <td><code>&lt;progress&gt;</code></td>
                <td>進行状況の表示</td>
                <td><code>value</code>, <code>max</code></td>
            </tr>
        </tbody>
    </table>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'details と summary',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>details と summary</title>
    <style>
        details {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            background: #f9f9f9;
        }
        summary {
            cursor: pointer;
            font-weight: bold;
            padding: 10px;
            background: #e9e9e9;
            border-radius: 4px;
            user-select: none;
        }
        summary:hover {
            background: #d9d9d9;
        }
        details[open] summary {
            margin-bottom: 10px;
            background: #4CAF50;
            color: white;
        }
        .faq details {
            background: white;
            border-left: 4px solid #2196F3;
        }
        .nested-details {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h1>details と summary の使い方</h1>

    <!-- 基本的な使い方 -->
    <h2>1. 基本的な使い方</h2>
    <details>
        <summary>詳細を表示</summary>
        <p>ここに詳細な内容が表示されます。クリックすると開閉できます。</p>
    </details>

    <!-- デフォルトで開いた状態 -->
    <h2>2. デフォルトで開く（open属性）</h2>
    <details open>
        <summary>この項目は最初から開いています</summary>
        <p>open属性を付けると、ページ読み込み時に開いた状態になります。</p>
    </details>

    <!-- FAQ例 -->
    <h2>3. よくある質問（FAQ）</h2>
    <div class=\"faq\">
        <details>
            <summary>Q1: アカウントの作成方法は？</summary>
            <p>
                A: トップページの「新規登録」ボタンをクリックし、メールアドレスとパスワードを入力してください。
                確認メールが送信されますので、リンクをクリックして登録を完了してください。
            </p>
        </details>

        <details>
            <summary>Q2: パスワードを忘れた場合は？</summary>
            <p>
                A: ログイン画面の「パスワードを忘れた方」リンクをクリックし、登録済みのメールアドレスを入力してください。
                パスワード再設定用のリンクが送信されます。
            </p>
        </details>

        <details>
            <summary>Q3: 支払い方法は何がありますか？</summary>
            <p>A: 以下の支払い方法に対応しています：</p>
            <ul>
                <li>クレジットカード（Visa、MasterCard、JCB）</li>
                <li>デビットカード</li>
                <li>銀行振込</li>
                <li>コンビニ決済</li>
            </ul>
        </details>

        <details>
            <summary>Q4: 退会方法は？</summary>
            <p>
                A: 設定画面から「アカウント削除」を選択してください。
                ただし、削除後はデータの復元ができませんのでご注意ください。
            </p>
        </details>
    </div>

    <!-- ネスト（入れ子） -->
    <h2>4. ネストした詳細表示</h2>
    <details>
        <summary>📁 プログラミング言語</summary>
        <div class=\"nested-details\">
            <details>
                <summary>📄 JavaScript</summary>
                <p>Webブラウザで動作するスクリプト言語です。</p>
                <ul>
                    <li>ES6の新機能</li>
                    <li>非同期処理</li>
                    <li>フレームワーク（React、Vue、Angular）</li>
                </ul>
            </details>

            <details>
                <summary>📄 Python</summary>
                <p>汎用プログラミング言語で、データ分析やAIに強いです。</p>
                <ul>
                    <li>データサイエンス</li>
                    <li>機械学習</li>
                    <li>Web開発（Django、Flask）</li>
                </ul>
            </details>

            <details>
                <summary>📄 Java</summary>
                <p>オブジェクト指向言語で、エンタープライズシステムに使われます。</p>
            </details>
        </div>
    </details>

    <!-- 長文の折りたたみ -->
    <h2>5. 長文の折りたたみ</h2>
    <details>
        <summary>利用規約を読む（クリックして展開）</summary>
        <article>
            <h3>第1条（適用）</h3>
            <p>
                本規約は、本サービスの利用に関する条件を定めるものです。
                ユーザーは本規約に同意した上で本サービスを利用するものとします。
            </p>

            <h3>第2条（アカウント）</h3>
            <p>
                ユーザーは、本サービスを利用するためにアカウントを作成する必要があります。
                アカウント情報は正確かつ最新の状態に保つ責任があります。
            </p>

            <h3>第3条（禁止事項）</h3>
            <p>ユーザーは以下の行為を行ってはなりません：</p>
            <ul>
                <li>法令または公序良俗に違反する行為</li>
                <li>犯罪行為に関連する行為</li>
                <li>他のユーザーに対する迷惑行為</li>
                <li>知的財産権を侵害する行為</li>
            </ul>
        </article>
    </details>

    <!-- コードの折りたたみ -->
    <h2>6. コードスニペットの折りたたみ</h2>
    <details>
        <summary>JavaScriptのサンプルコードを表示</summary>
        <pre><code>
// ユーザー情報を取得する関数
async function fetchUserData(userId) {
    try {
        const response = await fetch(`/api/users/${userId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching user data:', error);
        return null;
    }
}

// 使用例
fetchUserData(123).then(user => {
    console.log(user);
});
        </code></pre>
    </details>

    <!-- 画像の折りたたみ -->
    <h2>7. 画像ギャラリーの折りたたみ</h2>
    <details>
        <summary>写真ギャラリーを表示（3枚）</summary>
        <div style=\"display: flex; gap: 10px;\">
            <img src=\"https://via.placeholder.com/150\" alt=\"写真1\">
            <img src=\"https://via.placeholder.com/150\" alt=\"写真2\">
            <img src=\"https://via.placeholder.com/150\" alt=\"写真3\">
        </div>
    </details>

    <hr>

    <h2>まとめ</h2>
    <ul>
        <li><code>&lt;details&gt;</code>: 折りたたみ可能なコンテナ</li>
        <li><code>&lt;summary&gt;</code>: クリック可能な見出し</li>
        <li><code>open</code>属性: デフォルトで開いた状態にする</li>
        <li>JavaScriptなしで動作する</li>
        <li>FAQ、長文、コード、画像などの折りたたみに便利</li>
    </ul>

    <h2>JavaScriptで制御</h2>
    <details id=\"controlled-details\">
        <summary>JavaScriptで開閉制御</summary>
        <p>ボタンをクリックして、この詳細を開閉できます。</p>
    </details>

    <button onclick=\"document.getElementById('controlled-details').open = true\">開く</button>
    <button onclick=\"document.getElementById('controlled-details').open = false\">閉じる</button>
    <button onclick=\"document.getElementById('controlled-details').toggleAttribute('open')\">切り替え</button>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'その他のHTML5要素',
                        'content' => "# その他のHTML5要素

HTML5では、さまざまな便利な要素が追加されました。

---

## 1. meter（メーター）

**用途**: 既知の範囲内での数値を表示（ディスクの使用量、評価など）

```html
<!-- ディスク使用量 -->
<label for=\"disk\">ディスク使用量:</label>
<meter id=\"disk\" value=\"75\" min=\"0\" max=\"100\" low=\"30\" high=\"80\" optimum=\"20\">
    75%
</meter>
<span>75GB / 100GB</span>

<!-- 評価 -->
<label>商品評価:</label>
<meter value=\"4.5\" min=\"0\" max=\"5\" optimum=\"5\">4.5点</meter>

<!-- バッテリー残量 -->
<label>バッテリー:</label>
<meter value=\"25\" min=\"0\" max=\"100\" low=\"20\" high=\"80\" optimum=\"100\">25%</meter>
```

### 属性
- `value`: 現在の値
- `min`: 最小値（デフォルト: 0）
- `max`: 最大値（デフォルト: 1）
- `low`: 低い範囲の閾値
- `high`: 高い範囲の閾値
- `optimum`: 最適な値

### progress vs meter
- **progress**: タスクの進行状況（0% → 100%へ進む）
- **meter**: 既知の範囲内の測定値（常に変動）

---

## 2. datalist（データリスト）

**用途**: 入力候補のリストを提供（オートコンプリート）

```html
<label for=\"browser\">お気に入りのブラウザ:</label>
<input list=\"browsers\" id=\"browser\" name=\"browser\" placeholder=\"選択または入力\">

<datalist id=\"browsers\">
    <option value=\"Chrome\">
    <option value=\"Firefox\">
    <option value=\"Safari\">
    <option value=\"Edge\">
    <option value=\"Opera\">
</datalist>
```

### 実用例：都道府県選択

```html
<label for=\"prefecture\">都道府県:</label>
<input list=\"prefectures\" id=\"prefecture\" name=\"prefecture\" placeholder=\"都道府県を入力\">

<datalist id=\"prefectures\">
    <option value=\"北海道\">
    <option value=\"青森県\">
    <option value=\"東京都\">
    <option value=\"大阪府\">
    <option value=\"福岡県\">
    <option value=\"沖縄県\">
</datalist>
```

### URLの候補

```html
<label for=\"website\">Webサイト:</label>
<input type=\"url\" list=\"websites\" id=\"website\" name=\"website\">

<datalist id=\"websites\">
    <option value=\"https://www.google.com\" label=\"Google\">
    <option value=\"https://www.github.com\" label=\"GitHub\">
    <option value=\"https://www.stackoverflow.com\" label=\"Stack Overflow\">
</datalist>
```

---

## 3. output（計算結果の出力）

**用途**: フォームの計算結果を表示

```html
<form oninput=\"result.value = parseInt(a.value) + parseInt(b.value)\">
    <label>数値1:</label>
    <input type=\"number\" id=\"a\" name=\"a\" value=\"0\">
    +
    <label>数値2:</label>
    <input type=\"number\" id=\"b\" name=\"b\" value=\"0\">
    =
    <output name=\"result\" for=\"a b\">0</output>
</form>
```

### 価格計算の例

```html
<form oninput=\"total.value = parseInt(quantity.value) * parseInt(price.value)\">
    <label for=\"quantity\">数量:</label>
    <input type=\"number\" id=\"quantity\" name=\"quantity\" value=\"1\" min=\"1\">
    <br>

    <label for=\"price\">単価:</label>
    <input type=\"number\" id=\"price\" name=\"price\" value=\"1000\" min=\"0\">円
    <br>

    <label>合計金額:</label>
    <output name=\"total\" for=\"quantity price\">1000</output>円
</form>
```

---

## 4. dialog（ダイアログ）

**用途**: モーダルダイアログやポップアップの表示

```html
<button onclick=\"document.getElementById('myDialog').showModal()\">
    ダイアログを開く
</button>

<dialog id=\"myDialog\">
    <h2>確認</h2>
    <p>本当に削除しますか？</p>
    <form method=\"dialog\">
        <button value=\"cancel\">キャンセル</button>
        <button value=\"confirm\">OK</button>
    </form>
</dialog>

<script>
const dialog = document.getElementById('myDialog');
dialog.addEventListener('close', () => {
    console.log('ダイアログが閉じられました:', dialog.returnValue);
});
</script>
```

### モーダルとモードレスの違い

```html
<!-- モーダル（背景がブロックされる） -->
<button onclick=\"document.getElementById('modal').showModal()\">モーダルを開く</button>

<!-- モードレス（背景操作可能） -->
<button onclick=\"document.getElementById('modeless').show()\">モードレスを開く</button>

<dialog id=\"modal\">
    <p>これはモーダルダイアログです。</p>
    <button onclick=\"this.closest('dialog').close()\">閉じる</button>
</dialog>

<dialog id=\"modeless\">
    <p>これはモードレスダイアログです。</p>
    <button onclick=\"this.closest('dialog').close()\">閉じる</button>
</dialog>
```

---

## 5. wbr（改行の推奨位置）

**用途**: 長い単語の改行位置を指定

```html
<!-- 長いURLの改行 -->
<p>
    アクセス先:
    https://<wbr>www.<wbr>example.<wbr>com/<wbr>very/<wbr>long/<wbr>path/<wbr>to/<wbr>resource
</p>

<!-- 長いファイル名 -->
<p>
    ファイル名:
    super<wbr>_long<wbr>_file<wbr>_name<wbr>_with<wbr>_many<wbr>_words.pdf
</p>
```

### wbr と br の違い
- `<br>`: **必ず改行**する
- `<wbr>`: **必要に応じて改行**できる

---

## 6. template（テンプレート）

**用途**: JavaScriptで動的に複製するHTML片を定義

```html
<template id=\"user-card-template\">
    <div class=\"user-card\">
        <h3 class=\"name\"></h3>
        <p class=\"email\"></p>
        <button class=\"delete-btn\">削除</button>
    </div>
</template>

<div id=\"users-container\"></div>

<script>
const template = document.getElementById('user-card-template');
const container = document.getElementById('users-container');

// ユーザーデータ
const users = [
    { name: '田中太郎', email: 'tanaka@example.com' },
    { name: '佐藤花子', email: 'sato@example.com' }
];

// テンプレートから要素を複製
users.forEach(user => {
    const clone = template.content.cloneNode(true);
    clone.querySelector('.name').textContent = user.name;
    clone.querySelector('.email').textContent = user.email;
    container.appendChild(clone);
});
</script>
```

---

## まとめ

| 要素 | 用途 | 主な属性 |
|------|------|---------|
| `<meter>` | 範囲内の測定値 | `value`, `min`, `max`, `low`, `high`, `optimum` |
| `<datalist>` | 入力候補リスト | `id`（inputのlist属性と紐付け） |
| `<output>` | 計算結果の表示 | `for`, `name` |
| `<dialog>` | ダイアログ/モーダル | なし（JavaScriptで制御） |
| `<wbr>` | 改行推奨位置 | なし |
| `<template>` | 再利用可能なHTML片 | なし（JavaScriptで複製） |

---

## ベストプラクティス

1. **meter**: 進行状況には`<progress>`、測定値には`<meter>`を使う
2. **datalist**: ユーザーが自由入力もできる選択肢として使う
3. **output**: フォーム内の計算結果を動的に表示する
4. **dialog**: モーダルには`showModal()`、モードレスには`show()`を使う
5. **wbr**: 長いURL、ファイル名、コードの改行位置に使う
6. **template**: 同じHTML構造を繰り返し生成する際に使う",
                        'sort_order' => 4
                    ],
                ],
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
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'アクセシビリティの重要性',
                        'content' => "# Webアクセシビリティとは

**Webアクセシビリティ**とは、障害の有無に関わらず、すべてのユーザーがWebサイトを利用できるようにすることです。

---

## なぜアクセシビリティが重要か

### 1. 法的義務

多くの国で、Webアクセシビリティは法律で義務付けられています。

- 日本: 障害者差別解消法
- アメリカ: ADA (Americans with Disabilities Act)
- EU: 欧州アクセシビリティ法

### 2. ビジネス上のメリット

- **ユーザー層の拡大**: 障害者、高齢者、一時的な障害を持つ人も利用可能
- **SEO向上**: アクセシブルなサイトは検索エンジンにも理解されやすい
- **ブランドイメージ向上**: 社会的責任を果たす企業として評価される

### 3. すべてのユーザーに恩恵

アクセシビリティの向上は、障害者だけでなく**すべてのユーザー**に役立ちます。

- **高齢者**: 視力・聴力の低下
- **一時的な障害**: 怪我で片手が使えない、明るい場所で画面が見にくい
- **状況的制約**: 騒がしい環境、低速なネットワーク、古いデバイス

---

## 障害の種類と対応

### 1. 視覚障害

**症状**:
- 全盲（完全に見えない）
- 弱視（部分的に見える）
- 色覚異常（色の識別が困難）

**対応**:
- スクリーンリーダー対応
- 適切なコントラスト比
- 色だけに依存しない情報提供

```html
<!-- 悪い例 ✗ -->
<button style=\"background: red;\">エラー</button>

<!-- 良い例 ✓ -->
<button style=\"background: red;\">
    <span aria-label=\"エラー\">❌ エラー</span>
</button>
```

### 2. 聴覚障害

**症状**:
- 全聾（完全に聞こえない）
- 難聴（部分的に聞こえる）

**対応**:
- 動画に字幕を提供
- 音声情報の文字化
- 視覚的なフィードバック

```html
<!-- 動画に字幕を付ける -->
<video controls>
    <source src=\"video.mp4\" type=\"video/mp4\">
    <track kind=\"subtitles\" src=\"subtitles_ja.vtt\" srclang=\"ja\" label=\"日本語\">
</video>
```

### 3. 運動障害

**症状**:
- マウスが使えない
- 細かい操作が困難

**対応**:
- キーボード操作のサポート
- 大きなクリックエリア
- 音声入力のサポート

```html
<!-- 十分な大きさのボタン -->
<button style=\"min-width: 44px; min-height: 44px; padding: 10px;\">
    送信
</button>
```

### 4. 認知障害

**症状**:
- 複雑な内容の理解が困難
- 集中力の維持が困難

**対応**:
- シンプルな言葉遣い
- 明確なナビゲーション
- 一貫したデザイン

---

## WCAG（Web Content Accessibility Guidelines）

**WCAG**は、Webアクセシビリティの国際標準です。

### 4つの原則（POUR）

1. **Perceivable（知覚可能）**
   - 情報とUIコンポーネントは、ユーザーが知覚できる方法で提示される

2. **Operable（操作可能）**
   - UIコンポーネントとナビゲーションは操作可能である

3. **Understandable（理解可能）**
   - 情報とUIの操作は理解可能である

4. **Robust（堅牢）**
   - コンテンツは、支援技術を含む様々なユーザーエージェントで解釈できる

### 適合レベル

- **Level A**: 最低限の要件
- **Level AA**: 中程度の要件（推奨）
- **Level AAA**: 最高レベルの要件

---

## 基本的なアクセシビリティのチェックリスト

### ✅ 画像

- [ ] すべての画像に`alt`属性を設定
- [ ] 装飾的な画像は`alt=\"\"`（空）にする
- [ ] 重要な情報を画像だけで提供しない

```html
<!-- 良い例 -->
<img src=\"logo.png\" alt=\"ABC株式会社\">
<img src=\"decoration.png\" alt=\"\" role=\"presentation\">
```

### ✅ リンクとボタン

- [ ] リンクテキストは目的が明確
- [ ] \"こちら\"だけのリンクを避ける
- [ ] ボタンは`<button>`タグを使う

```html
<!-- 悪い例 ✗ -->
<a href=\"/products\">こちら</a>

<!-- 良い例 ✓ -->
<a href=\"/products\">製品一覧を見る</a>
```

### ✅ フォーム

- [ ] すべての入力欄に`<label>`を設定
- [ ] エラーメッセージは分かりやすく
- [ ] 必須項目を明示

```html
<label for=\"email\">メールアドレス（必須）</label>
<input type=\"email\" id=\"email\" name=\"email\" required aria-required=\"true\">
```

### ✅ 見出し

- [ ] 見出しタグ（h1～h6）を適切に使う
- [ ] 見出しの階層をスキップしない

```html
<!-- 悪い例 ✗ -->
<h1>タイトル</h1>
<h3>セクション</h3> <!-- h2をスキップ -->

<!-- 良い例 ✓ -->
<h1>タイトル</h1>
<h2>セクション</h2>
<h3>サブセクション</h3>
```

### ✅ コントラスト比

- [ ] 文字と背景のコントラスト比は4.5:1以上（通常テキスト）
- [ ] 大きな文字は3:1以上

```css
/* 悪い例 ✗ */
color: #999; /* 背景が白の場合、コントラスト不足 */

/* 良い例 ✓ */
color: #333; /* 十分なコントラスト */
```

### ✅ キーボード操作

- [ ] すべての機能をキーボードで操作可能
- [ ] フォーカスの順序が論理的
- [ ] フォーカスの視覚的インジケーターがある

```css
/* フォーカススタイルを削除しない！ */
button:focus {
    outline: 2px solid blue;
}
```

---

## アクセシビリティ検証ツール

### 自動検証ツール

1. **Lighthouse**（Chrome DevTools）
2. **axe DevTools**（ブラウザ拡張）
3. **WAVE**（Web Accessibility Evaluation Tool）

### 手動検証

1. キーボードのみで操作してみる
2. スクリーンリーダーで確認（NVDA、JAWS、VoiceOverなど）
3. ブラウザのズーム機能で200%表示してみる

---

## まとめ

アクセシビリティは**すべてのユーザー**のためのものです。

- 法的義務であり、ビジネス上のメリットもある
- 基本的なHTML要素を正しく使うことが第一歩
- 自動ツールと手動テストを組み合わせて検証する
- 継続的な改善が重要",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'ARIAロールと属性',
                        'content' => "# WAI-ARIA（Accessible Rich Internet Applications）

**ARIA**は、動的なWebアプリケーションをアクセシブルにするための技術仕様です。

---

## ARIAの3つの主要機能

### 1. Role（役割）

要素の役割を支援技術に伝えます。

```html
<div role=\"button\" tabindex=\"0\">クリック</div>
```

### 2. Property（プロパティ）

要素の性質や関係を示します（変化しない属性）。

```html
<input type=\"text\" aria-label=\"検索\" aria-required=\"true\">
```

### 3. State（状態）

要素の現在の状態を示します（変化する属性）。

```html
<button aria-pressed=\"true\">選択中</button>
```

---

## ARIAの5つのルール

### ルール1: ARIAを使わずに済むなら使わない

ネイティブHTMLが最優先です。

```html
<!-- 悪い例 ✗ -->
<div role=\"button\" tabindex=\"0\" onclick=\"submit()\">送信</div>

<!-- 良い例 ✓ -->
<button onclick=\"submit()\">送信</button>
```

### ルール2: ネイティブのセマンティクスを変更しない

```html
<!-- 悪い例 ✗ -->
<h1 role=\"button\">見出しがボタン？</h1>

<!-- 良い例 ✓ -->
<h1>見出し</h1>
<button>ボタン</button>
```

### ルール3: すべてのARIA要素はキーボード操作可能にする

```html
<!-- ARIAを使った要素には tabindex が必要 -->
<div role=\"button\" tabindex=\"0\" onkeypress=\"handleKey(event)\">
    クリック
</div>
```

### ルール4: フォーカス可能な要素を role=\"presentation\" にしない

```html
<!-- 悪い例 ✗ -->
<button role=\"presentation\">ボタン</button>

<!-- 良い例 ✓ -->
<button>ボタン</button>
```

### ルール5: すべてのインタラクティブ要素にアクセシブルな名前を付ける

```html
<button aria-label=\"閉じる\">✕</button>
```

---

## 主要なARIAロール

### ランドマークロール

ページの主要な領域を示します。

```html
<!-- ヘッダー -->
<header role=\"banner\">
    <h1>サイト名</h1>
</header>

<!-- メインナビゲーション -->
<nav role=\"navigation\" aria-label=\"メインメニュー\">
    <ul>
        <li><a href=\"/\">ホーム</a></li>
        <li><a href=\"/about\">概要</a></li>
    </ul>
</nav>

<!-- メインコンテンツ -->
<main role=\"main\">
    <h2>記事タイトル</h2>
    <p>本文...</p>
</main>

<!-- サイドバー -->
<aside role=\"complementary\">
    <h3>関連記事</h3>
</aside>

<!-- フッター -->
<footer role=\"contentinfo\">
    <p>&copy; 2024</p>
</footer>

<!-- 検索フォーム -->
<form role=\"search\">
    <input type=\"search\" aria-label=\"サイト内検索\">
    <button type=\"submit\">検索</button>
</form>
```

### ウィジェットロール

インタラクティブなコンポーネントの役割を示します。

```html
<!-- タブ -->
<div role=\"tablist\">
    <button role=\"tab\" aria-selected=\"true\" aria-controls=\"panel1\">タブ1</button>
    <button role=\"tab\" aria-selected=\"false\" aria-controls=\"panel2\">タブ2</button>
</div>
<div id=\"panel1\" role=\"tabpanel\">内容1</div>
<div id=\"panel2\" role=\"tabpanel\" hidden>内容2</div>

<!-- アラート -->
<div role=\"alert\" aria-live=\"assertive\">
    エラーが発生しました。
</div>

<!-- ダイアログ -->
<div role=\"dialog\" aria-labelledby=\"dialog-title\" aria-modal=\"true\">
    <h2 id=\"dialog-title\">確認</h2>
    <p>削除しますか？</p>
    <button>OK</button>
    <button>キャンセル</button>
</div>
```

---

## 主要なARIA属性

### aria-label

要素のラベルを直接指定します。

```html
<button aria-label=\"メニューを閉じる\">
    ✕
</button>

<nav aria-label=\"パンくずリスト\">
    <a href=\"/\">ホーム</a> &gt; <a href=\"/products\">製品</a>
</nav>
```

### aria-labelledby

別の要素のIDを参照してラベルを指定します。

```html
<h2 id=\"section-title\">設定</h2>
<div aria-labelledby=\"section-title\">
    <p>設定項目...</p>
</div>
```

### aria-describedby

要素の詳細な説明を別の要素から参照します。

```html
<input
    type=\"password\"
    id=\"password\"
    aria-describedby=\"password-hint\"
>
<div id=\"password-hint\">
    パスワードは8文字以上で、英大文字・小文字・数字を含む必要があります。
</div>
```

### aria-hidden

要素をスクリーンリーダーから隠します。

```html
<!-- 装飾的なアイコンを隠す -->
<button>
    <span aria-hidden=\"true\">🔍</span>
    検索
</button>
```

### aria-live

動的に変化するコンテンツを通知します。

```html
<!-- polite: 現在の読み上げが終わってから通知 -->
<div aria-live=\"polite\">
    検索結果: 15件
</div>

<!-- assertive: すぐに通知（重要な情報） -->
<div role=\"alert\" aria-live=\"assertive\">
    エラー: 入力内容を確認してください
</div>
```

### aria-expanded

折りたたみ可能な要素の状態を示します。

```html
<button
    aria-expanded=\"false\"
    aria-controls=\"menu\"
    onclick=\"toggleMenu()\"
>
    メニュー
</button>
<ul id=\"menu\" hidden>
    <li>項目1</li>
    <li>項目2</li>
</ul>
```

### aria-pressed

トグルボタンの状態を示します。

```html
<button aria-pressed=\"false\" onclick=\"toggleBold()\">
    太字
</button>

<script>
function toggleBold() {
    const button = event.target;
    const pressed = button.getAttribute('aria-pressed') === 'true';
    button.setAttribute('aria-pressed', !pressed);
}
</script>
```

### aria-checked

チェックボックスやラジオボタンの状態を示します。

```html
<div role=\"checkbox\" aria-checked=\"false\" tabindex=\"0\">
    利用規約に同意する
</div>
```

### aria-invalid

フォーム入力のエラー状態を示します。

```html
<input
    type=\"email\"
    id=\"email\"
    aria-invalid=\"true\"
    aria-describedby=\"email-error\"
>
<div id=\"email-error\" role=\"alert\">
    有効なメールアドレスを入力してください
</div>
```

### aria-required

必須項目を示します。

```html
<label for=\"username\">ユーザー名（必須）</label>
<input
    type=\"text\"
    id=\"username\"
    required
    aria-required=\"true\"
>
```

---

## 実践例

### アクセシブルなモーダルダイアログ

```html
<button onclick=\"openModal()\">ダイアログを開く</button>

<div
    id=\"modal\"
    role=\"dialog\"
    aria-labelledby=\"modal-title\"
    aria-describedby=\"modal-description\"
    aria-modal=\"true\"
    hidden
>
    <h2 id=\"modal-title\">確認</h2>
    <p id=\"modal-description\">この操作を実行しますか？</p>
    <button onclick=\"confirm()\">はい</button>
    <button onclick=\"closeModal()\">いいえ</button>
</div>

<script>
function openModal() {
    const modal = document.getElementById('modal');
    modal.hidden = false;
    modal.querySelector('button').focus(); // フォーカスを移動
}

function closeModal() {
    document.getElementById('modal').hidden = true;
}
</script>
```

### アクセシブルなタブパネル

```html
<div class=\"tabs\">
    <div role=\"tablist\" aria-label=\"設定タブ\">
        <button
            role=\"tab\"
            aria-selected=\"true\"
            aria-controls=\"panel-general\"
            id=\"tab-general\"
        >
            一般
        </button>
        <button
            role=\"tab\"
            aria-selected=\"false\"
            aria-controls=\"panel-privacy\"
            id=\"tab-privacy\"
        >
            プライバシー
        </button>
    </div>

    <div
        id=\"panel-general\"
        role=\"tabpanel\"
        aria-labelledby=\"tab-general\"
    >
        一般設定の内容...
    </div>

    <div
        id=\"panel-privacy\"
        role=\"tabpanel\"
        aria-labelledby=\"tab-privacy\"
        hidden
    >
        プライバシー設定の内容...
    </div>
</div>
```

---

## まとめ

| 属性 | 用途 |
|------|------|
| `aria-label` | 要素の名前を直接指定 |
| `aria-labelledby` | 別の要素から名前を参照 |
| `aria-describedby` | 詳細な説明を参照 |
| `aria-hidden` | スクリーンリーダーから隠す |
| `aria-live` | 動的変化を通知 |
| `aria-expanded` | 展開/折りたたみ状態 |
| `aria-pressed` | トグルボタンの状態 |
| `aria-invalid` | 入力エラー状態 |

**重要**: ARIAは、ネイティブHTMLで実現できない場合のみ使用してください。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'キーボード操作とフォーカス管理',
                        'content' => "<!DOCTYPE html>
<html lang=\"ja\">
<head>
    <meta charset=\"UTF-8\">
    <title>キーボード操作とフォーカス</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        /* フォーカススタイル */
        *:focus {
            outline: 3px solid #4CAF50;
            outline-offset: 2px;
        }
        button, a {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #000;
            color: white;
            padding: 8px;
            text-decoration: none;
        }
        .skip-link:focus {
            top: 0;
        }
        .custom-button {
            display: inline-block;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 2px solid #333;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1000;
        }
        .modal.active {
            display: block;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <!-- スキップリンク -->
    <a href=\"#main-content\" class=\"skip-link\">メインコンテンツへスキップ</a>

    <h1>キーボード操作とフォーカス管理</h1>

    <!-- ========== 1. 基本的なキーボード操作 ========== -->
    <section>
        <h2>1. 基本的なキーボード操作</h2>
        <p>以下のキーでページを操作できます：</p>
        <ul>
            <li><kbd>Tab</kbd>: 次の要素へフォーカス移動</li>
            <li><kbd>Shift + Tab</kbd>: 前の要素へフォーカス移動</li>
            <li><kbd>Enter</kbd>: リンクやボタンを実行</li>
            <li><kbd>Space</kbd>: チェックボックスやボタンを実行</li>
            <li><kbd>↑↓←→</kbd>: ラジオボタンやメニューの選択</li>
        </ul>

        <button>ボタン1</button>
        <button>ボタン2</button>
        <a href=\"#\">リンク</a>
        <input type=\"text\" placeholder=\"テキスト入力\">
    </section>

    <!-- ========== 2. tabindex の使い方 ========== -->
    <section id=\"main-content\">
        <h2>2. tabindex の使い方</h2>

        <!-- tabindex=\"0\": フォーカス可能にする（自然な順序） -->
        <div
            class=\"custom-button\"
            tabindex=\"0\"
            role=\"button\"
            onclick=\"alert('クリックされました')\"
            onkeypress=\"if(event.key === 'Enter' || event.key === ' ') alert('押されました')\"
        >
            カスタムボタン（tabindex=\"0\"）
        </div>

        <!-- tabindex=\"-1\": JavaScriptでのみフォーカス可能 -->
        <div id=\"programmatic-focus\" tabindex=\"-1\" style=\"padding: 10px; border: 1px solid #ddd;\">
            プログラムでフォーカス可能（tabindex=\"-1\"）
        </div>
        <button onclick=\"document.getElementById('programmatic-focus').focus()\">
            上の要素にフォーカスを移動
        </button>

        <!-- tabindex=\"1以上\": 使用非推奨（自然な順序を壊す） -->
        <p><strong>注意</strong>: tabindex=\"1\"以上は避けてください。フォーカス順序が不自然になります。</p>
    </section>

    <!-- ========== 3. フォーカストラップ（モーダル） ========== -->
    <section>
        <h2>3. フォーカストラップ（モーダル）</h2>
        <p>モーダル内でフォーカスを閉じ込めます。</p>
        <button onclick=\"openModal()\">モーダルを開く</button>
    </section>

    <!-- モーダルダイアログ -->
    <div class=\"overlay\" id=\"overlay\" onclick=\"closeModal()\"></div>
    <div class=\"modal\" id=\"modal\" role=\"dialog\" aria-labelledby=\"modal-title\" aria-modal=\"true\">
        <h3 id=\"modal-title\">確認ダイアログ</h3>
        <p>この操作を実行しますか？</p>
        <button id=\"modal-confirm\">はい</button>
        <button id=\"modal-cancel\" onclick=\"closeModal()\">いいえ</button>
        <button onclick=\"closeModal()\">閉じる</button>
    </div>

    <!-- ========== 4. キーボードイベント処理 ========== -->
    <section>
        <h2>4. キーボードイベント処理</h2>
        <div
            class=\"custom-button\"
            tabindex=\"0\"
            role=\"button\"
            id=\"keyboard-button\"
        >
            EnterまたはSpaceで実行
        </div>
        <p id=\"key-output\"></p>
    </section>

    <!-- ========== 5. カスタムコンポーネント ========== -->
    <section>
        <h2>5. カスタムチェックボックス</h2>
        <div
            role=\"checkbox\"
            aria-checked=\"false\"
            tabindex=\"0\"
            id=\"custom-checkbox\"
            style=\"display: inline-block; width: 20px; height: 20px; border: 2px solid #333; cursor: pointer;\"
        ></div>
        <label for=\"custom-checkbox\" style=\"margin-left: 10px;\">利用規約に同意する</label>
    </section>

    <!-- ========== 6. フォーカス可視化 ========== -->
    <section>
        <h2>6. フォーカス可視化</h2>
        <p>フォーカスインジケーターを削除してはいけません。</p>

        <style>
            .good-focus:focus {
                outline: 3px solid #FF5722;
                outline-offset: 2px;
            }
        </style>

        <button class=\"good-focus\">良いフォーカススタイル</button>

        <p><strong>悪い例</strong>（絶対にやらないでください）:</p>
        <pre><code>button:focus {
    outline: none; /* これは絶対ダメ！ */
}</code></pre>
    </section>

    <script>
        // ========== モーダルのフォーカストラップ ==========
        let previousFocus;

        function openModal() {
            previousFocus = document.activeElement; // 現在のフォーカスを保存

            const modal = document.getElementById('modal');
            const overlay = document.getElementById('overlay');

            modal.classList.add('active');
            overlay.classList.add('active');

            // モーダル内の最初のフォーカス可能要素にフォーカス
            const firstFocusable = modal.querySelector('button');
            firstFocusable.focus();

            // フォーカストラップを設定
            modal.addEventListener('keydown', trapFocus);
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            const overlay = document.getElementById('overlay');

            modal.classList.remove('active');
            overlay.classList.remove('active');

            modal.removeEventListener('keydown', trapFocus);

            // 元のフォーカスに戻す
            if (previousFocus) {
                previousFocus.focus();
            }
        }

        function trapFocus(e) {
            const modal = document.getElementById('modal');
            const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])');
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            // Escで閉じる
            if (e.key === 'Escape') {
                closeModal();
                return;
            }

            // Tabキーでのフォーカストラップ
            if (e.key === 'Tab') {
                if (e.shiftKey) { // Shift + Tab
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else { // Tab
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        }

        // ========== キーボードイベント処理 ==========
        const keyboardButton = document.getElementById('keyboard-button');
        const keyOutput = document.getElementById('key-output');

        keyboardButton.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault(); // Spaceのデフォルト動作（スクロール）を防ぐ
                keyOutput.textContent = `${e.key} キーが押されました！`;

                // ボタンを押した視覚的フィードバック
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            }
        });

        // ========== カスタムチェックボックス ==========
        const customCheckbox = document.getElementById('custom-checkbox');

        customCheckbox.addEventListener('click', toggleCheckbox);
        customCheckbox.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                toggleCheckbox();
            }
        });

        function toggleCheckbox() {
            const checked = customCheckbox.getAttribute('aria-checked') === 'true';
            customCheckbox.setAttribute('aria-checked', !checked);
            customCheckbox.style.backgroundColor = !checked ? '#4CAF50' : 'transparent';
        }

        // ========== フォーカス管理のユーティリティ ==========

        // すべてのフォーカス可能な要素を取得
        function getFocusableElements(container = document) {
            return container.querySelectorAll(
                'a[href], button:not([disabled]), textarea:not([disabled]), ' +
                'input:not([disabled]), select:not([disabled]), ' +
                '[tabindex]:not([tabindex=\"-1\"])'
            );
        }

        // 次のフォーカス可能要素にフォーカス
        function focusNext() {
            const focusable = Array.from(getFocusableElements());
            const currentIndex = focusable.indexOf(document.activeElement);
            const nextElement = focusable[currentIndex + 1] || focusable[0];
            nextElement.focus();
        }

        // 前のフォーカス可能要素にフォーカス
        function focusPrevious() {
            const focusable = Array.from(getFocusableElements());
            const currentIndex = focusable.indexOf(document.activeElement);
            const prevElement = focusable[currentIndex - 1] || focusable[focusable.length - 1];
            prevElement.focus();
        }
    </script>

    <hr>

    <h2>まとめ</h2>
    <ul>
        <li><strong>Tab</strong>でフォーカス移動、<strong>Enter/Space</strong>で実行</li>
        <li><code>tabindex=\"0\"</code>: カスタム要素をフォーカス可能にする</li>
        <li><code>tabindex=\"-1\"</code>: プログラムでのみフォーカス可能</li>
        <li>モーダルではフォーカストラップを実装する</li>
        <li>フォーカススタイル（outline）を削除しない</li>
        <li>Escキーでモーダルを閉じる</li>
        <li>閉じた後は元のフォーカスに戻す</li>
    </ul>
</body>
</html>",
                        'code_language' => 'html',
                        'sort_order' => 3
                    ],
                ],
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
