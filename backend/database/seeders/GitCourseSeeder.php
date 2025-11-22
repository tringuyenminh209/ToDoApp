<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class GitCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Git/GitHub完全コース - 10週間の実践コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Git/GitHub完全コース',
            'description' => '初心者から中級者向けGit/GitHubコース。10週間でバージョン管理の基礎から、チーム開発、CI/CD、GitHub Actionsまで実践的に学習します。',
            'category' => 'programming',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 80,
            'tags' => ['git', 'github', 'バージョン管理', '初心者', 'チーム開発', 'CI/CD'],
            'icon' => 'ic_git',
            'color' => '#F05032',
            'is_featured' => true,
        ]);

        // Milestone 1: Git基礎 (第1週～第3週)
        $milestone1 = $template->milestones()->create([
            'title' => 'Git基礎',
            'description' => 'Gitのインストールから、基本コマンド、コミット、ブランチまで学習',
            'sort_order' => 1,
            'estimated_hours' => 24,
            'deliverables' => [
                'Git環境をセットアップ完了',
                '基本的なGitコマンドを理解',
                'ローカルリポジトリを作成・管理',
                'ブランチを作成・切り替え'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：Gitとは？環境設定',
                'description' => 'バージョン管理の概念とGitのインストール、初期設定',
                'sort_order' => 1,
                'estimated_minutes' => 240,
                'priority' => 5,
                'resources' => ['Git公式サイト', 'Pro Git Book'],
                'subtasks' => [
                    ['title' => 'Gitをインストール', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'Gitの初期設定', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'バージョン管理の概念を理解', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => '最初のリポジトリを作成', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Gitとは？',
                        'content' => "# Gitとは？\n\n**Git**は、2005年にLinus Torvaldsによって開発された分散型バージョン管理システムです。\n\n## バージョン管理とは？\nソースコードの変更履歴を記録・管理するシステム。\n\n### メリット\n1. **履歴管理**: いつ、誰が、何を変更したか記録\n2. **バックアップ**: 過去の状態に戻せる\n3. **並行開発**: 複数人が同時に開発可能\n4. **実験的変更**: ブランチで安全にテスト\n\n## Gitの特徴\n- **分散型**: 各開発者がフルコピーを持つ\n- **高速**: ローカルで大部分の操作が完結\n- **ブランチが軽量**: 実験やフィーチャー開発が簡単\n- **データ整合性**: SHA-1ハッシュで管理\n\n## GitとGitHubの違い\n- **Git**: バージョン管理システム（ツール）\n- **GitHub**: Gitリポジトリのホスティングサービス（Webサービス）",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Git初期設定',
                        'content' => "# Gitのバージョン確認\ngit --version\n\n# ユーザー情報の設定（必須）\ngit config --global user.name \"Your Name\"\ngit config --global user.email \"your.email@example.com\"\n\n# デフォルトブランチ名の設定\ngit config --global init.defaultBranch main\n\n# エディタの設定（オプション）\ngit config --global core.editor \"code --wait\"\n\n# 設定の確認\ngit config --list\ngit config user.name\ngit config user.email",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '最初のリポジトリ作成',
                        'content' => "# 新しいディレクトリを作成\nmkdir my-first-repo\ncd my-first-repo\n\n# Gitリポジトリとして初期化\ngit init\n\n# .gitディレクトリが作成される（隠しフォルダ）\nls -la\n\n# ファイルを作成\necho \"# My First Repository\" > README.md\n\n# ステータス確認\ngit status",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：基本コマンド（add, commit, log）',
                'description' => 'ステージング、コミット、履歴確認の基本操作',
                'sort_order' => 2,
                'estimated_minutes' => 360,
                'priority' => 5,
                'resources' => ['Git Documentation - Basic Commands'],
                'subtasks' => [
                    ['title' => 'git addを学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'git commitを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'git logを学習', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => 'コミットメッセージの書き方を学習', 'estimated_minutes' => 60, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Gitの3つのエリア',
                        'content' => "# Gitの3つのエリア\n\n```\nWorking Directory → Staging Area → Repository\n（作業ディレクトリ）  （ステージング）   （リポジトリ）\n```\n\n1. **Working Directory**: ファイルを編集する場所\n2. **Staging Area**: コミット予定の変更を準備する場所\n3. **Repository**: コミット済みの履歴が保存される場所\n\n## 基本ワークフロー\n```bash\n# 1. ファイルを編集\necho \"Hello Git\" > file.txt\n\n# 2. ステージングに追加\ngit add file.txt\n\n# 3. コミット（履歴に記録）\ngit commit -m \"Add file.txt\"\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git add - ステージング',
                        'content' => "# 特定のファイルをステージング\ngit add README.md\ngit add src/main.js\n\n# 複数ファイルを指定\ngit add file1.txt file2.txt\n\n# 全ての変更をステージング\ngit add .\ngit add -A\n\n# インタラクティブモード\ngit add -i\n\n# パッチモード（変更の一部だけステージング）\ngit add -p\n\n# ステージングの確認\ngit status",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git commit - コミット',
                        'content' => "# 基本的なコミット\ngit commit -m \"Add new feature\"\n\n# 複数行のコミットメッセージ\ngit commit -m \"Add user authentication\" -m \"- Implement login form\" -m \"- Add validation\"\n\n# ステージング + コミットを同時に（追跡済みファイルのみ）\ngit commit -am \"Update README\"\n\n# エディタでコミットメッセージを書く\ngit commit\n\n# 直前のコミットを修正\ngit commit --amend\n\n# 空のコミット（テスト用など）\ngit commit --allow-empty -m \"Trigger CI\"",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git log - 履歴確認',
                        'content' => "# 基本的なログ表示\ngit log\n\n# コンパクトに表示（1行）\ngit log --oneline\n\n# グラフ表示\ngit log --graph --oneline --all\n\n# 最新N件のみ表示\ngit log -5\ngit log -n 3\n\n# 統計情報付き\ngit log --stat\n\n# 変更内容も表示\ngit log -p\ngit log --patch\n\n# 作者で絞り込み\ngit log --author=\"John\"\n\n# 日付で絞り込み\ngit log --since=\"2024-01-01\"\ngit log --after=\"1 week ago\"\n\n# ファイルの履歴\ngit log README.md\n\n# カスタムフォーマット\ngit log --pretty=format:\"%h - %an, %ar : %s\"",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => '良いコミットメッセージの書き方',
                        'content' => "# 良いコミットメッセージの書き方\n\n## 基本ルール\n1. **1行目**: 50文字以内の要約（命令形）\n2. **2行目**: 空行\n3. **3行目以降**: 詳細説明（72文字で改行）\n\n## フォーマット例\n```\nAdd user authentication feature\n\n- Implement login form with email/password\n- Add JWT token generation\n- Create auth middleware for protected routes\n- Add input validation\n\nFixes #123\n```\n\n## プレフィックス規則（Conventional Commits）\n- `feat:` 新機能\n- `fix:` バグ修正\n- `docs:` ドキュメント\n- `style:` コードスタイル\n- `refactor:` リファクタリング\n- `test:` テスト追加\n- `chore:` ビルド、設定変更\n\n例:\n```\nfeat: add user profile page\nfix: resolve login redirect issue\ndocs: update API documentation\n```",
                        'sort_order' => 5
                    ],
                ],
            ],
            // Week 3
            [
                'title' => '第3週：ブランチとマージの基礎',
                'description' => 'ブランチの作成、切り替え、マージの基本操作',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Git Branching Tutorial'],
                'subtasks' => [
                    ['title' => 'ブランチの概念を理解', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'ブランチを作成・切り替え', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'マージを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                    ['title' => 'コンフリクト解消を学習', 'estimated_minutes' => 150, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'ブランチとは？',
                        'content' => "# ブランチとは？\n\nブランチは、独立した開発ラインを作成する機能です。\n\n## ブランチの使い道\n1. **機能開発**: 新機能を独立して開発\n2. **バグ修正**: 本番の問題を修正\n3. **実験**: 試験的な変更をテスト\n4. **リリース**: バージョン管理\n\n## ブランチ戦略\n```\nmain (本番)\n  |\n  |-- develop (開発)\n  |     |\n  |     |-- feature/user-auth (機能A)\n  |     |-- feature/payment (機能B)\n  |\n  |-- hotfix/critical-bug (緊急修正)\n```\n\n## デフォルトブランチ\n- **main** / **master**: 本番環境用\n- **develop**: 開発用\n- **feature/xxx**: 機能開発用",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'ブランチ操作',
                        'content' => "# ブランチ一覧を表示\ngit branch\ngit branch -a  # リモートブランチも表示\n\n# 新しいブランチを作成\ngit branch feature/login\ngit branch develop\n\n# ブランチを切り替え\ngit checkout feature/login\ngit checkout develop\n\n# ブランチ作成 + 切り替え（同時）\ngit checkout -b feature/payment\ngit checkout -b hotfix/bug-123\n\n# 新しいコマンド（Git 2.23+）\ngit switch feature/login  # 切り替え\ngit switch -c feature/new  # 作成 + 切り替え\n\n# ブランチ名の変更\ngit branch -m old-name new-name\n\n# ブランチの削除\ngit branch -d feature/login  # マージ済みのみ\ngit branch -D feature/login  # 強制削除",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'マージ',
                        'content' => "# 基本的なマージ（Fast-forward）\ngit checkout main\ngit merge feature/login\n\n# マージコミットを作成（--no-ff）\ngit merge --no-ff feature/payment\n\n# マージの中止\ngit merge --abort\n\n# マージ済みブランチの確認\ngit branch --merged\ngit branch --no-merged\n\n# 例: feature開発の流れ\n# 1. 新ブランチ作成\ngit checkout -b feature/user-profile\n\n# 2. 開発 & コミット\ngit add .\ngit commit -m \"Add user profile page\"\n\n# 3. mainブランチに戻る\ngit checkout main\n\n# 4. マージ\ngit merge feature/user-profile\n\n# 5. ブランチ削除\ngit branch -d feature/user-profile",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'コンフリクト（競合）解消',
                        'content' => "# コンフリクトが発生した場合\n$ git merge feature/payment\nAuto-merging file.txt\nCONFLICT (content): Merge conflict in file.txt\nAutomatic merge failed; fix conflicts and then commit the result.\n\n# コンフリクトファイルの確認\ngit status\n\n# ファイル内のコンフリクトマーカー\n<<<<<<< HEAD\nこれは現在のブランチの内容\n=======\nこれはマージしようとしているブランチの内容\n>>>>>>> feature/payment\n\n# 解消手順:\n# 1. ファイルを手動で編集してマーカーを削除\n# 2. どちらの変更を残すか決定\n# 3. ステージング\ngit add file.txt\n\n# 4. マージコミット完了\ngit commit\n\n# コンフリクト解消を中止する場合\ngit merge --abort\n\n# マージツールを使う\ngit mergetool",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 2: GitHub基礎 (第4週～第6週)
        $milestone2 = $template->milestones()->create([
            'title' => 'GitHub基礎',
            'description' => 'GitHubアカウント作成、リモートリポジトリ、プッシュ・プル、プルリクエスト',
            'sort_order' => 2,
            'estimated_hours' => 24,
            'deliverables' => [
                'GitHubアカウントを作成',
                'リモートリポジトリを作成・管理',
                'プッシュとプルを理解',
                'プルリクエストを作成'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 4
            [
                'title' => '第4週：GitHubとリモートリポジトリ',
                'description' => 'GitHubアカウント作成、リポジトリ作成、clone、remote操作',
                'sort_order' => 4,
                'estimated_minutes' => 270,
                'priority' => 5,
                'resources' => ['GitHub Docs', 'GitHub Getting Started'],
                'subtasks' => [
                    ['title' => 'GitHubアカウントを作成', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'SSH/HTTPS認証を設定', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'リモートリポジトリを作成', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => 'clone、remoteを学習', 'estimated_minutes' => 120, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'GitHubとは？',
                        'content' => "# GitHubとは？\n\n**GitHub**は、Gitリポジトリのホスティングサービスです。\n\n## GitHubの主な機能\n1. **リポジトリホスティング**: コードをクラウドに保存\n2. **コラボレーション**: チームで開発\n3. **Issue管理**: バグや機能要望を追跡\n4. **Pull Request**: コードレビュー\n5. **GitHub Actions**: CI/CD自動化\n6. **GitHub Pages**: Webサイトホスティング\n7. **プロジェクト管理**: かんばんボード\n\n## GitHubの競合サービス\n- **GitLab**: 自己ホスティング可能\n- **Bitbucket**: Atlassian製品と連携\n- **Azure Repos**: Microsoft製\n\n## パブリック vs プライベート\n- **パブリック**: 誰でも閲覧可能（オープンソース）\n- **プライベート**: 招待された人のみ閲覧可能",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'SSH認証設定',
                        'content' => "# SSH鍵の生成\nssh-keygen -t ed25519 -C \"your.email@example.com\"\n\n# SSH鍵をクリップボードにコピー（Mac）\npbcopy < ~/.ssh/id_ed25519.pub\n\n# SSH鍵をクリップボードにコピー（Windows）\ncat ~/.ssh/id_ed25519.pub | clip\n\n# SSH鍵をクリップボードにコピー（Linux）\ncat ~/.ssh/id_ed25519.pub | xclip -selection clipboard\n\n# GitHub > Settings > SSH and GPG keys > New SSH key\n# タイトルを入力して、鍵を貼り付け\n\n# 接続テスト\nssh -T git@github.com\n# 成功すると: \"Hi username! You've successfully authenticated...\"",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'リモートリポジトリ操作',
                        'content' => "# 既存のリポジトリをクローン\ngit clone https://github.com/username/repo.git\ngit clone git@github.com:username/repo.git  # SSH\n\n# 特定のブランチをクローン\ngit clone -b develop https://github.com/username/repo.git\n\n# リモートリポジトリを追加\ngit remote add origin https://github.com/username/repo.git\n\n# リモートリポジトリの確認\ngit remote -v\n\n# リモートリポジトリの詳細\ngit remote show origin\n\n# リモートリポジトリのURL変更\ngit remote set-url origin git@github.com:username/repo.git\n\n# リモートリポジトリを削除\ngit remote remove origin\n\n# 複数のリモート\ngit remote add upstream https://github.com/original/repo.git\ngit remote add origin https://github.com/myname/repo.git",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 5
            [
                'title' => '第5週：Push、Pull、Fetch',
                'description' => 'リモートリポジトリとの同期、プッシュ・プル操作',
                'sort_order' => 5,
                'estimated_minutes' => 420,
                'priority' => 5,
                'resources' => ['Git Remote Commands'],
                'subtasks' => [
                    ['title' => 'git pushを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'git pullを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'git fetchを学習', 'estimated_minutes' => 90, 'sort_order' => 3],
                    ['title' => 'pullとfetchの違いを理解', 'estimated_minutes' => 90, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'git push - アップロード',
                        'content' => "# 基本的なプッシュ\ngit push origin main\ngit push origin develop\n\n# 初回プッシュ（上流ブランチを設定）\ngit push -u origin main\ngit push --set-upstream origin feature/login\n\n# 以降は省略可能\ngit push\n\n# 全てのブランチをプッシュ\ngit push --all\n\n# タグをプッシュ\ngit push --tags\ngit push origin v1.0.0\n\n# 強制プッシュ（危険！）\ngit push -f origin main\ngit push --force-with-lease origin main  # より安全\n\n# ブランチを削除\ngit push origin --delete feature/old-branch\ngit push origin :feature/old-branch  # 古い書き方",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git pull - ダウンロード + マージ',
                        'content' => "# 基本的なプル（fetch + merge）\ngit pull origin main\n\n# 上流ブランチが設定済みの場合\ngit pull\n\n# リベース方式でプル\ngit pull --rebase origin main\n\n# 全てのリモートブランチを取得\ngit pull --all\n\n# 例: チーム開発の流れ\n# 1. 作業前に最新を取得\ngit pull origin main\n\n# 2. 開発作業\ngit add .\ngit commit -m \"Add feature\"\n\n# 3. プッシュ前にもう一度プル\ngit pull origin main\n\n# 4. コンフリクトがあれば解消\n\n# 5. プッシュ\ngit push origin main",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git fetch - ダウンロードのみ',
                        'content' => "# リモートの変更を取得（マージはしない）\ngit fetch origin\n\n# 特定のブランチを取得\ngit fetch origin main\n\n# 全てのリモートから取得\ngit fetch --all\n\n# 削除されたリモートブランチを反映\ngit fetch --prune\ngit fetch -p\n\n# fetchとpullの違い\n# git pull = git fetch + git merge\n\n# 安全な更新フロー\n# 1. まず取得だけ\ngit fetch origin main\n\n# 2. 差分を確認\ngit diff main origin/main\ngit log main..origin/main\n\n# 3. 問題なければマージ\ngit merge origin/main\n\n# または、最初からpull\ngit pull origin main",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'PullとFetchの違い',
                        'content' => "# PullとFetchの違い\n\n## git fetch\n- リモートの変更を**ダウンロードのみ**\n- ローカルブランチは変更されない\n- 安全に差分を確認できる\n\n```bash\ngit fetch origin\ngit diff main origin/main  # 差分確認\ngit merge origin/main      # 手動でマージ\n```\n\n## git pull\n- リモートの変更を**ダウンロード + マージ**\n- `git fetch` + `git merge` の組み合わせ\n- 自動的にマージされる\n\n```bash\ngit pull origin main\n# = git fetch origin main + git merge origin/main\n```\n\n## どちらを使うべき？\n- **初心者**: `git pull` でOK\n- **慎重派**: `git fetch` → 確認 → `git merge`\n- **チーム開発**: `git pull --rebase` で履歴をきれいに保つ\n\n## pull --rebase\n```bash\ngit pull --rebase origin main\n```\nマージコミットを作らず、履歴を一直線に保つ。",
                        'sort_order' => 4
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：プルリクエストとコードレビュー',
                'description' => 'Pull Requestの作成、レビュー、マージ、Issue管理',
                'sort_order' => 6,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['GitHub Pull Requests', 'GitHub Issues'],
                'subtasks' => [
                    ['title' => 'Pull Requestを作成', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'コードレビューを学習', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'Issueを作成・管理', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Pull Requestとは？',
                        'content' => "# Pull Request（プルリクエスト）とは？\n\n**Pull Request（PR）**は、あなたの変更を他の人にレビューしてもらい、マージしてもらうための依頼です。\n\n## PRのメリット\n1. **コードレビュー**: バグやベストプラクティス違反を発見\n2. **知識共有**: チーム全体がコードを理解\n3. **品質向上**: 複数人の目でチェック\n4. **議論の場**: 実装方法について議論\n\n## PRのワークフロー\n```\n1. ブランチを作成\n   git checkout -b feature/new-feature\n\n2. 開発 & コミット\n   git add .\n   git commit -m \"Add new feature\"\n\n3. プッシュ\n   git push origin feature/new-feature\n\n4. GitHubでPRを作成\n   - タイトルと説明を記入\n   - レビュアーを指定\n\n5. レビュー & 修正\n   - フィードバックに対応\n   - 追加コミット\n\n6. マージ\n   - レビュー承認後にマージ\n\n7. ブランチ削除\n   git branch -d feature/new-feature\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => '良いPRの書き方',
                        'content' => "# 良いPull Requestの書き方\n\n## タイトル\n- 簡潔で内容が分かる\n- 例: `Add user authentication feature`\n- 例: `Fix login redirect bug #123`\n\n## 説明（Description）\n```markdown\n## 概要\nユーザー認証機能を追加しました。\n\n## 変更内容\n- ログインフォームの実装\n- JWT認証の追加\n- 認証ミドルウェアの作成\n- バリデーションの追加\n\n## テスト\n- [ ] ログイン成功ケース\n- [ ] ログイン失敗ケース\n- [ ] トークンの有効期限\n\n## スクリーンショット\n（UIの変更がある場合）\n\n## 関連Issue\nFixes #123\nCloses #124\n```\n\n## PRのサイズ\n- **小さいほど良い**: レビューしやすい\n- **1つの機能/修正につき1PR**\n- 500行以下が理想\n\n## セルフレビュー\nPRを作成する前に、自分でコードを見直す。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'PR作成の流れ',
                        'content' => "# 1. 新しいブランチを作成\ngit checkout -b feature/user-profile\n\n# 2. 開発作業\n# ... コードを書く ...\n\n# 3. コミット\ngit add .\ngit commit -m \"feat: add user profile page\"\n\n# 4. リモートにプッシュ\ngit push -u origin feature/user-profile\n\n# 5. GitHub上でPRを作成\n# https://github.com/username/repo/pull/new/feature/user-profile\n# または、ブラウザに表示されるリンクをクリック\n\n# 6. レビューコメントに対応する場合\n# ... 修正 ...\ngit add .\ngit commit -m \"refactor: improve validation logic\"\ngit push\n\n# 7. マージ後、ローカルブランチを削除\ngit checkout main\ngit pull\ngit branch -d feature/user-profile\n\n# 8. リモートブランチも削除（GitHubで自動削除設定も可能）\ngit push origin --delete feature/user-profile",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'GitHub Issues',
                        'content' => "# GitHub Issues\n\n**Issue**は、バグ報告、機能要望、タスク管理に使います。\n\n## Issueの種類\n1. **Bug Report**: バグの報告\n2. **Feature Request**: 新機能の提案\n3. **Question**: 質問\n4. **Task**: タスク管理\n\n## Issueテンプレート例\n```markdown\n## バグの説明\nログインボタンを押しても反応しない\n\n## 再現手順\n1. ログインページを開く\n2. メールアドレスとパスワードを入力\n3. ログインボタンをクリック\n\n## 期待される動作\nダッシュボードにリダイレクトされる\n\n## 実際の動作\n何も起こらない\n\n## 環境\n- OS: Windows 11\n- ブラウザ: Chrome 120\n```\n\n## IssueとPRの連携\n```bash\n# コミットメッセージでIssueを参照\ngit commit -m \"Fix login button, refs #123\"\n\n# PRでIssueをクローズ\n# PR説明に以下を記載\nFixes #123\nCloses #124\nResolves #125\n```",
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 3: 高度なGit操作 (第7週～第8週)
        $milestone3 = $template->milestones()->create([
            'title' => '高度なGit操作',
            'description' => 'Rebase、Cherry-pick、Stash、Reset、Revert、タグ管理',
            'sort_order' => 3,
            'estimated_hours' => 16,
            'deliverables' => [
                'Rebaseを理解・使用',
                'Stashで作業を一時保存',
                'コミット履歴を修正',
                'タグでバージョン管理'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 7
            [
                'title' => '第7週：Rebase、Stash、Reset',
                'description' => 'コミット履歴の整理、一時保存、コミットの取り消し',
                'sort_order' => 7,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['Git Rebase Guide', 'Git Reset vs Revert'],
                'subtasks' => [
                    ['title' => 'git rebaseを学習', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'git stashを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'git reset/revertを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'git rebase - 履歴を整理',
                        'content' => "# 基本的なrebase\ngit checkout feature\ngit rebase main\n\n# インタラクティブrebase（過去のコミットを編集）\ngit rebase -i HEAD~3  # 最新3件を編集\n\n# rebase中に使えるコマンド:\n# pick: コミットをそのまま使う\n# reword: コミットメッセージを変更\n# edit: コミットを編集\n# squash: 前のコミットと統合\n# fixup: 前のコミットと統合（メッセージは破棄）\n# drop: コミットを削除\n\n# 例: 複数のコミットを1つにまとめる\ngit rebase -i HEAD~4\n# pick abc123 Add feature A\n# squash def456 Fix typo\n# squash ghi789 Update tests\n# squash jkl012 Fix bug\n\n# rebaseの中止\ngit rebase --abort\n\n# rebaseの続行（コンフリクト解消後）\ngit add .\ngit rebase --continue\n\n# rebaseのスキップ\ngit rebase --skip",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'MergeとRebaseの違い',
                        'content' => "# MergeとRebaseの違い\n\n## git merge\n```\nmain:     A---B---C---F (merge commit)\n               \\       /\nfeature:        D---E\n```\n- マージコミットを作成\n- 履歴が分岐する\n- 全ての履歴が保存される\n- **安全**で分かりやすい\n\n## git rebase\n```\nmain:     A---B---C\nfeature:              D'---E'\n```\n- 履歴を一直線にする\n- きれいな履歴\n- コミットが書き換えられる\n- **履歴がシンプル**\n\n## どちらを使う？\n- **マージ**: 公開済みブランチ、チーム開発\n- **リベース**: 自分だけのブランチ、履歴をきれいに保ちたい時\n\n## ⚠️ 注意点\n**プッシュ済みコミットはrebaseしない！**\nチームメンバーが混乱します。",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git stash - 一時保存',
                        'content' => "# 現在の作業を一時保存\ngit stash\ngit stash save \"Work in progress on feature X\"\n\n# untracked filesも含める\ngit stash -u\ngit stash --include-untracked\n\n# stashリストを表示\ngit stash list\n# stash@{0}: WIP on main: abc123 Latest commit\n# stash@{1}: On feature: def456 Old work\n\n# stashを適用（削除しない）\ngit stash apply\ngit stash apply stash@{1}\n\n# stashを適用して削除\ngit stash pop\ngit stash pop stash@{0}\n\n# stashの内容を確認\ngit stash show\ngit stash show -p stash@{0}\n\n# stashを削除\ngit stash drop stash@{0}\ngit stash clear  # 全て削除\n\n# 実用例:\n# 1. 作業中に緊急のバグ修正が必要になった\ngit stash  # 現在の作業を保存\ngit checkout -b hotfix/critical-bug\n# ... バグ修正 ...\ngit checkout feature\ngit stash pop  # 作業を再開",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git reset vs git revert',
                        'content' => "# git reset - コミットを取り消す（履歴改変）\n# --soft: コミットだけ取り消し（変更はステージングに残る）\ngit reset --soft HEAD~1\n\n# --mixed（デフォルト）: コミット + ステージング取り消し\ngit reset HEAD~1\ngit reset --mixed HEAD~1\n\n# --hard: 全て取り消す（危険！）\ngit reset --hard HEAD~1\n\n# 特定のコミットまで戻る\ngit reset --hard abc123\n\n# git revert - 打ち消しコミットを作成（履歴維持）\ngit revert HEAD  # 最新のコミットを打ち消す\ngit revert abc123  # 特定のコミットを打ち消す\n\n# 複数のコミットをrevert\ngit revert HEAD~3..HEAD\n\n# reset vs revert の使い分け\n# - プッシュ前: reset でOK\n# - プッシュ済み: revert を使う（安全）\n\n# ファイル単位でreset\ngit reset HEAD file.txt  # ステージングから外す\ngit checkout -- file.txt  # 変更を破棄",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                ],
            ],
            // Week 8
            [
                'title' => '第8週：タグ、Cherry-pick、高度なテクニック',
                'description' => 'バージョンタグ管理、特定コミットの取り込み、便利なコマンド',
                'sort_order' => 8,
                'estimated_minutes' => 330,
                'priority' => 4,
                'resources' => ['Git Tag', 'Git Tips and Tricks'],
                'subtasks' => [
                    ['title' => 'タグを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'git cherry-pickを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => '便利なGitコマンドを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'git tag - バージョン管理',
                        'content' => "# 軽量タグ（lightweight tag）\ngit tag v1.0.0\ngit tag v1.1.0\n\n# 注釈付きタグ（annotated tag）- 推奨\ngit tag -a v1.0.0 -m \"Version 1.0.0 release\"\ngit tag -a v2.0.0 -m \"Major update with breaking changes\"\n\n# タグ一覧\ngit tag\ngit tag -l \"v1.*\"  # パターン検索\n\n# タグの詳細表示\ngit show v1.0.0\n\n# 過去のコミットにタグを付ける\ngit tag -a v0.9.0 abc123 -m \"Beta release\"\n\n# タグをプッシュ\ngit push origin v1.0.0  # 特定のタグ\ngit push origin --tags  # 全てのタグ\n\n# タグをチェックアウト\ngit checkout v1.0.0\n\n# タグを削除\ngit tag -d v1.0.0  # ローカル\ngit push origin --delete v1.0.0  # リモート\n\n# セマンティックバージョニング\n# v1.2.3\n#  | | └─ Patch: バグ修正\n#  | └─── Minor: 機能追加（後方互換）\n#  └───── Major: 破壊的変更",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'git cherry-pick',
                        'content' => "# 特定のコミットだけを取り込む\ngit cherry-pick abc123\n\n# 複数のコミット\ngit cherry-pick abc123 def456 ghi789\n\n# コミット範囲\ngit cherry-pick abc123..def456\n\n# cherry-pick中にコンフリクトが発生\ngit cherry-pick abc123\n# ... コンフリクト解消 ...\ngit add .\ngit cherry-pick --continue\n\n# cherry-pickを中止\ngit cherry-pick --abort\n\n# 実用例:\n# hotfixブランチで修正したバグを、developにも適用\ngit checkout develop\ngit cherry-pick abc123  # hotfixのコミットを取り込む",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '便利なGitコマンド',
                        'content' => "# git blame - 各行の最終編集者を表示\ngit blame file.txt\ngit blame -L 10,20 file.txt  # 10-20行目のみ\n\n# git diff - 差分表示\ngit diff  # 作業ディレクトリ vs ステージング\ngit diff --staged  # ステージング vs リポジトリ\ngit diff HEAD  # 作業ディレクトリ vs 最新コミット\ngit diff main..feature  # ブランチ間の差分\ngit diff abc123 def456  # コミット間の差分\ngit diff --stat  # 統計情報のみ\n\n# git show - コミット内容を表示\ngit show abc123\ngit show HEAD~2\ngit show v1.0.0\n\n# git reflog - 全ての操作履歴\ngit reflog\ngit reflog show HEAD\n\n# 間違えてresetした場合の復旧\ngit reflog\ngit reset --hard abc123  # reflogから見つけたコミットに戻る\n\n# git clean - 未追跡ファイルを削除\ngit clean -n  # 削除されるファイルを確認（dry run）\ngit clean -f  # 削除実行\ngit clean -fd  # ディレクトリも削除\n\n# git bisect - バグが入ったコミットを特定\ngit bisect start\ngit bisect bad  # 現在はバグあり\ngit bisect good abc123  # このコミットはOKだった\n# ... 二分探索で自動的に絞り込む ...\ngit bisect reset  # 終了",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 4: チーム開発とワークフロー (第9週～第10週)
        $milestone4 = $template->milestones()->create([
            'title' => 'チーム開発とワークフロー',
            'description' => 'Git Flow、GitHub Flow、コラボレーション、.gitignore、GitHub Actions基礎',
            'sort_order' => 4,
            'estimated_hours' => 16,
            'deliverables' => [
                'Git Flowを理解',
                'チーム開発のベストプラクティス',
                '.gitignoreを設定',
                'GitHub Actionsで自動化'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 9
            [
                'title' => '第9週：ブランチ戦略とチーム開発',
                'description' => 'Git Flow、GitHub Flow、フォーク、コラボレーター',
                'sort_order' => 9,
                'estimated_minutes' => 330,
                'priority' => 5,
                'resources' => ['Git Flow', 'GitHub Flow Guide'],
                'subtasks' => [
                    ['title' => 'Git Flowを学習', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'GitHub Flowを学習', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'フォークとコラボレーションを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Git Flow',
                        'content' => "# Git Flow\n\nGit Flowは、Vincent Driessenが提唱したブランチモデルです。\n\n## ブランチの種類\n\n### 永続ブランチ\n1. **main（master）**: 本番環境用\n2. **develop**: 開発用のメインブランチ\n\n### 一時ブランチ\n3. **feature/xxx**: 新機能開発\n4. **release/x.x.x**: リリース準備\n5. **hotfix/xxx**: 緊急バグ修正\n\n## ワークフロー\n\n```\nmain ─────●─────────────●─────────●──\n          │             ↑         ↑\n          │        release/1.1  hotfix/bug\n          ↓             │         │\ndevelop ──●─────●───●───●─────────●──\n              ↗   ↗   ↘\n        feature/ feature/ feature/\n          A        B        C\n```\n\n## 使い方\n\n### 新機能開発\n```bash\n# developから分岐\ngit checkout develop\ngit checkout -b feature/user-auth\n# 開発...\ngit checkout develop\ngit merge --no-ff feature/user-auth\ngit branch -d feature/user-auth\n```\n\n### リリース\n```bash\ngit checkout -b release/1.0.0 develop\n# バグ修正、バージョン番号更新\ngit checkout main\ngit merge --no-ff release/1.0.0\ngit tag -a v1.0.0\ngit checkout develop\ngit merge --no-ff release/1.0.0\n```\n\n### Hotfix\n```bash\ngit checkout -b hotfix/critical-bug main\n# 修正...\ngit checkout main\ngit merge --no-ff hotfix/critical-bug\ngit tag -a v1.0.1\ngit checkout develop\ngit merge --no-ff hotfix/critical-bug\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'GitHub Flow',
                        'content' => "# GitHub Flow\n\nGitHubが推奨するシンプルなワークフロー。\n\n## 特徴\n- **1つのメインブランチ**: main のみ\n- **継続的デプロイ**: 頻繁にデプロイ\n- **シンプル**: Git Flowより簡単\n\n## ワークフロー\n\n```\nmain ──●─────────●─────────●─────────●──\n        ↘       ↗  ↘      ↗  ↘       ↗\n      feature/A  feature/B  feature/C\n```\n\n## 手順\n\n### 1. ブランチを作成\n```bash\ngit checkout -b feature/new-button\n```\n\n### 2. コミットを追加\n```bash\ngit add .\ngit commit -m \"Add new button\"\ngit push origin feature/new-button\n```\n\n### 3. Pull Requestを作成\nGitHub上でPRを作成\n\n### 4. レビュー & 議論\nチームメンバーがレビュー\n\n### 5. デプロイしてテスト\nステージング環境でテスト\n\n### 6. mainにマージ\nPRをマージ → 自動デプロイ\n\n## Git FlowとGitHub Flowの比較\n\n| | Git Flow | GitHub Flow |\n|---|---|---|\n| **複雑さ** | 複雑 | シンプル |\n| **ブランチ** | 5種類 | 2種類 |\n| **向いてる** | 定期リリース | 継続的デプロイ |\n| **チーム** | 大規模 | 小〜中規模 |\n| **リリース** | バージョン管理 | 常に最新 |",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'フォークとコラボレーション',
                        'content' => "# フォークとコラボレーション\n\n## コラボレーターモデル\nリポジトリに直接アクセス権を持つ\n\n```bash\n# 1. クローン\ngit clone git@github.com:team/project.git\n\n# 2. ブランチ作成\ngit checkout -b feature/my-work\n\n# 3. プッシュ\ngit push origin feature/my-work\n\n# 4. PRを作成\n```\n\n## フォークモデル（オープンソース）\n自分のアカウントにコピーして作業\n\n### 手順\n\n1. **GitHubでFork**\n   - 元のリポジトリで「Fork」ボタンをクリック\n\n2. **クローン**\n```bash\ngit clone git@github.com:myname/project.git\ncd project\n```\n\n3. **upstreamを追加**\n```bash\ngit remote add upstream git@github.com:original/project.git\ngit remote -v\n# origin: 自分のフォーク\n# upstream: 元のリポジトリ\n```\n\n4. **ブランチ作成 & 作業**\n```bash\ngit checkout -b feature/my-contribution\n# ... 開発 ...\ngit push origin feature/my-contribution\n```\n\n5. **PRを作成**\n   - 自分のフォークから元のリポジトリへPR\n\n6. **upstreamの変更を取り込む**\n```bash\ngit fetch upstream\ngit checkout main\ngit merge upstream/main\ngit push origin main\n```\n\n## どちらを使う？\n- **コラボレーター**: プライベート、チーム開発\n- **フォーク**: オープンソース、外部貢献者",
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 10
            [
                'title' => '第10週：.gitignore、GitHub Actions、ベストプラクティス',
                'description' => '無視ファイル設定、CI/CD自動化、Gitのベストプラクティス',
                'sort_order' => 10,
                'estimated_minutes' => 360,
                'priority' => 4,
                'resources' => ['gitignore.io', 'GitHub Actions Documentation'],
                'subtasks' => [
                    ['title' => '.gitignoreを学習', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'GitHub Actionsの基礎を学習', 'estimated_minutes' => 150, 'sort_order' => 2],
                    ['title' => 'Gitのベストプラクティスを学習', 'estimated_minutes' => 120, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => '.gitignore',
                        'content' => "# .gitignore - 無視するファイルを指定\n\n# OS固有ファイル\n.DS_Store        # macOS\nThumbs.db        # Windows\n\n# エディタ\n.vscode/\n.idea/\n*.swp\n*.swo\n\n# 依存関係\nnode_modules/\nvendor/\n__pycache__/\n*.pyc\n\n# ビルド成果物\nbuild/\ndist/\n*.exe\n*.o\n*.class\n\n# 環境変数・設定ファイル\n.env\n.env.local\nconfig.local.js\n\n# ログファイル\n*.log\nlogs/\n\n# データベース\n*.sqlite\n*.db\n\n# パターン\n# 全てのtxtファイル\n*.txt\n\n# 例外: README.txtは追跡\n!README.txt\n\n# ディレクトリ全体\ntemp/\n\n# ネストされたパターン\n**/node_modules/\n\n# グローバル.gitignoreの設定\ngit config --global core.excludesfile ~/.gitignore_global\n\n# .gitignoreジェネレーター\n# https://gitignore.io",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'GitHub Actions基礎',
                        'content' => "# .github/workflows/ci.yml\nname: CI\n\non:\n  push:\n    branches: [ main, develop ]\n  pull_request:\n    branches: [ main ]\n\njobs:\n  test:\n    runs-on: ubuntu-latest\n    \n    steps:\n    - uses: actions/checkout@v3\n    \n    - name: Setup Node.js\n      uses: actions/setup-node@v3\n      with:\n        node-version: '18'\n    \n    - name: Install dependencies\n      run: npm install\n    \n    - name: Run tests\n      run: npm test\n    \n    - name: Run linter\n      run: npm run lint\n\n  build:\n    runs-on: ubuntu-latest\n    needs: test\n    \n    steps:\n    - uses: actions/checkout@v3\n    \n    - name: Build\n      run: npm run build\n    \n    - name: Upload artifact\n      uses: actions/upload-artifact@v3\n      with:\n        name: build-files\n        path: dist/",
                        'code_language' => 'yaml',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Gitのベストプラクティス',
                        'content' => "# Gitのベストプラクティス\n\n## コミット\n1. **小さく頻繁に**: 1つの機能・修正ごとにコミット\n2. **意味のある単位**: 関連する変更をまとめる\n3. **良いメッセージ**: 何を、なぜ変更したか明確に\n4. **動くコードをコミット**: ビルドエラーは避ける\n\n## ブランチ\n1. **main/developは保護**: 直接pushしない\n2. **機能ごとにブランチ**: feature/xxx\n3. **短命に**: 長期間放置しない\n4. **命名規則**: 一貫性を保つ\n\n## プルリクエスト\n1. **小さいPR**: レビューしやすいサイズ\n2. **説明を詳しく**: 背景、変更内容、テスト方法\n3. **セルフレビュー**: 自分で先にチェック\n4. **CIを通す**: テストが成功してから\n\n## コードレビュー\n1. **建設的に**: 改善提案を明確に\n2. **迅速に**: 24時間以内に\n3. **質問する**: 理解できない箇所は聞く\n4. **承認基準**: 明確にする\n\n## やってはいけないこと\n1. ❌ プッシュ済みコミットをrebase\n2. ❌ mainブランチに直接push\n3. ❌ 大きすぎるコミット\n4. ❌ 意味のないコミットメッセージ（「fix」「update」のみ）\n5. ❌ パスワードや秘密鍵をコミット\n6. ❌ `git push -f` を本番ブランチで使う\n\n## 便利なエイリアス\n```bash\ngit config --global alias.st status\ngit config --global alias.co checkout\ngit config --global alias.br branch\ngit config --global alias.ci commit\ngit config --global alias.unstage 'reset HEAD --'\ngit config --global alias.last 'log -1 HEAD'\ngit config --global alias.lg \"log --graph --oneline --all\"\n```",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "Git/GitHub Course Seeder completed successfully!\n";
    }
}
