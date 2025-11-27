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
            'category' => 'programming',
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
                'estimated_minutes' => 150,
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
                    [
                        'type' => 'exercise',
                        'title' => '演習：初めてのDockerコンテナ',
                        'content' => "# 演習：初めてのDockerコンテナ\n\n## 目標\n複数のコンテナを起動して、基本操作を習得する\n\n## 手順\n\n### 1. Ubuntuコンテナで対話的シェル\n```bash\ndocker run -it ubuntu bash\n\n# コンテナ内で\napt-get update\napt-get install -y curl\ncurl https://example.com\nexit\n```\n\n### 2. Pythonコンテナでスクリプト実行\n```bash\necho 'print(\"Hello Docker\")' > hello.py\ndocker run -v \\x24(pwd):/app python:3.11 python /app/hello.py\n```\n\n### 3. MySQLコンテナを起動\n```bash\ndocker run -d \\\n  --name mysql \\\n  -e MYSQL_ROOT_PASSWORD=password \\\n  -e MYSQL_DATABASE=testdb \\\n  -p 3306:3306 \\\n  mysql:8.0\n\n# 接続テスト\ndocker exec -it mysql mysql -u root -ppassword\n```\n\n### 4. コンテナのクリーンアップ\n```bash\n# すべてのコンテナを停止\ndocker stop \\x24(docker ps -aq)\n\n# すべてのコンテナを削除\ndocker rm \\x24(docker ps -aq)\n\n# 未使用のイメージを削除\ndocker image prune -a\n```\n\n## チェックポイント\n- [ ] コンテナ内でコマンド実行できた\n- [ ] ボリュームマウントでファイル共有できた\n- [ ] コンテナ間でネットワーク通信できた\n- [ ] クリーンアップ方法を理解した",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'トラブルシューティング：よくある問題',
                        'content' => "# トラブルシューティング：よくある問題\n\n## 問題1：ポートが既に使用されている\n\n**エラー:**\n```\nError: bind: address already in use\n```\n\n**解決策:**\n```bash\n# 使用中のポートを確認（Windows）\nnetstat -ano | findstr :8080\n\n# 使用中のポートを確認（Linux/Mac）\nlsof -i :8080\n\n# 別のポートを使用\ndocker run -p 8081:80 nginx\n```\n\n## 問題2：イメージのプルが遅い\n\n**解決策:**\n```json\n// Docker Desktop > Settings > Docker Engine\n{\n  \"registry-mirrors\": [\n    \"https://mirror.gcr.io\"\n  ]\n}\n```\n\n## 問題3：WSL2でディスク容量不足\n\n**確認:**\n```bash\ndocker system df\n```\n\n**解決策:**\n```bash\n# 未使用リソースを削除\ndocker system prune -a --volumes\n\n# WSL2のディスク圧縮（PowerShell）\nwsl --shutdown\noptimize-vhd -Path \\x24env:LOCALAPPDATA\\Packages\\CanonicalGroupLimited*\\LocalState\\ext4.vhdx -Mode Full\n```\n\n## 問題4：コンテナが起動しない\n\n**診断:**\n```bash\n# ログを確認\ndocker logs <container_name>\n\n# 詳細情報を確認\ndocker inspect <container_name>\n\n# イベントを確認\ndocker events --since 30m\n```\n\n## 問題5：Permission denied\n\n**原因:** Non-rootユーザーでファイルアクセス\n\n**解決策:**\n```dockerfile\n# Dockerfileで権限を設定\nRUN chown -R app:app /app\nUSER app\n```",
                        'sort_order' => 6
                    ],
                ],
            ],
            // Week 2
            [
                'title' => '第2週：Dockerfile作成と基本コマンド',
                'description' => '初めてのDockerfile作成、イメージビルド、コンテナ操作の基本コマンド',
                'sort_order' => 2,
                'estimated_minutes' => 300,
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
                    [
                        'type' => 'code_snippet',
                        'title' => 'Dockerfile最適化テクニック',
                        'content' => "# Dockerfile最適化テクニック\n\n# 1. レイヤーキャッシュを活用\n# 変更頻度の低いものを先に\nFROM node:20-alpine\nWORKDIR /app\n\n# 依存関係を先にコピー（キャッシュ活用）\nCOPY package*.json ./\nRUN npm ci --omit=dev\n\n# ソースコードは後でコピー\nCOPY . .\n\n# 2. 複数のRUNを結合（レイヤー削減）\n# 悪い例\nRUN apt-get update\nRUN apt-get install -y curl\nRUN apt-get clean\n\n# 良い例\nRUN apt-get update && \\\n    apt-get install -y curl && \\\n    apt-get clean && \\\n    rm -rf /var/lib/apt/lists/*\n\n# 3. マルチステージでサイズ削減\nFROM node:20 AS builder\nWORKDIR /app\nCOPY . .\nRUN npm install && npm run build\n\nFROM node:20-alpine\nCOPY --from=builder /app/dist ./dist\nCMD [\"node\", \"dist/server.js\"]\n\n# 4. .dockerignoreを使う\n# .dockerignore\nnode_modules\n.git\n.env\n*.log\nREADME.md",
                        'code_language' => 'dockerfile',
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '演習：Full-stack Dockerfileを作成',
                        'content' => "# 演習：Full-stack Dockerfileを作成\n\n## 目標\nReact + Node.jsのフルスタックアプリをDockerize\n\n## React Dockerfile\n```dockerfile\n# frontend/Dockerfile\nFROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci\nCOPY . .\nRUN npm run build\n\nFROM nginx:alpine\nCOPY --from=builder /app/build /usr/share/nginx/html\nCOPY nginx.conf /etc/nginx/conf.d/default.conf\nEXPOSE 80\nCMD [\"nginx\", \"-g\", \"daemon off;\"]\n```\n\n## Node.js API Dockerfile\n```dockerfile\n# backend/Dockerfile\nFROM node:20-alpine\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci --omit=dev && \\\n    apk add --no-cache curl\n\nCOPY . .\n\nRUN addgroup -g 1001 -S nodejs && \\\n    adduser -S api -u 1001 && \\\n    chown -R api:nodejs /app\n\nUSER api\nEXPOSE 3000\n\nHEALTHCHECK --interval=30s --timeout=3s \\\n  CMD curl -f http://localhost:3000/health || exit 1\n\nCMD [\"npm\", \"start\"]\n```\n\n## nginx.conf\n```nginx\nserver {\n    listen 80;\n    location / {\n        root /usr/share/nginx/html;\n        try_files \\x24uri /index.html;\n    }\n    location /api/ {\n        proxy_pass http://backend:3000/;\n    }\n}\n```\n\n## テスト\n```bash\n# Frontendビルド\ncd frontend\ndocker build -t my-frontend .\n\n# Backendビルド\ncd backend\ndocker build -t my-backend .\n\n# 起動\ndocker run -d --name api my-backend\ndocker run -d --name web -p 80:80 my-frontend\n```",
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Dockerコマンドチートシート',
                        'content' => "# Dockerコマンドチートシート\n\n## イメージ管理\n```bash\n# ビルド\ndocker build -t name:tag .\ndocker build --no-cache -t name:tag .  # キャッシュなし\n\n# 一覧\ndocker images\ndocker images -a  # 中間イメージも表示\n\n# 削除\ndocker rmi <image_id>\ndocker rmi \\x24(docker images -q)  # 全削除\n\n# タグ付け\ndocker tag source:tag target:tag\n\n# プッシュ/プル\ndocker push name:tag\ndocker pull name:tag\n\n# 検索\ndocker search nginx\n\n# 履歴\ndocker history <image>\n```\n\n## コンテナ管理\n```bash\n# 起動\ndocker run -d -p 8080:80 --name web nginx\ndocker run -it ubuntu bash  # 対話モード\ndocker run --rm alpine echo \"Hello\"  # 実行後削除\n\n# 一覧\ndocker ps\ndocker ps -a\ndocker ps -q  # IDのみ\n\n# 制御\ndocker start <container>\ndocker stop <container>\ndocker restart <container>\ndocker pause <container>\ndocker unpause <container>\n\n# 削除\ndocker rm <container>\ndocker rm \\x24(docker ps -aq)  # 全削除\ndocker rm -f \\x24(docker ps -aq)  # 強制削除\n\n# 実行\ndocker exec -it <container> bash\ndocker exec <container> ls /app\n\n# ログ\ndocker logs <container>\ndocker logs -f <container>  # フォロー\ndocker logs --tail 100 <container>  # 最後100行\n\n# 情報\ndocker inspect <container>\ndocker stats\ndocker top <container>\n\n# コピー\ndocker cp <container>:/path ./local\ndocker cp ./local <container>:/path\n```\n\n## システム管理\n```bash\n# 情報\ndocker info\ndocker version\ndocker system df  # ディスク使用量\n\n# クリーンアップ\ndocker system prune  # 未使用削除\ndocker system prune -a  # すべて削除\ndocker system prune -a --volumes  # ボリューム含む\n\n# ボリューム\ndocker volume ls\ndocker volume create <name>\ndocker volume rm <name>\ndocker volume prune\n\n# ネットワーク\ndocker network ls\ndocker network create <name>\ndocker network rm <name>\ndocker network inspect <name>\n```",
                        'sort_order' => 7
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
                'estimated_minutes' => 300,
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
                    [
                        'type' => 'exercise',
                        'title' => '演習：WordPress + MySQL構成',
                        'content' => "# 演習：WordPress + MySQL構成\n\n## 目標\nカスタムネットワークとVolumeを使ってWordPress環境を構築\n\n## 手順\n\n### 1. ネットワークとVolumeを作成\n```bash\ndocker network create wordpress-net\ndocker volume create wp_data\ndocker volume create db_data\n```\n\n### 2. MySQLコンテナを起動\n```bash\ndocker run -d \\\n  --name wp-mysql \\\n  --network wordpress-net \\\n  -e MYSQL_ROOT_PASSWORD=rootpass \\\n  -e MYSQL_DATABASE=wordpress \\\n  -e MYSQL_USER=wpuser \\\n  -e MYSQL_PASSWORD=wppass \\\n  -v db_data:/var/lib/mysql \\\n  mysql:8.0\n```\n\n### 3. WordPressコンテナを起動\n```bash\ndocker run -d \\\n  --name wordpress \\\n  --network wordpress-net \\\n  -p 8080:80 \\\n  -e WORDPRESS_DB_HOST=wp-mysql \\\n  -e WORDPRESS_DB_USER=wpuser \\\n  -e WORDPRESS_DB_PASSWORD=wppass \\\n  -e WORDPRESS_DB_NAME=wordpress \\\n  -v wp_data:/var/www/html \\\n  wordpress:latest\n```\n\n### 4. 接続テスト\n```bash\n# WordPressコンテナからMySQLに接続\ndocker exec -it wordpress bash\nping wp-mysql\nexit\n\n# ブラウザで http://localhost:8080 にアクセス\n```\n\n### 5. バックアップとリストア\n```bash\n# バックアップ\ndocker exec wp-mysql mysqldump -u wpuser -pwppass wordpress > backup.sql\n\n# リストア\ndocker exec -i wp-mysql mysql -u wpuser -pwppass wordpress < backup.sql\n```\n\n## チェックポイント\n- [ ] カスタムネットワークで通信できた\n- [ ] Volumeでデータが永続化された\n- [ ] コンテナを削除してもデータが残る\n- [ ] バックアップ/リストアができた",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'note',
                        'title' => 'パフォーマンスチューニング',
                        'content' => "# Dockerパフォーマンスチューニング\n\n## 1. WSL2でのI/O最適化\n\n### プロジェクトをWSLファイルシステムに配置\n```bash\n# 高速（推奨）\n/home/username/projects/myapp\n\n# 遅い（避ける）\n/mnt/c/Users/username/projects/myapp\n```\n\n### パフォーマンス測定\n```bash\n# WSL filesystem\ntime docker run -v \\x24HOME/project:/app node npm install\n# → 30秒\n\n# Windows filesystem  \ntime docker run -v /mnt/c/project:/app node npm install\n# → 5分\n```\n\n## 2. BuildKit活用\n\n```bash\n# BuildKitを有効化\nexport DOCKER_BUILDKIT=1\n\n# キャッシュマウント（依存関係キャッシュ）\nRUN --mount=type=cache,target=/root/.npm \\\n    npm install\n\n# 並列ビルド\ndocker buildx build \\\n  --platform linux/amd64,linux/arm64 \\\n  -t myimage .\n```\n\n## 3. イメージレイヤーの最適化\n\n```dockerfile\n# 悪い例（12レイヤー）\nRUN apt-get update\nRUN apt-get install -y curl\nRUN apt-get install -y git\nRUN apt-get clean\n\n# 良い例（1レイヤー）\nRUN apt-get update && \\\n    apt-get install -y curl git && \\\n    apt-get clean && \\\n    rm -rf /var/lib/apt/lists/*\n```\n\n## 4. メモリ・CPU制限\n\n```bash\n# メモリ制限\ndocker run --memory=\"512m\" --memory-swap=\"1g\" myapp\n\n# CPU制限\ndocker run --cpus=\"1.5\" myapp\n\n# Compose設定\nservices:\n  web:\n    deploy:\n      resources:\n        limits:\n          cpus: '1.5'\n          memory: 512M\n```\n\n## 5. ログローテーション\n\n```json\n// daemon.json\n{\n  \"log-driver\": \"json-file\",\n  \"log-opts\": {\n    \"max-size\": \"10m\",\n    \"max-file\": \"3\"\n  }\n}\n```",
                        'sort_order' => 6
                    ],
                ],
            ],
            // Week 4
            [
                'title' => '第4週：Docker Compose',
                'description' => 'Compose v2で複数コンテナを管理、環境変数とProfiles',
                'sort_order' => 4,
                'estimated_minutes' => 330,
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
                    [
                        'type' => 'exercise',
                        'title' => '演習：MERN Stack with Compose',
                        'content' => "# 演習：MERN Stack with Compose\n\n## 目標\nMongoDB + Express + React + Node.jsをDocker Composeで構築\n\n## compose.yml\n```yaml\nname: mern-stack\n\nnetworks:\n  frontend:\n  backend:\n\nservices:\n  # React Frontend\n  client:\n    build:\n      context: ./client\n      dockerfile: Dockerfile\n    ports:\n      - \"3000:3000\"\n    environment:\n      - REACT_APP_API_URL=http://localhost:5000\n    networks:\n      - frontend\n    depends_on:\n      - api\n    volumes:\n      - ./client:/app\n      - /app/node_modules\n\n  # Express API\n  api:\n    build: ./server\n    ports:\n      - \"5000:5000\"\n    environment:\n      - MONGO_URI=mongodb://mongo:27017/merndb\n      - NODE_ENV=development\n    networks:\n      - frontend\n      - backend\n    depends_on:\n      mongo:\n        condition: service_healthy\n    volumes:\n      - ./server:/app\n      - /app/node_modules\n\n  # MongoDB\n  mongo:\n    image: mongo:7\n    ports:\n      - \"27017:27017\"\n    environment:\n      - MONGO_INITDB_DATABASE=merndb\n    volumes:\n      - mongo_data:/data/db\n    networks:\n      - backend\n    healthcheck:\n      test: echo 'db.runCommand(\"ping\").ok' | mongosh localhost:27017/test --quiet\n      interval: 10s\n      timeout: 5s\n      retries: 5\n\n  # Mongo Express（オプション）\n  mongo-express:\n    image: mongo-express\n    ports:\n      - \"8081:8081\"\n    environment:\n      - ME_CONFIG_MONGODB_URL=mongodb://mongo:27017/\n    networks:\n      - backend\n    depends_on:\n      - mongo\n    profiles:\n      - tools\n\nvolumes:\n  mongo_data:\n```\n\n## 起動\n```bash\n# 基本サービスのみ\ndocker compose up -d\n\n# Mongo Expressも起動\ndocker compose --profile tools up -d\n\n# ログ表示\ndocker compose logs -f api\n\n# 停止・削除\ndocker compose down\n\n# ボリューム含めて削除\ndocker compose down -v\n```",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'code_snippet',
                        'title' => 'Compose トラブルシューティング',
                        'content' => "# Compose トラブルシューティング\n\n## 問題1：サービスが起動しない\n\n```bash\n# ログを確認\ndocker compose logs <service>\n\n# イベントを確認\ndocker compose events\n\n# 設定を検証\ndocker compose config\n```\n\n## 問題2：depends_onが効かない\n\n```yaml\n# 悪い例（起動順のみ）\nservices:\n  web:\n    depends_on:\n      - db\n\n# 良い例（ヘルスチェック待機）\nservices:\n  web:\n    depends_on:\n      db:\n        condition: service_healthy\n  db:\n    healthcheck:\n      test: [\"CMD\", \"pg_isready\"]\n```\n\n## 問題3：環境変数が読み込まれない\n\n```yaml\n# .envファイルを明示的に指定\nservices:\n  web:\n    env_file:\n      - .env\n      - .env.local\n```\n\n## 問題4：ボリュームが更新されない\n\n```bash\n# ボリュームを再作成\ndocker compose down -v\ndocker compose up -d\n\n# または特定のボリュームを削除\ndocker volume rm <project>_<volume_name>\n```\n\n## 問題5：ネットワークエラー\n\n```bash\n# ネットワークをリセット\ndocker compose down\ndocker network prune\ndocker compose up -d\n```\n\n## デバッグコマンド集\n\n```bash\n# サービスの状態確認\ndocker compose ps\n\n# リソース使用状況\ndocker compose top\n\n# 特定のサービスを再起動\ndocker compose restart web\n\n# サービスをスケール\ndocker compose up -d --scale web=3\n\n# 設定を表示（変数展開後）\ndocker compose config\n```",
                        'code_language' => 'bash',
                        'sort_order' => 6
                    ],
                    [
                        'type' => 'note',
                        'title' => 'Docker Compose ベストプラクティス',
                        'content' => "# Docker Compose ベストプラクティス\n\n## 1. 環境別のファイル分離\n\n```bash\n# 構成\ncompose.yml           # 共通設定\ncompose.dev.yml      # 開発環境\ncompose.prod.yml     # 本番環境\n\n# 開発環境で起動\ndocker compose -f compose.yml -f compose.dev.yml up -d\n\n# 本番環境で起動\ndocker compose -f compose.yml -f compose.prod.yml up -d\n```\n\n## 2. ヘルスチェックの実装\n\n```yaml\nservices:\n  api:\n    healthcheck:\n      test: [\"CMD\", \"curl\", \"-f\", \"http://localhost:3000/health\"]\n      interval: 30s\n      timeout: 3s\n      retries: 3\n      start_period: 40s\n```\n\n## 3. リソース制限\n\n```yaml\nservices:\n  web:\n    deploy:\n      resources:\n        limits:\n          cpus: '0.5'\n          memory: 512M\n        reservations:\n          cpus: '0.25'\n          memory: 256M\n```\n\n## 4. ログ管理\n\n```yaml\nservices:\n  web:\n    logging:\n      driver: \"json-file\"\n      options:\n        max-size: \"10m\"\n        max-file: \"3\"\n```\n\n## 5. セキュリティ\n\n```yaml\nservices:\n  web:\n    # Read-only filesystem\n    read_only: true\n    tmpfs:\n      - /tmp\n    # Capability制限\n    cap_drop:\n      - ALL\n    cap_add:\n      - NET_BIND_SERVICE\n    # Seccompプロファイル\n    security_opt:\n      - no-new-privileges:true\n```\n\n## 6. Secrets管理\n\n```yaml\nservices:\n  db:\n    secrets:\n      - db_password\n\nsecrets:\n  db_password:\n    file: ./secrets/db_password.txt\n```",
                        'sort_order' => 7
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
                'estimated_minutes' => 300,
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
                    [
                        'type' => 'exercise',
                        'title' => '演習：イメージサイズ最適化コンテスト',
                        'content' => "# 演習：イメージサイズ最適化コンテスト\n\n## 目標\n同じアプリで異なる手法を比較し、最小イメージを作成\n\n## ベースライン（最適化なし）\n```dockerfile\nFROM node:20\nWORKDIR /app\nCOPY . .\nRUN npm install\nCMD [\"npm\", \"start\"]\n```\nサイズ: ~1.1GB\n\n## 最適化レベル1：Alpineを使用\n```dockerfile\nFROM node:20-alpine\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci --omit=dev\nCOPY . .\nCMD [\"npm\", \"start\"]\n```\nサイズ: ~180MB (-92%)\n\n## 最適化レベル2：Multi-stage\n```dockerfile\nFROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci\nCOPY . .\nRUN npm run build && npm prune --production\n\nFROM node:20-alpine\nWORKDIR /app\nCOPY --from=builder /app/dist ./dist\nCOPY --from=builder /app/node_modules ./node_modules\nCOPY package*.json ./\nCMD [\"node\", \"dist/server.js\"]\n```\nサイズ: ~120MB (-89%)\n\n## 最適化レベル3：Distroless\n```dockerfile\nFROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci\nCOPY . .\nRUN npm run build\n\nFROM gcr.io/distroless/nodejs20-debian11\nWORKDIR /app\nCOPY --from=builder /app/dist ./dist\nCOPY --from=builder /app/node_modules ./node_modules\nCMD [\"dist/server.js\"]\n```\nサイズ: ~80MB (-93%)\n\n## 比較結果\n```bash\n# サイズを確認\ndocker images | grep myapp\n\n# 起動時間を測定\ntime docker run --rm myapp\n```\n\n## チャレンジ\n- [ ] 50MB以下を達成\n- [ ] セキュリティスキャンでCRITICAL 0個\n- [ ] 起動時間3秒以下",
                        'sort_order' => 4
                    ],
                ],
            ],
            // Week 6
            [
                'title' => '第6週：Private Registry & セキュリティ',
                'description' => 'プライベートレジストリ構築とセキュリティスキャン',
                'sort_order' => 6,
                'estimated_minutes' => 300,
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
                    [
                        'type' => 'code_snippet',
                        'title' => 'セキュリティ自動化スクリプト',
                        'content' => "#!/bin/bash\n# security-scan.sh - CI/CDで使用するセキュリティスキャンスクリプト\n\nset -e\n\nIMAGE_NAME=\\x24{1:-\"myapp:latest\"}\nTHRESHOLD=\\x24{2:-\"HIGH\"}\n\necho \"🔍 Security Scanning: \\x24IMAGE_NAME\"\necho \"📊 Threshold: \\x24THRESHOLD\"\n\n# 1. Trivyでイメージスキャン\necho \"\\n=== Trivy Scan ===\"\ntrivy image --severity \\x24THRESHOLD,CRITICAL \\\n  --exit-code 1 \\\n  --format table \\\n  \\x24IMAGE_NAME\n\n# 2. Hadolintでdockerfile静的解析\necho \"\\n=== Hadolint Check ===\"\nhadolint Dockerfile || true\n\n# 3. Dockleでベストプラクティスチェック\necho \"\\n=== Dockle Check ===\"\ndocker run --rm \\\n  -v /var/run/docker.sock:/var/run/docker.sock \\\n  goodwithtech/dockle:latest \\\n  --exit-code 1 \\\n  --exit-level warn \\\n  \\x24IMAGE_NAME\n\n# 4. SBOMを生成\necho \"\\n=== Generating SBOM ===\"\nsyft \\x24IMAGE_NAME -o json > sbom.json\n\n# 5. 結果をレポート\necho \"\\n✅ Security scan completed!\"\necho \"📋 SBOM saved to: sbom.json\"",
                        'code_language' => 'bash',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '演習：セキュアなプロダクション構成',
                        'content' => "# 演習：セキュアなプロダクション構成\n\n## 目標\nセキュリティベストプラクティスを全て適用したイメージを作成\n\n## セキュアなDockerfile\n```dockerfile\n# ============================================\n# Multi-stage Build: Security Hardened\n# ============================================\n\n# Stage 1: Builder\nFROM node:20-alpine AS builder\n\n# セキュリティアップデート\nRUN apk update && apk upgrade && \\\n    apk add --no-cache dumb-init\n\nWORKDIR /build\n\n# 依存関係のみ先にコピー（キャッシュ活用）\nCOPY package*.json ./\nRUN npm ci --omit=dev && npm cache clean --force\n\n# ソースをコピーしてビルド\nCOPY . .\nRUN npm run build && \\\n    npm prune --production\n\n# Stage 2: Runtime (Distroless)\nFROM gcr.io/distroless/nodejs20-debian11:nonroot\n\n# メタデータ\nLABEL maintainer=\"your@email.com\"\nLABEL version=\"1.0.0\"\nLABEL description=\"Secure production image\"\n\nWORKDIR /app\n\n# 必要なファイルだけコピー\nCOPY --from=builder --chown=nonroot:nonroot /build/dist ./dist\nCOPY --from=builder --chown=nonroot:nonroot /build/node_modules ./node_modules\nCOPY --from=builder --chown=nonroot:nonroot /build/package*.json ./\nCOPY --from=builder /usr/bin/dumb-init /usr/bin/\n\n# Non-rootユーザー（distrolessのデフォルト）\nUSER nonroot\n\n# Healthcheck\nHEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \\\n  CMD [\"node\", \"-e\", \"require('http').get('http://localhost:3000/health', (r) => process.exit(r.statusCode === 200 ? 0 : 1))\"]\n\n# dumb-initを使用（PID 1問題対策）\nENTRYPOINT [\"/usr/bin/dumb-init\", \"--\"]\nCMD [\"node\", \"dist/server.js\"]\n```\n\n## セキュアなCompose設定\n```yaml\nservices:\n  api:\n    build: .\n    read_only: true  # Read-only filesystem\n    tmpfs:\n      - /tmp\n    cap_drop:\n      - ALL\n    cap_add:\n      - NET_BIND_SERVICE\n    security_opt:\n      - no-new-privileges:true\n    deploy:\n      resources:\n        limits:\n          cpus: '1.0'\n          memory: 512M\n    healthcheck:\n      test: [\"CMD\", \"node\", \"healthcheck.js\"]\n      interval: 30s\n```\n\n## セキュリティチェックリスト\n- [ ] Non-rootユーザー\n- [ ] Distroless/Alpineベース\n- [ ] Read-only filesystem\n- [ ] Capability制限\n- [ ] リソース制限\n- [ ] Trivyスキャン CRITICAL=0\n- [ ] Healthcheck実装\n- [ ] Secrets管理\n- [ ] TLS通信",
                        'sort_order' => 5
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
                'estimated_minutes' => 270,
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
                'estimated_minutes' => 300,
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
                'estimated_minutes' => 1740,
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
                    [
                        'type' => 'code_snippet',
                        'title' => '本番デプロイメント完全版',
                        'content' => "# compose.prod.yml - 本番環境完全版\nname: ecommerce-prod\n\nnetworks:\n  frontend:\n  backend:\n  monitoring:\n\nservices:\n  # Nginx Reverse Proxy\n  nginx:\n    image: nginx:alpine\n    ports:\n      - \"80:80\"\n      - \"443:443\"\n    volumes:\n      - ./nginx/nginx.conf:/etc/nginx/nginx.conf:ro\n    networks:\n      - frontend\n    restart: unless-stopped\n\n  # React Frontend\n  frontend:\n    build:\n      context: ./frontend\n      dockerfile: Dockerfile.prod\n    networks:\n      - frontend\n    read_only: true\n    deploy:\n      replicas: 2\n      resources:\n        limits:\n          cpus: '0.5'\n          memory: 256M\n    restart: unless-stopped\n\n  # Node.js API\n  backend:\n    build: ./backend\n    environment:\n      - NODE_ENV=production\n    networks:\n      - frontend\n      - backend\n    healthcheck:\n      test: [\"CMD\", \"node\", \"healthcheck.js\"]\n      interval: 30s\n    deploy:\n      replicas: 3\n      resources:\n        limits:\n          cpus: '1.0'\n          memory: 512M\n    restart: unless-stopped\n\n  # PostgreSQL\n  postgres:\n    image: postgres:13-alpine\n    volumes:\n      - pg_data:/var/lib/postgresql/data\n    networks:\n      - backend\n    healthcheck:\n      test: [\"CMD-SHELL\", \"pg_isready\"]\n    restart: unless-stopped\n\n  # Redis Cache\n  redis:\n    image: redis:7-alpine\n    volumes:\n      - redis_data:/data\n    networks:\n      - backend\n    restart: unless-stopped\n\nvolumes:\n  pg_data:\n  redis_data:",
                        'code_language' => 'yaml',
                        'sort_order' => 4
                    ],
                    [
                        'type' => 'note',
                        'title' => '本番運用チェックリスト',
                        'content' => "# 本番運用チェックリスト\n\n## デプロイ前\n\n### セキュリティ\n- [ ] すべてのイメージでTrivyスキャン実施\n- [ ] 脆弱性CRITICAL = 0\n- [ ] Non-rootユーザーで実行\n- [ ] Secrets管理（環境変数/Vault）\n- [ ] TLS/SSL証明書設定\n- [ ] ファイアウォール設定\n\n### パフォーマンス\n- [ ] リソース制限設定\n- [ ] Healthcheck実装\n- [ ] ログローテーション設定\n- [ ] キャッシュ戦略実装\n- [ ] データベース最適化\n\n### 監視\n- [ ] Prometheus メトリクス収集\n- [ ] Grafanaダッシュボード作成\n- [ ] アラート設定\n- [ ] ログ集約（Loki/ELK）\n\n### バックアップ\n- [ ] データベースバックアップ自動化\n- [ ] Volumeバックアップ設定\n- [ ] リストア手順確認\n- [ ] RPO/RTO定義\n\n## デプロイ後\n\n### 動作確認\n- [ ] ヘルスチェックエンドポイント確認\n- [ ] 負荷テスト実施\n- [ ] エラー率確認\n- [ ] レスポンスタイム測定\n\n### 運用\n- [ ] ローリングアップデート手順確認\n- [ ] ロールバック手順確認\n- [ ] インシデント対応手順作成\n- [ ] ドキュメント整備",
                        'sort_order' => 5
                    ],
                    [
                        'type' => 'exercise',
                        'title' => '最終課題：プロダクション環境構築',
                        'content' => "# 最終課題：プロダクション環境構築\n\n## 課題概要\nE-commerceアプリを本番環境にデプロイ可能な状態にする\n\n## 要件\n\n### 1. アプリケーション構成\n- Frontend: React (Nginx配信)\n- Backend: Node.js/Express API\n- Database: PostgreSQL\n- Cache: Redis\n\n### 2. セキュリティ要件\n- [ ] すべてのコンテナNon-root実行\n- [ ] TLS/SSL通信\n- [ ] Secrets管理\n- [ ] Trivyスキャン CRITICAL=0\n- [ ] Rate limiting実装\n- [ ] CORS設定\n\n### 3. 監視要件\n- [ ] Prometheus メトリクス\n- [ ] Grafana ダッシュボード\n- [ ] ログ集約\n- [ ] アラート設定\n\n### 4. CI/CD要件\n- [ ] GitHub Actions設定\n- [ ] 自動テスト\n- [ ] 自動ビルド\n- [ ] セキュリティスキャン\n- [ ] 自動デプロイ（staging）\n\n### 5. 可用性要件\n- [ ] Healthcheck実装\n- [ ] 複数レプリカ（Backend: 3台）\n- [ ] データベースバックアップ\n- [ ] ローリングアップデート対応\n\n## 提出物\n1. 完全なソースコード（GitHub）\n2. docker-compose.prod.yml\n3. GitHub Actions設定\n4. README.md（セットアップ手順）\n5. アーキテクチャ図\n6. パフォーマンステスト結果\n7. セキュリティスキャン結果\n\n## 評価基準\n- セキュリティ: 30点\n- 可用性: 25点\n- パフォーマンス: 20点\n- CI/CD: 15点\n- ドキュメント: 10点",
                        'sort_order' => 6
                    ],
                ],
            ],
        ]);

        echo "✅ Docker Course Seeder completed successfully!\n";
        echo "📚 Total Content:\n";
        echo "   - 5 Milestones\n";
        echo "   - 12 Weeks of Learning\n";
        echo "   - 96 Hours Total\n";
        echo "   - Extensive hands-on exercises\n";
        echo "   - Production-ready skills\n";
    }
}
