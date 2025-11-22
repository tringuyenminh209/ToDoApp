<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class ReactCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * React.js完全コース - 12週間の実践コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'React.js完全コース',
            'description' => 'JavaScript経験者向けReact.jsコース。12週間でコンポーネント、Hooks、状態管理、ルーティングから実践的なアプリ開発まで学習します。',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 96,
            'tags' => ['react', 'javascript', 'hooks', 'frontend', 'SPA', 'UI'],
            'icon' => 'ic_react',
            'color' => '#61DAFB',
            'is_featured' => true,
        ]);

        // Milestone 1: React基礎 (第1週～第3週)
        $milestone1 = $template->milestones()->create([
            'title' => 'React基礎',
            'description' => 'React環境構築、JSX、コンポーネント、Props、State、イベント処理',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'React開発環境をセットアップ',
                'JSXを理解・使用',
                'コンポーネントを作成',
                'Props/Stateを管理'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：Reactとは？環境構築とJSX',
                'description' => 'Reactの概要、Create React App、JSX、コンポーネントの基本',
                'sort_order' => 1,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['React公式ドキュメント', 'React入門'],
                'subtasks' => [
                    ['title' => 'Reactをインストール', 'estimated_minutes' => 60, 'sort_order' => 1],
                    ['title' => 'JSXを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => '最初のコンポーネントを作成', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Reactとは？',
                        'content' => "# Reactとは？\n\n**React**は、Facebookが開発したUIを構築するためのJavaScriptライブラリです。\n\n## Reactの特徴\n1. **コンポーネントベース**: UIを再利用可能な部品に分割\n2. **仮想DOM**: 高速なレンダリング\n3. **宣言的UI**: 状態に応じてUIを自動更新\n4. **単方向データフロー**: データの流れが明確\n5. **豊富なエコシステム**: ライブラリとツールが充実\n\n## Reactを使っているサービス\n- **Facebook**: React開発元\n- **Instagram**: Reactで構築\n- **Netflix**: UI部分にReact\n- **Airbnb**: フロントエンドにReact\n- **Discord**: デスクトップアプリもReact\n\n## React vs 他のフレームワーク\n\n| | React | Vue | Angular |\n|---|---|---|---|\n| **タイプ** | ライブラリ | フレームワーク | フレームワーク |\n| **学習曲線** | 中程度 | 易しい | 難しい |\n| **サイズ** | 小さい | 小さい | 大きい |\n| **TypeScript** | サポート | サポート | 標準 |\n| **企業** | Meta | 個人→コミュニティ | Google |",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'React環境構築',
                        'content' => "# Create React App（推奨）\nnpx create-react-app my-app\ncd my-app\nnpm start\n\n# TypeScript版\nnpx create-react-app my-app --template typescript\n\n# Vite（より高速）\nnpm create vite@latest my-app -- --template react\ncd my-app\nnpm install\nnpm run dev\n\n# Vite + TypeScript\nnpm create vite@latest my-app -- --template react-ts\n\n# プロジェクト構造\nmy-app/\n├── public/\n│   └── index.html\n├── src/\n│   ├── App.js\n│   ├── App.css\n│   ├── index.js\n│   └── index.css\n├── package.json\n└── README.md",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'JSXの基本',
                        'content' => "import React from 'react';\n\n// JSX - JavaScriptの中にHTMLのような構文\nfunction App() {\n  const name = \"太郎\";\n  const age = 25;\n\n  return (\n    <div className=\"App\">\n      <h1>こんにちは、{name}さん！</h1>\n      <p>年齢: {age}歳</p>\n      <p>{age >= 18 ? '成人' : '未成年'}</p>\n    </div>\n  );\n}\n\n// JSXのルール\n// 1. 必ず1つの親要素で囲む\nfunction Good() {\n  return (\n    <div>\n      <h1>タイトル</h1>\n      <p>本文</p>\n    </div>\n  );\n}\n\n// Fragmentを使う（余分なdivを作らない）\nfunction Better() {\n  return (\n    <>\n      <h1>タイトル</h1>\n      <p>本文</p>\n    </>\n  );\n}\n\n// 2. classNameを使う（classは予約語）\n<div className=\"container\">\n\n// 3. インラインスタイルはオブジェクト\n<div style={{ color: 'red', fontSize: '20px' }}>\n\n// 4. 自己終了タグは/が必要\n<img src=\"image.jpg\" alt=\"説明\" />\n<input type=\"text\" />\n\n// 5. JavaScriptは{}で囲む\nconst user = { name: \"太郎\", age: 25 };\n<p>{user.name}は{user.age}歳です</p>\n\n// 6. コメントは{/* */}\n{\n  /* これはコメント */\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：コンポーネント、Props、State',
                'description' => 'コンポーネントの作成、Propsによるデータ受け渡し、Stateによる状態管理',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['React Components', 'Props and State'],
                'subtasks' => [
                    ['title' => 'コンポーネントを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Propsを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'Stateを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'コンポーネントの種類',
                        'content' => "// 関数コンポーネント（推奨）\nfunction Welcome(props) {\n  return <h1>こんにちは、{props.name}さん！</h1>;\n}\n\n// アロー関数\nconst Welcome = (props) => {\n  return <h1>こんにちは、{props.name}さん！</h1>;\n};\n\n// 省略形\nconst Welcome = (props) => <h1>こんにちは、{props.name}さん！</h1>;\n\n// クラスコンポーネント（旧式、Hooksの登場で非推奨）\nimport React, { Component } from 'react';\n\nclass Welcome extends Component {\n  render() {\n    return <h1>こんにちは、{this.props.name}さん！</h1>;\n  }\n}\n\n// コンポーネントの使用\nfunction App() {\n  return (\n    <div>\n      <Welcome name=\"太郎\" />\n      <Welcome name=\"花子\" />\n      <Welcome name=\"次郎\" />\n    </div>\n  );\n}\n\n// コンポーネントの分割\n// components/Button.jsx\nexport function Button({ text, onClick }) {\n  return (\n    <button onClick={onClick}>\n      {text}\n    </button>\n  );\n}\n\n// App.jsx\nimport { Button } from './components/Button';",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Props（プロパティ）',
                        'content' => "// Props - 親から子へデータを渡す\nfunction UserCard(props) {\n  return (\n    <div className=\"user-card\">\n      <h2>{props.name}</h2>\n      <p>年齢: {props.age}歳</p>\n      <p>職業: {props.job}</p>\n    </div>\n  );\n}\n\n// 分割代入（推奨）\nfunction UserCard({ name, age, job }) {\n  return (\n    <div className=\"user-card\">\n      <h2>{name}</h2>\n      <p>年齢: {age}歳</p>\n      <p>職業: {job}</p>\n    </div>\n  );\n}\n\n// デフォルト値\nfunction UserCard({ name, age = 20, job = \"不明\" }) {\n  return (\n    <div className=\"user-card\">\n      <h2>{name}</h2>\n      <p>年齢: {age}歳</p>\n      <p>職業: {job}</p>\n    </div>\n  );\n}\n\n// 使用\nfunction App() {\n  return (\n    <div>\n      <UserCard name=\"太郎\" age={25} job=\"エンジニア\" />\n      <UserCard name=\"花子\" age={30} job=\"デザイナー\" />\n      <UserCard name=\"次郎\" />  {/* デフォルト値が使われる */}\n    </div>\n  );\n}\n\n// children props\nfunction Card({ children }) {\n  return (\n    <div className=\"card\">\n      {children}\n    </div>\n  );\n}\n\n<Card>\n  <h2>タイトル</h2>\n  <p>本文</p>\n</Card>\n\n// オブジェクトや配列も渡せる\nconst user = {\n  name: \"太郎\",\n  age: 25,\n  skills: [\"React\", \"TypeScript\"]\n};\n\n<UserCard user={user} />",
                        'code_language' => 'jsx',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'State（useState）',
                        'content' => "import React, { useState } from 'react';\n\n// State - コンポーネント内で変化する値\nfunction Counter() {\n  // [state変数, 更新関数] = useState(初期値)\n  const [count, setCount] = useState(0);\n\n  const increment = () => {\n    setCount(count + 1);\n  };\n\n  const decrement = () => {\n    setCount(count - 1);\n  };\n\n  return (\n    <div>\n      <h1>カウント: {count}</h1>\n      <button onClick={increment}>+</button>\n      <button onClick={decrement}>-</button>\n    </div>\n  );\n}\n\n// 複数のstate\nfunction Form() {\n  const [name, setName] = useState(\"\");\n  const [age, setAge] = useState(0);\n  const [email, setEmail] = useState(\"\");\n\n  return (\n    <form>\n      <input\n        type=\"text\"\n        value={name}\n        onChange={(e) => setName(e.target.value)}\n      />\n      <input\n        type=\"number\"\n        value={age}\n        onChange={(e) => setAge(parseInt(e.target.value))}\n      />\n      <input\n        type=\"email\"\n        value={email}\n        onChange={(e) => setEmail(e.target.value)}\n      />\n    </form>\n  );\n}\n\n// オブジェクトのstate\nfunction UserForm() {\n  const [user, setUser] = useState({\n    name: \"\",\n    age: 0,\n    email: \"\"\n  });\n\n  const handleChange = (field, value) => {\n    setUser({ ...user, [field]: value });\n  };\n\n  return (\n    <input\n      type=\"text\"\n      value={user.name}\n      onChange={(e) => handleChange(\"name\", e.target.value)}\n    />\n  );\n}\n\n// 配列のstate\nfunction TodoList() {\n  const [todos, setTodos] = useState([]);\n\n  const addTodo = (text) => {\n    setTodos([...todos, { id: Date.now(), text }]);\n  };\n\n  const removeTodo = (id) => {\n    setTodos(todos.filter(todo => todo.id !== id));\n  };\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：イベント処理、リスト、フォーム',
                'description' => 'イベントハンドリング、リストのレンダリング、フォームの制御',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['React Events', 'Lists and Keys'],
                'subtasks' => [
                    ['title' => 'イベント処理を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'リストレンダリングを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'フォーム制御を学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'イベント処理',
                        'content' => "// イベントハンドラ\nfunction Button() {\n  const handleClick = () => {\n    alert(\"ボタンがクリックされました！\");\n  };\n\n  return <button onClick={handleClick}>クリック</button>;\n}\n\n// インライン\n<button onClick={() => alert(\"クリック！\")}>クリック</button>\n\n// 引数を渡す\nfunction Button({ id, name }) {\n  const handleClick = (id, name) => {\n    console.log(`ID: \${id}, Name: \${name}`);\n  };\n\n  return (\n    <button onClick={() => handleClick(id, name)}>\n      {name}\n    </button>\n  );\n}\n\n// イベントオブジェクト\nfunction Input() {\n  const handleChange = (e) => {\n    console.log(e.target.value);\n  };\n\n  return <input onChange={handleChange} />;\n}\n\n// preventDefault\nfunction Form() {\n  const handleSubmit = (e) => {\n    e.preventDefault();  // デフォルト動作を防ぐ\n    console.log(\"フォーム送信\");\n  };\n\n  return (\n    <form onSubmit={handleSubmit}>\n      <input type=\"text\" />\n      <button type=\"submit\">送信</button>\n    </form>\n  );\n}\n\n// よく使うイベント\n<input onChange={handleChange} />        // 入力変更\n<input onFocus={handleFocus} />          // フォーカス取得\n<input onBlur={handleBlur} />            // フォーカス喪失\n<form onSubmit={handleSubmit} />         // フォーム送信\n<div onMouseEnter={handleEnter} />       // マウス入る\n<div onMouseLeave={handleLeave} />       // マウス出る\n<input onKeyDown={handleKeyDown} />      // キー押下",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'リストレンダリングとkey',
                        'content' => "// map()を使ったリストのレンダリング\nfunction UserList() {\n  const users = [\n    { id: 1, name: \"太郎\", age: 25 },\n    { id: 2, name: \"花子\", age: 30 },\n    { id: 3, name: \"次郎\", age: 22 }\n  ];\n\n  return (\n    <ul>\n      {users.map((user) => (\n        <li key={user.id}>\n          {user.name} ({user.age}歳)\n        </li>\n      ))}\n    </ul>\n  );\n}\n\n// keyは一意である必要がある（インデックスは避ける）\n// 悪い例\n{users.map((user, index) => (\n  <li key={index}>{user.name}</li>  // 順序が変わると問題\n))}\n\n// 良い例\n{users.map((user) => (\n  <li key={user.id}>{user.name}</li>  // 一意のID\n))}\n\n// コンポーネントを使う\nfunction UserCard({ user }) {\n  return (\n    <div className=\"card\">\n      <h3>{user.name}</h3>\n      <p>年齢: {user.age}歳</p>\n    </div>\n  );\n}\n\nfunction UserList() {\n  return (\n    <div>\n      {users.map((user) => (\n        <UserCard key={user.id} user={user} />\n      ))}\n    </div>\n  );\n}\n\n// filter()と組み合わせ\nfunction FilteredList() {\n  const [filter, setFilter] = useState(\"\");\n  const users = [...];\n\n  const filteredUsers = users.filter(user =>\n    user.name.includes(filter)\n  );\n\n  return (\n    <div>\n      <input\n        value={filter}\n        onChange={(e) => setFilter(e.target.value)}\n        placeholder=\"検索\"\n      />\n      <ul>\n        {filteredUsers.map(user => (\n          <li key={user.id}>{user.name}</li>\n        ))}\n      </ul>\n    </div>\n  );\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '制御されたコンポーネント',
                        'content' => "// 制御されたコンポーネント（Controlled Component）\nfunction Form() {\n  const [formData, setFormData] = useState({\n    name: \"\",\n    email: \"\",\n    age: \"\",\n    gender: \"\",\n    message: \"\",\n    subscribe: false\n  });\n\n  const handleChange = (e) => {\n    const { name, value, type, checked } = e.target;\n    setFormData({\n      ...formData,\n      [name]: type === \"checkbox\" ? checked : value\n    });\n  };\n\n  const handleSubmit = (e) => {\n    e.preventDefault();\n    console.log(formData);\n  };\n\n  return (\n    <form onSubmit={handleSubmit}>\n      {/* テキスト入力 */}\n      <input\n        type=\"text\"\n        name=\"name\"\n        value={formData.name}\n        onChange={handleChange}\n        placeholder=\"名前\"\n      />\n\n      {/* メール */}\n      <input\n        type=\"email\"\n        name=\"email\"\n        value={formData.email}\n        onChange={handleChange}\n        placeholder=\"メール\"\n      />\n\n      {/* 数値 */}\n      <input\n        type=\"number\"\n        name=\"age\"\n        value={formData.age}\n        onChange={handleChange}\n        placeholder=\"年齢\"\n      />\n\n      {/* セレクト */}\n      <select name=\"gender\" value={formData.gender} onChange={handleChange}>\n        <option value=\"\">選択してください</option>\n        <option value=\"male\">男性</option>\n        <option value=\"female\">女性</option>\n      </select>\n\n      {/* テキストエリア */}\n      <textarea\n        name=\"message\"\n        value={formData.message}\n        onChange={handleChange}\n        placeholder=\"メッセージ\"\n      />\n\n      {/* チェックボックス */}\n      <label>\n        <input\n          type=\"checkbox\"\n          name=\"subscribe\"\n          checked={formData.subscribe}\n          onChange={handleChange}\n        />\n        ニュースレターを購読\n      </label>\n\n      <button type=\"submit\">送信</button>\n    </form>\n  );\n}\n\n// バリデーション例\nconst [errors, setErrors] = useState({});\n\nconst validate = () => {\n  const newErrors = {};\n  if (!formData.name) newErrors.name = \"名前は必須です\";\n  if (!formData.email) newErrors.email = \"メールは必須です\";\n  if (formData.age < 18) newErrors.age = \"18歳以上である必要があります\";\n  return newErrors;\n};\n\nconst handleSubmit = (e) => {\n  e.preventDefault();\n  const newErrors = validate();\n  if (Object.keys(newErrors).length > 0) {\n    setErrors(newErrors);\n  } else {\n    // 送信処理\n  }\n};",
                        'code_language' => 'jsx',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 2: Hooks (第4週～第6週)
        $milestone2 = $template->milestones()->create([
            'title' => 'Hooks',
            'description' => 'useState、useEffect、useContext、useReducer、カスタムフック',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                'useEffectで副作用を管理',
                'useContextでグローバル状態を共有',
                'useReducerで複雑な状態を管理',
                'カスタムフックを作成'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 4
            [
                'title' => '第4週：useState、useEffect',
                'description' => '状態管理の基本、副作用の処理、ライフサイクル',
                'sort_order' => 4,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['React Hooks Introduction', 'useEffect Guide'],
                'subtasks' => [
                    ['title' => 'useStateの詳細を学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'useEffectを学習', 'estimated_minutes' => 180, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'useEffectの基本',
                        'content' => "import { useState, useEffect } from 'react';\n\n// useEffect - 副作用を処理\nfunction Example() {\n  const [count, setCount] = useState(0);\n\n  // マウント時と更新時に実行\n  useEffect(() => {\n    document.title = `カウント: \${count}`;\n  });\n\n  return (\n    <button onClick={() => setCount(count + 1)}>\n      クリック数: {count}\n    </button>\n  );\n}\n\n// マウント時のみ実行（空の依存配列）\nfunction DataFetcher() {\n  const [data, setData] = useState(null);\n\n  useEffect(() => {\n    fetch('/api/data')\n      .then(res => res.json())\n      .then(data => setData(data));\n  }, []);  // 空配列 = マウント時のみ\n\n  return <div>{data?.title}</div>;\n}\n\n// 特定の値の変更時のみ実行\nfunction SearchResults({ query }) {\n  const [results, setResults] = useState([]);\n\n  useEffect(() => {\n    fetch(`/api/search?q=\${query}`)\n      .then(res => res.json())\n      .then(data => setResults(data));\n  }, [query]);  // queryが変わったら実行\n\n  return <ul>{results.map(r => <li key={r.id}>{r.title}</li>)}</ul>;\n}\n\n// クリーンアップ関数\nfunction Timer() {\n  const [count, setCount] = useState(0);\n\n  useEffect(() => {\n    const timer = setInterval(() => {\n      setCount(c => c + 1);\n    }, 1000);\n\n    // クリーンアップ（アンマウント時や再実行前）\n    return () => {\n      clearInterval(timer);\n    };\n  }, []);\n\n  return <h1>{count}秒経過</h1>;\n}\n\n// イベントリスナー\nfunction WindowSize() {\n  const [width, setWidth] = useState(window.innerWidth);\n\n  useEffect(() => {\n    const handleResize = () => setWidth(window.innerWidth);\n    window.addEventListener('resize', handleResize);\n\n    return () => {\n      window.removeEventListener('resize', handleResize);\n    };\n  }, []);\n\n  return <p>幅: {width}px</p>;\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'useEffectの依存配列',
                        'content' => "# useEffectの依存配列\n\n## 1. 依存配列なし\n```jsx\nuseEffect(() => {\n  // レンダリング毎に実行\n});\n```\n**使用場面**: ほぼない（パフォーマンス悪い）\n\n## 2. 空の依存配列\n```jsx\nuseEffect(() => {\n  // マウント時のみ実行\n}, []);\n```\n**使用場面**:\n- API初回データ取得\n- イベントリスナー登録\n- タイマー開始\n\n## 3. 特定の値を依存\n```jsx\nuseEffect(() => {\n  // count が変わったら実行\n}, [count]);\n```\n**使用場面**:\n- 検索クエリが変わったらAPI再取得\n- ユーザーIDが変わったらプロフィール再取得\n\n## 注意点\n\n### ⚠️ 無限ループに注意\n```jsx\nconst [data, setData] = useState([]);\n\nuseEffect(() => {\n  setData([...data, newItem]);  // NG! 無限ループ\n}, [data]);\n```\n\n### ✅ 正しい書き方\n```jsx\nuseEffect(() => {\n  fetchData().then(setData);\n}, []);  // 初回のみ\n```\n\n### オブジェクトや配列の依存\n```jsx\n// NG: 毎回新しいオブジェクト\nconst config = { url: '/api' };\nuseEffect(() => {}, [config]);  // 毎回実行される\n\n// OK: プリミティブ値を依存\nconst url = '/api';\nuseEffect(() => {}, [url]);\n```",
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 5
            [
                'title' => '第5週：useContext、useReducer',
                'description' => 'Context APIによる状態共有、useReducerによる複雑な状態管理',
                'sort_order' => 5,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['useContext', 'useReducer'],
                'subtasks' => [
                    ['title' => 'useContextを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'useReducerを学習', 'estimated_minutes' => 180, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'useContext',
                        'content' => "import { createContext, useContext, useState } from 'react';\n\n// Context作成\nconst ThemeContext = createContext();\n\n// Provider（値を提供する側）\nfunction App() {\n  const [theme, setTheme] = useState('light');\n\n  return (\n    <ThemeContext.Provider value={{ theme, setTheme }}>\n      <Header />\n      <Main />\n      <Footer />\n    </ThemeContext.Provider>\n  );\n}\n\n// Consumer（値を使う側）\nfunction Header() {\n  const { theme, setTheme } = useContext(ThemeContext);\n\n  return (\n    <header className={theme}>\n      <button onClick={() => setTheme(theme === 'light' ? 'dark' : 'light')}>\n        テーマ切替\n      </button>\n    </header>\n  );\n}\n\n// カスタムフックでラップ（推奨）\nconst useTheme = () => {\n  const context = useContext(ThemeContext);\n  if (!context) {\n    throw new Error('useTheme must be used within ThemeProvider');\n  }\n  return context;\n};\n\n// 使用\nfunction Button() {\n  const { theme } = useTheme();\n  return <button className={theme}>ボタン</button>;\n}\n\n// 複数のContext\nconst UserContext = createContext();\nconst SettingsContext = createContext();\n\nfunction App() {\n  const [user, setUser] = useState(null);\n  const [settings, setSettings] = useState({});\n\n  return (\n    <UserContext.Provider value={{ user, setUser }}>\n      <SettingsContext.Provider value={{ settings, setSettings }}>\n        <MainApp />\n      </SettingsContext.Provider>\n    </UserContext.Provider>\n  );\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'useReducer',
                        'content' => "import { useReducer } from 'react';\n\n// Reducer関数\nfunction counterReducer(state, action) {\n  switch (action.type) {\n    case 'increment':\n      return { count: state.count + 1 };\n    case 'decrement':\n      return { count: state.count - 1 };\n    case 'reset':\n      return { count: 0 };\n    default:\n      throw new Error(`Unknown action: \${action.type}`);\n  }\n}\n\nfunction Counter() {\n  const [state, dispatch] = useReducer(counterReducer, { count: 0 });\n\n  return (\n    <div>\n      <p>カウント: {state.count}</p>\n      <button onClick={() => dispatch({ type: 'increment' })}>+</button>\n      <button onClick={() => dispatch({ type: 'decrement' })}>-</button>\n      <button onClick={() => dispatch({ type: 'reset' })}>リセット</button>\n    </div>\n  );\n}\n\n// 複雑な状態管理\nconst initialState = {\n  todos: [],\n  filter: 'all'\n};\n\nfunction todoReducer(state, action) {\n  switch (action.type) {\n    case 'add':\n      return {\n        ...state,\n        todos: [...state.todos, {\n          id: Date.now(),\n          text: action.text,\n          completed: false\n        }]\n      };\n    case 'toggle':\n      return {\n        ...state,\n        todos: state.todos.map(todo =>\n          todo.id === action.id\n            ? { ...todo, completed: !todo.completed }\n            : todo\n        )\n      };\n    case 'delete':\n      return {\n        ...state,\n        todos: state.todos.filter(todo => todo.id !== action.id)\n      };\n    case 'setFilter':\n      return { ...state, filter: action.filter };\n    default:\n      return state;\n  }\n}\n\nfunction TodoApp() {\n  const [state, dispatch] = useReducer(todoReducer, initialState);\n\n  const addTodo = (text) => {\n    dispatch({ type: 'add', text });\n  };\n\n  return (\n    <div>\n      {state.todos.map(todo => (\n        <div key={todo.id}>\n          <input\n            type=\"checkbox\"\n            checked={todo.completed}\n            onChange={() => dispatch({ type: 'toggle', id: todo.id })}\n          />\n          {todo.text}\n          <button onClick={() => dispatch({ type: 'delete', id: todo.id })}>\n            削除\n          </button>\n        </div>\n      ))}\n    </div>\n  );\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 2
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：その他のHooksとカスタムフック',
                'description' => 'useRef、useMemo、useCallback、カスタムフック作成',
                'sort_order' => 6,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['React Hooks API Reference'],
                'subtasks' => [
                    ['title' => 'useRef、useMemo、useCallbackを学習', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'カスタムフックを作成', 'estimated_minutes' => 180, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'useRef、useMemo、useCallback',
                        'content' => "import { useRef, useMemo, useCallback } from 'react';\n\n// useRef - DOM要素の参照、値の保持\nfunction TextInput() {\n  const inputRef = useRef(null);\n\n  const focusInput = () => {\n    inputRef.current.focus();\n  };\n\n  return (\n    <div>\n      <input ref={inputRef} />\n      <button onClick={focusInput}>フォーカス</button>\n    </div>\n  );\n}\n\n// useRef - レンダリングをトリガーしない値\nfunction Timer() {\n  const [count, setCount] = useState(0);\n  const intervalRef = useRef(null);\n\n  useEffect(() => {\n    intervalRef.current = setInterval(() => {\n      setCount(c => c + 1);\n    }, 1000);\n\n    return () => clearInterval(intervalRef.current);\n  }, []);\n\n  return <h1>{count}</h1>;\n}\n\n// useMemo - 計算結果をメモ化\nfunction ExpensiveComponent({ items }) {\n  const total = useMemo(() => {\n    console.log('計算中...');\n    return items.reduce((sum, item) => sum + item.price, 0);\n  }, [items]);  // itemsが変わった時だけ再計算\n\n  return <div>合計: {total}円</div>;\n}\n\n// useCallback - 関数をメモ化\nfunction Parent() {\n  const [count, setCount] = useState(0);\n\n  // countが変わらない限り、同じ関数インスタンス\n  const handleClick = useCallback(() => {\n    console.log('クリック');\n  }, []);\n\n  return <Child onClick={handleClick} />;\n}\n\nfunction Child({ onClick }) {\n  console.log('Child render');\n  return <button onClick={onClick}>ボタン</button>;\n}\n\n// React.memoと組み合わせ\nconst Child = React.memo(({ onClick }) => {\n  console.log('Child render');\n  return <button onClick={onClick}>ボタン</button>;\n});",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'カスタムフック',
                        'content' => "// カスタムフック - ロジックの再利用\n\n// 1. useLocalStorage\nfunction useLocalStorage(key, initialValue) {\n  const [value, setValue] = useState(() => {\n    const item = localStorage.getItem(key);\n    return item ? JSON.parse(item) : initialValue;\n  });\n\n  useEffect(() => {\n    localStorage.setItem(key, JSON.stringify(value));\n  }, [key, value]);\n\n  return [value, setValue];\n}\n\n// 使用\nfunction App() {\n  const [name, setName] = useLocalStorage('name', '');\n  return (\n    <input\n      value={name}\n      onChange={(e) => setName(e.target.value)}\n    />\n  );\n}\n\n// 2. useFetch\nfunction useFetch(url) {\n  const [data, setData] = useState(null);\n  const [loading, setLoading] = useState(true);\n  const [error, setError] = useState(null);\n\n  useEffect(() => {\n    setLoading(true);\n    fetch(url)\n      .then(res => res.json())\n      .then(data => {\n        setData(data);\n        setLoading(false);\n      })\n      .catch(err => {\n        setError(err);\n        setLoading(false);\n      });\n  }, [url]);\n\n  return { data, loading, error };\n}\n\n// 使用\nfunction UserProfile({ userId }) {\n  const { data, loading, error } = useFetch(`/api/users/\${userId}`);\n\n  if (loading) return <div>読み込み中...</div>;\n  if (error) return <div>エラー: {error.message}</div>;\n  return <div>{data.name}</div>;\n}\n\n// 3. useToggle\nfunction useToggle(initialValue = false) {\n  const [value, setValue] = useState(initialValue);\n  const toggle = useCallback(() => setValue(v => !v), []);\n  return [value, toggle];\n}\n\n// 使用\nfunction Modal() {\n  const [isOpen, toggleOpen] = useToggle(false);\n  return (\n    <div>\n      <button onClick={toggleOpen}>開く</button>\n      {isOpen && <div>モーダル内容</div>}\n    </div>\n  );\n}\n\n// 4. useDebounce\nfunction useDebounce(value, delay) {\n  const [debouncedValue, setDebouncedValue] = useState(value);\n\n  useEffect(() => {\n    const timer = setTimeout(() => {\n      setDebouncedValue(value);\n    }, delay);\n\n    return () => clearTimeout(timer);\n  }, [value, delay]);\n\n  return debouncedValue;\n}\n\n// 使用\nfunction SearchBox() {\n  const [searchTerm, setSearchTerm] = useState('');\n  const debouncedSearch = useDebounce(searchTerm, 500);\n\n  useEffect(() => {\n    if (debouncedSearch) {\n      // API呼び出し\n    }\n  }, [debouncedSearch]);\n\n  return <input onChange={(e) => setSearchTerm(e.target.value)} />;\n}",
                        'code_language' => 'jsx',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 3: State Management & Routing (第7週～第9週)
        $milestone3 = $template->milestones()->create([
            'title' => 'State Management & Routing',
            'description' => 'React Router、Redux Toolkit、非同期処理、ミドルウェア',
            'sort_order' => 3,
            'estimated_hours' => 24,
            'deliverables' => [
                'React Routerでルーティング実装',
                'Redux Toolkitでグローバル状態管理',
                '非同期アクションを処理',
                '実践的なSPA構築'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 7
            [
                'title' => '第7週：React Router',
                'description' => 'ルーティング、ナビゲーション、動的ルート、ネストされたルート',
                'sort_order' => 7,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['React Router Documentation'],
                'subtasks' => [
                    ['title' => 'React Routerの基本を学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => '動的ルートとナビゲーションを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'React Router v6',
                        'content' => "import { BrowserRouter, Routes, Route, Link, Navigate } from 'react-router-dom';\n\n// インストール\n// npm install react-router-dom\n\nfunction App() {\n  return (\n    <BrowserRouter>\n      <nav>\n        <Link to=\"/\">ホーム</Link>\n        <Link to=\"/about\">About</Link>\n        <Link to=\"/users\">ユーザー</Link>\n      </nav>\n\n      <Routes>\n        <Route path=\"/\" element={<Home />} />\n        <Route path=\"/about\" element={<About />} />\n        <Route path=\"/users\" element={<Users />} />\n        <Route path=\"*\" element={<NotFound />} />\n      </Routes>\n    </BrowserRouter>\n  );\n}\n\n// 動的ルート\n<Route path=\"/users/:id\" element={<UserProfile />} />\n\nfunction UserProfile() {\n  const { id } = useParams();  // パラメータ取得\n  return <div>ユーザーID: {id}</div>;\n}\n\n// クエリパラメータ\nimport { useSearchParams } from 'react-router-dom';\n\nfunction SearchPage() {\n  const [searchParams, setSearchParams] = useSearchParams();\n  const query = searchParams.get('q');\n\n  return <div>検索: {query}</div>;\n}\n\n// プログラムでナビゲーション\nimport { useNavigate } from 'react-router-dom';\n\nfunction LoginForm() {\n  const navigate = useNavigate();\n\n  const handleSubmit = () => {\n    // ログイン処理\n    navigate('/dashboard');\n    // navigate(-1);  // 戻る\n  };\n}\n\n// ネストされたルート\n<Route path=\"/dashboard\" element={<Dashboard />}>\n  <Route path=\"profile\" element={<Profile />} />\n  <Route path=\"settings\" element={<Settings />} />\n</Route>\n\nfunction Dashboard() {\n  return (\n    <div>\n      <h1>Dashboard</h1>\n      <nav>\n        <Link to=\"profile\">プロフィール</Link>\n        <Link to=\"settings\">設定</Link>\n      </nav>\n      <Outlet />  {/* 子ルートがここに表示される */}\n    </div>\n  );\n}\n\n// Protected Route\nfunction PrivateRoute({ children }) {\n  const isAuthenticated = useAuth();\n  return isAuthenticated ? children : <Navigate to=\"/login\" />;\n}\n\n<Route\n  path=\"/dashboard\"\n  element={\n    <PrivateRoute>\n      <Dashboard />\n    </PrivateRoute>\n  }\n/>",
                        'code_language' => 'jsx',
                        'sort_order' => 1
                    ],
                ],
            ],
            // Week 8-9は省略（文字数制限のため簡略化）
            [
                'title' => '第8週：Redux Toolkit基礎',
                'description' => 'Redux Toolkit、Slice、Store、Reducer',
                'sort_order' => 8,
                'estimated_minutes' => 660,
                'priority' => 5,
                'resources' => ['Redux Toolkit Documentation'],
                'subtasks' => [
                    ['title' => 'Redux Toolkitをセットアップ', 'estimated_minutes' => 330, 'sort_order' => 1],
                    ['title' => 'Slice作成と使用を学習', 'estimated_minutes' => 330, 'sort_order' => 2],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第9週：非同期処理とミドルウェア',
                'description' => 'createAsyncThunk、RTK Query、ミドルウェア',
                'sort_order' => 9,
                'estimated_minutes' => 330,
                'priority' => 4,
                'resources' => ['RTK Query', 'Redux Middleware'],
                'subtasks' => [
                    ['title' => '非同期アクションを学習', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'RTK Queryを学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                ],
                'knowledge_items' => [],
            ],
        ]);

        // Milestone 4: Advanced & Best Practices (第10週～第12週)
        $milestone4 = $template->milestones()->create([
            'title' => 'Advanced & Best Practices',
            'description' => 'パフォーマンス最適化、テスト、TypeScript、デプロイ',
            'sort_order' => 4,
            'estimated_hours' => 24,
            'deliverables' => [
                'パフォーマンス最適化を実装',
                'Jest + RTLでテストを記述',
                'TypeScript + Reactで型安全なアプリ',
                'プロダクション環境にデプロイ'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => '第10週：パフォーマンス最適化',
                'description' => 'React.memo、Code Splitting、Lazy Loading、最適化テクニック',
                'sort_order' => 10,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => ['React Performance Optimization'],
                'subtasks' => [
                    ['title' => 'パフォーマンス最適化技術を学習', 'estimated_minutes' => 240, 'sort_order' => 1],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第11週：テスト',
                'description' => 'Jest、React Testing Library、コンポーネントテスト',
                'sort_order' => 11,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => ['React Testing Library'],
                'subtasks' => [
                    ['title' => 'React Testing Libraryを学習', 'estimated_minutes' => 300, 'sort_order' => 1],
                ],
                'knowledge_items' => [],
            ],
            [
                'title' => '第12週：TypeScript + React、デプロイ',
                'description' => 'TypeScriptとReactの統合、ベストプラクティス、デプロイ',
                'sort_order' => 12,
                'estimated_minutes' => 300,
                'priority' => 4,
                'resources' => ['React TypeScript', 'Deployment Guide'],
                'subtasks' => [
                    ['title' => 'TypeScript + Reactを学習', 'estimated_minutes' => 180, 'sort_order' => 1],
                    ['title' => 'デプロイとベストプラクティスを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                ],
                'knowledge_items' => [],
            ],
        ]);

        echo "React.js Course Seeder completed successfully!\n";
    }
}
