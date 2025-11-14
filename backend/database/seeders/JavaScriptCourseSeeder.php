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
            [
                'title' => '第5週：ループとイベント',
                'description' => 'ループ処理とイベント処理の組み合わせ、動的なイベント登録',
                'sort_order' => 5,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '配列とループの復習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'ループで複数要素にイベントを追加', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'イベント委譲（Event Delegation）', 'estimated_minutes' => 150, 'sort_order' => 3],
                    ['title' => '実践：ToDoリストアプリ', 'estimated_minutes' => 150, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'ループで複数要素にイベントを追加',
                        'content' => "# ループで複数要素にイベントを追加

複数の要素に同じイベント処理を追加する場合、ループを使うと効率的です。

## querySelectorAll + forEach

```javascript
// すべてのボタンにクリックイベントを追加
const buttons = document.querySelectorAll('.btn');

buttons.forEach((button) => {
  button.addEventListener('click', () => {
    console.log('ボタンがクリックされました');
  });
});
```

## 個別の処理を追加

```javascript
const items = document.querySelectorAll('.item');

items.forEach((item, index) => {
  item.addEventListener('click', () => {
    console.log(\`\${index + 1}番目のアイテムがクリックされました\`);
    item.classList.toggle('active');
  });
});
```

## イベントハンドラを関数として定義

```javascript
const handleClick = (event) => {
  const element = event.target;
  element.style.backgroundColor = 'yellow';
  console.log('クリックされた要素:', element.textContent);
};

const buttons = document.querySelectorAll('button');
buttons.forEach((button) => {
  button.addEventListener('click', handleClick);
});
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '複数ボタンのイベント処理例',
                        'content' => "// HTML
// <button class=\"color-btn\" data-color=\"red\">赤</button>
// <button class=\"color-btn\" data-color=\"blue\">青</button>
// <button class=\"color-btn\" data-color=\"green\">緑</button>
// <div id=\"display\"></div>

const colorButtons = document.querySelectorAll('.color-btn');
const display = document.getElementById('display');

colorButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const color = button.getAttribute('data-color');
    display.style.backgroundColor = color;
    display.textContent = \`選択された色: \${color}\`;
  });
});

// リストアイテムの削除
const deleteButtons = document.querySelectorAll('.delete-btn');

deleteButtons.forEach((button) => {
  button.addEventListener('click', (e) => {
    const listItem = e.target.closest('li');
    listItem.remove();
  });
});",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'イベント委譲（Event Delegation）',
                        'content' => "# イベント委譲（Event Delegation）

**イベント委譲**は、親要素にイベントリスナーを設定し、子要素のイベントを処理する手法です。

## なぜイベント委譲を使うのか？

### 問題：動的に追加された要素
```javascript
// 最初からある要素にはイベントが追加される
const buttons = document.querySelectorAll('button');
buttons.forEach((btn) => {
  btn.addEventListener('click', handleClick);
});

// 後から追加されたボタンにはイベントがない！
const newButton = document.createElement('button');
newButton.textContent = '新しいボタン';
document.body.appendChild(newButton);
// このボタンをクリックしても反応しない
```

### 解決策：イベント委譲
```javascript
// 親要素にイベントリスナーを設定
document.body.addEventListener('click', (e) => {
  // クリックされた要素がボタンかチェック
  if (e.target.tagName === 'BUTTON') {
    handleClick(e);
  }
});

// 後から追加されたボタンもちゃんと動く！
```

## イベントバブリング

イベントは子要素から親要素へ伝播します（バブリング）。

```
<div id=\"parent\">
  <button id=\"child\">クリック</button>
</div>
```

ボタンをクリック → buttonのイベント → divのイベント → bodyのイベント...

## 実践例

```javascript
const list = document.getElementById('todoList');

// ul要素にイベントを設定
list.addEventListener('click', (e) => {
  // クリックされたのが削除ボタンか確認
  if (e.target.classList.contains('delete-btn')) {
    const li = e.target.closest('li');
    li.remove();
  }

  // クリックされたのがチェックボックスか確認
  if (e.target.classList.contains('checkbox')) {
    const li = e.target.closest('li');
    li.classList.toggle('completed');
  }
});
```

## メリット

1. **パフォーマンス向上**: イベントリスナーが1つだけ
2. **動的要素に対応**: 後から追加された要素も自動的に対応
3. **メモリ効率**: 大量の要素でもメモリを節約",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ToDoリストの実装例',
                        'content' => "// HTML
// <input type=\"text\" id=\"todoInput\" placeholder=\"新しいタスク\">
// <button id=\"addBtn\">追加</button>
// <ul id=\"todoList\"></ul>

const todoInput = document.getElementById('todoInput');
const addBtn = document.getElementById('addBtn');
const todoList = document.getElementById('todoList');

// タスクを追加
const addTodo = () => {
  const text = todoInput.value.trim();

  if (text === '') {
    alert('タスクを入力してください');
    return;
  }

  const li = document.createElement('li');
  li.innerHTML = \`
    <span class=\"task-text\">\${text}</span>
    <button class=\"delete-btn\">削除</button>
  \`;

  todoList.appendChild(li);
  todoInput.value = '';
};

// 追加ボタンのクリック
addBtn.addEventListener('click', addTodo);

// Enterキーで追加
todoInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    addTodo();
  }
});

// イベント委譲で削除ボタンを処理
todoList.addEventListener('click', (e) => {
  if (e.target.classList.contains('delete-btn')) {
    e.target.closest('li').remove();
  }

  if (e.target.classList.contains('task-text')) {
    e.target.classList.toggle('completed');
  }
});",
                        'code_language' => 'javascript',
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第6週：Elementの作成・追加',
                'description' => 'DOM要素の動的な作成と追加、documentFragment',
                'sort_order' => 6,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'createElement()で要素を作成', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'appendChild(), insertBefore()', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'documentFragmentの活用', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => '実践：動的なカード生成', 'estimated_minutes' => 180, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '要素の作成と追加',
                        'content' => "# 要素の作成と追加

## createElement() - 要素の作成

```javascript
const newDiv = document.createElement('div');
const newP = document.createElement('p');
const newButton = document.createElement('button');
```

## 要素の内容を設定

```javascript
const p = document.createElement('p');

// テキストを設定
p.textContent = 'これは段落です';

// HTMLを設定
p.innerHTML = 'これは<strong>太字</strong>の段落です';

// 属性を設定
p.id = 'myParagraph';
p.className = 'text-content';
p.setAttribute('data-info', 'important');
```

## appendChild() - 末尾に追加

```javascript
const container = document.getElementById('container');
const newP = document.createElement('p');
newP.textContent = '新しい段落';

// containerの末尾に追加
container.appendChild(newP);
```

## insertBefore() - 指定位置に挿入

```javascript
const container = document.getElementById('container');
const newP = document.createElement('p');
newP.textContent = '新しい段落';

const referenceNode = container.firstChild;
// referenceNodeの前に挿入
container.insertBefore(newP, referenceNode);
```

## append() / prepend() - 便利なメソッド

```javascript
const container = document.getElementById('container');

// 末尾に追加（appendChildと似ているが、複数追加可能）
container.append('テキスト', newElement, 'もっとテキスト');

// 先頭に追加
container.prepend(newElement);
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '要素作成の実践例',
                        'content' => "// 新しいリストアイテムを作成して追加
const createListItem = (text) => {
  const li = document.createElement('li');
  li.textContent = text;
  li.className = 'list-item';
  return li;
};

const ul = document.getElementById('myList');
ul.appendChild(createListItem('アイテム1'));
ul.appendChild(createListItem('アイテム2'));
ul.appendChild(createListItem('アイテム3'));

// カードコンポーネントを作成
const createCard = (title, description) => {
  const card = document.createElement('div');
  card.className = 'card';

  const cardTitle = document.createElement('h3');
  cardTitle.textContent = title;

  const cardDesc = document.createElement('p');
  cardDesc.textContent = description;

  const cardButton = document.createElement('button');
  cardButton.textContent = '詳細を見る';
  cardButton.addEventListener('click', () => {
    alert(\`\${title}の詳細\`);
  });

  card.appendChild(cardTitle);
  card.appendChild(cardDesc);
  card.appendChild(cardButton);

  return card;
};

const container = document.getElementById('cardContainer');
container.appendChild(createCard('タイトル1', '説明文1'));
container.appendChild(createCard('タイトル2', '説明文2'));",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'DocumentFragmentの活用',
                        'content' => "# DocumentFragment

**DocumentFragment**は、複数の要素を一度にDOMに追加する際に使用する軽量なコンテナです。

## なぜDocumentFragmentを使うのか？

### 問題：パフォーマンス
```javascript
// 悪い例：DOMに100回アクセス
for (let i = 0; i < 100; i++) {
  const li = document.createElement('li');
  li.textContent = \`アイテム\${i}\`;
  ul.appendChild(li);  // 毎回リフロー発生！
}
```

### 解決策：DocumentFragment
```javascript
// 良い例：DOMには1回だけアクセス
const fragment = document.createDocumentFragment();

for (let i = 0; i < 100; i++) {
  const li = document.createElement('li');
  li.textContent = \`アイテム\${i}\`;
  fragment.appendChild(li);  // メモリ上で処理
}

ul.appendChild(fragment);  // 一度にDOMに追加！
```

## メリット

1. **パフォーマンス向上**: DOMへのアクセスを最小限に
2. **リフローの削減**: 画面の再描画を1回だけに
3. **メモリ効率**: 軽量なコンテナ

## 使い方

```javascript
// DocumentFragmentを作成
const fragment = document.createDocumentFragment();

// 複数の要素を追加
const items = ['りんご', 'バナナ', 'オレンジ'];
items.forEach((item) => {
  const li = document.createElement('li');
  li.textContent = item;
  fragment.appendChild(li);
});

// 一度にDOMに追加
document.getElementById('list').appendChild(fragment);
```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'DocumentFragmentの実践例',
                        'content' => "// 大量のデータを効率的に表示
const renderProducts = (products) => {
  const fragment = document.createDocumentFragment();
  const container = document.getElementById('productList');

  products.forEach((product) => {
    const card = document.createElement('div');
    card.className = 'product-card';

    card.innerHTML = \`
      <img src=\"\${product.image}\" alt=\"\${product.name}\">
      <h3>\${product.name}</h3>
      <p class=\"price\">¥\${product.price.toLocaleString()}</p>
      <button class=\"add-to-cart\" data-id=\"\${product.id}\">
        カートに追加
      </button>
    \`;

    fragment.appendChild(card);
  });

  // 一度に追加
  container.appendChild(fragment);
};

// サンプルデータ
const products = [
  { id: 1, name: '商品A', price: 1000, image: 'a.jpg' },
  { id: 2, name: '商品B', price: 2000, image: 'b.jpg' },
  { id: 3, name: '商品C', price: 1500, image: 'c.jpg' },
  // ... 100個以上でも高速
];

renderProducts(products);

// テーブルの動的生成
const createTable = (data) => {
  const fragment = document.createDocumentFragment();

  data.forEach((row) => {
    const tr = document.createElement('tr');

    Object.values(row).forEach((value) => {
      const td = document.createElement('td');
      td.textContent = value;
      tr.appendChild(td);
    });

    fragment.appendChild(tr);
  });

  document.querySelector('tbody').appendChild(fragment);
};",
                        'code_language' => 'javascript',
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第7週：Elementの削除・動的なElementへのイベント追加',
                'description' => '要素の削除、クローン、動的要素へのイベント処理',
                'sort_order' => 7,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => '要素の削除（remove, removeChild）', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '要素のクローン（cloneNode）', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '動的要素へのイベント追加', 'estimated_minutes' => 150, 'sort_order' => 3],
                    ['title' => '実践：動的フォーム生成', 'estimated_minutes' => 150, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '要素の削除',
                        'content' => "# 要素の削除

## remove() - 自分自身を削除（推奨）

```javascript
const element = document.getElementById('myElement');
element.remove();  // 要素を削除
```

## removeChild() - 子要素を削除

```javascript
const parent = document.getElementById('parent');
const child = document.getElementById('child');

parent.removeChild(child);  // 親から子を削除
```

## すべての子要素を削除

```javascript
const container = document.getElementById('container');

// 方法1: innerHTML（速いが注意が必要）
container.innerHTML = '';

// 方法2: whileループ（より安全）
while (container.firstChild) {
  container.removeChild(container.firstChild);
}

// 方法3: replaceChildren()（モダンな方法）
container.replaceChildren();
```

## 条件付き削除

```javascript
// クラスを持つ要素をすべて削除
const elements = document.querySelectorAll('.remove-me');
elements.forEach((el) => el.remove());

// 空の<p>タグを削除
const paragraphs = document.querySelectorAll('p');
paragraphs.forEach((p) => {
  if (p.textContent.trim() === '') {
    p.remove();
  }
});
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '削除の実践例',
                        'content' => "// リストアイテムに削除ボタンを追加
const addListItem = (text) => {
  const li = document.createElement('li');
  li.innerHTML = \`
    <span>\${text}</span>
    <button class=\"delete-btn\">削除</button>
  \`;

  const deleteBtn = li.querySelector('.delete-btn');
  deleteBtn.addEventListener('click', () => {
    li.remove();  // このli要素を削除
  });

  document.getElementById('list').appendChild(li);
};

addListItem('タスク1');
addListItem('タスク2');
addListItem('タスク3');

// 複数選択して一括削除
const deleteSelected = () => {
  const checkboxes = document.querySelectorAll('.item-checkbox:checked');

  checkboxes.forEach((checkbox) => {
    const listItem = checkbox.closest('li');
    listItem.remove();
  });
};

// すべてクリアボタン
const clearAllBtn = document.getElementById('clearAll');
clearAllBtn.addEventListener('click', () => {
  if (confirm('本当にすべて削除しますか？')) {
    document.getElementById('list').innerHTML = '';
  }
});",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'cloneNode() - 要素の複製',
                        'content' => "# cloneNode() - 要素の複製

**cloneNode()**は、要素のコピーを作成するメソッドです。

## 基本的な使い方

```javascript
const original = document.getElementById('template');

// 浅いコピー（子要素はコピーされない）
const clone1 = original.cloneNode(false);

// 深いコピー（子要素もすべてコピーされる）
const clone2 = original.cloneNode(true);
```

## 注意点

1. **イベントリスナーはコピーされない**
```javascript
original.addEventListener('click', handleClick);
const clone = original.cloneNode(true);
// cloneにはイベントリスナーがない！
```

2. **IDは重複に注意**
```javascript
const clone = original.cloneNode(true);
// IDが重複するので、変更が必要
clone.id = 'newId';
```

## テンプレートパターン

```html
<!-- HTML: 非表示のテンプレート -->
<div id=\"card-template\" style=\"display: none;\">
  <div class=\"card\">
    <h3 class=\"title\"></h3>
    <p class=\"description\"></p>
  </div>
</div>
```

```javascript
const createCard = (title, description) => {
  const template = document.getElementById('card-template');
  const clone = template.cloneNode(true);

  clone.style.display = 'block';
  clone.querySelector('.title').textContent = title;
  clone.querySelector('.description').textContent = description;

  return clone;
};

document.body.appendChild(createCard('タイトル', '説明文'));
```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '動的フォームフィールドの追加',
                        'content' => "// HTML
// <div id=\"formFields\">
//   <div class=\"field-group\" id=\"field-template\">
//     <input type=\"text\" placeholder=\"入力してください\">
//     <button class=\"remove-field\">削除</button>
//   </div>
// </div>
// <button id=\"addField\">フィールドを追加</button>

const formFields = document.getElementById('formFields');
const addFieldBtn = document.getElementById('addField');
const template = document.getElementById('field-template');

let fieldCount = 1;

// フィールドを追加
const addField = () => {
  const newField = template.cloneNode(true);
  newField.id = \`field-\${fieldCount++}\`;

  const input = newField.querySelector('input');
  input.value = '';
  input.name = \`field\${fieldCount}\`;

  const removeBtn = newField.querySelector('.remove-field');
  removeBtn.addEventListener('click', () => {
    newField.remove();
  });

  formFields.appendChild(newField);
};

addFieldBtn.addEventListener('click', addField);

// 初期フィールドの削除ボタンも設定
template.querySelector('.remove-field').addEventListener('click', () => {
  if (formFields.children.length > 1) {
    template.remove();
  } else {
    alert('最低1つのフィールドが必要です');
  }
});",
                        'code_language' => 'javascript',
                        'sort_order' => 4
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

        $milestone3->tasks()->createMany([
            [
                'title' => '第8週：予備日（中間評価週）',
                'description' => '第1週～第7週の総合復習と中間課題',
                'sort_order' => 8,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
                'subtasks' => [
                    ['title' => '基礎知識の復習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'DOM操作の復習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '中間課題：ToDoアプリの完成', 'estimated_minutes' => 300, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => '第1週～第7週の復習ポイント',
                        'content' => "# 復習ポイント

## 第1週～第2週: JavaScript基礎
- 変数宣言（let, const）
- データ型（Number, String, Boolean, Array, Object）
- 演算子（算術、比較、論理）
- 条件分岐（if, switch）
- ループ（for, while, forEach）
- 関数（アロー関数、引数、戻り値）

## 第3週: DOM基礎
- 要素の取得（getElementById, querySelector）
- 内容の変更（textContent, innerHTML）
- スタイルの変更（style, classList）
- 属性の操作（getAttribute, setAttribute）

## 第4週: イベント処理
- addEventListener()
- 各種イベント（click, input, keydown）
- イベントオブジェクト（event.target, event.key）
- preventDefault(), stopPropagation()

## 第5週～第7週: DOM応用
- イベント委譲（Event Delegation）
- 要素の作成（createElement）
- 要素の追加（appendChild, append）
- 要素の削除（remove, removeChild）
- DocumentFragment
- cloneNode()

## よくあるミス

1. **querySelector vs querySelectorAll**
```javascript
// ❌ querySelector()は最初の1つだけ
const button = document.querySelector('.btn');
button.addEventListener('click', handler);

// ✅ 複数の要素にはquerySelectorAll() + forEach
const buttons = document.querySelectorAll('.btn');
buttons.forEach(btn => btn.addEventListener('click', handler));
```

2. **動的要素へのイベント**
```javascript
// ❌ 後から追加された要素には反応しない
document.querySelector('.delete-btn').addEventListener('click', handler);

// ✅ イベント委譲を使う
document.getElementById('list').addEventListener('click', (e) => {
  if (e.target.classList.contains('delete-btn')) {
    handler(e);
  }
});
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '中間課題：ToDoアプリ完全版',
                        'content' => "// 完全なToDoアプリケーション
const todoInput = document.getElementById('todoInput');
const addBtn = document.getElementById('addBtn');
const todoList = document.getElementById('todoList');
const filterBtns = document.querySelectorAll('.filter-btn');

let todos = [];
let filter = 'all';  // 'all', 'active', 'completed'

// ToDoを追加
const addTodo = () => {
  const text = todoInput.value.trim();
  if (text === '') return;

  const todo = {
    id: Date.now(),
    text: text,
    completed: false
  };

  todos.push(todo);
  todoInput.value = '';
  renderTodos();
};

// ToDoを削除
const deleteTodo = (id) => {
  todos = todos.filter(todo => todo.id !== id);
  renderTodos();
};

// 完了状態をトグル
const toggleTodo = (id) => {
  todos = todos.map(todo =>
    todo.id === id ? { ...todo, completed: !todo.completed } : todo
  );
  renderTodos();
};

// ToDoを表示
const renderTodos = () => {
  const fragment = document.createDocumentFragment();

  const filteredTodos = todos.filter(todo => {
    if (filter === 'active') return !todo.completed;
    if (filter === 'completed') return todo.completed;
    return true;
  });

  filteredTodos.forEach(todo => {
    const li = document.createElement('li');
    li.className = todo.completed ? 'completed' : '';
    li.innerHTML = \`
      <input type=\"checkbox\" class=\"checkbox\" \${todo.completed ? 'checked' : ''}>
      <span class=\"todo-text\">\${todo.text}</span>
      <button class=\"delete-btn\">削除</button>
    \`;
    li.dataset.id = todo.id;
    fragment.appendChild(li);
  });

  todoList.innerHTML = '';
  todoList.appendChild(fragment);
};

// イベントリスナー
addBtn.addEventListener('click', addTodo);
todoInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') addTodo();
});

todoList.addEventListener('click', (e) => {
  const id = parseInt(e.target.closest('li').dataset.id);

  if (e.target.classList.contains('delete-btn')) {
    deleteTodo(id);
  } else if (e.target.classList.contains('checkbox')) {
    toggleTodo(id);
  }
});

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filter = btn.dataset.filter;
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderTodos();
  });
});

renderTodos();",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                ],
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

        $milestone4->tasks()->createMany([
            [
                'title' => '第9週：WebStorageの利用',
                'description' => 'localStorage、sessionStorageを使ったデータの永続化',
                'sort_order' => 9,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'WebStorageとは', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'localStorage, sessionStorageの使い方', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'ToDoアプリにストレージ機能を追加', 'estimated_minutes' => 330, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'WebStorageとは',
                        'content' => "# WebStorage

**WebStorage**は、ブラウザにデータを保存する仕組みです。

## 2種類のWebStorage

### localStorage
- **永続的**: ブラウザを閉じてもデータが残る
- **容量**: 約5MB
- **用途**: 設定、ユーザーデータの保存

```javascript
// データを保存
localStorage.setItem('username', '太郎');

// データを取得
const username = localStorage.getItem('username');

// データを削除
localStorage.removeItem('username');

// すべて削除
localStorage.clear();
```

### sessionStorage
- **一時的**: タブやウィンドウを閉じると削除される
- **容量**: 約5MB
- **用途**: 一時的なデータ、セッション情報

```javascript
// 使い方はlocalStorageと同じ
sessionStorage.setItem('tempData', 'value');
const data = sessionStorage.getItem('tempData');
```

## 注意点

1. **文字列のみ保存可能**
   - 数値やオブジェクトは文字列に変換が必要

2. **同期的な処理**
   - 大量のデータを扱うと遅くなる可能性

3. **ドメインごとに独立**
   - 異なるドメインからはアクセスできない

4. **セキュリティ**
   - パスワードなどの機密情報は保存しない",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'localStorageの基本的な使い方',
                        'content' => "// 文字列の保存と取得
localStorage.setItem('name', '田中太郎');
const name = localStorage.getItem('name');
console.log(name);  // '田中太郎'

// 数値の保存（文字列に変換される）
localStorage.setItem('age', 25);
const age = parseInt(localStorage.getItem('age'));
console.log(age);  // 25

// 配列の保存（JSONに変換）
const fruits = ['りんご', 'バナナ', 'オレンジ'];
localStorage.setItem('fruits', JSON.stringify(fruits));

// 配列の取得
const storedFruits = JSON.parse(localStorage.getItem('fruits'));
console.log(storedFruits);  // ['りんご', 'バナナ', 'オレンジ']

// オブジェクトの保存
const user = {
  name: '太郎',
  age: 25,
  email: 'taro@example.com'
};
localStorage.setItem('user', JSON.stringify(user));

// オブジェクトの取得
const storedUser = JSON.parse(localStorage.getItem('user'));
console.log(storedUser.name);  // '太郎'

// 存在確認
if (localStorage.getItem('username') !== null) {
  console.log('usernameが保存されています');
}

// 削除
localStorage.removeItem('name');

// すべて削除
// localStorage.clear();",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ToDoアプリ with localStorage',
                        'content' => "// localStorageを使ったToDoアプリ
let todos = JSON.parse(localStorage.getItem('todos')) || [];

// ToDoを保存
const saveTodos = () => {
  localStorage.setItem('todos', JSON.stringify(todos));
};

// ToDoを追加
const addTodo = (text) => {
  const todo = {
    id: Date.now(),
    text: text,
    completed: false
  };
  todos.push(todo);
  saveTodos();
  renderTodos();
};

// ToDoを削除
const deleteTodo = (id) => {
  todos = todos.filter(todo => todo.id !== id);
  saveTodos();
  renderTodos();
};

// 完了状態をトグル
const toggleTodo = (id) => {
  todos = todos.map(todo =>
    todo.id === id ? { ...todo, completed: !todo.completed } : todo
  );
  saveTodos();
  renderTodos();
};

// 設定の保存
const settings = {
  theme: 'dark',
  showCompleted: true
};
localStorage.setItem('settings', JSON.stringify(settings));

// 設定の読み込み
const loadSettings = () => {
  const saved = localStorage.getItem('settings');
  return saved ? JSON.parse(saved) : { theme: 'light', showCompleted: true };
};

// ページロード時に復元
window.addEventListener('DOMContentLoaded', () => {
  renderTodos();
  const settings = loadSettings();
  applySettings(settings);
});",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                ],
            ],
            [
                'title' => '第10週：JSONについて',
                'description' => 'JSONデータの扱い方、JSON.stringify()、JSON.parse()',
                'sort_order' => 10,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'JSONとは', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'JSON.stringify(), JSON.parse()', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'JSONデータの操作', 'estimated_minutes' => 300, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'JSONとは',
                        'content' => "# JSON (JavaScript Object Notation)

**JSON**は、データを表現するための軽量なフォーマットです。

## JSONの特徴

1. **軽量**: テキストベースで読みやすい
2. **言語非依存**: 多くのプログラミング言語で使える
3. **広く使われている**: Web API、設定ファイルなど

## JSONの構文

### オブジェクト
```json
{
  \"name\": \"田中太郎\",
  \"age\": 25,
  \"email\": \"taro@example.com\"
}
```

### 配列
```json
[
  \"りんご\",
  \"バナナ\",
  \"オレンジ\"
]
```

### ネストされた構造
```json
{
  \"user\": {
    \"name\": \"太郎\",
    \"age\": 25,
    \"hobbies\": [\"読書\", \"音楽\", \"スポーツ\"]
  },
  \"isActive\": true
}
```

## JSONで使えるデータ型

- **文字列**: \"hello\" (ダブルクォート必須)
- **数値**: 42, 3.14
- **真偽値**: true, false
- **null**: null
- **配列**: []
- **オブジェクト**: {}

## 注意点

1. **キーは必ずダブルクォート**
```json
// ✅ 正しい
{\"name\": \"太郎\"}

// ❌ 間違い（シングルクォート不可）
{'name': '太郎'}
```

2. **末尾のカンマは不可**
```json
// ❌ 間違い
{
  \"name\": \"太郎\",
  \"age\": 25,
}
```

3. **コメント不可**",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'JSON.stringify() と JSON.parse()',
                        'content' => "# JSON変換メソッド

## JSON.stringify() - オブジェクト → JSON文字列

```javascript
const user = {
  name: '太郎',
  age: 25,
  hobbies: ['読書', '音楽']
};

const jsonString = JSON.stringify(user);
console.log(jsonString);
// '{\"name\":\"太郎\",\"age\":25,\"hobbies\":[\"読書\",\"音楽\"]}'

// 読みやすく整形（インデント2スペース）
const formatted = JSON.stringify(user, null, 2);
console.log(formatted);
/*
{
  \"name\": \"太郎\",
  \"age\": 25,
  \"hobbies\": [
    \"読書\",
    \"音楽\"
  ]
}
*/
```

## JSON.parse() - JSON文字列 → オブジェクト

```javascript
const jsonString = '{\"name\":\"太郎\",\"age\":25}';
const user = JSON.parse(jsonString);

console.log(user.name);  // '太郎'
console.log(user.age);   // 25
```

## エラーハンドリング

```javascript
try {
  const data = JSON.parse(invalidJsonString);
} catch (error) {
  console.error('JSONのパースに失敗:', error);
}
```

## よくある使い方

### localStorageとの連携
```javascript
// 保存
const todos = [{id: 1, text: 'タスク1'}];
localStorage.setItem('todos', JSON.stringify(todos));

// 読み込み
const storedTodos = JSON.parse(localStorage.getItem('todos')) || [];
```

### オブジェクトのコピー（Deep Copy）
```javascript
const original = {name: '太郎', scores: [85, 90]};
const copy = JSON.parse(JSON.stringify(original));
// 注意: 関数やundefinedは失われる
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'JSONデータの実践例',
                        'content' => "// APIレスポンスのようなJSON データ
const usersJson = \`[
  {
    \"id\": 1,
    \"name\": \"田中太郎\",
    \"email\": \"taro@example.com\",
    \"age\": 25
  },
  {
    \"id\": 2,
    \"name\": \"佐藤花子\",
    \"email\": \"hanako@example.com\",
    \"age\": 30
  }
]\`;

// JSONをパース
const users = JSON.parse(usersJson);

// データを表示
users.forEach(user => {
  console.log(\`\${user.name} (\${user.age}歳)\`);
});

// フィルタリング
const adults = users.filter(user => user.age >= 20);

// 新しいデータを追加
users.push({
  id: 3,
  name: '鈴木次郎',
  email: 'jiro@example.com',
  age: 28
});

// JSON文字列に変換
const updatedJson = JSON.stringify(users, null, 2);
console.log(updatedJson);

// 設定データの管理
const config = {
  apiUrl: 'https://api.example.com',
  timeout: 5000,
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer token123'
  }
};

// 設定を保存
localStorage.setItem('config', JSON.stringify(config));

// 設定を読み込み
const loadedConfig = JSON.parse(localStorage.getItem('config'));
console.log(loadedConfig.apiUrl);",
                        'code_language' => 'javascript',
                        'sort_order' => 3
                    ],
                ],
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

        $milestone5->tasks()->createMany([
            [
                'title' => '第11週：非同期処理・タイマー関数',
                'description' => 'setTimeout, setInterval, Promise, async/await',
                'sort_order' => 11,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'setTimeout, setInterval', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Promiseの基礎', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'async/await', 'estimated_minutes' => 240, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'タイマー関数とは',
                        'content' => "# タイマー関数

**タイマー関数**は、一定時間後や一定間隔で処理を実行するための関数です。

## setTimeout()

**一定時間後に一度だけ**実行します。

```javascript
setTimeout(() => {
  console.log('3秒後に実行されます');
}, 3000); // 3000ミリ秒 = 3秒
```

### タイマーのキャンセル

```javascript
const timerId = setTimeout(() => {
  console.log('これは実行されません');
}, 5000);

// タイマーをキャンセル
clearTimeout(timerId);
```

## setInterval()

**一定間隔で繰り返し**実行します。

```javascript
let count = 0;
const intervalId = setInterval(() => {
  count++;
  console.log(`${count}秒経過`);

  if (count >= 5) {
    clearInterval(intervalId); // 5秒で停止
  }
}, 1000); // 1秒ごと
```

## 実用例：カウントダウンタイマー

```javascript
let seconds = 10;
const countdownEl = document.getElementById('countdown');

const intervalId = setInterval(() => {
  countdownEl.textContent = seconds;
  seconds--;

  if (seconds < 0) {
    clearInterval(intervalId);
    countdownEl.textContent = '終了！';
  }
}, 1000);
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Promiseとは',
                        'content' => "# Promise（プロミス）

**Promise**は、非同期処理の結果を表すオブジェクトです。

## Promiseの3つの状態

1. **Pending（待機中）**: 処理が完了していない
2. **Fulfilled（成功）**: 処理が成功した
3. **Rejected（失敗）**: 処理が失敗した

## Promiseの基本的な使い方

```javascript
// Promiseを返す関数
function wait(ms) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      resolve(`${ms}ミリ秒待ちました`);
    }, ms);
  });
}

// then()で成功時の処理
wait(2000)
  .then(result => {
    console.log(result); // '2000ミリ秒待ちました'
  })
  .catch(error => {
    console.error('エラー:', error);
  });
```

## Promiseチェーン

複数の非同期処理を順番に実行できます。

```javascript
fetch('https://api.example.com/user/1')
  .then(response => response.json())
  .then(user => {
    console.log('ユーザー名:', user.name);
    return fetch(`https://api.example.com/user/${user.id}/posts`);
  })
  .then(response => response.json())
  .then(posts => {
    console.log('投稿数:', posts.length);
  })
  .catch(error => {
    console.error('エラーが発生:', error);
  });
```

## Promise.all()

複数のPromiseを並列実行し、すべて完了するまで待ちます。

```javascript
const promise1 = fetch('/api/users');
const promise2 = fetch('/api/posts');
const promise3 = fetch('/api/comments');

Promise.all([promise1, promise2, promise3])
  .then(([users, posts, comments]) => {
    console.log('すべてのデータ取得完了');
  })
  .catch(error => {
    console.error('いずれかの取得に失敗:', error);
  });
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'async/awaitとは',
                        'content' => "# async/await

**async/await**は、Promiseをより読みやすく書くための構文です。

## async関数

`async`キーワードを付けた関数は、必ずPromiseを返します。

```javascript
async function fetchUser() {
  return { id: 1, name: '田中太郎' };
}

// これは以下と同じ
function fetchUser() {
  return Promise.resolve({ id: 1, name: '田中太郎' });
}
```

## await

`await`は**Promiseの完了を待つ**キーワードです。

```javascript
async function getUser() {
  // fetch()の完了を待つ
  const response = await fetch('/api/user/1');

  // レスポンスのJSON解析を待つ
  const user = await response.json();

  return user;
}

// 使用例
getUser().then(user => {
  console.log(user.name);
});
```

## エラーハンドリング

try-catchでエラーを捕捉できます。

```javascript
async function loadUserData() {
  try {
    const response = await fetch('/api/user/1');

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const user = await response.json();
    console.log('ユーザー:', user.name);
    return user;

  } catch (error) {
    console.error('データ取得エラー:', error.message);
    // フォールバック処理
    return null;
  }
}
```

## 複数の非同期処理

### 順次実行

```javascript
async function loadAllData() {
  const user = await fetch('/api/user/1').then(r => r.json());
  const posts = await fetch(`/api/user/${user.id}/posts`).then(r => r.json());
  const comments = await fetch(`/api/user/${user.id}/comments`).then(r => r.json());

  return { user, posts, comments };
}
```

### 並列実行

```javascript
async function loadAllDataParallel() {
  const [userRes, postsRes, commentsRes] = await Promise.all([
    fetch('/api/user/1'),
    fetch('/api/posts'),
    fetch('/api/comments')
  ]);

  const user = await userRes.json();
  const posts = await postsRes.json();
  const comments = await commentsRes.json();

  return { user, posts, comments };
}
```

## 実用例：ローディング表示付きデータ取得

```javascript
async function displayUserPosts() {
  const loadingEl = document.getElementById('loading');
  const postsEl = document.getElementById('posts');

  try {
    // ローディング表示
    loadingEl.style.display = 'block';
    postsEl.innerHTML = '';

    // データ取得
    const response = await fetch('/api/posts');
    const posts = await response.json();

    // 投稿を表示
    posts.forEach(post => {
      const postEl = document.createElement('div');
      postEl.className = 'post';
      postEl.innerHTML = `
        <h3>${post.title}</h3>
        <p>${post.content}</p>
      `;
      postsEl.appendChild(postEl);
    });

  } catch (error) {
    postsEl.innerHTML = '<p class=\"error\">データの取得に失敗しました</p>';

  } finally {
    // 必ずローディングを非表示
    loadingEl.style.display = 'none';
  }
}
```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：カウントダウン付きメッセージ表示',
                        'content' => "// カウントダウン後にメッセージを表示する例

// 指定秒数待つPromise
function wait(seconds) {
  return new Promise(resolve => {
    setTimeout(resolve, seconds * 1000);
  });
}

// カウントダウン表示
async function showCountdown() {
  const messageEl = document.getElementById('message');

  for (let i = 3; i > 0; i--) {
    messageEl.textContent = `${i}秒後に開始...`;
    await wait(1); // 1秒待つ
  }

  messageEl.textContent = '開始！';
}

// ボタンクリックで実行
document.getElementById('startBtn').addEventListener('click', async () => {
  const btn = document.getElementById('startBtn');
  btn.disabled = true; // ボタンを無効化

  await showCountdown();

  // 何か処理を実行
  console.log('処理実行中...');
  await wait(2);

  document.getElementById('message').textContent = '完了！';
  btn.disabled = false; // ボタンを有効化
});",
                        'code_language' => 'javascript',
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第12週：外部APIと非同期通信',
                'description' => 'fetch API、REST API連携、エラーハンドリング',
                'sort_order' => 12,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'fetch APIの基礎', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'REST APIとの連携', 'estimated_minutes' => 210, 'sort_order' => 2],
                    ['title' => 'エラーハンドリング', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'fetch APIとは',
                        'content' => "# fetch API

**fetch API**は、サーバーとHTTP通信を行うための最新のJavaScript APIです。

## 基本的な使い方

```javascript
fetch('https://api.example.com/users')
  .then(response => response.json())
  .then(data => {
    console.log(data);
  })
  .catch(error => {
    console.error('エラー:', error);
  });
```

## async/awaitを使った書き方

```javascript
async function getUsers() {
  try {
    const response = await fetch('https://api.example.com/users');
    const data = await response.json();
    console.log(data);
  } catch (error) {
    console.error('エラー:', error);
  }
}
```

## HTTPメソッド

### GET（データ取得）

```javascript
// デフォルトはGET
const response = await fetch('https://api.example.com/users/1');
const user = await response.json();
```

### POST（データ送信）

```javascript
const newUser = {
  name: '田中太郎',
  email: 'taro@example.com'
};

const response = await fetch('https://api.example.com/users', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(newUser)
});

const result = await response.json();
console.log('作成されたユーザー:', result);
```

### PUT（データ更新）

```javascript
const updatedUser = {
  name: '田中次郎',
  email: 'jiro@example.com'
};

const response = await fetch('https://api.example.com/users/1', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(updatedUser)
});
```

### DELETE（データ削除）

```javascript
const response = await fetch('https://api.example.com/users/1', {
  method: 'DELETE'
});

if (response.ok) {
  console.log('削除成功');
}
```

## レスポンスの処理

```javascript
const response = await fetch('https://api.example.com/users');

// ステータスコードの確認
console.log(response.status); // 200, 404, 500など
console.log(response.ok); // 200-299ならtrue

// 様々な形式でデータを取得
const json = await response.json(); // JSON
const text = await response.text(); // テキスト
const blob = await response.blob(); // バイナリデータ
```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'REST APIとは',
                        'content' => "# REST API

**REST API**は、HTTPプロトコルを使ってデータをやり取りする設計パターンです。

## RESTの基本概念

### リソースとURL

- **リソース**: データの単位（ユーザー、投稿など）
- **URL**: リソースを指し示す住所

```
GET    /api/users      → ユーザー一覧取得
GET    /api/users/1    → ID=1のユーザー取得
POST   /api/users      → 新規ユーザー作成
PUT    /api/users/1    → ID=1のユーザー更新
DELETE /api/users/1    → ID=1のユーザー削除
```

## JSONPlaceholder（練習用API）

無料で使える練習用REST APIです。

```javascript
// 投稿一覧を取得
async function getPosts() {
  const response = await fetch('https://jsonplaceholder.typicode.com/posts');
  const posts = await response.json();
  console.log('投稿数:', posts.length);
  return posts;
}

// 特定の投稿を取得
async function getPost(id) {
  const response = await fetch(`https://jsonplaceholder.typicode.com/posts/${id}`);
  const post = await response.json();
  console.log('タイトル:', post.title);
  return post;
}

// 新規投稿を作成
async function createPost() {
  const newPost = {
    title: 'テスト投稿',
    body: 'これはテストです',
    userId: 1
  };

  const response = await fetch('https://jsonplaceholder.typicode.com/posts', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(newPost)
  });

  const created = await response.json();
  console.log('作成された投稿ID:', created.id);
  return created;
}
```

## クエリパラメータ

URLにパラメータを付けてデータをフィルタリングできます。

```javascript
// ユーザーID=1の投稿のみ取得
const response = await fetch('https://jsonplaceholder.typicode.com/posts?userId=1');
const posts = await response.json();

// 複数のパラメータ
const url = new URL('https://jsonplaceholder.typicode.com/comments');
url.searchParams.append('postId', '1');
url.searchParams.append('_limit', '5');

const response = await fetch(url);
const comments = await response.json();
```

## 実用例：投稿一覧の表示

```javascript
async function displayPosts() {
  try {
    const response = await fetch('https://jsonplaceholder.typicode.com/posts?_limit=10');
    const posts = await response.json();

    const postsContainer = document.getElementById('posts');
    postsContainer.innerHTML = '';

    posts.forEach(post => {
      const postEl = document.createElement('div');
      postEl.className = 'post';
      postEl.innerHTML = `
        <h3>${post.title}</h3>
        <p>${post.body}</p>
        <small>User ID: ${post.userId}</small>
      `;
      postsContainer.appendChild(postEl);
    });

  } catch (error) {
    console.error('投稿の取得に失敗:', error);
  }
}
```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'エラーハンドリング',
                        'content' => "# エラーハンドリング

fetch APIでは、**ネットワークエラー**と**HTTPエラー**の両方を適切に処理する必要があります。

## エラーの種類

### 1. ネットワークエラー

インターネット接続がない、サーバーが応答しないなど。

```javascript
try {
  const response = await fetch('https://api.example.com/users');
  const data = await response.json();
} catch (error) {
  // ネットワークエラーはここで捕捉
  console.error('ネットワークエラー:', error.message);
}
```

### 2. HTTPエラー

サーバーは応答したが、エラーステータス（404, 500など）を返した。

```javascript
async function fetchWithErrorHandling(url) {
  try {
    const response = await fetch(url);

    // HTTPエラーのチェック
    if (!response.ok) {
      throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
    }

    return await response.json();

  } catch (error) {
    console.error('エラー:', error.message);
    throw error; // 呼び出し元に再スロー
  }
}
```

## ステータスコード別の処理

```javascript
async function getUser(id) {
  try {
    const response = await fetch(`https://api.example.com/users/${id}`);

    switch (response.status) {
      case 200:
        return await response.json();

      case 404:
        throw new Error('ユーザーが見つかりません');

      case 500:
        throw new Error('サーバーエラーが発生しました');

      default:
        throw new Error(`予期しないエラー: ${response.status}`);
    }

  } catch (error) {
    console.error('ユーザー取得エラー:', error.message);
    return null;
  }
}
```

## リトライ処理

一時的なエラーに対して再試行を行います。

```javascript
async function fetchWithRetry(url, options = {}, maxRetries = 3) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      const response = await fetch(url, options);

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      return await response.json();

    } catch (error) {
      console.log(`試行 ${i + 1}/${maxRetries} 失敗:`, error.message);

      // 最後の試行で失敗したらエラーをスロー
      if (i === maxRetries - 1) {
        throw error;
      }

      // 次の試行まで待機（指数バックオフ）
      await new Promise(resolve => setTimeout(resolve, 1000 * Math.pow(2, i)));
    }
  }
}
```

## タイムアウト処理

長時間待たないようにタイムアウトを設定します。

```javascript
async function fetchWithTimeout(url, timeout = 5000) {
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  try {
    const response = await fetch(url, {
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    return await response.json();

  } catch (error) {
    if (error.name === 'AbortError') {
      throw new Error('リクエストがタイムアウトしました');
    }
    throw error;
  }
}
```

## 実用例：ユーザーフレンドリーなエラー表示

```javascript
async function loadAndDisplayUsers() {
  const container = document.getElementById('users');
  const errorEl = document.getElementById('error');
  const loadingEl = document.getElementById('loading');

  try {
    // ローディング表示
    loadingEl.style.display = 'block';
    errorEl.style.display = 'none';
    container.innerHTML = '';

    // データ取得
    const response = await fetch('https://jsonplaceholder.typicode.com/users');

    if (!response.ok) {
      throw new Error(`サーバーエラー (${response.status})`);
    }

    const users = await response.json();

    // ユーザー表示
    users.forEach(user => {
      const userEl = document.createElement('div');
      userEl.textContent = user.name;
      container.appendChild(userEl);
    });

  } catch (error) {
    // エラー表示
    errorEl.textContent = `エラーが発生しました: ${error.message}`;
    errorEl.style.display = 'block';

    // リトライボタンを表示
    const retryBtn = document.createElement('button');
    retryBtn.textContent = '再試行';
    retryBtn.onclick = () => loadAndDisplayUsers();
    errorEl.appendChild(retryBtn);

  } finally {
    // ローディング非表示
    loadingEl.style.display = 'none';
  }
}
```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '実践例：天気情報の取得と表示',
                        'content' => "// OpenWeatherMap APIを使った天気情報取得の例
// （実際に使用する場合は、APIキーを取得してください）

async function getWeather(city) {
  const API_KEY = 'YOUR_API_KEY'; // 実際のAPIキーに置き換え
  const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${API_KEY}&units=metric&lang=ja`;

  try {
    const response = await fetch(url);

    if (!response.ok) {
      if (response.status === 404) {
        throw new Error('都市が見つかりません');
      } else if (response.status === 401) {
        throw new Error('APIキーが無効です');
      } else {
        throw new Error(`エラー: ${response.status}`);
      }
    }

    const data = await response.json();

    // 天気情報を表示
    displayWeather(data);

  } catch (error) {
    document.getElementById('weather').innerHTML =
      `<p class=\"error\">エラー: ${error.message}</p>`;
  }
}

function displayWeather(data) {
  const weatherEl = document.getElementById('weather');

  weatherEl.innerHTML = `
    <h2>${data.name}の天気</h2>
    <p>気温: ${data.main.temp}°C</p>
    <p>天気: ${data.weather[0].description}</p>
    <p>湿度: ${data.main.humidity}%</p>
    <p>風速: ${data.wind.speed} m/s</p>
  `;
}

// 検索フォームのイベントリスナー
document.getElementById('searchForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const city = document.getElementById('cityInput').value;
  getWeather(city);
});

// 使用例（JSONPlaceholderで練習）
async function practiceWithJSONPlaceholder() {
  try {
    // ユーザー一覧を取得
    const usersRes = await fetch('https://jsonplaceholder.typicode.com/users');
    const users = await usersRes.json();

    // 最初のユーザーの投稿を取得
    const postsRes = await fetch(`https://jsonplaceholder.typicode.com/posts?userId=${users[0].id}`);
    const posts = await postsRes.json();

    console.log(`${users[0].name}の投稿数: ${posts.length}`);

  } catch (error) {
    console.error('データ取得エラー:', error);
  }
}",
                        'code_language' => 'javascript',
                        'sort_order' => 4
                    ],
                ],
            ],
            [
                'title' => '第13週：総合課題①',
                'description' => '天気予報アプリまたはタスク管理アプリの作成',
                'sort_order' => 13,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'プロジェクト計画', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'UI設計と実装', 'estimated_minutes' => 240, 'sort_order' => 2],
                    ['title' => '機能実装', 'estimated_minutes' => 240, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第14週：総合課題②',
                'description' => '総合課題の機能追加とブラッシュアップ',
                'sort_order' => 14,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'データ永続化の実装', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'エラーハンドリング', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'UI/UXの改善', 'estimated_minutes' => 180, 'sort_order' => 3],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第15週：総合課題③（最終発表）',
                'description' => '最終調整、コードレビュー、発表準備',
                'sort_order' => 15,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
                'subtasks' => [
                    ['title' => 'コードのリファクタリング', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'テストとデバッグ', 'estimated_minutes' => 180, 'sort_order' => 2],
                    ['title' => 'ドキュメント作成', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => '発表準備', 'estimated_minutes' => 90, 'sort_order' => 4],
                ],
                'knowledge_items' => [],
            ],
        ]);
    }
}
