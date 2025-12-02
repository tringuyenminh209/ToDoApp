<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KnowledgeCategory;
use Illuminate\Support\Facades\DB;

class KnowledgeCategorySeeder extends Seeder
{
    /**
     * データベースのシードを実行します。
     *
     * IT学生向けのデフォルト知識カテゴリを作成します
     */
    public function run(): void
    {
        // 最初のユーザーを取得（またはデモユーザーを作成）
        $user = User::first();

        if (!$user) {
            $this->command->warn('ユーザーが見つかりません。最初にユーザーを作成してください。');
            return;
        }

        $this->command->info("ユーザー {$user->email} のデフォルト知識カテゴリを作成中...");

        DB::beginTransaction();

        try {
            // このユーザーの既存カテゴリをクリア（オプション - 不要な場合はコメントアウト）
            // KnowledgeCategory::where('user_id', $user->id)->delete();

            $categories = $this->getCategoryStructure($user->id);

            foreach ($categories as $category) {
                $this->createCategory($category);
            }

            DB::commit();

            $this->command->info('✅ デフォルト知識カテゴリの作成に成功しました！');
            $this->command->info('作成されたカテゴリ数: ' . KnowledgeCategory::where('user_id', $user->id)->count());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('カテゴリの作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 子カテゴリを再帰的に作成します
     */
    private function createCategory(array $data, ?int $parentId = null): KnowledgeCategory
    {
        $children = $data['children'] ?? [];
        unset($data['children']);

        $category = KnowledgeCategory::create([
            ...$data,
            'parent_id' => $parentId
        ]);

        // 子カテゴリを再帰的に作成
        foreach ($children as $childData) {
            $this->createCategory($childData, $category->id);
        }

        return $category;
    }

    /**
     * 完全なカテゴリ構造を取得します
     */
    private function getCategoryStructure(int $userId): array
    {
        return [
            // 1. プログラミング言語
            [
                'user_id' => $userId,
                'name' => 'プログラミング言語',
                'description' => 'プログラミング言語のノート、構文、ベストプラクティス',
                'color' => '#0FA968',
                'icon' => 'code',
                'sort_order' => 1,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Python',
                        'description' => 'Pythonプログラミングノート',
                        'color' => '#3776AB',
                        'icon' => 'python',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => '基礎', 'color' => '#3776AB', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'データ構造', 'color' => '#3776AB', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'ライブラリ', 'description' => 'pandas, numpy, requests など', 'color' => '#3776AB', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => '面接問題', 'color' => '#3776AB', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Java',
                        'description' => 'Javaプログラミングノート',
                        'color' => '#007396',
                        'icon' => 'java',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'コアJava', 'color' => '#007396', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Spring Framework', 'color' => '#6DB33F', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'デザインパターン', 'color' => '#007396', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'JavaScript',
                        'description' => 'JavaScriptとTypeScriptのノート',
                        'color' => '#F7DF1E',
                        'icon' => 'javascript',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'ES6+機能', 'color' => '#F7DF1E', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'React.js', 'color' => '#61DAFB', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Node.js', 'color' => '#339933', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'TypeScript', 'color' => '#3178C6', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'PHP',
                        'description' => 'PHPとLaravelのノート',
                        'color' => '#777BB4',
                        'icon' => 'php',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Laravel', 'color' => '#FF2D20', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'ベストプラクティス', 'color' => '#777BB4', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'C/C++',
                        'description' => 'CとC++プログラミング',
                        'color' => '#00599C',
                        'icon' => 'cpp',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'STL', 'description' => '標準テンプレートライブラリ', 'color' => '#00599C', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'メモリ管理', 'color' => '#00599C', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Go',
                        'description' => 'Golangプログラミング',
                        'color' => '#00ADD8',
                        'icon' => 'go',
                        'sort_order' => 6,
                        'children' => [
                            ['user_id' => $userId, 'name' => '並行処理', 'color' => '#00ADD8', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Webサービス', 'color' => '#00ADD8', 'sort_order' => 2],
                        ]
                    ],
                ]
            ],

            // 2. コンピュータサイエンス基礎
            [
                'user_id' => $userId,
                'name' => 'コンピュータサイエンス基礎',
                'description' => 'コアCS概念、アルゴリズム、理論',
                'color' => '#FF6B6B',
                'icon' => 'school',
                'sort_order' => 2,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'データ構造',
                        'description' => '配列、連結リスト、木、グラフなど',
                        'color' => '#FF6B6B',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => '配列 & 文字列', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '連結リスト', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => '木 & グラフ', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'ハッシュテーブル', 'color' => '#FF6B6B', 'sort_order' => 4],
                            ['user_id' => $userId, 'name' => 'ヒープ & スタック', 'color' => '#FF6B6B', 'sort_order' => 5],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'アルゴリズム',
                        'description' => 'ソート、検索、動的計画法など',
                        'color' => '#FF6B6B',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'ソート & 検索', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '動的計画法', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => '貪欲アルゴリズム', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'グラフアルゴリズム', 'color' => '#FF6B6B', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'データベース理論',
                        'description' => 'SQL、正規化、トランザクション',
                        'color' => '#FF6B6B',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'SQL基礎', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '正規化', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'インデックス', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'トランザクション', 'color' => '#FF6B6B', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'ネットワーク',
                        'description' => 'TCP/IP、HTTP、DNS',
                        'color' => '#FF6B6B',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'TCP/IP', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'HTTP/HTTPS', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'DNS & ルーティング', 'color' => '#FF6B6B', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'オペレーティングシステム',
                        'description' => 'プロセス、メモリ、ファイルシステム',
                        'color' => '#FF6B6B',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'プロセス管理', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'メモリ管理', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'ファイルシステム', 'color' => '#FF6B6B', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 3. Web開発
            [
                'user_id' => $userId,
                'name' => 'Web開発',
                'description' => 'フロントエンド、バックエンド、DevOps',
                'color' => '#4ECDC4',
                'icon' => 'web',
                'sort_order' => 3,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'フロントエンド',
                        'description' => 'HTML、CSS、JavaScriptフレームワーク',
                        'color' => '#4ECDC4',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'HTML & CSS', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'JavaScript', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'フレームワーク', 'description' => 'React、Vue、Angular', 'color' => '#4ECDC4', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'UI/UXベストプラクティス', 'color' => '#4ECDC4', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'バックエンド',
                        'description' => 'API、認証、データベース',
                        'color' => '#4ECDC4',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'REST API', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '認証', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'キャッシング', 'color' => '#4ECDC4', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'マイクロサービス', 'color' => '#4ECDC4', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'DevOps',
                        'description' => 'CI/CD、モニタリング、クラウド',
                        'color' => '#4ECDC4',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'CI/CD', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'モニタリング', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'クラウドサービス', 'description' => 'AWS、Azure、GCP', 'color' => '#4ECDC4', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'セキュリティ',
                        'description' => 'OWASP、認証、暗号化',
                        'color' => '#E74C3C',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'OWASP Top 10', 'color' => '#E74C3C', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '認証 & 認可', 'color' => '#E74C3C', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => '暗号化', 'color' => '#E74C3C', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 4. ツール & ワークフロー
            [
                'user_id' => $userId,
                'name' => 'ツール & ワークフロー',
                'description' => '開発ツールとユーティリティ',
                'color' => '#95A5A6',
                'icon' => 'build',
                'sort_order' => 4,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Git & バージョン管理',
                        'description' => 'Gitコマンドとワークフロー',
                        'color' => '#F05032',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => '基本コマンド', 'color' => '#F05032', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'ブランチ戦略', 'color' => '#F05032', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'マージコンフリクト', 'color' => '#F05032', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Docker',
                        'description' => 'コンテナ化とオーケストレーション',
                        'color' => '#2496ED',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Dockerfile', 'color' => '#2496ED', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Docker Compose', 'color' => '#2496ED', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'コンテナオーケストレーション', 'color' => '#2496ED', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Linuxコマンド',
                        'description' => 'シェルコマンドとスクリプト',
                        'color' => '#FCC624',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'ファイル操作', 'color' => '#FCC624', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'プロセス管理', 'color' => '#FCC624', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'シェルスクリプト', 'color' => '#FCC624', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'IDE & エディタ',
                        'description' => 'エディタのヒントとショートカット',
                        'color' => '#007ACC',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'VS Codeヒント', 'color' => '#007ACC', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'IntelliJ IDEA', 'color' => '#000000', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Vim', 'color' => '#019733', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'テスト',
                        'description' => '単体テスト、統合テスト、E2Eテスト',
                        'color' => '#8DD6F9',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => '単体テスト', 'color' => '#8DD6F9', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '統合テスト', 'color' => '#8DD6F9', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'テスト駆動開発', 'color' => '#8DD6F9', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 5. 面接準備
            [
                'user_id' => $userId,
                'name' => '面接準備',
                'description' => 'コーディングチャレンジと面接問題',
                'color' => '#9B59B6',
                'icon' => 'work',
                'sort_order' => 5,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'コーディングチャレンジ',
                        'description' => 'LeetCode、HackerRankなど',
                        'color' => '#9B59B6',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'LeetCode Easy', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'LeetCode Medium', 'color' => '#9B59B6', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'LeetCode Hard', 'color' => '#9B59B6', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'HackerRank', 'color' => '#00EA64', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'システム設計',
                        'description' => 'スケーラビリティとアーキテクチャ',
                        'color' => '#9B59B6',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'スケーラビリティ', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'ロードバランシング', 'color' => '#9B59B6', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'データベース設計', 'color' => '#9B59B6', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => '行動面接質問',
                        'description' => 'STAR法と一般的な質問',
                        'color' => '#9B59B6',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'STAR法', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '一般的な質問', 'color' => '#9B59B6', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => '計算量解析',
                        'description' => 'Big O記法',
                        'color' => '#9B59B6',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => '時間計算量', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => '空間計算量', 'color' => '#9B59B6', 'sort_order' => 2],
                        ]
                    ],
                ]
            ],

            // 6. プロジェクト & アイデア
            [
                'user_id' => $userId,
                'name' => 'プロジェクト & アイデア',
                'description' => '個人プロジェクトのノートとコードスニペット',
                'color' => '#F39C12',
                'icon' => 'lightbulb',
                'sort_order' => 6,
                'children' => [
                    ['user_id' => $userId, 'name' => 'プロジェクトアイデア', 'color' => '#F39C12', 'sort_order' => 1],
                    ['user_id' => $userId, 'name' => 'プロジェクトノート', 'color' => '#F39C12', 'sort_order' => 2],
                    ['user_id' => $userId, 'name' => 'アーキテクチャ決定', 'color' => '#F39C12', 'sort_order' => 3],
                    ['user_id' => $userId, 'name' => 'コードスニペットライブラリ', 'color' => '#F39C12', 'sort_order' => 4],
                ]
            ],

            // 7. Note (フォーカスセッションのメモ)
            [
                'user_id' => $userId,
                'name' => 'Note',
                'description' => 'メモを保存',
                'color' => '#0FA968',
                'icon' => 'note',
                'sort_order' => 7,
            ],
        ];
    }
}
