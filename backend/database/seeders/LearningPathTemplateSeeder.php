<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class LearningPathTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: Java Developer
        $this->createJavaDeveloperTemplate();

        // Template 2: React Frontend
        $this->createReactFrontendTemplate();

        // Template 3: Python Data Science
        $this->createPythonDataScienceTemplate();

        // Template 4: UI/UX Design
        $this->createUIUXDesignTemplate();

        // Template 5: Full-Stack Web Development
        $this->createFullStackTemplate();

        // Template 6: Android Development
        $this->createAndroidDevelopmentTemplate();

        // Template 7: English for Business
        $this->createBusinessEnglishTemplate();

        // Template 8: Digital Marketing
        $this->createDigitalMarketingTemplate();

        // Template 9: Laravel Backend
        $this->createLaravelBackendTemplate();

        // Template 10: Machine Learning Basics
        $this->createMachineLearningTemplate();
    }

    private function createJavaDeveloperTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Javaデベロッパーになる',
            'description' => '6ヶ月で初心者からジュニアJavaデベロッパーになるための完全ロードマップ。基礎からSpring Bootまで学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 240,
            'tags' => ['java', 'backend', 'spring', 'oop'],
            'icon' => '☕',
            'color' => '#ED8B00',
            'is_featured' => true,
        ]);

        // Milestone 1: Java Fundamentals
        $milestone1 = $template->milestones()->create([
            'title' => 'Java基礎',
            'description' => 'Java言語の基本構文とオブジェクト指向プログラミングの基礎を学習',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => [
                '50個のコーディング練習問題を完了',
                'コンソール計算機アプリを構築',
                'OOP原則を理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'Java構文とデータ型',
                'description' => '変数、演算子、制御フロー、配列を学習',
                'sort_order' => 1,
                'estimated_minutes' => 300,
                'priority' => 5,
                'resources' => [
                    'https://docs.oracle.com/javase/tutorial/',
                    'https://www.codecademy.com/learn/learn-java'
                ],
            ],
            [
                'title' => 'オブジェクト指向プログラミング',
                'description' => 'クラス、オブジェクト、継承、ポリモーフィズムを学習',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [
                    'https://www.baeldung.com/java-oop'
                ],
            ],
            [
                'title' => 'Java例外処理',
                'description' => 'try-catch、カスタム例外、エラーハンドリング',
                'sort_order' => 3,
                'estimated_minutes' => 240,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'Javaコレクション',
                'description' => 'List、Set、Map、Queue、Stackの使い方',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 2: Advanced Java
        $milestone2 = $template->milestones()->create([
            'title' => 'Java応用',
            'description' => 'Stream API、マルチスレッド、ファイルI/Oなどの高度な機能',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => [
                'マルチスレッドアプリケーションを構築',
                'Stream APIをマスター',
                'ファイル処理システムを実装'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Java Stream API',
                'description' => 'ラムダ式、map、filter、reduce、collectを学習',
                'sort_order' => 1,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'マルチスレッドとコンカレンシー',
                'description' => 'Thread、Executor、Synchronization、Lockを学習',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'ファイルI/O',
                'description' => 'ファイル読み書き、NIO、Path、Filesクラス',
                'sort_order' => 3,
                'estimated_minutes' => 300,
                'priority' => 3,
                'resources' => [],
            ],
        ]);

        // Milestone 3: Database & JDBC
        $milestone3 = $template->milestones()->create([
            'title' => 'データベースとJDBC',
            'description' => 'SQLとJDBCを使用したデータベース操作',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => [
                'CRUDアプリケーションを構築',
                'JDBCを使用したデータベース接続',
                'SQLクエリの最適化'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'SQL基礎',
                'description' => 'SELECT、INSERT、UPDATE、DELETE、JOIN',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'JDBC',
                'description' => 'データベース接続、PreparedStatement、ResultSet',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 4: Spring Boot
        $milestone4 = $template->milestones()->create([
            'title' => 'Spring Boot',
            'description' => 'Spring Bootを使用したRESTful APIの構築',
            'sort_order' => 4,
            'estimated_hours' => 80,
            'deliverables' => [
                'RESTful APIを構築',
                'Spring Data JPAを使用',
                'セキュリティを実装'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'Spring Boot基礎',
                'description' => 'プロジェクト構造、依存性注入、自動設定',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring MVC',
                'description' => 'Controller、Service、Repository層の実装',
                'sort_order' => 2,
                'estimated_minutes' => 720,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring Data JPA',
                'description' => 'エンティティ、リポジトリ、クエリメソッド',
                'sort_order' => 3,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Spring Security',
                'description' => '認証、認可、JWT',
                'sort_order' => 4,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        // Milestone 5: Testing & Deployment
        $milestone5 = $template->milestones()->create([
            'title' => 'テストとデプロイ',
            'description' => 'ユニットテスト、統合テスト、本番環境へのデプロイ',
            'sort_order' => 5,
            'estimated_hours' => 20,
            'deliverables' => [
                'テストカバレッジ80%以上',
                'CI/CDパイプラインを構築',
                'アプリケーションをデプロイ'
            ],
        ]);

        $milestone5->tasks()->createMany([
            [
                'title' => 'JUnitとMockito',
                'description' => 'ユニットテストとモックの作成',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'Docker',
                'description' => 'Dockerfileの作成、コンテナ化',
                'sort_order' => 2,
                'estimated_minutes' => 300,
                'priority' => 3,
                'resources' => [],
            ],
            [
                'title' => 'デプロイ',
                'description' => 'Heroku/AWS/GCPへのデプロイ',
                'sort_order' => 3,
                'estimated_minutes' => 240,
                'priority' => 3,
                'resources' => [],
            ],
        ]);
    }

    private function createReactFrontendTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'React フロントエンド開発',
            'description' => '4ヶ月でモダンなReactアプリケーションを構築できるようになる',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 180,
            'tags' => ['react', 'javascript', 'frontend', 'web'],
            'icon' => '⚛️',
            'color' => '#61DAFB',
            'is_featured' => true,
        ]);

        // Milestone 1: HTML/CSS/JavaScript
        $milestone1 = $template->milestones()->create([
            'title' => 'Web基礎',
            'description' => 'HTML、CSS、JavaScriptの基礎を学習',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => [
                'レスポンシブWebサイトを構築',
                'JavaScriptの基本をマスター',
                'DOM操作を理解'
            ],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'HTML5とセマンティックHTML',
                'description' => 'タグ、属性、フォーム、アクセシビリティ',
                'sort_order' => 1,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'CSS3とFlexbox/Grid',
                'description' => 'レイアウト、レスポンシブデザイン、アニメーション',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'JavaScript基礎',
                'description' => '変数、関数、配列、オブジェクト、DOM操作',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ES6+モダンJavaScript',
                'description' => 'アロー関数、分割代入、Promise、async/await',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        // Milestone 2: React Basics
        $milestone2 = $template->milestones()->create([
            'title' => 'React基礎',
            'description' => 'コンポーネント、State、Propsを学習',
            'sort_order' => 2,
            'estimated_hours' => 50,
            'deliverables' => [
                'Todoアプリを構築',
                'コンポーネントの再利用',
                'State管理を理解'
            ],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Reactセットアップ',
                'description' => 'Create React App、プロジェクト構造',
                'sort_order' => 1,
                'estimated_minutes' => 180,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'コンポーネントとJSX',
                'description' => '関数コンポーネント、Props、条件レンダリング',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'State と Hooks',
                'description' => 'useState、useEffect、カスタムフック',
                'sort_order' => 3,
                'estimated_minutes' => 540,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'イベント処理とフォーム',
                'description' => 'イベントハンドラー、制御コンポーネント',
                'sort_order' => 4,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        // Milestone 3: Advanced React
        $milestone3 = $template->milestones()->create([
            'title' => 'React応用',
            'description' => 'Context API、React Router、パフォーマンス最適化',
            'sort_order' => 3,
            'estimated_hours' => 50,
            'deliverables' => [
                'SPAを構築',
                'グローバルState管理',
                'パフォーマンス最適化'
            ],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'React Router',
                'description' => 'ルーティング、ナビゲーション、動的ルート',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Context API',
                'description' => 'グローバルState、useContext',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'パフォーマンス最適化',
                'description' => 'useMemo、useCallback、React.memo',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 3,
                'resources' => [],
            ],
        ]);

        // Milestone 4: State Management & API
        $milestone4 = $template->milestones()->create([
            'title' => 'State管理とAPI連携',
            'description' => 'Redux/Zustand、REST API、認証',
            'sort_order' => 4,
            'estimated_hours' => 40,
            'deliverables' => [
                'Redux/Zustandを実装',
                'API連携',
                '認証システム'
            ],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'REST API連携',
                'description' => 'fetch、axios、エラーハンドリング',
                'sort_order' => 1,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Redux/Zustand',
                'description' => 'グローバルState管理ライブラリ',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => '認証とセキュリティ',
                'description' => 'JWT、ローカルストレージ、保護ルート',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => [],
            ],
        ]);
    }

    private function createPythonDataScienceTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Pythonデータサイエンス',
            'description' => 'データ分析と機械学習の基礎を5ヶ月で習得',
            'category' => 'data_science',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 200,
            'tags' => ['python', 'data-science', 'machine-learning', 'pandas'],
            'icon' => '🐍',
            'color' => '#3776AB',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'Python基礎',
            'description' => 'Python言語の基本構文とライブラリ',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['Python基礎をマスター', 'NumPy/Pandasを使用'],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'Python構文',
                'description' => '変数、関数、クラス、モジュール',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'NumPy',
                'description' => '配列操作、数値計算',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Pandas',
                'description' => 'DataFrame、データ操作、分析',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'データ可視化',
            'description' => 'Matplotlib、Seabornでデータを可視化',
            'sort_order' => 2,
            'estimated_hours' => 30,
            'deliverables' => ['グラフ作成', 'ダッシュボード構築'],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'Matplotlib',
                'description' => '基本的なグラフ、カスタマイズ',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Seaborn',
                'description' => '統計的可視化、ヒートマップ',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => '機械学習基礎',
            'description' => 'Scikit-learnで機械学習モデルを構築',
            'sort_order' => 3,
            'estimated_hours' => 80,
            'deliverables' => ['予測モデル構築', 'モデル評価'],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => '教師あり学習',
                'description' => '回帰、分類、決定木',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => '教師なし学習',
                'description' => 'クラスタリング、次元削減',
                'sort_order' => 2,
                'estimated_minutes' => 540,
                'priority' => 4,
                'resources' => [],
            ],
            [
                'title' => 'モデル評価',
                'description' => '精度、再現率、F1スコア',
                'sort_order' => 3,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'プロジェクト実践',
            'description' => '実データでプロジェクトを完成',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['Kaggleコンペ参加', 'ポートフォリオ作成'],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'データ収集とクリーニング',
                'description' => 'Web scraping、データ前処理',
                'sort_order' => 1,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'エンドツーエンドプロジェクト',
                'description' => '問題定義からデプロイまで',
                'sort_order' => 2,
                'estimated_minutes' => 720,
                'priority' => 5,
                'resources' => [],
            ],
        ]);
    }

    private function createUIUXDesignTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'UI/UXデザイン',
            'description' => '3ヶ月でUI/UXデザイナーになる',
            'category' => 'design',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 120,
            'tags' => ['ui', 'ux', 'figma', 'design'],
            'icon' => '🎨',
            'color' => '#F24E1E',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'デザイン基礎',
            'description' => 'デザイン原則とツール',
            'sort_order' => 1,
            'estimated_hours' => 30,
            'deliverables' => ['デザイン原則理解', 'Figmaマスター'],
        ]);

        $milestone1->tasks()->createMany([
            [
                'title' => 'デザイン原則',
                'description' => 'タイポグラフィ、色彩理論、レイアウト',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'Figma基礎',
                'description' => 'ツール操作、コンポーネント、Auto Layout',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'UXリサーチ',
            'description' => 'ユーザー調査と分析',
            'sort_order' => 2,
            'estimated_hours' => 30,
            'deliverables' => ['ユーザーインタビュー', 'ペルソナ作成'],
        ]);

        $milestone2->tasks()->createMany([
            [
                'title' => 'ユーザーリサーチ',
                'description' => 'インタビュー、アンケート、観察',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ペルソナとユーザージャーニー',
                'description' => 'ペルソナ作成、ジャーニーマップ',
                'sort_order' => 2,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => [],
            ],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'UIデザイン実践',
            'description' => 'モバイル・Webアプリのデザイン',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => ['モバイルアプリデザイン', 'Webサイトデザイン'],
        ]);

        $milestone3->tasks()->createMany([
            [
                'title' => 'ワイヤーフレーム',
                'description' => 'ローファイ・ハイファイワイヤーフレーム',
                'sort_order' => 1,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'プロトタイプ',
                'description' => 'インタラクティブプロトタイプ作成',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'デザインシステム',
                'description' => 'コンポーネントライブラリ、スタイルガイド',
                'sort_order' => 3,
                'estimated_minutes' => 420,
                'priority' => 4,
                'resources' => [],
            ],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'ポートフォリオ',
            'description' => 'ポートフォリオ作成とプレゼン',
            'sort_order' => 4,
            'estimated_hours' => 20,
            'deliverables' => ['ポートフォリオサイト', 'ケーススタディ3件'],
        ]);

        $milestone4->tasks()->createMany([
            [
                'title' => 'ケーススタディ作成',
                'description' => 'プロセス、課題、解決策を文書化',
                'sort_order' => 1,
                'estimated_minutes' => 600,
                'priority' => 5,
                'resources' => [],
            ],
            [
                'title' => 'ポートフォリオサイト',
                'description' => 'オンラインポートフォリオ構築',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => [],
            ],
        ]);
    }

    private function createFullStackTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'フルスタックWeb開発',
            'description' => '8ヶ月でフロントエンドからバックエンドまで習得',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 320,
            'tags' => ['fullstack', 'react', 'node', 'database'],
            'icon' => '🌐',
            'color' => '#68A063',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'フロントエンド',
            'description' => 'HTML/CSS/JavaScript/React',
            'sort_order' => 1,
            'estimated_hours' => 80,
            'deliverables' => ['レスポンシブWebサイト', 'Reactアプリ'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'バックエンド',
            'description' => 'Node.js/Express/API',
            'sort_order' => 2,
            'estimated_hours' => 80,
            'deliverables' => ['RESTful API', '認証システム'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'データベース',
            'description' => 'SQL/NoSQL/ORM',
            'sort_order' => 3,
            'estimated_hours' => 60,
            'deliverables' => ['データベース設計', 'CRUD操作'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'デプロイとDevOps',
            'description' => 'Docker/CI/CD/クラウド',
            'sort_order' => 4,
            'estimated_hours' => 60,
            'deliverables' => ['本番環境デプロイ', 'CI/CD構築'],
        ]);

        $milestone5 = $template->milestones()->create([
            'title' => 'フルスタックプロジェクト',
            'description' => 'エンドツーエンドアプリ構築',
            'sort_order' => 5,
            'estimated_hours' => 40,
            'deliverables' => ['完全なWebアプリ', 'ポートフォリオ'],
        ]);
    }

    private function createAndroidDevelopmentTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Androidアプリ開発',
            'description' => 'Kotlin/Jetpack Composeで6ヶ月でAndroid開発者に',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 220,
            'tags' => ['android', 'kotlin', 'mobile', 'jetpack-compose'],
            'icon' => '📱',
            'color' => '#3DDC84',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'Kotlin基礎',
            'description' => 'Kotlin言語の基本',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['Kotlin構文マスター', 'OOP理解'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'Android基礎',
            'description' => 'Activity/Fragment/Layout',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => ['基本アプリ構築', 'UI実装'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'Jetpack Compose',
            'description' => 'モダンUIフレームワーク',
            'sort_order' => 3,
            'estimated_hours' => 60,
            'deliverables' => ['Composeアプリ', 'State管理'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'データ永続化とAPI',
            'description' => 'Room/Retrofit/Coroutines',
            'sort_order' => 4,
            'estimated_hours' => 40,
            'deliverables' => ['ローカルDB', 'API連携'],
        ]);

        $milestone5 = $template->milestones()->create([
            'title' => 'Google Playリリース',
            'description' => 'テスト/最適化/公開',
            'sort_order' => 5,
            'estimated_hours' => 20,
            'deliverables' => ['アプリ公開', 'ストアリスティング'],
        ]);
    }

    private function createBusinessEnglishTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'ビジネス英語',
            'description' => '6ヶ月でビジネスシーンで使える英語力を習得',
            'category' => 'language',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 180,
            'tags' => ['english', 'business', 'communication'],
            'icon' => '🗣️',
            'color' => '#4285F4',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'ビジネス英語基礎',
            'description' => 'ビジネスシーンの基本表現',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['自己紹介', 'メール作成'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'プレゼンテーション',
            'description' => '英語でのプレゼンスキル',
            'sort_order' => 2,
            'estimated_hours' => 40,
            'deliverables' => ['プレゼン実施', 'Q&A対応'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => '会議とネゴシエーション',
            'description' => '会議進行と交渉術',
            'sort_order' => 3,
            'estimated_hours' => 50,
            'deliverables' => ['会議参加', '意見表明'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'ビジネス文書',
            'description' => 'レポート、提案書作成',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['ビジネスメール', '提案書'],
        ]);
    }

    private function createDigitalMarketingTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'デジタルマーケティング',
            'description' => '4ヶ月でデジタルマーケターになる',
            'category' => 'business',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 160,
            'tags' => ['marketing', 'seo', 'sns', 'analytics'],
            'icon' => '📊',
            'color' => '#EA4335',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'マーケティング基礎',
            'description' => 'デジタルマーケティングの基本',
            'sort_order' => 1,
            'estimated_hours' => 30,
            'deliverables' => ['マーケティング戦略', 'ターゲット設定'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'SEOとコンテンツマーケティング',
            'description' => '検索エンジン最適化',
            'sort_order' => 2,
            'estimated_hours' => 40,
            'deliverables' => ['SEO施策', 'コンテンツ作成'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'SNSマーケティング',
            'description' => 'Facebook/Instagram/Twitter広告',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => ['SNS広告運用', 'エンゲージメント向上'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'データ分析',
            'description' => 'Google Analytics/広告効果測定',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['レポート作成', 'ROI分析'],
        ]);
    }

    private function createLaravelBackendTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Laravelバックエンド開発',
            'description' => '5ヶ月でLaravelを使ったAPI開発をマスター',
            'category' => 'programming',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 200,
            'tags' => ['laravel', 'php', 'backend', 'api'],
            'icon' => '🔴',
            'color' => '#FF2D20',
            'is_featured' => true,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => 'PHP基礎',
            'description' => 'PHP言語とOOP',
            'sort_order' => 1,
            'estimated_hours' => 40,
            'deliverables' => ['PHP構文マスター', 'OOP実装'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'Laravel基礎',
            'description' => 'ルーティング/コントローラー/ビュー',
            'sort_order' => 2,
            'estimated_hours' => 50,
            'deliverables' => ['CRUDアプリ', 'Blade使用'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'Eloquent ORM',
            'description' => 'データベース操作とリレーション',
            'sort_order' => 3,
            'estimated_hours' => 40,
            'deliverables' => ['モデル設計', 'リレーション実装'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'RESTful API',
            'description' => 'API開発と認証',
            'sort_order' => 4,
            'estimated_hours' => 50,
            'deliverables' => ['API構築', 'JWT認証'],
        ]);

        $milestone5 = $template->milestones()->create([
            'title' => 'テストとデプロイ',
            'description' => 'PHPUnit/本番環境',
            'sort_order' => 5,
            'estimated_hours' => 20,
            'deliverables' => ['ユニットテスト', 'デプロイ'],
        ]);
    }

    private function createMachineLearningTemplate(): void
    {
        $template = LearningPathTemplate::create([
            'title' => '機械学習入門',
            'description' => '4ヶ月で機械学習の基礎を習得',
            'category' => 'data_science',
            'difficulty' => 'intermediate',
            'estimated_hours_total' => 160,
            'tags' => ['machine-learning', 'python', 'ai', 'deep-learning'],
            'icon' => '🤖',
            'color' => '#FF6F00',
            'is_featured' => false,
        ]);

        $milestone1 = $template->milestones()->create([
            'title' => '機械学習基礎',
            'description' => '教師あり学習/教師なし学習',
            'sort_order' => 1,
            'estimated_hours' => 50,
            'deliverables' => ['回帰モデル', '分類モデル'],
        ]);

        $milestone2 = $template->milestones()->create([
            'title' => 'ディープラーニング',
            'description' => 'ニューラルネットワーク/CNN/RNN',
            'sort_order' => 2,
            'estimated_hours' => 60,
            'deliverables' => ['画像分類', 'テキスト分析'],
        ]);

        $milestone3 = $template->milestones()->create([
            'title' => 'モデル最適化',
            'description' => 'ハイパーパラメータチューニング',
            'sort_order' => 3,
            'estimated_hours' => 30,
            'deliverables' => ['モデル改善', 'パフォーマンス向上'],
        ]);

        $milestone4 = $template->milestones()->create([
            'title' => 'MLプロジェクト',
            'description' => 'エンドツーエンドMLプロジェクト',
            'sort_order' => 4,
            'estimated_hours' => 20,
            'deliverables' => ['MLアプリ', 'モデルデプロイ'],
        ]);
    }
}

