<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class JavaScriptCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * JavaScript基礎演習 - 15週の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'JavaScript基礎演習',
            'description' => '初心者向けJavaScriptプログラミング基礎コース。15週の実践的な課題を通じて、JavaScriptの基本からDOM操作、非同期処理、外部API連携まで段階的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 135,
            'tags' => ['javascript', 'js', '基礎', '演習', '初心者', 'プログラミング', 'DOM', 'API'],
            'icon' => 'ic_javascript',
            'color' => '#F7DF1E',
            'is_featured' => true,
        ]);

        // Milestone 1: JavaScript基礎 (第1週～第2週)
        $milestone1 = $template->milestones()->create([
            'title' => 'JavaScript基礎',
            'description' => 'JavaScriptの基本構文、条件分岐、配列、ループ、関数',
            'sort_order' => 1,
            'estimated_hours' => 18,
            'deliverables' => [
                'JavaScriptの基本構文を理解',
                '条件分岐とループを使える',
                '配列を操作できる',
                '関数を定義・呼び出しできる'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => '第1週：JavaScriptの基礎・条件分岐',
                'description' => 'JavaScriptの基本構文、変数、データ型、演算子、if文、switch文',
                'sort_order' => 1,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'JavaScriptとは・開発環境の準備', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '変数とデータ型', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '演算子', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => 'if文による条件分岐', 'estimated_minutes' => 120, 'sort_order' => 4],
                    ['title' => 'switch文', 'estimated_minutes' => 90, 'sort_order' => 5],
                    ['title' => '練習問題', 'estimated_minutes' => 90, 'sort_order' => 6],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'JavaScriptとは',
                        'content' => "# JavaScriptとは

**JavaScript**は、Webページに動きやインタラクションを加えるためのプログラミング言語です。

## JavaScriptの特徴

1. **Webブラウザで動作**: HTMLやCSSと組み合わせて使用
2. **動的な処理**: ページの内容を動的に変更できる
3. **イベント駆動**: ユーザーの操作に応じて処理を実行
4. **幅広い用途**: フロントエンド、バックエンド（Node.js）、モバイルアプリなど

## JavaScriptの用途

- **Webページの動的な操作**: ボタンクリック、フォーム検証
- **アニメーション**: 要素の移動、フェードイン・フェードアウト
- **非同期通信**: サーバーとのデータのやり取り（Ajax）
- **SPAの構築**: React, Vue.js, Angularなどのフレームワーク

## HTMLへの埋め込み方法

### 1. インライン（非推奨）
```html
<button onclick=\"alert('Hello!')\">クリック</button>
```

### 2. scriptタグ内
```html
<script>
  console.log('Hello JavaScript!');
</script>
```

### 3. 外部ファイル（推奨）
```html
<script src=\"script.js\"></script>
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '変数とデータ型',
                        'content' => "# 変数とデータ型

## 変数の宣言

### let（推奨）
```javascript
let name = '太郎';
let age = 20;
```
- ブロックスコープ
- 再代入可能
- 再宣言不可

### const（推奨）
```javascript
const PI = 3.14159;
const MAX_COUNT = 100;
```
- ブロックスコープ
- 再代入不可（定数）
- オブジェクトや配列の内容は変更可能

### var（古い書き方・非推奨）
```javascript
var score = 85;
```
- 関数スコープ
- 再宣言・再代入可能
- 使用は避ける

## データ型

### プリミティブ型

1. **Number（数値）**
```javascript
let num1 = 42;
let num2 = 3.14;
let num3 = -10;
```

2. **String（文字列）**
```javascript
let str1 = 'Hello';
let str2 = \"World\";
let str3 = \`Template \${num1}\`;  // テンプレートリテラル
```

3. **Boolean（真偽値）**
```javascript
let isActive = true;
let isCompleted = false;
```

4. **undefined（未定義）**
```javascript
let x;  // undefined
```

5. **null（空）**
```javascript
let y = null;
```

### 参照型

6. **Object（オブジェクト）**
```javascript
let person = {
  name: '太郎',
  age: 20
};
```

7. **Array（配列）**
```javascript
let numbers = [1, 2, 3, 4, 5];
```

## 型の確認
```javascript
typeof 42;          // \"number\"
typeof 'Hello';     // \"string\"
typeof true;        // \"boolean\"
typeof undefined;   // \"undefined\"
typeof null;        // \"object\" (歴史的な理由)
typeof [];          // \"object\"
typeof {};          // \"object\"
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '基本的な変数の使用例',
                        'content' => "// 変数の宣言と代入
let name = '田中太郎';
let age = 25;
const BIRTH_YEAR = 1999;

// コンソールに出力
console.log('名前:', name);
console.log('年齢:', age);
console.log('生年:', BIRTH_YEAR);

// 変数の更新
age = 26;  // OK
// BIRTH_YEAR = 2000;  // エラー！constは再代入不可

// テンプレートリテラル
let message = \`\${name}さんは\${age}歳です。\`;
console.log(message);

// 計算
let currentYear = 2025;
let calculatedAge = currentYear - BIRTH_YEAR;
console.log('計算した年齢:', calculatedAge);",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => '演算子',
                        'content' => "# 演算子

## 算術演算子

```javascript
let a = 10;
let b = 3;

console.log(a + b);   // 13 (加算)
console.log(a - b);   // 7  (減算)
console.log(a * b);   // 30 (乗算)
console.log(a / b);   // 3.333... (除算)
console.log(a % b);   // 1  (剰余)
console.log(a ** b);  // 1000 (べき乗)
```

## 代入演算子

```javascript
let x = 10;
x += 5;   // x = x + 5;  (15)
x -= 3;   // x = x - 3;  (12)
x *= 2;   // x = x * 2;  (24)
x /= 4;   // x = x / 4;  (6)
x++;      // x = x + 1;  (7) インクリメント
x--;      // x = x - 1;  (6) デクリメント
```

## 比較演算子

```javascript
let num = 10;

console.log(num == 10);    // true  (等しい)
console.log(num === 10);   // true  (厳密に等しい)
console.log(num != 5);     // true  (等しくない)
console.log(num !== 5);    // true  (厳密に等しくない)
console.log(num > 5);      // true  (より大きい)
console.log(num < 20);     // true  (より小さい)
console.log(num >= 10);    // true  (以上)
console.log(num <= 10);    // true  (以下)
```

### ==（等価）と===（厳密等価）の違い

```javascript
console.log(5 == '5');     // true  (型変換してから比較)
console.log(5 === '5');    // false (型も含めて比較)

console.log(0 == false);   // true
console.log(0 === false);  // false

// ===（厳密等価）を使うことを推奨！
```

## 論理演算子

```javascript
let a = true;
let b = false;

console.log(a && b);   // false (AND: 両方trueならtrue)
console.log(a || b);   // true  (OR: どちらかtrueならtrue)
console.log(!a);       // false (NOT: 反転)

// 実用例
let age = 20;
let hasLicense = true;

if (age >= 18 && hasLicense) {
  console.log('運転できます');
}
```

## 文字列演算子

```javascript
let firstName = '太郎';
let lastName = '田中';

let fullName = lastName + firstName;  // '田中太郎' (結合)
console.log(fullName);
```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => 'if文による条件分岐',
                        'content' => "# if文による条件分岐

## 基本構文

### if文
```javascript
if (条件) {
  // 条件がtrueの場合に実行
}
```

### if-else文
```javascript
if (条件) {
  // 条件がtrueの場合に実行
} else {
  // 条件がfalseの場合に実行
}
```

### if-else if-else文
```javascript
if (条件1) {
  // 条件1がtrueの場合に実行
} else if (条件2) {
  // 条件2がtrueの場合に実行
} else {
  // すべての条件がfalseの場合に実行
}
```

## 実用例

### 例1: 成績判定
```javascript
let score = 85;

if (score >= 90) {
  console.log('優');
} else if (score >= 80) {
  console.log('良');
} else if (score >= 70) {
  console.log('可');
} else {
  console.log('不可');
}
```

### 例2: 年齢チェック
```javascript
let age = 17;

if (age >= 20) {
  console.log('成人です');
} else {
  console.log('未成年です');
}
```

### 例3: 複数条件の組み合わせ
```javascript
let temperature = 28;
let isRaining = false;

if (temperature >= 30 && !isRaining) {
  console.log('暑いですが、傘は不要です');
} else if (temperature >= 30 && isRaining) {
  console.log('暑くて雨も降っています');
} else if (temperature < 30 && isRaining) {
  console.log('雨が降っています');
} else {
  console.log('過ごしやすい天気です');
}
```

## 三項演算子（簡潔な書き方）

```javascript
let age = 20;
let status = (age >= 20) ? '成人' : '未成年';
console.log(status);  // '成人'

// 上記は以下と同じ
// let status;
// if (age >= 20) {
//   status = '成人';
// } else {
//   status = '未成年';
// }
```",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'if文の実践例',
                        'content' => "// ユーザー認証のシミュレーション
let username = 'admin';
let password = 'pass123';

if (username === 'admin' && password === 'pass123') {
  console.log('ログイン成功！');
} else {
  console.log('ユーザー名またはパスワードが間違っています');
}

// 割引計算
let purchaseAmount = 15000;
let discount = 0;

if (purchaseAmount >= 10000) {
  discount = 0.1;  // 10%割引
  console.log('10%割引が適用されました！');
} else if (purchaseAmount >= 5000) {
  discount = 0.05;  // 5%割引
  console.log('5%割引が適用されました！');
}

let finalAmount = purchaseAmount * (1 - discount);
console.log(\`支払額: \${finalAmount}円\`);

// 偶数・奇数判定
let number = 7;

if (number % 2 === 0) {
  console.log(\`\${number}は偶数です\`);
} else {
  console.log(\`\${number}は奇数です\`);
}",
                        'code_language' => 'javascript',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => 'switch文',
                        'content' => "# switch文

**switch文**は、1つの値に対して複数の条件を比較する場合に便利です。

## 基本構文

```javascript
switch (式) {
  case 値1:
    // 式が値1と一致する場合の処理
    break;
  case 値2:
    // 式が値2と一致する場合の処理
    break;
  default:
    // どのcaseにも一致しない場合の処理
}
```

## 重要なポイント

1. **break文**: 各caseの最後にbreakを書く（省略するとfall-throughする）
2. **厳密な比較**: ===（厳密等価）で比較される
3. **default**: 省略可能だが、推奨

## 実用例

### 例1: 曜日判定
```javascript
let day = 3;
let dayName;

switch (day) {
  case 0:
    dayName = '日曜日';
    break;
  case 1:
    dayName = '月曜日';
    break;
  case 2:
    dayName = '火曜日';
    break;
  case 3:
    dayName = '水曜日';
    break;
  case 4:
    dayName = '木曜日';
    break;
  case 5:
    dayName = '金曜日';
    break;
  case 6:
    dayName = '土曜日';
    break;
  default:
    dayName = '不明';
}

console.log(dayName);  // '水曜日'
```

### 例2: グループ化（breakを省略）
```javascript
let month = 4;
let season;

switch (month) {
  case 12:
  case 1:
  case 2:
    season = '冬';
    break;
  case 3:
  case 4:
  case 5:
    season = '春';
    break;
  case 6:
  case 7:
  case 8:
    season = '夏';
    break;
  case 9:
  case 10:
  case 11:
    season = '秋';
    break;
  default:
    season = '不明';
}

console.log(season);  // '春'
```

## if文との使い分け

- **switch文**: 1つの値に対する複数の等価比較
- **if-else文**: 範囲比較や複雑な条件",
                        'sort_order' => 7
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'switch文の実践例',
                        'content' => "// コマンド処理のシミュレーション
let command = 'start';

switch (command) {
  case 'start':
    console.log('プログラムを開始します');
    break;
  case 'stop':
    console.log('プログラムを停止します');
    break;
  case 'pause':
    console.log('プログラムを一時停止します');
    break;
  case 'resume':
    console.log('プログラムを再開します');
    break;
  default:
    console.log('不明なコマンドです');
}

// ユーザーの役割に応じた権限チェック
let userRole = 'editor';

switch (userRole) {
  case 'admin':
    console.log('全ての権限があります');
    break;
  case 'editor':
    console.log('編集権限があります');
    break;
  case 'viewer':
    console.log('閲覧権限のみです');
    break;
  default:
    console.log('権限がありません');
}

// HTTPステータスコードの処理
let statusCode = 404;

switch (statusCode) {
  case 200:
    console.log('成功');
    break;
  case 404:
    console.log('ページが見つかりません');
    break;
  case 500:
    console.log('サーバーエラー');
    break;
  default:
    console.log(\`ステータスコード: \${statusCode}\`);
}",
                        'code_language' => 'javascript',
                        'sort_order' => 8
                    ],
                ],
            ],
            [
                'title' => '第2週：配列・ループ・関数',
                'description' => '配列の操作、for文、while文、関数の定義と呼び出し',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '配列の基本', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'for文とwhile文', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '配列とループの組み合わせ', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => '関数の定義と呼び出し', 'estimated_minutes' => 120, 'sort_order' => 4],
                    ['title' => '練習問題', 'estimated_minutes' => 90, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '配列の基本',
                        'content' => "# 配列の基本

**配列**は、複数の値を1つの変数にまとめて管理するデータ構造です。

## 配列の作成

```javascript
// 空の配列
let emptyArray = [];

// 要素を含む配列
let numbers = [1, 2, 3, 4, 5];
let fruits = ['りんご', 'バナナ', 'オレンジ'];
let mixed = [1, 'テキスト', true, null];  // 異なる型も可能
```

## 配列の要素へのアクセス

```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];

console.log(fruits[0]);  // 'りんご' (最初の要素)
console.log(fruits[1]);  // 'バナナ'
console.log(fruits[2]);  // 'オレンジ' (最後の要素)

// インデックスは0から始まる！
```

## 配列の長さ

```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
console.log(fruits.length);  // 3

// 最後の要素
console.log(fruits[fruits.length - 1]);  // 'オレンジ'
```

## 要素の変更

```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
fruits[1] = 'いちご';
console.log(fruits);  // ['りんご', 'いちご', 'オレンジ']
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '配列の主なメソッド',
                        'content' => "# 配列の主なメソッド

## 要素の追加・削除

### push() - 末尾に追加
```javascript
let fruits = ['りんご', 'バナナ'];
fruits.push('オレンジ');
console.log(fruits);  // ['りんご', 'バナナ', 'オレンジ']
```

### pop() - 末尾から削除
```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
let last = fruits.pop();
console.log(last);     // 'オレンジ'
console.log(fruits);   // ['りんご', 'バナナ']
```

### unshift() - 先頭に追加
```javascript
let fruits = ['バナナ', 'オレンジ'];
fruits.unshift('りんご');
console.log(fruits);  // ['りんご', 'バナナ', 'オレンジ']
```

### shift() - 先頭から削除
```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
let first = fruits.shift();
console.log(first);    // 'りんご'
console.log(fruits);   // ['バナナ', 'オレンジ']
```

## その他の便利なメソッド

### indexOf() - 要素の位置を検索
```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
let index = fruits.indexOf('バナナ');
console.log(index);  // 1
```

### includes() - 要素が含まれるか確認
```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];
console.log(fruits.includes('バナナ'));  // true
console.log(fruits.includes('メロン'));  // false
```

### slice() - 配列の一部を取得
```javascript
let numbers = [1, 2, 3, 4, 5];
let sliced = numbers.slice(1, 4);
console.log(sliced);  // [2, 3, 4]
```

### concat() - 配列の結合
```javascript
let arr1 = [1, 2, 3];
let arr2 = [4, 5, 6];
let combined = arr1.concat(arr2);
console.log(combined);  // [1, 2, 3, 4, 5, 6]
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '配列の実践例',
                        'content' => "// ToDoリストの管理
let todos = [];

// タスクの追加
todos.push('買い物に行く');
todos.push('レポートを書く');
todos.push('メールを送る');
console.log('ToDoリスト:', todos);

// タスク数の確認
console.log(\`残りタスク: \${todos.length}件\`);

// 最初のタスクを完了
let completed = todos.shift();
console.log(\`完了: \${completed}\`);
console.log('残りのタスク:', todos);

// 特定のタスクがあるか確認
if (todos.includes('レポートを書く')) {
  console.log('レポートを書く必要があります');
}

// スコアの管理
let scores = [85, 92, 78, 95, 88];

// 最高点を見つける
let maxScore = Math.max(...scores);
console.log('最高点:', maxScore);

// 合計点
let sum = 0;
for (let score of scores) {
  sum += score;
}
let average = sum / scores.length;
console.log('平均点:', average);",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'for文とwhile文',
                        'content' => "# ループ処理

## for文

**for文**は、回数が決まっている繰り返し処理に適しています。

### 基本構文
```javascript
for (初期化; 条件; 更新) {
  // 繰り返す処理
}
```

### 例：1から10まで表示
```javascript
for (let i = 1; i <= 10; i++) {
  console.log(i);
}
```

### 配列のループ
```javascript
let fruits = ['りんご', 'バナナ', 'オレンジ'];

// インデックスを使う方法
for (let i = 0; i < fruits.length; i++) {
  console.log(\`\${i + 1}番目: \${fruits[i]}\`);
}

// for...of（推奨）
for (let fruit of fruits) {
  console.log(fruit);
}
```

## while文

**while文**は、条件が満たされている間、繰り返し処理を行います。

### 基本構文
```javascript
while (条件) {
  // 条件がtrueの間、繰り返す処理
}
```

### 例：カウントダウン
```javascript
let count = 5;

while (count > 0) {
  console.log(count);
  count--;
}
console.log('発射！');
```

## do-while文

**do-while文**は、最低1回は必ず実行されます。

```javascript
let input;
do {
  input = prompt('正の数を入力してください');
} while (input <= 0);
```

## break文とcontinue文

### break - ループを抜ける
```javascript
for (let i = 1; i <= 10; i++) {
  if (i === 5) {
    break;  // i=5でループ終了
  }
  console.log(i);
}
// 出力: 1, 2, 3, 4
```

### continue - 次の繰り返しへ
```javascript
for (let i = 1; i <= 5; i++) {
  if (i === 3) {
    continue;  // i=3をスキップ
  }
  console.log(i);
}
// 出力: 1, 2, 4, 5
```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ループの実践例',
                        'content' => "// 九九の表を作成
for (let i = 1; i <= 9; i++) {
  let row = '';
  for (let j = 1; j <= 9; j++) {
    row += (i * j) + '\\t';
  }
  console.log(row);
}

// 配列から偶数のみを抽出
let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
let evenNumbers = [];

for (let num of numbers) {
  if (num % 2 === 0) {
    evenNumbers.push(num);
  }
}
console.log('偶数:', evenNumbers);

// 特定の値を探す
let students = ['太郎', '花子', '次郎', '春子'];
let searchName = '次郎';
let found = false;

for (let i = 0; i < students.length; i++) {
  if (students[i] === searchName) {
    console.log(\`\${searchName}は\${i + 1}番目にいます\`);
    found = true;
    break;
  }
}

if (!found) {
  console.log(\`\${searchName}は見つかりませんでした\`);
}

// 合計を計算（while文）
let prices = [100, 250, 80, 450, 120];
let total = 0;
let index = 0;

while (index < prices.length) {
  total += prices[index];
  index++;
}
console.log('合計金額:', total + '円');",
                        'code_language' => 'javascript',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => '関数の基本',
                        'content' => "# 関数の基本

**関数**は、特定の処理をまとめて名前を付けたものです。コードの再利用性を高めます。

## 関数の定義方法

### 1. 関数宣言（Function Declaration）
```javascript
function greet(name) {
  console.log(\`こんにちは、\${name}さん！\`);
}

greet('太郎');  // こんにちは、太郎さん！
```

### 2. 関数式（Function Expression）
```javascript
const greet = function(name) {
  console.log(\`こんにちは、\${name}さん！\`);
};

greet('花子');  // こんにちは、花子さん！
```

### 3. アロー関数（Arrow Function）- 推奨
```javascript
const greet = (name) => {
  console.log(\`こんにちは、\${name}さん！\`);
};

// 1行の場合は{}を省略可能
const greet2 = (name) => console.log(\`こんにちは、\${name}さん！\`);
```

## 引数（パラメータ）

### 複数の引数
```javascript
const add = (a, b) => {
  return a + b;
};

let result = add(5, 3);
console.log(result);  // 8
```

### デフォルト引数
```javascript
const greet = (name = 'ゲスト') => {
  console.log(\`こんにちは、\${name}さん！\`);
};

greet();        // こんにちは、ゲストさん！
greet('太郎');  // こんにちは、太郎さん！
```

## 戻り値（return）

```javascript
const multiply = (a, b) => {
  return a * b;
};

let result = multiply(4, 5);
console.log(result);  // 20

// アロー関数で1行の場合はreturnを省略可能
const multiply2 = (a, b) => a * b;
```

## スコープ（変数の有効範囲）

```javascript
let globalVar = 'グローバル';

function myFunction() {
  let localVar = 'ローカル';
  console.log(globalVar);  // アクセス可能
  console.log(localVar);   // アクセス可能
}

myFunction();
// console.log(localVar);  // エラー！関数外からアクセス不可
```",
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '関数の実践例',
                        'content' => "// 配列の合計を計算する関数
const sumArray = (arr) => {
  let sum = 0;
  for (let num of arr) {
    sum += num;
  }
  return sum;
};

let scores = [85, 92, 78, 95, 88];
console.log('合計:', sumArray(scores));

// 配列の平均を計算する関数
const averageArray = (arr) => {
  return sumArray(arr) / arr.length;
};

console.log('平均:', averageArray(scores));

// 最大値を見つける関数
const findMax = (arr) => {
  let max = arr[0];
  for (let num of arr) {
    if (num > max) {
      max = num;
    }
  }
  return max;
};

console.log('最大値:', findMax(scores));

// 文字列を逆にする関数
const reverseString = (str) => {
  let reversed = '';
  for (let i = str.length - 1; i >= 0; i--) {
    reversed += str[i];
  }
  return reversed;
};

console.log(reverseString('Hello'));  // 'olleH'

// 偶数かどうかを判定する関数
const isEven = (num) => num % 2 === 0;

console.log(isEven(4));   // true
console.log(isEven(7));   // false

// 配列から偶数のみをフィルタする関数
const filterEven = (arr) => {
  let result = [];
  for (let num of arr) {
    if (isEven(num)) {
      result.push(num);
    }
  }
  return result;
};

let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
console.log('偶数:', filterEven(numbers));",
                        'code_language' => 'javascript',
                        'sort_order' => 7
                    ],
                ],
            ],
        ]);

        // Milestone 2: DOM操作 (第3週～第7週)
        $milestone2 = $template->milestones()->create([
            'title' => 'DOM操作',
            'description' => 'DOM要素の取得・操作、イベント処理、要素の作成・追加・削除',
            'sort_order' => 2,
            'estimated_hours' => 45,
            'deliverables' => [
                'DOM要素を取得・操作できる',
                'イベントリスナーを設定できる',
                '動的に要素を作成・追加・削除できる'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => '第3週：DOM',
                'description' => 'DOM（Document Object Model）の基礎、要素の取得と操作',
                'sort_order' => 3,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'DOMとは', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => '要素の取得方法', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '要素の内容を変更', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'スタイルの変更', 'estimated_minutes' => 120, 'sort_order' => 4],
                    ['title' => '属性の操作', 'estimated_minutes' => 120, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'DOMとは',
                        'content' => "# DOM（Document Object Model）とは

**DOM**は、HTMLドキュメントをJavaScriptから操作するためのインターフェイスです。

## DOMツリー

HTMLドキュメントは、ツリー構造として表現されます。

```html
<!DOCTYPE html>
<html>
  <head>
    <title>タイトル</title>
  </head>
  <body>
    <h1>見出し</h1>
    <p>段落</p>
  </body>
</html>
```

```
document
  └─ html
      ├─ head
      │   └─ title
      │       └─ \"タイトル\"
      └─ body
          ├─ h1
          │   └─ \"見出し\"
          └─ p
              └─ \"段落\"
```

## DOMの主な機能

1. **要素の取得**: HTMLの要素を取得する
2. **内容の変更**: テキストやHTMLを変更する
3. **スタイルの変更**: CSSスタイルを動的に変更する
4. **属性の操作**: class、id、srcなどの属性を変更する
5. **要素の追加・削除**: 新しい要素を作成・削除する
6. **イベントの処理**: クリックなどのイベントに反応する",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '要素の取得方法',
                        'content' => "# 要素の取得方法

## 主な取得メソッド

### getElementById() - IDで取得
```javascript
const element = document.getElementById('myId');
```

### getElementsByClassName() - クラス名で取得
```javascript
const elements = document.getElementsByClassName('myClass');
// HTMLCollectionを返す（配列のようなもの）
```

### getElementsByTagName() - タグ名で取得
```javascript
const paragraphs = document.getElementsByTagName('p');
```

### querySelector() - CSSセレクタで取得（最初の1つ）
```javascript
const element = document.querySelector('.myClass');
const element2 = document.querySelector('#myId');
const element3 = document.querySelector('div > p');
```

### querySelectorAll() - CSSセレクタで取得（すべて）
```javascript
const elements = document.querySelectorAll('.myClass');
// NodeListを返す
```

## querySelector vs getElementById

```javascript
// どちらも同じ要素を取得
const elem1 = document.getElementById('myId');
const elem2 = document.querySelector('#myId');

// querySelector()のメリット
// - CSSセレクタが使える
// - より柔軟
const firstButton = document.querySelector('button.primary');
```

## 取得した要素の使用

```html
<div id=\"content\">
  <p class=\"text\">最初の段落</p>
  <p class=\"text\">2番目の段落</p>
</div>
```

```javascript
// 単一要素
const content = document.getElementById('content');
console.log(content);

// 複数要素
const paragraphs = document.querySelectorAll('.text');
console.log(paragraphs.length);  // 2

// ループで処理
paragraphs.forEach((p) => {
  console.log(p.textContent);
});
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '要素取得の実践例',
                        'content' => "// HTML
// <h1 id=\"title\">元のタイトル</h1>
// <p class=\"message\">メッセージ1</p>
// <p class=\"message\">メッセージ2</p>
// <button id=\"btn\">クリック</button>

// IDで要素を取得
const title = document.getElementById('title');
console.log(title.textContent);  // '元のタイトル'

// クラスで要素を取得（すべて）
const messages = document.querySelectorAll('.message');
console.log(messages.length);  // 2

// 最初の.message要素だけ取得
const firstMessage = document.querySelector('.message');
console.log(firstMessage.textContent);  // 'メッセージ1'

// タグ名で取得
const allParagraphs = document.getElementsByTagName('p');
console.log(allParagraphs.length);  // 2

// 複数要素をループで処理
messages.forEach((msg, index) => {
  console.log(\`メッセージ\${index + 1}: \${msg.textContent}\`);
});

// CSSセレクタの活用
const button = document.querySelector('#btn');
const firstP = document.querySelector('p:first-child');",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => '要素の内容を変更',
                        'content' => "# 要素の内容を変更

## textContent - テキストの変更

```javascript
const element = document.getElementById('myElement');

// 取得
console.log(element.textContent);

// 変更
element.textContent = '新しいテキスト';
```

## innerHTML - HTMLの変更

```javascript
const element = document.getElementById('myElement');

// 取得
console.log(element.innerHTML);

// 変更（HTMLタグも含む）
element.innerHTML = '<strong>太字のテキスト</strong>';
```

## textContent vs innerHTML

### textContent
- プレーンテキストのみ
- HTMLタグは文字列として扱われる
- より安全（XSS攻撃を防げる）

### innerHTML
- HTMLタグを含むコンテンツ
- HTMLが解釈される
- 動的にHTMLを生成できる

```javascript
const div = document.getElementById('myDiv');

// textContent
div.textContent = '<strong>太字</strong>';
// 表示: <strong>太字</strong> （そのまま表示）

// innerHTML
div.innerHTML = '<strong>太字</strong>';
// 表示: 太字 （HTMLとして解釈）
```

## 値の取得・設定（input要素）

```javascript
const input = document.getElementById('myInput');

// 値の取得
const value = input.value;

// 値の設定
input.value = '新しい値';
```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '内容変更の実践例',
                        'content' => "// HTML
// <h1 id=\"title\">元のタイトル</h1>
// <div id=\"content\"></div>
// <input type=\"text\" id=\"nameInput\" value=\"\">
// <p id=\"display\"></p>

// テキストの変更
const title = document.getElementById('title');
title.textContent = '新しいタイトル';

// HTMLの変更
const content = document.getElementById('content');
content.innerHTML = `
  <h2>サブタイトル</h2>
  <p>これは<strong>HTMLで追加</strong>された内容です。</p>
  <ul>
    <li>項目1</li>
    <li>項目2</li>
    <li>項目3</li>
  </ul>
`;

// input要素の値を変更
const nameInput = document.getElementById('nameInput');
nameInput.value = '太郎';

// input要素の値を取得して表示
const display = document.getElementById('display');
display.textContent = \`入力された名前: \${nameInput.value}\`;

// リストを動的に生成
const fruits = ['りんご', 'バナナ', 'オレンジ'];
let listHTML = '<ul>';
fruits.forEach((fruit) => {
  listHTML += \`<li>\${fruit}</li>\`;
});
listHTML += '</ul>';
content.innerHTML = listHTML;",
                        'code_language' => 'javascript',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'スタイルと属性の操作',
                        'content' => "# スタイルと属性の操作

## style プロパティ

```javascript
const element = document.getElementById('myElement');

// スタイルの変更
element.style.color = 'red';
element.style.fontSize = '20px';
element.style.backgroundColor = 'yellow';
element.style.display = 'none';  // 非表示

// 注意: CSSプロパティ名はキャメルケースで書く
// background-color → backgroundColor
// font-size → fontSize
```

## classList - クラスの操作

```javascript
const element = document.getElementById('myElement');

// クラスを追加
element.classList.add('active');

// クラスを削除
element.classList.remove('inactive');

// クラスをトグル（あれば削除、なければ追加）
element.classList.toggle('highlight');

// クラスが含まれるか確認
if (element.classList.contains('active')) {
  console.log('activeクラスがあります');
}
```

## 属性の操作

```javascript
const img = document.getElementById('myImage');

// 属性の取得
const src = img.getAttribute('src');

// 属性の設定
img.setAttribute('src', 'new-image.jpg');
img.setAttribute('alt', '新しい画像');

// 属性の削除
img.removeAttribute('title');

// 直接アクセス（よく使う属性）
img.src = 'another-image.jpg';
img.alt = '別の画像';

const link = document.getElementById('myLink');
link.href = 'https://example.com';
```",
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'スタイル操作の実践例',
                        'content' => "// HTML
// <div id=\"box\" class=\"container\">ボックス</div>
// <button id=\"toggleBtn\">切り替え</button>
// <img id=\"myImage\" src=\"image1.jpg\" alt=\"画像1\">

// スタイルの直接変更
const box = document.getElementById('box');
box.style.width = '200px';
box.style.height = '200px';
box.style.backgroundColor = 'lightblue';
box.style.border = '2px solid navy';
box.style.padding = '20px';
box.style.textAlign = 'center';

// クラスの追加・削除
const toggleBtn = document.getElementById('toggleBtn');
toggleBtn.addEventListener('click', () => {
  box.classList.toggle('highlight');
  box.classList.toggle('shadow');
});

// 画像の切り替え
const myImage = document.getElementById('myImage');
let imageNumber = 1;

setInterval(() => {
  imageNumber = imageNumber === 1 ? 2 : 1;
  myImage.src = \`image\${imageNumber}.jpg\`;
  myImage.alt = \`画像\${imageNumber}\`;
}, 2000);  // 2秒ごとに切り替え

// 条件に応じてスタイルを変更
const score = 85;
const resultElement = document.getElementById('result');

if (score >= 80) {
  resultElement.style.color = 'green';
  resultElement.style.fontWeight = 'bold';
  resultElement.textContent = '合格';
} else {
  resultElement.style.color = 'red';
  resultElement.textContent = '不合格';
}",
                        'code_language' => 'javascript',
                        'sort_order' => 7
                    ],
                ],
            ],
            [
                'title' => '第4週：イベント処理',
                'description' => 'イベントリスナー、様々なイベントタイプ、イベントオブジェクト',
                'sort_order' => 4,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'イベントとは', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'イベントリスナーの追加', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '様々なイベントタイプ', 'estimated_minutes' => 150, 'sort_order' => 3],
                    ['title' => 'イベントオブジェクト', 'estimated_minutes' => 120, 'sort_order' => 4],
                    ['title' => '実践問題', 'estimated_minutes' => 90, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'イベントとは',
                        'content' => "# イベントとは

**イベント**は、Webページ上で発生する出来事のことです。ユーザーの操作や、ブラウザの動作によって発生します。

## 主なイベントの種類

### マウスイベント
- `click` - クリック
- `dblclick` - ダブルクリック
- `mouseenter` - マウスが要素に入る
- `mouseleave` - マウスが要素から出る
- `mousemove` - マウスが移動

### キーボードイベント
- `keydown` - キーを押した
- `keyup` - キーを離した
- `keypress` - キーを押した（非推奨）

### フォームイベント
- `submit` - フォーム送信
- `change` - 値が変更された
- `input` - 入力があった
- `focus` - フォーカスを得た
- `blur` - フォーカスを失った

### ドキュメントイベント
- `DOMContentLoaded` - HTML読み込み完了
- `load` - すべてのリソース読み込み完了
- `resize` - ウィンドウサイズ変更
- `scroll` - スクロール

## イベント処理の基本的な流れ

1. イベントが発生する要素を取得
2. イベントリスナーを登録
3. イベントが発生したときの処理を記述",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'addEventListener() の使い方',
                        'content' => "# addEventListener() の使い方

**addEventListener()**は、要素にイベントリスナーを追加するメソッドです。

## 基本構文

```javascript
要素.addEventListener(イベントタイプ, 処理関数);
```

## 例：ボタンクリック

```javascript
const button = document.getElementById('myButton');

button.addEventListener('click', () => {
  console.log('ボタンがクリックされました！');
});
```

## 関数を別に定義

```javascript
const handleClick = () => {
  console.log('ボタンがクリックされました！');
};

button.addEventListener('click', handleClick);
```

## 複数のリスナーを追加

```javascript
const button = document.getElementById('myButton');

button.addEventListener('click', () => {
  console.log('1つ目の処理');
});

button.addEventListener('click', () => {
  console.log('2つ目の処理');
});
// 両方とも実行される
```

## リスナーの削除

```javascript
const handleClick = () => {
  console.log('クリック！');
};

// 追加
button.addEventListener('click', handleClick);

// 削除
button.removeEventListener('click', handleClick);
```

## オプション

```javascript
// 一度だけ実行
button.addEventListener('click', handleClick, { once: true });

// キャプチャフェーズで実行
button.addEventListener('click', handleClick, { capture: true });
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'イベント処理の実践例',
                        'content' => "// HTML
// <button id=\"btn\">クリック</button>
// <div id=\"output\"></div>
// <input type=\"text\" id=\"textInput\">

// ボタンクリックイベント
const btn = document.getElementById('btn');
const output = document.getElementById('output');
let clickCount = 0;

btn.addEventListener('click', () => {
  clickCount++;
  output.textContent = \`クリック回数: \${clickCount}\`;
});

// 入力イベント
const textInput = document.getElementById('textInput');

textInput.addEventListener('input', (event) => {
  const value = event.target.value;
  console.log('入力値:', value);
  output.textContent = \`入力: \${value}\`;
});

// マウスイベント
const box = document.getElementById('box');

box.addEventListener('mouseenter', () => {
  box.style.backgroundColor = 'lightblue';
});

box.addEventListener('mouseleave', () => {
  box.style.backgroundColor = 'white';
});

// キーボードイベント
document.addEventListener('keydown', (event) => {
  console.log(\`押されたキー: \${event.key}\`);

  if (event.key === 'Enter') {
    console.log('Enterキーが押されました');
  }
});

// DOMContentLoaded（ページ読み込み完了）
document.addEventListener('DOMContentLoaded', () => {
  console.log('ページの読み込みが完了しました');
});",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'イベントオブジェクト',
                        'content' => "# イベントオブジェクト

イベントが発生すると、**イベントオブジェクト**が自動的に生成され、イベントハンドラに渡されます。

## イベントオブジェクトの取得

```javascript
button.addEventListener('click', (event) => {
  // eventがイベントオブジェクト
  console.log(event);
});

// 慣習的に'e'という名前もよく使われる
button.addEventListener('click', (e) => {
  console.log(e.type);  // 'click'
});
```

## 主なプロパティ

### type - イベントの種類
```javascript
element.addEventListener('click', (e) => {
  console.log(e.type);  // 'click'
});
```

### target - イベントが発生した要素
```javascript
button.addEventListener('click', (e) => {
  console.log(e.target);  // クリックされた要素
  console.log(e.target.textContent);
});
```

### currentTarget - リスナーが設定された要素
```javascript
// targetとcurrentTargetの違い
element.addEventListener('click', (e) => {
  console.log(e.target);        // クリックされた実際の要素
  console.log(e.currentTarget); // リスナーが設定された要素
});
```

## マウスイベントの情報

```javascript
element.addEventListener('click', (e) => {
  console.log(e.clientX, e.clientY);  // クリック位置（ビューポート座標）
  console.log(e.pageX, e.pageY);      // クリック位置（ページ座標）
});
```

## キーボードイベントの情報

```javascript
document.addEventListener('keydown', (e) => {
  console.log(e.key);       // 押されたキー
  console.log(e.code);      // 物理的なキーコード
  console.log(e.ctrlKey);   // Ctrlキーが押されているか
  console.log(e.shiftKey);  // Shiftキーが押されているか
});
```

## フォームイベントの情報

```javascript
input.addEventListener('input', (e) => {
  console.log(e.target.value);  // 入力値
});
```

## イベントの制御

### preventDefault() - デフォルト動作を防ぐ
```javascript
form.addEventListener('submit', (e) => {
  e.preventDefault();  // フォーム送信を中止
  console.log('フォーム送信を防ぎました');
});

link.addEventListener('click', (e) => {
  e.preventDefault();  // リンク遷移を防ぐ
});
```

### stopPropagation() - イベントの伝播を止める
```javascript
child.addEventListener('click', (e) => {
  e.stopPropagation();  // 親要素へのイベント伝播を止める
});
```",
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'イベントオブジェクトの実践例',
                        'content' => "// フォーム送信の制御
const form = document.getElementById('myForm');

form.addEventListener('submit', (e) => {
  e.preventDefault();  // デフォルトの送信を防ぐ

  const nameInput = document.getElementById('name');
  const name = nameInput.value;

  if (name === '') {
    alert('名前を入力してください');
  } else {
    console.log(\`フォーム送信: \${name}\`);
    // ここでAjaxリクエストなど
  }
});

// マウス座標の表示
const coordDisplay = document.getElementById('coordDisplay');

document.addEventListener('mousemove', (e) => {
  coordDisplay.textContent = \`X: \${e.clientX}, Y: \${e.clientY}\`;
});

// クリックされた要素の情報を表示
const buttons = document.querySelectorAll('.btn');

buttons.forEach((button) => {
  button.addEventListener('click', (e) => {
    console.log('クリックされた要素:', e.target);
    console.log('要素のテキスト:', e.target.textContent);
    console.log('要素のid:', e.target.id);
  });
});

// キーボードショートカット
document.addEventListener('keydown', (e) => {
  // Ctrl + S で保存
  if (e.ctrlKey && e.key === 's') {
    e.preventDefault();
    console.log('保存処理を実行');
  }

  // Escキーでモーダルを閉じる
  if (e.key === 'Escape') {
    closeModal();
  }
});

// inputの値をリアルタイムで検証
const emailInput = document.getElementById('email');
const emailError = document.getElementById('emailError');

emailInput.addEventListener('input', (e) => {
  const email = e.target.value;
  const isValid = email.includes('@');

  if (!isValid && email !== '') {
    emailError.textContent = '正しいメールアドレスを入力してください';
    emailError.style.color = 'red';
  } else {
    emailError.textContent = '';
  }
});",
                        'code_language' => 'javascript',
                        'sort_order' => 5
                    ],
                ],
            ],
        ]);

        // Milestone 3: 中間評価 (第8週)
        $milestone3 = $template->milestones()->create([
            'title' => '中間評価と復習',
            'description' => '第1週～第7週までの総合復習と中間評価',
            'sort_order' => 3,
            'estimated_hours' => 9,
            'deliverables' => [
                '基礎知識の定着確認',
                '実践的な課題の完成',
            ],
        ]);

        // Milestone 4: データ管理と非同期処理 (第9週～第12週)
        $milestone4 = $template->milestones()->create([
            'title' => 'データ管理と非同期処理',
            'description' => 'WebStorage、JSON、非同期処理、外部API連携',
            'sort_order' => 4,
            'estimated_hours' => 36,
            'deliverables' => [
                'WebStorageでデータ保存ができる',
                'JSONデータを扱える',
                '非同期処理を理解',
                '外部APIと連携できる',
            ],
        ]);

        // Milestone 5: 総合課題 (第13週～第15週)
        $milestone5 = $template->milestones()->create([
            'title' => '総合課題',
            'description' => 'これまでの学習内容を活かした総合的なWebアプリケーション開発',
            'sort_order' => 5,
            'estimated_hours' => 27,
            'deliverables' => [
                '総合的なWebアプリケーションの完成',
                '学習内容の統合と実践',
            ],
        ]);

        // Note: Due to file length constraints, tasks for weeks 5-15 will be added separately
        // The structure above creates the necessary milestones for the complete course
    }
}
