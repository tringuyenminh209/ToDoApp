<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeDockerSeeder extends Seeder
{
    /**
     * Seed Docker cheat code data from doleaf
     * Reference: https://doleaf.com/docker
     */
    public function run(): void
    {
        // Create Docker Language
        $dockerLanguage = CheatCodeLanguage::create([
            'name' => 'docker',
            'display_name' => 'Docker',
            'slug' => 'docker',
            'icon' => 'ic_docker',
            'color' => '#2496ED',
            'description' => 'Dockerのリファレンス。最も一般的なDockerコマンドをここで見つけることができます。',
            'category' => 'devops',
            'popularity' => 90,
            'is_active' => true,
            'sort_order' => 12,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($dockerLanguage, 'はじめに', 1, 'Dockerの基本操作', 'getting-started');

        $this->createExample($section1, $dockerLanguage, 'バックグラウンドでコンテナを実行', 1,
            "$ docker run -d -p 80:80 docker/getting-started\n\n# -d - Run the container in detached mode\n# -p 80:80 - Map port 80 to port 80 in the container\n# docker/getting-started - The image to use",
            'バックグラウンドモードでコンテナを作成・実行',
            null,
            'easy'
        );

        $this->createExample($section1, $dockerLanguage, 'フォアグラウンドでコンテナを実行', 2,
            "$ docker run -it -p 8001:8080 --name my-nginx nginx\n\n# -it - Interactive bash mode\n# -p 8001:8080 - Map port 8001 to port 8080 in the container\n# --name my-nginx - Specify a name\n# nginx - The image to use",
            'インタラクティブモードでコンテナを実行',
            null,
            'easy'
        );

        $this->createExample($section1, $dockerLanguage, '一般的なコマンド', 3,
            "docker ps                        # List running containers\ndocker ps -a                     # List all containers\ndocker ps -s                     # List running containers (with CPU / memory)\ndocker images                    # List all images\ndocker exec -it <container> bash # Connecting to container\ndocker logs <container>          # Shows container's console log\ndocker stop <container>          # Stop a container\ndocker restart <container>       # Restart a container\ndocker rm <container>            # Remove a container\ndocker port <container>          # Shows container's port mapping\ndocker top <container>           # List processes\ndocker kill <container>          # Kill a container",
            '最も一般的なDockerコマンド',
            null,
            'easy'
        );

        // Section 2: Docker Containers
        $section2 = $this->createSection($dockerLanguage, 'Dockerコンテナ', 2, 'コンテナの操作と管理', 'containers');

        $this->createExample($section2, $dockerLanguage, '開始と停止', 1,
            "docker start my-nginx   # Starting\ndocker stop my-nginx    # Stopping\ndocker restart my-nginx  # Restarting\ndocker pause my-nginx   # Pausing\ndocker unpause my-nginx # Unpausing\ndocker wait my-nginx    # Blocking a Container\ndocker kill my-nginx    # Sending a SIGKILL\ndocker attach my-nginx  # Connecting to an Existing Container",
            'コンテナの開始・停止・再起動',
            null,
            'easy'
        );

        $this->createExample($section2, $dockerLanguage, '情報の取得', 2,
            "docker ps               # List running containers\ndocker ps -a            # List all containers\ndocker logs my-nginx    # Container Logs\ndocker inspect my-nginx # Inspecting Containers\ndocker events my-nginx  # Containers Events\ndocker port my-nginx    # Public Ports\ndocker top my-nginx     # Running Processes\ndocker stats my-nginx   # Container Resource Usage\ndocker diff my-nginx    # Lists the changes made to a container",
            'コンテナ情報の取得',
            null,
            'easy'
        );

        $this->createExample($section2, $dockerLanguage, 'コンテナの作成', 3,
            "docker create [options] IMAGE\n  -a, --attach               # attach stdout/err\n  -i, --interactive          # attach stdin (interactive)\n  -t, --tty                  # pseudo-tty\n      --name NAME            # name your image\n  -p, --publish 5000:5000    # port map (host:container)\n      --expose 5432          # expose a port to containers\n  -P, --publish-all          # publish all ports\n      --link container:alias # linking\n  -v, --volume `pwd`:/app    # mount (absolute paths needed)\n  -e, --env NAME=hello       # env vars",
            'コンテナ作成のオプション',
            null,
            'medium'
        );

        $this->createExample($section2, $dockerLanguage, 'コンテナ作成の例', 4,
            "$ docker create --name my_redis --expose 6379 redis:3.0.2",
            'Redisコンテナの作成例',
            null,
            'easy'
        );

        $this->createExample($section2, $dockerLanguage, 'コンテナの操作', 5,
            "# Renaming a Container\ndocker rename my-nginx my-nginx\n\n# Removing a Container\ndocker rm my-nginx\n\n# Updating a Container\ndocker update --cpu-shares 512 -m 300M my-nginx",
            'コンテナの名前変更・削除・更新',
            null,
            'easy'
        );

        // Section 3: Docker Images
        $section3 = $this->createSection($dockerLanguage, 'Dockerイメージ', 3, 'イメージの操作とビルド', 'images');

        $this->createExample($section3, $dockerLanguage, 'イメージの操作', 1,
            "docker images                    # Listing images\ndocker rmi nginx                 # Removing an image\ndocker load < ubuntu.tar.gz      # Loading a tarred repository\ndocker load --input ubuntu.tar   # Loading a tarred repository\ndocker save busybox > ubuntu.tar # Save an image to a tar archive\ndocker history                   # Showing the history of an image\ndocker commit nginx              # Save a container as an image\ndocker tag nginx eon01/nginx     # Tagging an image\ndocker push eon01/nginx         # Pushing an image",
            'イメージの一覧・削除・保存・読み込み',
            null,
            'easy'
        );

        $this->createExample($section3, $dockerLanguage, 'イメージのビルド', 2,
            "$ docker build .\n$ docker build github.com/creack/docker-firefox\n$ docker build - < Dockerfile\n$ docker build - < context.tar.gz\n$ docker build -t eon/my-nginx .\n$ docker build -f myOtherDockerfile .\n$ curl example.com/remote/Dockerfile | docker build -f - .",
            'Dockerfileからイメージをビルド',
            null,
            'easy'
        );

        // Section 4: Docker Networking
        $section4 = $this->createSection($dockerLanguage, 'Dockerネットワーク', 4, 'ネットワークの作成と管理', 'networking');

        $this->createExample($section4, $dockerLanguage, 'ネットワークの操作', 1,
            "# Removing a network\ndocker network rm MyOverlayNetwork\n\n# Listing networks\ndocker network ls\n\n# Getting information about a network\ndocker network inspect MyOverlayNetwork\n\n# Connecting a running container to a network\ndocker network connect MyOverlayNetwork nginx\n\n# Connecting a container to a network when it starts\ndocker run -it -d --network=MyOverlayNetwork nginx\n\n# Disconnecting a container from a network\ndocker network disconnect MyOverlayNetwork nginx",
            'ネットワークの作成・接続・切断',
            null,
            'medium'
        );

        $this->createExample($section4, $dockerLanguage, 'ネットワークの作成', 2,
            "# Create overlay network\ndocker network create -d overlay MyOverlayNetwork\n\n# Create bridge network\ndocker network create -d bridge MyBridgeNetwork\n\n# Create overlay network with custom configuration\ndocker network create -d overlay \\\n  --subnet=192.168.0.0/16 \\\n  --subnet=192.170.0.0/16 \\\n  --gateway=192.168.0.100 \\\n  --gateway=192.170.0.100 \\\n  --ip-range=192.168.1.0/24 \\\n  --aux-address=\"my-router=192.168.1.5\" \\\n  --aux-address=\"my-switch=192.168.1.6\" \\\n  --aux-address=\"my-printer=192.170.1.5\" \\\n  --aux-address=\"my-nas=192.170.1.6\" \\\n  MyOverlayNetwork",
            'カスタム設定でネットワークを作成',
            null,
            'medium'
        );

        // Section 5: Clean Up
        $section5 = $this->createSection($dockerLanguage, 'クリーンアップ', 5, '未使用リソースの削除', 'cleanup');

        $this->createExample($section5, $dockerLanguage, 'すべてをクリーンアップ', 1,
            "# Cleans up dangling images, containers, volumes, and networks\ndocker system prune\n\n# Additionally, remove any stopped containers and all unused images\ndocker system prune -a",
            'システム全体のクリーンアップ',
            null,
            'easy'
        );

        $this->createExample($section5, $dockerLanguage, 'コンテナのクリーンアップ', 2,
            "# Stop all running containers\ndocker stop \$(docker ps -a -q)\n\n# Delete stopped containers\ndocker container prune",
            '停止中のコンテナを削除',
            null,
            'easy'
        );

        $this->createExample($section5, $dockerLanguage, 'イメージのクリーンアップ', 3,
            "# Remove all dangling (not tagged and is not associated with a container) images\ndocker image prune\n\n# Remove all images which are not used by existing containers\ndocker image prune -a",
            '未使用のイメージを削除',
            null,
            'easy'
        );

        $this->createExample($section5, $dockerLanguage, 'ボリュームのクリーンアップ', 4,
            "# Remove all volumes not used by at least one container\ndocker volume prune",
            '未使用のボリュームを削除',
            null,
            'easy'
        );

        // Section 6: Docker Hub
        $section6 = $this->createSection($dockerLanguage, 'Docker Hub', 6, 'Docker Hubの操作', 'docker-hub');

        $this->createExample($section6, $dockerLanguage, 'Docker Hubコマンド', 1,
            "docker search search_word # Search docker hub for images\ndocker pull user/image     # Downloads an image from docker hub\ndocker login                 # Authenticate to docker hub\ndocker push user/image      # Uploads an image to docker hub",
            'Docker Hubの基本操作',
            null,
            'easy'
        );

        $this->createExample($section6, $dockerLanguage, 'レジストリへのログイン', 2,
            "$ docker login\n$ docker login localhost:8080",
            'Dockerレジストリへの認証',
            null,
            'easy'
        );

        $this->createExample($section6, $dockerLanguage, 'レジストリからのログアウト', 3,
            "$ docker logout\n$ docker logout localhost:8080",
            'Dockerレジストリからのログアウト',
            null,
            'easy'
        );

        $this->createExample($section6, $dockerLanguage, 'イメージの検索', 4,
            "$ docker search nginx\n$ docker search nginx --stars=3 --no-trunc busybox",
            'Docker Hubでイメージを検索',
            null,
            'easy'
        );

        $this->createExample($section6, $dockerLanguage, 'イメージのプル', 5,
            "$ docker pull nginx\n$ docker pull eon01/nginx localhost:5000/myadmin/nginx",
            'Docker Hubからイメージをダウンロード',
            null,
            'easy'
        );

        $this->createExample($section6, $dockerLanguage, 'イメージのプッシュ', 6,
            "$ docker push eon01/nginx\n$ docker push eon01/nginx localhost:5000/myadmin/nginx",
            'Docker Hubにイメージをアップロード',
            null,
            'easy'
        );

        // Section 7: Miscellaneous
        $section7 = $this->createSection($dockerLanguage, 'その他', 7, 'その他の便利なコマンド', 'miscellaneous');

        $this->createExample($section7, $dockerLanguage, '一括クリーンアップ', 1,
            "docker stop -f \$(docker ps -a -q)  # Stopping all containers\ndocker rm -f \$(docker ps -a -q)    # Removing all containers\ndocker rmi -f \$(docker images -q)  # Removing all images",
            'すべてのコンテナ・イメージを一括削除',
            null,
            'easy'
        );

        $this->createExample($section7, $dockerLanguage, 'ボリュームの確認', 2,
            "$ docker volume ls",
            'ボリュームの一覧表示',
            null,
            'easy'
        );

        $this->createExample($section7, $dockerLanguage, '未使用ボリュームのクリーンアップ', 3,
            "$ docker volume prune",
            '未使用のボリュームを削除',
            null,
            'easy'
        );

        // Update language counts
        $this->updateLanguageCounts($dockerLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null, ?string $slug = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug ?? Str::slug($title),
            'description' => $description,
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function createExample(
        CheatCodeSection $section,
        CheatCodeLanguage $language,
        string $title,
        int $sortOrder,
        string $code,
        ?string $description = null,
        ?string $output = null,
        string $difficulty = 'easy'
    ): CodeExample {
        return CodeExample::create([
            'section_id' => $section->id,
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'code' => $code,
            'description' => $description,
            'output' => $output,
            'difficulty' => $difficulty,
            'tags' => $this->generateTags($title, $description),
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function generateTags(string $title, ?string $description): array
    {
        $tags = [];
        $titleLower = strtolower($title);
        $descLower = $description ? strtolower($description) : '';

        // Add tags based on title and description
        if (str_contains($titleLower, 'container') || str_contains($descLower, 'コンテナ')) {
            $tags[] = 'container';
        }
        if (str_contains($titleLower, 'image') || str_contains($descLower, 'イメージ')) {
            $tags[] = 'image';
        }
        if (str_contains($titleLower, 'network') || str_contains($descLower, 'ネットワーク')) {
            $tags[] = 'network';
        }
        if (str_contains($titleLower, 'volume') || str_contains($titleLower, 'ボリューム')) {
            $tags[] = 'volume';
        }
        if (str_contains($titleLower, 'build') || str_contains($descLower, 'ビルド')) {
            $tags[] = 'build';
        }
        if (str_contains($titleLower, 'clean') || str_contains($titleLower, 'prune') || str_contains($descLower, 'クリーン')) {
            $tags[] = 'cleanup';
        }
        if (str_contains($titleLower, 'hub') || str_contains($titleLower, 'registry') || str_contains($titleLower, 'push') || str_contains($titleLower, 'pull')) {
            $tags[] = 'registry';
        }
        if (str_contains($titleLower, 'run') || str_contains($titleLower, 'start') || str_contains($titleLower, 'stop')) {
            $tags[] = 'management';
        }
        if (str_contains($titleLower, 'log') || str_contains($titleLower, 'inspect') || str_contains($titleLower, 'stats')) {
            $tags[] = 'monitoring';
        }

        // Add basic tags
        $tags[] = 'docker';
        $tags[] = 'devops';
        $tags[] = 'containerization';
        $tags[] = 'basics';

        return array_unique($tags);
    }

    private function updateLanguageCounts(CheatCodeLanguage $language): void
    {
        $language->update([
            'sections_count' => $language->sections()->count(),
            'examples_count' => $language->codeExamples()->count(),
            'exercises_count' => $language->exercises()->count(),
        ]);

        // Update section counts
        foreach ($language->sections as $section) {
            $section->update([
                'examples_count' => $section->examples()->count(),
            ]);
        }
    }
}

