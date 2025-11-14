<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use Illuminate\Database\Seeder;

class DockerCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Docker基礎コース - 12週間の完全コース
     */
    public function run(): void
    {
        $template = LearningPathTemplate::create([
            'title' => 'Docker実践マスターコース',
            'description' => 'WSL2 + Docker Desktop環境で学ぶ、初心者から実践まで完全対応のDockerコース。コンテナ化からCI/CD、セキュリティ、監視まで12週間で習得します。',
            'category' => 'devops',
            'difficulty' => 'beginner',
            'estimated_hours_total' => 96,
            'tags' => ['docker', 'container', 'devops', 'microservices', 'ci/cd', 'kubernetes'],
            'icon' => 'ic_docker',
            'color' => '#2496ED',
            'is_featured' => true,
        ]);

        // Milestone 1: 基礎（第1週～第2週）
        $milestone1 = $template->milestones()->create([
            'title' => 'Docker基礎',
            'description' => '環境構築からDockerの基本概念、Dockerfile作成、基本コマンドまで',
            'sort_order' => 1,
            'estimated_hours' => 16,
            'deliverables' => [
                'WSL2 + Docker Desktop環境構築完了',
                'Hello Worldコンテナ実行',
                'Dockerfileを作成して独自イメージをビルド',
                '基本コマンドを習得'
            ],
        ]);

        $milestone1->tasks()->createMany([
            // Week 1
            [
                'title' => '第1週：環境構築とDocker入門',
                'description' => 'WSL2 + Docker Desktopのセットアップ、Dockerの基本概念、アーキテクチャ',
                'sort_order' => 1,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Docker公式ドキュメント', 'WSL2設定ガイド'],
                'subtasks' => [
                    ['title' => 'WSL2を有効化', 'estimated_minutes' => 30, 'sort_order' => 1],
                    ['title' => 'Docker Desktopをインストール', 'estimated_minutes' => 30, 'sort_order' => 2],
                    ['title' => 'BuildKitを有効化', 'estimated_minutes' => 15, 'sort_order' => 3],
                    ['title' => 'Hello Worldコンテナを実行', 'estimated_minutes' => 30, 'sort_order' => 4],
                    ['title' => 'Nginx Webサーバーを起動', 'estimated_minutes' => 45, 'sort_order' => 5],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Dockerとは？',
                        'content' => "# Dockerとは？\n\n**Docker**は、アプリケーションをコンテナ化して実行するためのプラットフォームです。\n\n## Dockerの特徴\n\n1. **環境の一貫性**: Dev→Prod環境で同じように動作\n2. **軽量**: VMより高速起動、リソース効率的\n3. **ポータビリティ**: どこでも同じように動作\n4. **スケーラビリティ**: 簡単にスケールアウト可能\n5. **分離性**: アプリケーションを独立して実行\n\n## Container vs VM\n\n| 特徴 | Container | VM |\n|------|-----------|----|\n| 起動時間 | 秒単位 | 分単位 |\n| サイズ | MB単位 | GB単位 |\n| OS | ホストOSを共有 | 独立したOS |\n| オーバーヘッド | 低い | 高い |\n\n## Dockerの用途\n\n- マイクロサービスアーキテクチャ\n- CI/CDパイプライン\n- 開発環境の統一\n- クラウドネイティブアプリケーション",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'WSL2 + Docker Desktop環境構築',
                        'content' => "# WSL2 + Docker Desktop環境構築\n\n## 1. WSL2の有効化\n\nPowerShell（管理者権限）で実行：\n```powershell\nwsl --install\n```\n\n## 2. Docker Desktopのインストール\n\n- Docker公式サイトからダウンロード\n- Settings → General → Use WSL 2 based engine にチェック\n\n## 3. BuildKitの有効化\n\nSettings → Docker Engine で以下を追加：\n```json\n{\n  \"features\": {\n    \"buildkit\": true\n  }\n}\n```\n\nまたは環境変数で設定：\n```bash\nexport DOCKER_BUILDKIT=1\n```\n\n## 4. プロジェクトの配置\n\nWSL filesystem内に配置（高速I/O）：\n```bash\n/home/<username>/projects/...\n```\n\nWindows filesystem（遅い）を避ける：\n```bash\n/mnt/c/Users/...\n```\n\n## 5. .dockerignoreファイル作成\n\n```\nnode_modules\n.git\n.env\nDockerfile*\ndocker-compose*.yml\nlogs\ndist\ncoverage\n**/__pycache__\n**/.pytest_cache\n```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Hello World実行',
                        'content' => "# Hello Worldコンテナの実行\ndocker run hello-world\n\n# イメージ一覧を確認\ndocker images\n\n# 実行中のコンテナを確認\ndocker ps\n\n# すべてのコンテナを確認（停止中も含む）\ndocker ps -a\n\n# コンテナの削除\ndocker rm <container_id>\n\n# イメージの削除\ndocker rmi hello-world",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Nginx Webサーバーの起動',
                        'content' => "# Nginxコンテナをバックグラウンドで起動\ndocker run -d -p 8080:80 --name my-nginx nginx\n\n# ブラウザで http://localhost:8080 にアクセス\n\n# ログを確認\ndocker logs my-nginx\n\n# リアルタイムでログを表示\ndocker logs -f my-nginx\n\n# コンテナの停止\ndocker stop my-nginx\n\n# コンテナの再起動\ndocker restart my-nginx\n\n# コンテナの削除（停止後）\ndocker rm my-nginx",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：Dockerfile作成と基本コマンド',
                'description' => '初めてのDockerfile作成、イメージビルド、コンテナ操作の基本コマンド',
                'sort_order' => 2,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Dockerfile Best Practices', 'Docker CLI Reference'],
                'subtasks' => [
                    ['title' => 'Node.js Dockerfileを作成', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'イメージをビルド', 'estimated_minutes' => 60, 'sort_order' => 2],
                    ['title' => 'Non-rootユーザーで実行', 'estimated_minutes' => 60, 'sort_order' => 3],
                    ['title' => '基本コマンドを練習', 'estimated_minutes' => 90, 'sort_order' => 4],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Node.js Dockerfile（Non-root + Alpine）',
                        'content' => "# Dockerfile\nFROM node:20-alpine\n\n# 作業ディレクトリを設定\nWORKDIR /app\n\n# package.jsonをコピー\nCOPY package*.json ./\n\n# 本番用依存関係のみインストール\nRUN npm ci --omit=dev\n\n# ソースコードをコピー\nCOPY . .\n\n# Non-rootユーザーを作成\nRUN addgroup -g 1001 -S nodejs && adduser -S app -u 1001\n\n# ユーザーを切り替え\nUSER app\n\n# ポートを公開\nEXPOSE 3000\n\n# アプリケーションを起動\nCMD [\"npm\", \"start\"]",
                        'code_language' => 'dockerfile',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'package.jsonとserver.js',
                        'content' => "// package.json\n{\n  \"name\": \"docker-app\",\n  \"version\": \"1.0.0\",\n  \"scripts\": {\n    \"start\": \"node server.js\"\n  },\n  \"dependencies\": {\n    \"express\": \"^4.19.2\"\n  }\n}\n\n// server.js\nconst express = require('express');\nconst app = express();\n\napp.get('/', (req, res) => {\n  res.send('Hello from Docker!');\n});\n\napp.get('/health', (req, res) => {\n  res.status(200).json({ status: 'OK' });\n});\n\napp.listen(3000, () => {\n  console.log('Server running on port 3000');\n});",
                        'code_language' => 'javascript',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'イメージのビルドと実行',
                        'content' => "# イメージをビルド\ndocker build -t my-node-app .\n\n# コンテナを起動\ndocker run -d -p 3000:3000 --name app my-node-app\n\n# ブラウザで http://localhost:3000 にアクセス\n\n# コンテナ内でコマンド実行\ndocker exec -it app sh\n\n# ログを確認\ndocker logs -f app\n\n# コンテナの詳細情報\ndocker inspect app\n\n# リソース使用状況\ndocker stats app",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Docker基本コマンド一覧',
                        'content' => "# Docker基本コマンド一覧\n\n## イメージ操作\n\n```bash\n# イメージ一覧\ndocker images\n\n# イメージをビルド\ndocker build -t <name>:<tag> .\n\n# イメージを削除\ndocker rmi <image_id>\n\n# 未使用イメージを削除\ndocker image prune\n```\n\n## コンテナ操作\n\n```bash\n# コンテナを起動\ndocker run -d -p 8080:80 --name <name> <image>\n\n# コンテナ一覧\ndocker ps        # 実行中\ndocker ps -a     # すべて\n\n# コンテナを停止\ndocker stop <container>\n\n# コンテナを削除\ndocker rm <container>\n\n# コンテナ内でコマンド実行\ndocker exec -it <container> sh\n```\n\n## ログとデバッグ\n\n```bash\n# ログを表示\ndocker logs <container>\ndocker logs -f <container>  # リアルタイム\n\n# コンテナの詳細\ndocker inspect <container>\n\n# リソース使用状況\ndocker stats\n```\n\n## クリーンアップ\n\n```bash\n# すべて削除\ndocker system prune -a\n\n# ボリューム含めて削除\ndocker system prune -a --volumes\n```",
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 2: 中級（第3週～第4週）
        $milestone2 = $template->milestones()->create([
            'title' => 'Docker中級',
            'description' => 'Volumes、Networks、Docker Composeを使った複数コンテナ管理',
            'sort_order' => 2,
            'estimated_hours' => 16,
            'deliverables' => [
                'Volumesでデータ永続化',
                'Custom Networkでコンテナ間通信',
                'Docker Composeで2-tier構成',
                '環境変数とSecretsの管理'
            ],
        ]);

        $milestone2->tasks()->createMany([
            // Week 3
            [
                'title' => '第3週：Volumes & Networks',
                'description' => 'データ永続化とコンテナ間ネットワーク通信',
                'sort_order' => 3,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Docker Volumes', 'Docker Networks'],
                'subtasks' => [
                    ['title' => 'Named Volumesを使う', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Bind Mountsを使う', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'Custom Networkを作成', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Volumes vs Bind Mounts',
                        'content' => "# Volumes vs Bind Mounts\n\n## Named Volumes（推奨）\n\n**特徴:**\n- Dockerが管理\n- ポータビリティが高い\n- バックアップが簡単\n- プロダクション向け\n\n**使用例:**\n```bash\ndocker run -v mydata:/data postgres\n```\n\n## Bind Mounts\n\n**特徴:**\n- ホストのパスを直接マウント\n- 開発時に便利（ホットリロード）\n- Windows/WSL2ではパス指定に注意\n\n**使用例:**\n```bash\n# PowerShell\ndocker run -v \\x24{PWD}:/app node\n\n# Git Bash/WSL\ndocker run -v \\x24(pwd):/app node\n\n# cmd\ndocker run -v %cd%:/app node\n```\n\n## いつどちらを使うか？\n\n| 用途 | 推奨 |\n|------|------|\n| データベース | Named Volumes |\n| 開発（ホットリロード） | Bind Mounts |\n| プロダクション | Named Volumes |\n| 設定ファイル | Bind Mounts |",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'PostgreSQL with Volume',
                        'content' => "# Named Volumeを作成\ndocker volume create pg_data\n\n# PostgreSQLコンテナを起動\ndocker run -d \\\n  --name postgres \\\n  -e POSTGRES_PASSWORD=password \\\n  -e POSTGRES_DB=mydb \\\n  -v pg_data:/var/lib/postgresql/data \\\n  -p 5432:5432 \\\n  postgres:13\n\n# データが永続化されていることを確認\ndocker exec -it postgres psql -U postgres -d mydb\n\n# コンテナを削除してもデータは残る\ndocker rm -f postgres\n\n# 同じVolumeで再起動すればデータが復元される\ndocker run -d \\\n  --name postgres \\\n  -e POSTGRES_PASSWORD=password \\\n  -v pg_data:/var/lib/postgresql/data \\\n  postgres:13",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Docker Networks',
                        'content' => "# Docker Networks\n\n## ネットワークタイプ\n\n1. **bridge**（デフォルト）: 同一ホスト内のコンテナ通信\n2. **host**: ホストのネットワークを直接使用\n3. **none**: ネットワーク無効\n4. **custom bridge**: カスタムネットワーク（推奨）\n\n## Custom Network の利点\n\n- **DNS解決**: コンテナ名で通信可能\n- **分離**: ネットワークを分けてセキュリティ向上\n- **柔軟性**: 必要なコンテナだけ接続\n\n## 使用例\n\n```bash\n# カスタムネットワークを作成\ndocker network create mynet\n\n# コンテナをネットワークに接続して起動\ndocker run -d --name web --network mynet nginx\ndocker run -d --name db --network mynet postgres\n\n# webからdbへpingできる（DNS解決）\ndocker exec -it web ping db\n```",
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Custom Network実践',
                        'content' => "# ネットワークを作成\ndocker network create app-network\n\n# PostgreSQLを起動\ndocker run -d \\\n  --name db \\\n  --network app-network \\\n  -e POSTGRES_PASSWORD=password \\\n  postgres:13\n\n# Node.jsアプリを起動（dbに接続）\ndocker run -d \\\n  --name web \\\n  --network app-network \\\n  -p 3000:3000 \\\n  -e DB_HOST=db \\\n  -e DB_USER=postgres \\\n  -e DB_PASSWORD=password \\\n  my-node-app\n\n# ネットワーク情報を確認\ndocker network inspect app-network\n\n# コンテナ間の通信をテスト\ndocker exec -it web ping db",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                ],
            ],
            // Week 4
            [
                'title' => '第4週：Docker Compose',
                'description' => 'Compose v2で複数コンテナを管理、環境変数とProfiles',
                'sort_order' => 4,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Docker Compose Specification'],
                'subtasks' => [
                    ['title' => 'compose.ymlを作成', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => '2-tier構成を構築', 'estimated_minutes' => 120, 'sort_order' => 2],
                    ['title' => 'Networksを分離', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'note',
                        'title' => 'Docker Compose v2の特徴',
                        'content' => "# Docker Compose v2の特徴\n\n## Compose v2の変更点\n\n- **コマンド**: `docker compose`（ハイフンなし）\n- **version不要**: `version:` フィールドは不要\n- **BuildKit統合**: デフォルトでBuildKit使用\n- **profiles**: サービスのグループ化\n\n## 基本コマンド\n\n```bash\n# 起動\ndocker compose up -d\n\n# 停止・削除\ndocker compose down\n\n# ログ表示\ndocker compose logs -f\n\n# サービス一覧\ndocker compose ps\n\n# 再ビルド\ndocker compose build\n\n# 特定サービスのみ起動\ndocker compose up -d web\n\n# Profileを指定して起動\ndocker compose --profile monitoring up -d\n```",
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'compose.yml（2-tier構成）',
                        'content' => "# compose.yml\nname: my-app\n\nnetworks:\n  frontend:\n  backend:\n\nservices:\n  web:\n    build: .\n    ports:\n      - \"3000:3000\"\n    environment:\n      - DB_HOST=db\n      - DB_USER=postgres\n      - DB_PASSWORD=password\n      - DB_NAME=mydb\n    depends_on:\n      - db\n    networks:\n      - frontend\n      - backend\n    restart: unless-stopped\n\n  db:\n    image: postgres:13\n    environment:\n      - POSTGRES_USER=postgres\n      - POSTGRES_PASSWORD=password\n      - POSTGRES_DB=mydb\n    volumes:\n      - pg_data:/var/lib/postgresql/data\n    networks:\n      - backend\n    restart: unless-stopped\n\nvolumes:\n  pg_data:",
                        'code_language' => 'yaml',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => '環境変数ファイル（.env）',
                        'content' => "# .env\nPOSTGRES_USER=postgres\nPOSTGRES_PASSWORD=password\nPOSTGRES_DB=mydb\nNODE_ENV=development\n\n# compose.yml（環境変数を使用）\nservices:\n  db:\n    image: postgres:13\n    env_file: .env\n    # または個別に指定\n    environment:\n      - POSTGRES_USER=\\x24{POSTGRES_USER}\n      - POSTGRES_PASSWORD=\\x24{POSTGRES_PASSWORD}\n      - POSTGRES_DB=\\x24{POSTGRES_DB}",
                        'code_language' => 'bash',
                        'sort_order' => 3
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Profiles使用例',
                        'content' => "# compose.yml\nservices:\n  web:\n    build: .\n    ports: [\"3000:3000\"]\n\n  db:\n    image: postgres:13\n\n  # Monitoring services（オプション）\n  prometheus:\n    image: prom/prometheus\n    ports: [\"9090:9090\"]\n    profiles: [monitoring]\n\n  grafana:\n    image: grafana/grafana\n    ports: [\"3001:3000\"]\n    profiles: [monitoring]\n\n# 基本サービスのみ起動\ndocker compose up -d\n\n# Monitoring含めて起動\ndocker compose --profile monitoring up -d",
                        'code_language' => 'yaml',
                        'sort_order' => 4
                    ],
                ],
            ],
        ]);

        // Milestone 3: 上級（第5週～第6週）
        $milestone3 = $template->milestones()->create([
            'title' => 'Docker上級',
            'description' => 'Multi-stage Build、Healthcheck、Private Registry、セキュリティ',
            'sort_order' => 3,
            'estimated_hours' => 16,
            'deliverables' => [
                'Multi-stage Buildで最適化',
                'Healthcheckを実装',
                'Private Registryを構築',
                'Trivyでセキュリティスキャン'
            ],
        ]);

        $milestone3->tasks()->createMany([
            // Week 5
            [
                'title' => '第5週：Multi-stage Build & Healthcheck',
                'description' => 'イメージの最適化とヘルスチェック',
                'sort_order' => 5,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Multi-stage Builds', 'Healthcheck Reference'],
                'subtasks' => [
                    ['title' => 'Multi-stage Buildを作成', 'estimated_minutes' => 150, 'sort_order' => 1],
                    ['title' => 'Healthcheckを追加', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'イメージサイズを比較', 'estimated_minutes' => 60, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Multi-stage Dockerfile',
                        'content' => "# Multi-stage Dockerfile\n# ビルドステージ\nFROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci\nCOPY . .\nRUN npm run build\n\n# 実行ステージ\nFROM node:20-alpine\nWORKDIR /app\n\n# ビルドステージから必要なファイルだけコピー\nCOPY --from=builder /app/dist ./dist\nCOPY --from=builder /app/node_modules ./node_modules\nCOPY package*.json ./\n\n# Non-rootユーザーを作成\nRUN addgroup -g 1001 -S nodejs && adduser -S app -u 1001 \\\n    && apk add --no-cache curl\n\nUSER app\nEXPOSE 3000\n\n# Healthcheck\nHEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \\\n  CMD curl -fsS http://localhost:3000/health || exit 1\n\nCMD [\"node\", \"dist/server.js\"]",
                        'code_language' => 'dockerfile',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Multi-stage Buildの利点',
                        'content' => "# Multi-stage Buildの利点\n\n## メリット\n\n1. **イメージサイズ削減**\n   - ビルドツールを最終イメージに含めない\n   - 本番環境に不要なファイルを除外\n\n2. **セキュリティ向上**\n   - 攻撃面を減らす\n   - ビルド時の秘密情報を残さない\n\n3. **ビルド効率**\n   - レイヤーキャッシュを活用\n   - 並列ビルドが可能\n\n## サイズ比較例\n\n| 方式 | サイズ |\n|------|--------|\n| シングルステージ | 500MB |\n| Multi-stage | 150MB |\n\n## ベストプラクティス\n\n```dockerfile\n# 1. Alpineベースイメージを使う\nFROM node:20-alpine\n\n# 2. 依存関係を先にコピー（キャッシュ活用）\nCOPY package*.json ./\nRUN npm ci\n\n# 3. ソースコードは後でコピー\nCOPY . .\n\n# 4. 不要なファイルは.dockerignoreで除外\n```",
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Healthcheck実装',
                        'content' => "# Dockerfile内でHealthcheck定義\nHEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \\\n  CMD curl -fsS http://localhost:3000/health || exit 1\n\n# Compose内で定義\nservices:\n  web:\n    build: .\n    healthcheck:\n      test: [\"CMD\", \"curl\", \"-f\", \"http://localhost:3000/health\"]\n      interval: 30s\n      timeout: 3s\n      retries: 3\n      start_period: 5s\n\n# Node.jsでヘルスエンドポイント実装\napp.get('/health', (req, res) => {\n  res.status(200).json({ \n    status: 'OK',\n    uptime: process.uptime(),\n    timestamp: Date.now()\n  });\n});",
                        'code_language' => 'yaml',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：Private Registry & セキュリティ',
                'description' => 'プライベートレジストリ構築とセキュリティスキャン',
                'sort_order' => 6,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Docker Registry', 'Trivy Documentation'],
                'subtasks' => [
                    ['title' => 'Private Registryを構築', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Trivyでスキャン', 'estimated_minutes' => 90, 'sort_order' => 2],
                    ['title' => 'Non-rootユーザーで実行', 'estimated_minutes' => 90, 'sort_order' => 3],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Private Registry構築',
                        'content' => "# Registryコンテナを起動\ndocker run -d \\\n  -p 5000:5000 \\\n  --name registry \\\n  -v registry_data:/var/lib/registry \\\n  registry:2\n\n# イメージにタグ付け\ndocker tag my-app localhost:5000/my-app:latest\n\n# Registryにプッシュ\ndocker push localhost:5000/my-app:latest\n\n# Registryからプル\ndocker pull localhost:5000/my-app:latest\n\n# Registry内のイメージ一覧\ncurl http://localhost:5000/v2/_catalog",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Trivyでセキュリティスキャン',
                        'content' => "# Trivyをインストール（WSL2/Linux）\ncurl -sfL https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/install.sh | sh -s -- -b /usr/local/bin\n\n# イメージをスキャン\ntrivy image my-app:latest\n\n# 高リスクのみ表示\ntrivy image --severity HIGH,CRITICAL my-app:latest\n\n# JSON形式で出力\ntrivy image --format json --output results.json my-app:latest\n\n# Dockerで実行\ndocker run --rm \\\n  -v /var/run/docker.sock:/var/run/docker.sock \\\n  aquasec/trivy:latest \\\n  image my-app:latest",
                        'code_language' => 'bash',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'セキュリティベストプラクティス',
                        'content' => "# Dockerセキュリティベストプラクティス\n\n## 1. Non-rootユーザーで実行\n\n```dockerfile\nRUN addgroup -g 1001 -S app && adduser -S app -u 1001\nUSER app\n```\n\n## 2. 最小限のベースイメージ\n\n- Alpine Linuxを使用（5MB程度）\n- Distroless（Googleが提供）\n\n## 3. マルチステージビルド\n\n- ビルドツールを最終イメージに含めない\n\n## 4. 秘密情報の管理\n\n```bash\n# 環境変数で渡す（開発）\ndocker run -e DB_PASSWORD=secret app\n\n# Docker Secretsを使用（本番）\ndocker secret create db_password password.txt\n```\n\n## 5. 脆弱性スキャン\n\n- Trivy（推奨）\n- Snyk\n- Clair\n\n## 6. Read-onlyファイルシステム\n\n```yaml\nservices:\n  web:\n    read_only: true\n    tmpfs:\n      - /tmp\n```\n\n## 7. Capabilitiesの制限\n\n```bash\ndocker run --cap-drop=ALL --cap-add=NET_BIND_SERVICE app\n```",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        // Milestone 4: 実践（第7週～第8週）
        $milestone4 = $template->milestones()->create([
            'title' => 'Docker実践',
            'description' => 'Orchestration、Monitoring、Microservices構成',
            'sort_order' => 4,
            'estimated_hours' => 16,
            'deliverables' => [
                'Docker Swarmでオーケストレーション',
                'Prometheus + Grafanaで監視',
                'Microservices + API Gateway構成',
                'Loggingシステム構築'
            ],
        ]);

        $milestone4->tasks()->createMany([
            // Week 7
            [
                'title' => '第7週：Orchestration & Monitoring',
                'description' => 'Docker SwarmとPrometheus/Grafanaによる監視',
                'sort_order' => 7,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Docker Swarm', 'Prometheus', 'Grafana'],
                'subtasks' => [
                    ['title' => 'Docker Swarmを初期化', 'estimated_minutes' => 90, 'sort_order' => 1],
                    ['title' => 'Monitoring stackを構築', 'estimated_minutes' => 180, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Docker Swarm基本',
                        'content' => "# Swarmモードを初期化\ndocker swarm init\n\n# サービスを作成\ndocker service create \\\n  --name web \\\n  --replicas 3 \\\n  -p 80:80 \\\n  nginx\n\n# サービス一覧\ndocker service ls\n\n# サービスの詳細\ndocker service ps web\n\n# スケール変更\ndocker service scale web=5\n\n# サービスの更新\ndocker service update --image nginx:alpine web\n\n# サービスの削除\ndocker service rm web",
                        'code_language' => 'bash',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Monitoring Stack（Compose）',
                        'content' => "# compose.monitoring.yml\nservices:\n  cadvisor:\n    image: gcr.io/cadvisor/cadvisor:latest\n    ports:\n      - \"8080:8080\"\n    volumes:\n      - /:/rootfs:ro\n      - /var/run:/var/run:rw\n      - /sys:/sys:ro\n      - /var/lib/docker/:/var/lib/docker:ro\n    restart: unless-stopped\n\n  prometheus:\n    image: prom/prometheus\n    ports:\n      - \"9090:9090\"\n    volumes:\n      - ./prometheus.yml:/etc/prometheus/prometheus.yml\n      - prometheus_data:/prometheus\n    command:\n      - '--config.file=/etc/prometheus/prometheus.yml'\n      - '--storage.tsdb.path=/prometheus'\n    restart: unless-stopped\n\n  grafana:\n    image: grafana/grafana\n    ports:\n      - \"3000:3000\"\n    environment:\n      - GF_SECURITY_ADMIN_PASSWORD=admin\n    volumes:\n      - grafana_data:/var/lib/grafana\n    restart: unless-stopped\n\nvolumes:\n  prometheus_data:\n  grafana_data:",
                        'code_language' => 'yaml',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'prometheus.yml設定',
                        'content' => "# prometheus.yml\nglobal:\n  scrape_interval: 15s\n  evaluation_interval: 15s\n\nscrape_configs:\n  - job_name: 'prometheus'\n    static_configs:\n      - targets: ['localhost:9090']\n\n  - job_name: 'cadvisor'\n    static_configs:\n      - targets: ['cadvisor:8080']\n\n  - job_name: 'node-exporter'\n    static_configs:\n      - targets: ['node-exporter:9100']",
                        'code_language' => 'yaml',
                        'sort_order' => 3
                    ],
                ],
            ],
            // Week 8
            [
                'title' => '第8週：Microservices構成',
                'description' => 'マイクロサービス + API Gateway + ネットワーク分離',
                'sort_order' => 8,
                'estimated_minutes' => 480,
                'priority' => 5,
                'resources' => ['Microservices Pattern', 'Nginx as Gateway'],
                'subtasks' => [
                    ['title' => 'API Gatewayを構築', 'estimated_minutes' => 120, 'sort_order' => 1],
                    ['title' => 'Microservicesを作成', 'estimated_minutes' => 180, 'sort_order' => 2],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'Microservices Compose',
                        'content' => "# compose.microservices.yml\nnetworks:\n  frontend:\n  backend:\n\nservices:\n  gateway:\n    build: ./gateway\n    ports:\n      - \"80:80\"\n    networks:\n      - frontend\n      - backend\n    depends_on:\n      - user-service\n      - order-service\n\n  user-service:\n    build: ./user-service\n    networks:\n      - backend\n    environment:\n      - DB_HOST=user-db\n    depends_on:\n      - user-db\n\n  order-service:\n    build: ./order-service\n    networks:\n      - backend\n    environment:\n      - DB_HOST=order-db\n    depends_on:\n      - order-db\n\n  user-db:\n    image: postgres:13\n    networks:\n      - backend\n    environment:\n      - POSTGRES_DB=users\n      - POSTGRES_USER=user\n      - POSTGRES_PASSWORD=password\n    volumes:\n      - user_pg:/var/lib/postgresql/data\n\n  order-db:\n    image: postgres:13\n    networks:\n      - backend\n    environment:\n      - POSTGRES_DB=orders\n      - POSTGRES_USER=user\n      - POSTGRES_PASSWORD=password\n    volumes:\n      - order_pg:/var/lib/postgresql/data\n\nvolumes:\n  user_pg:\n  order_pg:",
                        'code_language' => 'yaml',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Nginx Gateway設定',
                        'content' => "# nginx.conf\nevents {}\n\nhttp {\n    upstream user_service {\n        server user-service:3001;\n    }\n\n    upstream order_service {\n        server order-service:3002;\n    }\n\n    server {\n        listen 80;\n\n        location /api/users/ {\n            proxy_pass http://user_service/;\n            proxy_set_header Host \\x24host;\n            proxy_set_header X-Real-IP \\x24remote_addr;\n        }\n\n        location /api/orders/ {\n            proxy_pass http://order_service/;\n            proxy_set_header Host \\x24host;\n            proxy_set_header X-Real-IP \\x24remote_addr;\n        }\n\n        location /health {\n            return 200 'OK';\n            add_header Content-Type text/plain;\n        }\n    }\n}",
                        'code_language' => 'nginx',
                        'sort_order' => 2
                    ],
                ],
            ],
        ]);

        // Milestone 5: Capstone Project（第9週～第12週）
        $milestone5 = $template->milestones()->create([
            'title' => 'Capstone Project',
            'description' => 'E-commerce全体構成 + CI/CD + 本番デプロイ',
            'sort_order' => 5,
            'estimated_hours' => 32,
            'deliverables' => [
                'Full-stack E-commerce構築',
                'GitHub Actions CI/CD',
                'セキュリティスキャン自動化',
                '本番環境構成完成'
            ],
        ]);

        $milestone5->tasks()->createMany([
            // Week 9-12
            [
                'title' => '第9-12週：E-commerce Capstone',
                'description' => 'Full-stack構成 + CI/CD + Monitoring + Security',
                'sort_order' => 9,
                'estimated_minutes' => 1920,
                'priority' => 5,
                'resources' => ['GitHub Actions', 'Docker Best Practices'],
                'subtasks' => [
                    ['title' => 'Frontend（React + Nginx）', 'estimated_minutes' => 360, 'sort_order' => 1],
                    ['title' => 'Backend（Node/Express）', 'estimated_minutes' => 360, 'sort_order' => 2],
                    ['title' => 'DB/Cache（Postgres + Redis）', 'estimated_minutes' => 240, 'sort_order' => 3],
                    ['title' => 'CI/CD構築', 'estimated_minutes' => 360, 'sort_order' => 4],
                    ['title' => 'Monitoring & Logging', 'estimated_minutes' => 240, 'sort_order' => 5],
                    ['title' => 'Security強化', 'estimated_minutes' => 180, 'sort_order' => 6],
                ],
                'knowledge_items' => [
                    [
                        'type' => 'code_snippet',
                        'title' => 'GitHub Actions CI/CD',
                        'content' => "# .github/workflows/docker.yml\nname: Docker CI/CD\n\non:\n  push:\n    branches: [main]\n  pull_request:\n    branches: [main]\n\njobs:\n  build:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v4\n\n      - uses: docker/setup-buildx-action@v3\n\n      - uses: docker/login-action@v3\n        with:\n          registry: ghcr.io\n          username: \\x24{{ github.actor }}\n          password: \\x24{{ secrets.GITHUB_TOKEN }}\n\n      - uses: docker/build-push-action@v6\n        with:\n          context: .\n          push: true\n          tags: ghcr.io/\\x24{{ github.repository }}:latest\n          cache-from: type=gha\n          cache-to: type=gha,mode=max\n\n      - name: Run Trivy scan\n        run: |\n          docker run --rm \\\n            -v /var/run/docker.sock:/var/run/docker.sock \\\n            aquasec/trivy:latest \\\n            image ghcr.io/\\x24{{ github.repository }}:latest",
                        'code_language' => 'yaml',
                        'sort_order' => 1
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Full Stack compose.yml',
                        'content' => "# compose.yml（本番用）\nservices:\n  frontend:\n    build:\n      context: ./frontend\n      dockerfile: Dockerfile.prod\n    ports:\n      - \"80:80\"\n    depends_on:\n      - backend\n    restart: unless-stopped\n\n  backend:\n    build: ./backend\n    environment:\n      - DB_HOST=postgres\n      - REDIS_HOST=redis\n    depends_on:\n      postgres:\n        condition: service_healthy\n      redis:\n        condition: service_started\n    healthcheck:\n      test: [\"CMD\", \"curl\", \"-f\", \"http://localhost:3000/health\"]\n      interval: 30s\n      timeout: 3s\n      retries: 3\n    restart: unless-stopped\n\n  postgres:\n    image: postgres:13-alpine\n    environment:\n      - POSTGRES_DB=ecommerce\n      - POSTGRES_USER=user\n      - POSTGRES_PASSWORD=password\n    volumes:\n      - pg_data:/var/lib/postgresql/data\n    healthcheck:\n      test: [\"CMD-SHELL\", \"pg_isready -U user\"]\n      interval: 10s\n      timeout: 5s\n      retries: 5\n    restart: unless-stopped\n\n  redis:\n    image: redis:7-alpine\n    volumes:\n      - redis_data:/data\n    restart: unless-stopped\n\nvolumes:\n  pg_data:\n  redis_data:",
                        'code_language' => 'yaml',
                        'sort_order' => 2
                    ],
                    [
                        'type' => 'note',
                        'title' => 'プロジェクト完成チェックリスト',
                        'content' => "# プロジェクト完成チェックリスト\n\n## Dockerfile\n- [ ] Multi-stage build使用\n- [ ] Non-rootユーザーで実行\n- [ ] Healthcheck定義\n- [ ] .dockerignore作成\n\n## Compose\n- [ ] Networksで分離\n- [ ] Volumesで永続化\n- [ ] 環境変数を.envで管理\n- [ ] depends_on + healthcheck\n\n## Security\n- [ ] Trivyスキャン実行\n- [ ] 脆弱性を修正\n- [ ] Secretsを環境変数化\n- [ ] Read-only filesystem（可能な場合）\n\n## CI/CD\n- [ ] GitHub Actions設定\n- [ ] 自動ビルド & プッシュ\n- [ ] 自動テスト\n- [ ] 自動スキャン\n\n## Monitoring\n- [ ] cAdvisor導入\n- [ ] Prometheus導入\n- [ ] Grafana導入\n- [ ] ダッシュボード作成\n\n## Logging\n- [ ] ログ収集設定\n- [ ] ログローテーション\n- [ ] 集中ログ管理\n\n## Documentation\n- [ ] README作成\n- [ ] アーキテクチャ図\n- [ ] デプロイ手順\n- [ ] トラブルシューティング",
                        'sort_order' => 3
                    ],
                ],
            ],
        ]);

        echo "Docker Course Seeder completed successfully!\n";
    }
}
