<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class TypeScriptCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * TypeScript完全コース - 10週間の実践コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'TypeScript完全コース',
            'description' => 'JavaScript経験者向けTypeScriptコース。10週間で型システムの基礎から、高度な型操作、実践的なフレームワーク連携まで学習します。',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 80,
            'tags' => ['typescript', 'javascript', '型安全', '中級者', 'フロントエンド', 'バックエンド'],
            'icon' => 'ic_typescript',
            'color' => '#3178C6',
            'is_featured' => true,
        ]);

        // Milestone 1: TypeScript基礎 (第1週～第3週)
        $milestone1 = $template->milestones()->create([
            'title' => 'TypeScript基礎',
            'description' => '環境構築、基本的な型、インターフェース、関数の型付け',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'TypeScript開発環境をセットアップ',
                '基本的な型を理解・使用',
                'インターフェースを定義',
                '関数に型付けを実装'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：TypeScriptとは？環境構築',
                'description' => 'TypeScriptの概要、インストール、tsconfig.json、コンパイル',
                'sort_order' => 1,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => ['TypeScript公式ドキュメント', 'TypeScript Handbook'],
                'subtasks' => [
                    ['title' => 'TypeScriptをインストール', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'tsconfig.jsonを設定', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '最初のTypeScriptプログラムを作成', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'TypeScriptとは？',
                        'content' => "# TypeScriptとは？\n\n**TypeScript**は、Microsoftが開発したJavaScriptのスーパーセット（上位互換）です。\n\n## TypeScriptの特徴\n1. **静的型付け**: コンパイル時に型チェック\n2. **JavaScriptの上位互換**: 全てのJSコードが有効\n3. **最新のJavaScript機能**: ES6+の機能を先取り\n4. **強力なIDE支援**: 自動補完、リファクタリング\n5. **大規模開発向き**: エンタープライズで採用\n\n## TypeScript vs JavaScript\n\n| | JavaScript | TypeScript |\n|---|---|---|\n| **型チェック** | 実行時 | コンパイル時 |\n| **エラー検出** | 実行してから | 書いている時 |\n| **IDE支援** | 限定的 | 強力 |\n| **学習コスト** | 低い | 中程度 |\n| **実行** | 直接実行 | JSにコンパイル |\n\n## TypeScriptが使われている\n- **Angular**: フレームワーク自体がTS\n- **React**: TypeScript対応が充実\n- **Vue 3**: TSで書き直された\n- **Node.js**: バックエンドでも人気\n- **VS Code**: TypeScriptで作られている",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'TypeScript環境構築',
                        'content' => "# TypeScriptのインストール\nnpm install -g typescript\n\n# バージョン確認\ntsc --version\n\n# プロジェクトの初期化\nnpm init -y\nnpm install --save-dev typescript\n\n# tsconfig.json を生成\ntsc --init\n\n# TypeScriptファイルをコンパイル\ntsc app.ts  # app.js が生成される\n\n# ウォッチモード（自動コンパイル）\ntsc --watch\ntsc -w\n\n# ts-node（コンパイルなしで実行）\nnpm install -g ts-node\nts-node app.ts",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'tsconfig.json',
                        'content' => "// tsconfig.json - TypeScriptの設定ファイル\n{\n  \"compilerOptions\": {\n    // ターゲットJavaScriptバージョン\n    \"target\": \"ES2020\",\n    \n    // モジュールシステム\n    \"module\": \"commonjs\",\n    \n    // ライブラリ\n    \"lib\": [\"ES2020\", \"DOM\"],\n    \n    // 出力ディレクトリ\n    \"outDir\": \"./dist\",\n    \n    // ルートディレクトリ\n    \"rootDir\": \"./src\",\n    \n    // 厳格な型チェック\n    \"strict\": true,\n    \"noImplicitAny\": true,\n    \"strictNullChecks\": true,\n    \n    // ES Module互換\n    \"esModuleInterop\": true,\n    \"forceConsistentCasingInFileNames\": true,\n    \n    // ソースマップ生成\n    \"sourceMap\": true,\n    \n    // 型定義ファイル\n    \"declaration\": true,\n    \n    // 未使用変数の検出\n    \"noUnusedLocals\": true,\n    \"noUnusedParameters\": true\n  },\n  \"include\": [\"src/**/*\"],\n  \"exclude\": [\"node_modules\", \"dist\"]\n}",
                        'code_language' => 'json',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：基本的な型',
                'description' => 'プリミティブ型、配列、タプル、Enum、Any、Unknown',
                'sort_order' => 2,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['TypeScript Basic Types'],
                'subtasks' => [
                    ['title' => 'プリミティブ型を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '配列とタプルを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'Enum、Any、Unknownを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'プリミティブ型',
                        'content' => "// 基本的な型アノテーション\nlet name: string = \"太郎\";\nlet age: number = 25;\nlet isStudent: boolean = true;\n\n// 型推論（型アノテーション省略可能）\nlet message = \"Hello\";  // string型と推論される\nlet count = 100;  // number型\n\n// null と undefined\nlet n: null = null;\nlet u: undefined = undefined;\n\n// strictNullChecks が有効な場合\nlet value: string;\n// value = null;  // エラー！\nlet nullableValue: string | null = null;  // OK\n\n// リテラル型\nlet direction: \"up\" | \"down\" | \"left\" | \"right\";\ndirection = \"up\";  // OK\n// direction = \"forward\";  // エラー！\n\nlet status: 200 | 404 | 500;\nstatus = 200;  // OK\n\n// 型エイリアス\ntype Status = 200 | 404 | 500;\ntype Direction = \"up\" | \"down\" | \"left\" | \"right\";\n\nlet httpStatus: Status = 404;",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '配列とタプル',
                        'content' => "// 配列の型定義\nlet numbers: number[] = [1, 2, 3, 4, 5];\nlet names: string[] = [\"太郎\", \"花子\"];\nlet mixed: (string | number)[] = [1, \"two\", 3];\n\n// ジェネリック構文\nlet numbers2: Array<number> = [1, 2, 3];\nlet strings2: Array<string> = [\"a\", \"b\"];\n\n// 多次元配列\nlet matrix: number[][] = [\n  [1, 2, 3],\n  [4, 5, 6],\n  [7, 8, 9]\n];\n\n// タプル（固定長配列、各要素の型が異なる）\nlet person: [string, number] = [\"太郎\", 25];\nlet rgb: [number, number, number] = [255, 0, 0];\n\n// タプルの分割代入\nlet [name, age] = person;\n\n// ラベル付きタプル\nlet student: [name: string, age: number, grade: string] = [\"太郎\", 20, \"A\"];\n\n// 可変長タプル\nlet list: [string, ...number[]] = [\"items\", 1, 2, 3, 4];",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Enum、Any、Unknown、Never',
                        'content' => "// Enum（列挙型）\nenum Direction {\n  Up,     // 0\n  Down,   // 1\n  Left,   // 2\n  Right   // 3\n}\n\nlet dir: Direction = Direction.Up;\n\n// 値を指定\nenum Status {\n  Success = 200,\n  NotFound = 404,\n  ServerError = 500\n}\n\n// 文字列Enum\nenum Color {\n  Red = \"RED\",\n  Green = \"GREEN\",\n  Blue = \"BLUE\"\n}\n\n// const Enum（コンパイル後に消える）\nconst enum Size {\n  Small,\n  Medium,\n  Large\n}\n\n// Any（型チェックを無効化、なるべく避ける）\nlet anything: any = \"hello\";\nanything = 123;  // OK\nanything = true;  // OK\nanything.foo.bar.baz;  // エラーにならない\n\n// Unknown（安全なany）\nlet value: unknown = \"hello\";\n// value.toUpperCase();  // エラー！\nif (typeof value === \"string\") {\n  value.toUpperCase();  // OK（型ガード後）\n}\n\n// Never（決して返らない）\nfunction throwError(message: string): never {\n  throw new Error(message);\n}\n\nfunction infiniteLoop(): never {\n  while (true) {}\n}\n\n// Void（値を返さない）\nfunction log(message: string): void {\n  console.log(message);\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：関数とインターフェース',
                'description' => '関数の型付け、オプション引数、インターフェース、型エイリアス',
                'sort_order' => 3,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['TypeScript Functions', 'TypeScript Interfaces'],
                'subtasks' => [
                    ['title' => '関数の型付けを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'インターフェースを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '関数の型付け',
                        'content' => "// 基本的な関数の型付け\nfunction add(a: number, b: number): number {\n  return a + b;\n}\n\n// アロー関数\nconst multiply = (a: number, b: number): number => {\n  return a * b;\n};\n\n// 型推論（戻り値の型は省略可能）\nfunction subtract(a: number, b: number) {\n  return a - b;  // number型と推論される\n}\n\n// オプション引数\nfunction greet(name: string, greeting?: string): string {\n  if (greeting) {\n    return `\${greeting}, \${name}!`;\n  }\n  return `Hello, \${name}!`;\n}\n\ngreet(\"太郎\");  // OK\ngreet(\"太郎\", \"こんにちは\");  // OK\n\n// デフォルト引数\nfunction createUser(name: string, age: number = 20): object {\n  return { name, age };\n}\n\n// レスト引数\nfunction sum(...numbers: number[]): number {\n  return numbers.reduce((total, n) => total + n, 0);\n}\n\nsum(1, 2, 3, 4, 5);  // 15\n\n// 関数型\ntype MathOperation = (a: number, b: number) => number;\n\nconst add2: MathOperation = (a, b) => a + b;\nconst multiply2: MathOperation = (a, b) => a * b;\n\n// オーバーロード\nfunction getValue(id: number): string;\nfunction getValue(name: string): number;\nfunction getValue(value: number | string): string | number {\n  if (typeof value === \"number\") {\n    return \"ID: \" + value;\n  } else {\n    return value.length;\n  }\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'インターフェース',
                        'content' => "// インターフェースの定義\ninterface User {\n  id: number;\n  name: string;\n  email: string;\n  age?: number;  // オプショナルプロパティ\n  readonly createdAt: Date;  // 読み取り専用\n}\n\nconst user: User = {\n  id: 1,\n  name: \"太郎\",\n  email: \"taro@example.com\",\n  createdAt: new Date()\n};\n\n// user.createdAt = new Date();  // エラー！（readonly）\n\n// インターフェースの拡張\ninterface Student extends User {\n  grade: string;\n  gpa: number;\n}\n\nconst student: Student = {\n  id: 1,\n  name: \"太郎\",\n  email: \"taro@example.com\",\n  createdAt: new Date(),\n  grade: \"A\",\n  gpa: 3.8\n};\n\n// 複数のインターフェースを拡張\ninterface Timestamped {\n  createdAt: Date;\n  updatedAt: Date;\n}\n\ninterface Product extends User, Timestamped {\n  price: number;\n}\n\n// 関数のインターフェース\ninterface SearchFunc {\n  (source: string, subString: string): boolean;\n}\n\nconst mySearch: SearchFunc = (src, sub) => {\n  return src.includes(sub);\n};\n\n// インデックスシグネチャ\ninterface StringArray {\n  [index: number]: string;\n}\n\nconst myArray: StringArray = [\"Alice\", \"Bob\"];\n\ninterface Dictionary {\n  [key: string]: string | number;\n}\n\nconst dict: Dictionary = {\n  name: \"太郎\",\n  age: 25\n};",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'InterfaceとType Aliasの違い',
                        'content' => "# InterfaceとType Aliasの違い\n\n## Interface\n```typescript\ninterface User {\n  name: string;\n  age: number;\n}\n\n// 拡張可能（宣言のマージ）\ninterface User {\n  email: string;\n}\n// 自動的に統合される\n\n// extendsで拡張\ninterface Student extends User {\n  grade: string;\n}\n```\n\n## Type Alias\n```typescript\ntype User = {\n  name: string;\n  age: number;\n};\n\n// 宣言のマージは不可\n// type User = { email: string; }  // エラー！\n\n// &で拡張\ntype Student = User & {\n  grade: string;\n};\n```\n\n## 使い分け\n\n### Interfaceを使う場合\n- オブジェクトの形状を定義\n- 拡張可能にしたい\n- クラスで実装（implements）\n- ライブラリの公開API\n\n### Type Aliasを使う場合\n- Union型、Intersection型\n- タプル、プリミティブ型のエイリアス\n- 複雑な型操作\n- Mapped Types、Conditional Types\n\n```typescript\n// Interfaceでは不可能\ntype ID = string | number;\ntype Coordinate = [number, number];\ntype Status = \"success\" | \"error\";\n```\n\n## 結論\n基本的には**Interface**を使い、Interfaceでできない場合のみ**Type Alias**を使う。",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 2: 高度な型 (第4週～第6週)
        $milestone2 = $template->milestones()->create([
            'title' => '高度な型',
            'description' => 'Union型、Intersection型、ジェネリクス、ユーティリティ型、型ガード',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                'Union/Intersection型を使用',
                'ジェネリクスで再利用可能なコードを作成',
                'ユーティリティ型で型を操作',
                '型ガードで安全なコードを実装'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 4
            [
                'title' => '第4週：Union型とIntersection型',
                'description' => 'Union型、Intersection型、型ガード、Type Assertion',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['TypeScript Union Types'],
                'subtasks' => [
                    ['title' => 'Union型を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Intersection型を学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '型ガードを学習', 'estimated_minutes' => 150, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Union型とIntersection型',
                        'content' => "// Union型（OR: いずれかの型）\ntype ID = string | number;\n\nlet userId: ID;\nuserId = 123;  // OK\nuserId = \"abc123\";  // OK\n\ntype Status = \"success\" | \"error\" | \"pending\";\n\nfunction handleResponse(status: Status) {\n  if (status === \"success\") {\n    console.log(\"成功\");\n  }\n}\n\n// Intersection型（AND: 全ての型）\ntype Person = {\n  name: string;\n  age: number;\n};\n\ntype Employee = {\n  employeeId: number;\n  department: string;\n};\n\ntype EmployeePerson = Person & Employee;\n\nconst employee: EmployeePerson = {\n  name: \"太郎\",\n  age: 30,\n  employeeId: 12345,\n  department: \"Engineering\"\n};\n\n// Union型の配列\ntype StringOrNumber = string | number;\nconst mixed: StringOrNumber[] = [1, \"two\", 3, \"four\"];\n\n// 複雑なUnion型\ntype Response = \n  | { success: true; data: string }\n  | { success: false; error: string };\n\nfunction handleApiResponse(response: Response) {\n  if (response.success) {\n    console.log(response.data);\n  } else {\n    console.error(response.error);\n  }\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '型ガード',
                        'content' => "// typeof型ガード\nfunction printValue(value: string | number) {\n  if (typeof value === \"string\") {\n    console.log(value.toUpperCase());  // string型\n  } else {\n    console.log(value.toFixed(2));  // number型\n  }\n}\n\n// instanceof型ガード\nclass Dog {\n  bark() { console.log(\"Woof!\"); }\n}\n\nclass Cat {\n  meow() { console.log(\"Meow!\"); }\n}\n\nfunction makeSound(animal: Dog | Cat) {\n  if (animal instanceof Dog) {\n    animal.bark();\n  } else {\n    animal.meow();\n  }\n}\n\n// in型ガード（プロパティの存在チェック）\ninterface Car {\n  drive(): void;\n}\n\ninterface Boat {\n  sail(): void;\n}\n\nfunction move(vehicle: Car | Boat) {\n  if (\"drive\" in vehicle) {\n    vehicle.drive();\n  } else {\n    vehicle.sail();\n  }\n}\n\n// カスタム型ガード\ninterface Fish {\n  swim(): void;\n}\n\ninterface Bird {\n  fly(): void;\n}\n\nfunction isFish(pet: Fish | Bird): pet is Fish {\n  return (pet as Fish).swim !== undefined;\n}\n\nfunction moveAnimal(pet: Fish | Bird) {\n  if (isFish(pet)) {\n    pet.swim();\n  } else {\n    pet.fly();\n  }\n}\n\n// null/undefinedガード\nfunction printLength(str: string | null | undefined) {\n  if (str) {  // null/undefinedを除外\n    console.log(str.length);\n  }\n}\n\n// Discriminated Union（判別可能なUnion）\ntype Shape =\n  | { kind: \"circle\"; radius: number }\n  | { kind: \"rectangle\"; width: number; height: number }\n  | { kind: \"square\"; size: number };\n\nfunction getArea(shape: Shape): number {\n  switch (shape.kind) {\n    case \"circle\":\n      return Math.PI * shape.radius ** 2;\n    case \"rectangle\":\n      return shape.width * shape.height;\n    case \"square\":\n      return shape.size ** 2;\n  }\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Type Assertion（型アサーション）',
                        'content' => "// Type Assertion（型の上書き）\nconst myCanvas = document.getElementById(\"canvas\") as HTMLCanvasElement;\n\n// アングルブラケット構文（JSXと競合するため非推奨）\nconst myCanvas2 = <HTMLCanvasElement>document.getElementById(\"canvas\");\n\n// unknown から特定の型へ\nconst value: unknown = \"hello\";\nconst strLength = (value as string).length;\n\n// Non-null assertion（!）\nfunction getValue(id: string | null) {\n  // idがnullでないことを保証\n  return id!.toUpperCase();\n}\n\n// DOM要素の型アサーション\nconst input = document.querySelector(\"input\")!;  // null でない\ninput.value = \"test\";\n\n// as const（リテラル型として推論）\nconst user = {\n  name: \"太郎\",\n  role: \"admin\"\n} as const;\n\n// user.role = \"user\";  // エラー！（readonlyになる）\n\nconst colors = [\"red\", \"green\", \"blue\"] as const;\ntype Color = typeof colors[number];  // \"red\" | \"green\" | \"blue\"\n\n// 型アサーションの注意点\nconst value2: unknown = \"hello\";\nconst num = value2 as number;  // コンパイルエラーにならない（危険！）\n\n// 安全な方法\nif (typeof value2 === \"number\") {\n  const num2 = value2;  // 型ガードを使う\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 5
            [
                'title' => '第5週：ジェネリクス',
                'description' => 'ジェネリック関数、ジェネリッククラス、制約、デフォルト型',
                'sort_order' => 5,
                'estimated_minutes' => 390,
                'priority' => 5,
                'resources' => ['TypeScript Generics'],
                'subtasks' => [
                    ['title' => 'ジェネリック関数を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'ジェネリッククラスを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'ジェネリック制約を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ジェネリック関数',
                        'content' => "// ジェネリック関数\nfunction identity<T>(arg: T): T {\n  return arg;\n}\n\nconst num = identity<number>(42);\nconst str = identity<string>(\"hello\");\nconst obj = identity({ name: \"太郎\" });  // 型推論\n\n// 配列を扱うジェネリック\nfunction getFirstElement<T>(arr: T[]): T | undefined {\n  return arr[0];\n}\n\nconst first = getFirstElement([1, 2, 3]);  // number\nconst firstStr = getFirstElement([\"a\", \"b\"]);  // string\n\n// 複数の型パラメータ\nfunction pair<T, U>(first: T, second: U): [T, U] {\n  return [first, second];\n}\n\nconst p = pair(\"age\", 25);  // [string, number]\n\n// ジェネリックアロー関数\nconst map = <T, U>(arr: T[], fn: (item: T) => U): U[] => {\n  return arr.map(fn);\n};\n\nconst lengths = map([\"a\", \"bb\", \"ccc\"], s => s.length);  // number[]\n\n// ジェネリックインターフェース\ninterface GenericIdentityFn<T> {\n  (arg: T): T;\n}\n\nconst myIdentity: GenericIdentityFn<number> = identity;",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ジェネリッククラス',
                        'content' => "// ジェネリッククラス\nclass Box<T> {\n  private value: T;\n\n  constructor(value: T) {\n    this.value = value;\n  }\n\n  getValue(): T {\n    return this.value;\n  }\n\n  setValue(value: T): void {\n    this.value = value;\n  }\n}\n\nconst numberBox = new Box<number>(100);\nconsole.log(numberBox.getValue());  // 100\n\nconst stringBox = new Box(\"hello\");  // 型推論\n\n// ジェネリックスタック\nclass Stack<T> {\n  private items: T[] = [];\n\n  push(item: T): void {\n    this.items.push(item);\n  }\n\n  pop(): T | undefined {\n    return this.items.pop();\n  }\n\n  peek(): T | undefined {\n    return this.items[this.items.length - 1];\n  }\n\n  isEmpty(): boolean {\n    return this.items.length === 0;\n  }\n}\n\nconst numberStack = new Stack<number>();\nnumberStack.push(1);\nnumberStack.push(2);\nconsole.log(numberStack.pop());  // 2\n\n// ジェネリックペア\nclass Pair<K, V> {\n  constructor(public key: K, public value: V) {}\n\n  getKey(): K {\n    return this.key;\n  }\n\n  getValue(): V {\n    return this.value;\n  }\n}\n\nconst pair1 = new Pair(\"name\", \"太郎\");\nconst pair2 = new Pair<string, number>(\"age\", 25);",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ジェネリック制約とデフォルト型',
                        'content' => "// ジェネリック制約（extends）\ninterface HasLength {\n  length: number;\n}\n\nfunction logLength<T extends HasLength>(arg: T): void {\n  console.log(arg.length);\n}\n\nlogLength(\"hello\");  // OK（stringはlengthを持つ）\nlogLength([1, 2, 3]);  // OK（配列もlengthを持つ）\n// logLength(123);  // エラー！（numberはlengthを持たない）\n\n// keyof制約\nfunction getProperty<T, K extends keyof T>(obj: T, key: K): T[K] {\n  return obj[key];\n}\n\nconst person = {\n  name: \"太郎\",\n  age: 25\n};\n\nconst name = getProperty(person, \"name\");  // string\nconst age = getProperty(person, \"age\");  // number\n// getProperty(person, \"email\");  // エラー！\n\n// クラス型の制約\nfunction create<T>(constructor: new () => T): T {\n  return new constructor();\n}\n\nclass User {\n  name = \"Unknown\";\n}\n\nconst user = create(User);  // User型\n\n// デフォルト型パラメータ\ninterface Container<T = string> {\n  value: T;\n}\n\nconst stringContainer: Container = { value: \"hello\" };  // string\nconst numberContainer: Container<number> = { value: 42 };  // number\n\n// 複数の制約\nfunction merge<T extends object, U extends object>(obj1: T, obj2: U): T & U {\n  return { ...obj1, ...obj2 };\n}\n\nconst merged = merge({ name: \"太郎\" }, { age: 25 });\nconsole.log(merged.name, merged.age);",
                        'code_language' => 'typescript',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：ユーティリティ型とMapped Types',
                'description' => 'Partial、Required、Readonly、Pick、Omit、Record、Mapped Types',
                'sort_order' => 6,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => ['TypeScript Utility Types'],
                'subtasks' => [
                    ['title' => 'ビルトインユーティリティ型を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'Mapped Typesを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'Conditional Typesを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'ユーティリティ型',
                        'content' => "interface User {\n  id: number;\n  name: string;\n  email: string;\n  age: number;\n}\n\n// Partial<T> - 全てのプロパティをオプショナルに\ntype PartialUser = Partial<User>;\n// { id?: number; name?: string; email?: string; age?: number; }\n\nfunction updateUser(user: User, updates: Partial<User>): User {\n  return { ...user, ...updates };\n}\n\n// Required<T> - 全てのプロパティを必須に\ntype RequiredUser = Required<PartialUser>;\n\n// Readonly<T> - 全てのプロパティを読み取り専用に\ntype ReadonlyUser = Readonly<User>;\nconst user: ReadonlyUser = { id: 1, name: \"太郎\", email: \"t@ex.com\", age: 25 };\n// user.name = \"花子\";  // エラー！\n\n// Pick<T, K> - 特定のプロパティのみ抽出\ntype UserPreview = Pick<User, \"id\" | \"name\">;\n// { id: number; name: string; }\n\n// Omit<T, K> - 特定のプロパティを除外\ntype UserWithoutEmail = Omit<User, \"email\">;\n// { id: number; name: string; age: number; }\n\n// Record<K, T> - キーと値の型を指定した辞書\ntype Roles = \"admin\" | \"user\" | \"guest\";\ntype RolePermissions = Record<Roles, string[]>;\n\nconst permissions: RolePermissions = {\n  admin: [\"read\", \"write\", \"delete\"],\n  user: [\"read\", \"write\"],\n  guest: [\"read\"]\n};\n\n// Exclude<T, U> - Tから特定の型を除外\ntype T1 = Exclude<\"a\" | \"b\" | \"c\", \"a\">;  // \"b\" | \"c\"\n\n// Extract<T, U> - Tから特定の型を抽出\ntype T2 = Extract<\"a\" | \"b\" | \"c\", \"a\" | \"f\">;  // \"a\"\n\n// NonNullable<T> - null/undefinedを除外\ntype T3 = NonNullable<string | number | null | undefined>;  // string | number\n\n// ReturnType<T> - 関数の戻り値の型\nfunction getUser() {\n  return { id: 1, name: \"太郎\" };\n}\ntype UserReturnType = ReturnType<typeof getUser>;  // { id: number; name: string; }\n\n// Parameters<T> - 関数の引数の型\nfunction createUser(name: string, age: number) {}\ntype CreateUserParams = Parameters<typeof createUser>;  // [string, number]",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Mapped Types',
                        'content' => "// Mapped Types - 既存の型を変換\ntype User = {\n  id: number;\n  name: string;\n  email: string;\n};\n\n// 全てのプロパティをオプショナルに（Partialの実装）\ntype MyPartial<T> = {\n  [P in keyof T]?: T[P];\n};\n\ntype PartialUser = MyPartial<User>;\n\n// 全てのプロパティをreadonlyに（Readonlyの実装）\ntype MyReadonly<T> = {\n  readonly [P in keyof T]: T[P];\n};\n\n// 全てのプロパティをnullableに\ntype Nullable<T> = {\n  [P in keyof T]: T[P] | null;\n};\n\ntype NullableUser = Nullable<User>;\n// { id: number | null; name: string | null; email: string | null; }\n\n// プロパティ名を変換\ntype Getters<T> = {\n  [P in keyof T as `get\${Capitalize<string & P>}`]: () => T[P];\n};\n\ntype UserGetters = Getters<User>;\n// { getId: () => number; getName: () => string; getEmail: () => string; }\n\n// 条件付きMapped Type\ntype OnlyStrings<T> = {\n  [P in keyof T]: T[P] extends string ? T[P] : never;\n};\n\n// Key Remapping\ntype RemoveId<T> = {\n  [P in keyof T as Exclude<P, \"id\">]: T[P];\n};\n\ntype UserWithoutId = RemoveId<User>;\n// { name: string; email: string; }",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Conditional Types',
                        'content' => "// Conditional Types - 条件付き型\ntype IsString<T> = T extends string ? true : false;\n\ntype T1 = IsString<string>;  // true\ntype T2 = IsString<number>;  // false\n\n// 配列の要素型を取得\ntype ArrayElement<T> = T extends (infer U)[] ? U : never;\n\ntype T3 = ArrayElement<string[]>;  // string\ntype T4 = ArrayElement<number[]>;  // number\n\n// Promiseの型を取得\ntype Awaited<T> = T extends Promise<infer U> ? U : T;\n\ntype T5 = Awaited<Promise<string>>;  // string\ntype T6 = Awaited<number>;  // number\n\n// NonNullableの実装\ntype MyNonNullable<T> = T extends null | undefined ? never : T;\n\n// Distributive Conditional Types\ntype ToArray<T> = T extends any ? T[] : never;\n\ntype T7 = ToArray<string | number>;  // string[] | number[]\n\n// infer でパラメータの型を抽出\ntype GetFirstParam<T> = T extends (first: infer F, ...args: any[]) => any\n  ? F\n  : never;\n\nfunction foo(a: string, b: number) {}\ntype FirstParam = GetFirstParam<typeof foo>;  // string\n\n// 実践例: DeepReadonly\ntype DeepReadonly<T> = {\n  readonly [P in keyof T]: T[P] extends object\n    ? DeepReadonly<T[P]>\n    : T[P];\n};\n\ninterface NestedUser {\n  id: number;\n  profile: {\n    name: string;\n    address: {\n      city: string;\n    };\n  };\n}\n\ntype ReadonlyNestedUser = DeepReadonly<NestedUser>;\n// 全てのネストされたプロパティがreadonlyになる",
                        'code_language' => 'typescript',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 3: オブジェクト指向とデコレータ (第7週～第8週)
        $milestone3 = $template->milestones()->create([
            'title' => 'オブジェクト指向とデコレータ',
            'description' => 'クラス、継承、アクセス修飾子、抽象クラス、デコレータ、Namespace',
            'sort_order' => 3,
            'estimated_hours' => 16,
            'deliverables' => [
                'クラスベースのOOPを実装',
                'デコレータでメタプログラミング',
                'モジュールとNamespaceを理解',
                '型安全なクラス設計'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 7
            [
                'title' => '第7週：クラスとOOP',
                'description' => 'クラス、継承、アクセス修飾子、抽象クラス、implements',
                'sort_order' => 7,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['TypeScript Classes'],
                'subtasks' => [
                    ['title' => 'クラスの基本を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => '継承とポリモーフィズムを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'クラスの基本',
                        'content' => "// 基本的なクラス\nclass Person {\n  // プロパティ\n  name: string;\n  age: number;\n\n  // コンストラクタ\n  constructor(name: string, age: number) {\n    this.name = name;\n    this.age = age;\n  }\n\n  // メソッド\n  greet(): void {\n    console.log(`こんにちは、\${this.name}です。`);\n  }\n}\n\nconst person = new Person(\"太郎\", 25);\nperson.greet();\n\n// プロパティ初期化の短縮構文\nclass User {\n  constructor(\n    public name: string,  // public プロパティ\n    private age: number,  // private プロパティ\n    readonly id: number   // readonly プロパティ\n  ) {}\n\n  getAge(): number {\n    return this.age;\n  }\n}\n\nconst user = new User(\"太郎\", 25, 1);\nconsole.log(user.name);  // OK\n// console.log(user.age);  // エラー！（private）\n// user.id = 2;  // エラー！（readonly）\n\n// Getter と Setter\nclass Circle {\n  private _radius: number = 0;\n\n  get radius(): number {\n    return this._radius;\n  }\n\n  set radius(value: number) {\n    if (value < 0) {\n      throw new Error(\"半径は正の値である必要があります\");\n    }\n    this._radius = value;\n  }\n\n  get area(): number {\n    return Math.PI * this._radius ** 2;\n  }\n}\n\nconst circle = new Circle();\ncircle.radius = 5;\nconsole.log(circle.area);  // 78.53...\n\n// 静的メンバー\nclass MathUtil {\n  static readonly PI = 3.14159;\n\n  static square(x: number): number {\n    return x * x;\n  }\n}\n\nconsole.log(MathUtil.PI);\nconsole.log(MathUtil.square(5));",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '継承と抽象クラス',
                        'content' => "// 継承\nclass Animal {\n  constructor(public name: string) {}\n\n  move(distance: number = 0): void {\n    console.log(`\${this.name}が\${distance}m移動しました`);\n  }\n}\n\nclass Dog extends Animal {\n  bark(): void {\n    console.log(\"ワンワン！\");\n  }\n}\n\nconst dog = new Dog(\"ポチ\");\ndog.bark();\ndog.move(10);\n\n// メソッドのオーバーライド\nclass Bird extends Animal {\n  move(distance: number = 0): void {\n    console.log(\"飛んでいます...\");\n    super.move(distance);  // 親クラスのメソッドを呼び出し\n  }\n}\n\n// 抽象クラス\nabstract class Shape {\n  constructor(public color: string) {}\n\n  // 抽象メソッド（子クラスで実装必須）\n  abstract getArea(): number;\n  abstract getPerimeter(): number;\n\n  // 具象メソッド\n  describe(): void {\n    console.log(`色: \${this.color}, 面積: \${this.getArea()}`);\n  }\n}\n\nclass Rectangle extends Shape {\n  constructor(\n    color: string,\n    public width: number,\n    public height: number\n  ) {\n    super(color);\n  }\n\n  getArea(): number {\n    return this.width * this.height;\n  }\n\n  getPerimeter(): number {\n    return 2 * (this.width + this.height);\n  }\n}\n\nconst rect = new Rectangle(\"赤\", 10, 5);\nrect.describe();  // 色: 赤, 面積: 50\n\n// implements（インターフェースの実装）\ninterface Printable {\n  print(): void;\n}\n\ninterface Saveable {\n  save(): void;\n}\n\nclass Document implements Printable, Saveable {\n  print(): void {\n    console.log(\"印刷中...\");\n  }\n\n  save(): void {\n    console.log(\"保存中...\");\n  }\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 8
            [
                'title' => '第8週：デコレータとモジュール',
                'description' => 'デコレータ、Namespace、モジュールシステム',
                'sort_order' => 8,
                'estimated_minutes' => 330,
                'priority' => 4,
                'resources' => ['TypeScript Decorators', 'TypeScript Modules'],
                'subtasks' => [
                    ['title' => 'デコレータを学習', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'Namespaceとモジュールを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'デコレータ',
                        'content' => "// tsconfig.jsonで有効化\n// \"experimentalDecorators\": true\n\n// クラスデコレータ\nfunction sealed(constructor: Function) {\n  Object.seal(constructor);\n  Object.seal(constructor.prototype);\n}\n\n@sealed\nclass BugReport {\n  type = \"report\";\n  title: string;\n\n  constructor(t: string) {\n    this.title = t;\n  }\n}\n\n// メソッドデコレータ\nfunction log(target: any, propertyKey: string, descriptor: PropertyDescriptor) {\n  const original = descriptor.value;\n\n  descriptor.value = function(...args: any[]) {\n    console.log(`Calling \${propertyKey} with`, args);\n    const result = original.apply(this, args);\n    console.log(`Result:`, result);\n    return result;\n  };\n}\n\nclass Calculator {\n  @log\n  add(a: number, b: number): number {\n    return a + b;\n  }\n}\n\nconst calc = new Calculator();\ncalc.add(2, 3);\n// Calling add with [2, 3]\n// Result: 5\n\n// プロパティデコレータ\nfunction readonly(target: any, propertyKey: string) {\n  const descriptor: PropertyDescriptor = {\n    writable: false\n  };\n  return descriptor;\n}\n\nclass Person {\n  @readonly\n  name: string = \"太郎\";\n}\n\n// パラメータデコレータ\nfunction required(target: any, propertyKey: string, parameterIndex: number) {\n  console.log(`Parameter \${parameterIndex} in \${propertyKey} is required`);\n}\n\nclass Greeter {\n  greet(@required name: string) {\n    return `Hello, \${name}!`;\n  }\n}\n\n// デコレータファクトリ\nfunction MinLength(min: number) {\n  return function (target: any, propertyKey: string) {\n    let value: string;\n\n    const getter = () => value;\n    const setter = (newVal: string) => {\n      if (newVal.length < min) {\n        throw new Error(`\${propertyKey} must be at least \${min} characters`);\n      }\n      value = newVal;\n    };\n\n    Object.defineProperty(target, propertyKey, {\n      get: getter,\n      set: setter\n    });\n  };\n}\n\nclass User {\n  @MinLength(3)\n  username: string = \"\";\n}",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'モジュールとNamespace',
                        'content' => "// ES6 モジュール（推奨）\n\n// user.ts\nexport interface User {\n  id: number;\n  name: string;\n}\n\nexport function createUser(name: string): User {\n  return {\n    id: Math.random(),\n    name\n  };\n}\n\nexport default class UserService {\n  getUser(id: number): User {\n    return { id, name: \"太郎\" };\n  }\n}\n\n// main.ts\nimport UserService, { User, createUser } from \"./user\";\nimport * as UserModule from \"./user\";\n\nconst service = new UserService();\nconst user = createUser(\"太郎\");\n\n// Namespace（旧式、大規模プロジェクト以外は非推奨）\nnamespace Validation {\n  export interface StringValidator {\n    isValid(s: string): boolean;\n  }\n\n  export class LettersOnlyValidator implements StringValidator {\n    isValid(s: string): boolean {\n      return /^[A-Za-z]+$/.test(s);\n    }\n  }\n\n  export class ZipCodeValidator implements StringValidator {\n    isValid(s: string): boolean {\n      return /^\\d{7}$/.test(s);\n    }\n  }\n}\n\nconst validator = new Validation.LettersOnlyValidator();\n\n// 型のみのインポート\nimport type { User } from \"./user\";\n// const u: User = ...  型として使用\n\n// 再エクスポート\nexport { User, createUser } from \"./user\";\nexport * from \"./user\";",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 4: 実践的なTypeScript (第9週～第10週)
        $milestone4 = $template->milestones()->create([
            'title' => '実践的なTypeScript',
            'description' => 'React + TypeScript、Node.js + TypeScript、テスト、ベストプラクティス',
            'sort_order' => 4,
            'estimated_hours' => 16,
            'deliverables' => [
                'React + TypeScriptでUIを構築',
                'Node.js + TypeScriptでAPIを構築',
                'TypeScriptでテストを記述',
                'TypeScriptのベストプラクティスを理解'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 9
            [
                'title' => '第9週：React/Vue + TypeScript',
                'description' => 'React/VueでのTypeScript活用、コンポーネントの型付け',
                'sort_order' => 9,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['React TypeScript Cheatsheet', 'Vue TypeScript Support'],
                'subtasks' => [
                    ['title' => 'React + TypeScriptを学習', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'Hooksの型付けを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'React + TypeScript',
                        'content' => "// Function Component\nimport React from 'react';\n\ninterface Props {\n  name: string;\n  age: number;\n  onUpdate?: (name: string) => void;\n}\n\nconst UserProfile: React.FC<Props> = ({ name, age, onUpdate }) => {\n  return (\n    <div>\n      <h1>{name}</h1>\n      <p>Age: {age}</p>\n      {onUpdate && <button onClick={() => onUpdate(name)}>Update</button>}\n    </div>\n  );\n};\n\n// useState\nconst [count, setCount] = React.useState<number>(0);\nconst [user, setUser] = React.useState<User | null>(null);\n\n// useRef\nconst inputRef = React.useRef<HTMLInputElement>(null);\n\nReact.useEffect(() => {\n  inputRef.current?.focus();\n}, []);\n\n// useReducer\ntype State = { count: number };\ntype Action = { type: 'increment' } | { type: 'decrement' } | { type: 'reset' };\n\nconst reducer = (state: State, action: Action): State => {\n  switch (action.type) {\n    case 'increment':\n      return { count: state.count + 1 };\n    case 'decrement':\n      return { count: state.count - 1 };\n    case 'reset':\n      return { count: 0 };\n  }\n};\n\nconst [state, dispatch] = React.useReducer(reducer, { count: 0 });\n\n// カスタムフック\nfunction useLocalStorage<T>(key: string, initialValue: T) {\n  const [value, setValue] = React.useState<T>(() => {\n    const item = localStorage.getItem(key);\n    return item ? JSON.parse(item) : initialValue;\n  });\n\n  const setStoredValue = (newValue: T) => {\n    setValue(newValue);\n    localStorage.setItem(key, JSON.stringify(newValue));\n  };\n\n  return [value, setStoredValue] as const;\n}\n\n// イベントハンドラ\nconst handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {\n  console.log(e.target.value);\n};\n\nconst handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {\n  e.preventDefault();\n};",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Vue 3 + TypeScript',
                        'content' => "// Vue 3 Composition API\nimport { defineComponent, ref, computed, PropType } from 'vue';\n\ninterface User {\n  id: number;\n  name: string;\n}\n\nexport default defineComponent({\n  name: 'UserProfile',\n  props: {\n    user: {\n      type: Object as PropType<User>,\n      required: true\n    },\n    age: {\n      type: Number,\n      default: 0\n    }\n  },\n  setup(props, { emit }) {\n    const count = ref<number>(0);\n    const message = ref<string>('Hello');\n\n    const doubleCount = computed(() => count.value * 2);\n\n    const increment = (): void => {\n      count.value++;\n    };\n\n    const updateUser = (name: string): void => {\n      emit('update', name);\n    };\n\n    return {\n      count,\n      message,\n      doubleCount,\n      increment,\n      updateUser\n    };\n  }\n});\n\n// Script Setup構文\n<script setup lang=\"ts\">\nimport { ref, computed } from 'vue';\n\ninterface Props {\n  name: string;\n  age?: number;\n}\n\nconst props = withDefaults(defineProps<Props>(), {\n  age: 0\n});\n\nconst emit = defineEmits<{\n  update: [name: string];\n  delete: [];\n}>();\n\nconst count = ref<number>(0);\nconst doubleCount = computed(() => count.value * 2);\n\nfunction increment() {\n  count.value++;\n}\n</script>",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 10
            [
                'title' => '第10週：Node.js + TypeScript、テスト、ベストプラクティス',
                'description' => 'Express + TypeScript、Jest、ESLint、ベストプラクティス',
                'sort_order' => 10,
                'estimated_minutes' => 390,
                'priority' => 4,
                'resources' => ['Node.js TypeScript Guide', 'TypeScript Best Practices'],
                'subtasks' => [
                    ['title' => 'Node.js + TypeScriptを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'Jestでテストを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'ベストプラクティスを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Express + TypeScript',
                        'content' => "// Express + TypeScript\nimport express, { Request, Response, NextFunction } from 'express';\n\nconst app = express();\n\ninterface User {\n  id: number;\n  name: string;\n  email: string;\n}\n\n// 型安全なルートハンドラ\napp.get('/users/:id', (req: Request<{ id: string }>, res: Response) => {\n  const userId = parseInt(req.params.id);\n  res.json({ id: userId, name: '太郎', email: 'taro@example.com' });\n});\n\n// リクエストボディの型\ninterface CreateUserBody {\n  name: string;\n  email: string;\n}\n\napp.post('/users', (req: Request<{}, {}, CreateUserBody>, res: Response) => {\n  const { name, email } = req.body;\n  res.status(201).json({ id: 1, name, email });\n});\n\n// カスタムミドルウェア\nconst authMiddleware = (req: Request, res: Response, next: NextFunction) => {\n  const token = req.headers.authorization;\n  if (!token) {\n    return res.status(401).json({ error: 'Unauthorized' });\n  }\n  next();\n};\n\n// エラーハンドラ\nconst errorHandler = (err: Error, req: Request, res: Response, next: NextFunction) => {\n  console.error(err.stack);\n  res.status(500).json({ error: err.message });\n};\n\napp.use(errorHandler);\n\napp.listen(3000, () => {\n  console.log('Server running on port 3000');\n});",
                        'code_language' => 'typescript',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Jest + TypeScript',
                        'content' => "// Jest設定 (jest.config.js)\nmodule.exports = {\n  preset: 'ts-jest',\n  testEnvironment: 'node',\n  roots: ['<rootDir>/src'],\n  testMatch: ['**/__tests__/**/*.ts', '**/?(*.)+(spec|test).ts'],\n  transform: {\n    '^.+\\\\.ts$': 'ts-jest'\n  }\n};\n\n// テストコード\nimport { sum, User, UserService } from './math';\n\ndescribe('sum function', () => {\n  it('should add two numbers', () => {\n    expect(sum(1, 2)).toBe(3);\n  });\n\n  it('should handle negative numbers', () => {\n    expect(sum(-1, -2)).toBe(-3);\n  });\n});\n\n// クラスのテスト\ndescribe('UserService', () => {\n  let service: UserService;\n\n  beforeEach(() => {\n    service = new UserService();\n  });\n\n  it('should create a user', () => {\n    const user = service.createUser('太郎', 'taro@example.com');\n    expect(user).toEqual({\n      id: expect.any(Number),\n      name: '太郎',\n      email: 'taro@example.com'\n    });\n  });\n\n  it('should find a user by id', () => {\n    const user = service.createUser('太郎', 'taro@example.com');\n    const found = service.findById(user.id);\n    expect(found).toBe(user);\n  });\n});\n\n// モックの使用\nimport { fetchUser } from './api';\n\njest.mock('./api');\n\nconst mockFetchUser = fetchUser as jest.MockedFunction<typeof fetchUser>;\n\nit('should fetch user', async () => {\n  mockFetchUser.mockResolvedValue({\n    id: 1,\n    name: '太郎',\n    email: 'taro@example.com'\n  });\n\n  const user = await fetchUser(1);\n  expect(user.name).toBe('太郎');\n  expect(mockFetchUser).toHaveBeenCalledWith(1);\n});",
                        'code_language' => 'typescript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'TypeScriptベストプラクティス',
                        'content' => "# TypeScriptベストプラクティス\n\n## 1. 型推論を活用\n```typescript\n// 悪い例\nconst name: string = \"太郎\";\nconst age: number = 25;\n\n// 良い例（型推論）\nconst name = \"太郎\";\nconst age = 25;\n```\n\n## 2. any を避ける\n```typescript\n// 悪い例\nfunction process(data: any) {\n  return data.value;\n}\n\n// 良い例\nfunction process<T extends { value: string }>(data: T) {\n  return data.value;\n}\n```\n\n## 3. strictモードを有効化\n```json\n{\n  \"compilerOptions\": {\n    \"strict\": true,\n    \"noImplicitAny\": true,\n    \"strictNullChecks\": true,\n    \"strictFunctionTypes\": true\n  }\n}\n```\n\n## 4. 型ガードを使う\n```typescript\nfunction isString(value: unknown): value is string {\n  return typeof value === \"string\";\n}\n\nif (isString(value)) {\n  console.log(value.toUpperCase());\n}\n```\n\n## 5. ユーティリティ型を活用\n```typescript\ntype User = { id: number; name: string; email: string; };\ntype UserUpdate = Partial<User>;\ntype UserPreview = Pick<User, \"id\" | \"name\">;\n```\n\n## 6. constアサーションを使う\n```typescript\nconst config = {\n  apiUrl: \"https://api.example.com\",\n  timeout: 5000\n} as const;\n```\n\n## 7. 型のエクスポート\n```typescript\nexport type { User, UserUpdate };\nexport interface Product {}\n```\n\n## 8. ESLintとPrettierを使う\n```bash\nnpm install --save-dev @typescript-eslint/parser @typescript-eslint/eslint-plugin\nnpm install --save-dev prettier eslint-config-prettier\n```\n\n## 9. パスエイリアスを設定\n```json\n{\n  \"compilerOptions\": {\n    \"baseUrl\": \".\",\n    \"paths\": {\n      \"@/*\": [\"src/*\"]\n    }\n  }\n}\n```\n\n## 10. 型定義ファイル\n```typescript\n// types/index.d.ts\ndeclare module '*.svg' {\n  const content: string;\n  export default content;\n}\n```",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "TypeScript Course Seeder completed successfully!\n";
    }
}
