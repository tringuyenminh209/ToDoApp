<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KnowledgeCategory;
use Illuminate\Support\Facades\DB;

class KnowledgeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates default knowledge categories for IT students
     */
    public function run(): void
    {
        // Get first user (or create demo user)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        $this->command->info("Creating default knowledge categories for user: {$user->email}");

        DB::beginTransaction();

        try {
            // Clear existing categories for this user (optional - comment out if not needed)
            // KnowledgeCategory::where('user_id', $user->id)->delete();

            $categories = $this->getCategoryStructure($user->id);

            foreach ($categories as $category) {
                $this->createCategory($category);
            }

            DB::commit();

            $this->command->info('✅ Default knowledge categories created successfully!');
            $this->command->info('Total categories created: ' . KnowledgeCategory::where('user_id', $user->id)->count());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create categories: ' . $e->getMessage());
        }
    }

    /**
     * Create category recursively with children
     */
    private function createCategory(array $data, ?int $parentId = null): KnowledgeCategory
    {
        $children = $data['children'] ?? [];
        unset($data['children']);

        $category = KnowledgeCategory::create([
            ...$data,
            'parent_id' => $parentId
        ]);

        // Create children recursively
        foreach ($children as $childData) {
            $this->createCategory($childData, $category->id);
        }

        return $category;
    }

    /**
     * Get complete category structure
     */
    private function getCategoryStructure(int $userId): array
    {
        return [
            // 1. Programming Languages
            [
                'user_id' => $userId,
                'name' => 'Programming Languages',
                'description' => 'Programming language notes, syntax, and best practices',
                'color' => '#0FA968',
                'icon' => 'code',
                'sort_order' => 1,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Python',
                        'description' => 'Python programming notes',
                        'color' => '#3776AB',
                        'icon' => 'python',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Basics', 'color' => '#3776AB', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Data Structures', 'color' => '#3776AB', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Libraries', 'description' => 'pandas, numpy, requests, etc.', 'color' => '#3776AB', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'Interview Questions', 'color' => '#3776AB', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Java',
                        'description' => 'Java programming notes',
                        'color' => '#007396',
                        'icon' => 'java',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Core Java', 'color' => '#007396', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Spring Framework', 'color' => '#6DB33F', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Design Patterns', 'color' => '#007396', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'JavaScript',
                        'description' => 'JavaScript and TypeScript notes',
                        'color' => '#F7DF1E',
                        'icon' => 'javascript',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'ES6+ Features', 'color' => '#F7DF1E', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'React.js', 'color' => '#61DAFB', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Node.js', 'color' => '#339933', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'TypeScript', 'color' => '#3178C6', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'PHP',
                        'description' => 'PHP and Laravel notes',
                        'color' => '#777BB4',
                        'icon' => 'php',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Laravel', 'color' => '#FF2D20', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Best Practices', 'color' => '#777BB4', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'C/C++',
                        'description' => 'C and C++ programming',
                        'color' => '#00599C',
                        'icon' => 'cpp',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'STL', 'description' => 'Standard Template Library', 'color' => '#00599C', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Memory Management', 'color' => '#00599C', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Go',
                        'description' => 'Golang programming',
                        'color' => '#00ADD8',
                        'icon' => 'go',
                        'sort_order' => 6,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Concurrency', 'color' => '#00ADD8', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Web Services', 'color' => '#00ADD8', 'sort_order' => 2],
                        ]
                    ],
                ]
            ],

            // 2. Computer Science Fundamentals
            [
                'user_id' => $userId,
                'name' => 'Computer Science Fundamentals',
                'description' => 'Core CS concepts, algorithms, and theory',
                'color' => '#FF6B6B',
                'icon' => 'school',
                'sort_order' => 2,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Data Structures',
                        'description' => 'Arrays, linked lists, trees, graphs, etc.',
                        'color' => '#FF6B6B',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Arrays & Strings', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Linked Lists', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Trees & Graphs', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'Hash Tables', 'color' => '#FF6B6B', 'sort_order' => 4],
                            ['user_id' => $userId, 'name' => 'Heaps & Stacks', 'color' => '#FF6B6B', 'sort_order' => 5],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Algorithms',
                        'description' => 'Sorting, searching, DP, etc.',
                        'color' => '#FF6B6B',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Sorting & Searching', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Dynamic Programming', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Greedy Algorithms', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'Graph Algorithms', 'color' => '#FF6B6B', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Database Theory',
                        'description' => 'SQL, normalization, transactions',
                        'color' => '#FF6B6B',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'SQL Fundamentals', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Normalization', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Indexing', 'color' => '#FF6B6B', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'Transactions', 'color' => '#FF6B6B', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Networks',
                        'description' => 'TCP/IP, HTTP, DNS',
                        'color' => '#FF6B6B',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'TCP/IP', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'HTTP/HTTPS', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'DNS & Routing', 'color' => '#FF6B6B', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Operating Systems',
                        'description' => 'Process, memory, file systems',
                        'color' => '#FF6B6B',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Process Management', 'color' => '#FF6B6B', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Memory Management', 'color' => '#FF6B6B', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'File Systems', 'color' => '#FF6B6B', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 3. Web Development
            [
                'user_id' => $userId,
                'name' => 'Web Development',
                'description' => 'Frontend, backend, and DevOps',
                'color' => '#4ECDC4',
                'icon' => 'web',
                'sort_order' => 3,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Frontend',
                        'description' => 'HTML, CSS, JavaScript frameworks',
                        'color' => '#4ECDC4',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'HTML & CSS', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'JavaScript', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Frameworks', 'description' => 'React, Vue, Angular', 'color' => '#4ECDC4', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'UI/UX Best Practices', 'color' => '#4ECDC4', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Backend',
                        'description' => 'APIs, authentication, databases',
                        'color' => '#4ECDC4',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'REST APIs', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Authentication', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Caching', 'color' => '#4ECDC4', 'sort_order' => 3],
                            ['user_id' => $userId, 'name' => 'Microservices', 'color' => '#4ECDC4', 'sort_order' => 4],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'DevOps',
                        'description' => 'CI/CD, monitoring, cloud',
                        'color' => '#4ECDC4',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'CI/CD', 'color' => '#4ECDC4', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Monitoring', 'color' => '#4ECDC4', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Cloud Services', 'description' => 'AWS, Azure, GCP', 'color' => '#4ECDC4', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Security',
                        'description' => 'OWASP, auth, encryption',
                        'color' => '#E74C3C',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'OWASP Top 10', 'color' => '#E74C3C', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Authentication & Authorization', 'color' => '#E74C3C', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Encryption', 'color' => '#E74C3C', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 4. Tools & Workflow
            [
                'user_id' => $userId,
                'name' => 'Tools & Workflow',
                'description' => 'Development tools and utilities',
                'color' => '#95A5A6',
                'icon' => 'build',
                'sort_order' => 4,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Git & Version Control',
                        'description' => 'Git commands and workflows',
                        'color' => '#F05032',
                        'sort_order' => 1,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Basic Commands', 'color' => '#F05032', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Branching Strategies', 'color' => '#F05032', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Merge Conflicts', 'color' => '#F05032', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Docker',
                        'description' => 'Containerization and orchestration',
                        'color' => '#2496ED',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Dockerfile', 'color' => '#2496ED', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Docker Compose', 'color' => '#2496ED', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Container Orchestration', 'color' => '#2496ED', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Linux Commands',
                        'description' => 'Shell commands and scripting',
                        'color' => '#FCC624',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'File Operations', 'color' => '#FCC624', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Process Management', 'color' => '#FCC624', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Shell Scripting', 'color' => '#FCC624', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'IDEs & Editors',
                        'description' => 'Editor tips and shortcuts',
                        'color' => '#007ACC',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'VS Code Tips', 'color' => '#007ACC', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'IntelliJ IDEA', 'color' => '#000000', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Vim', 'color' => '#019733', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Testing',
                        'description' => 'Unit, integration, and E2E testing',
                        'color' => '#8DD6F9',
                        'sort_order' => 5,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Unit Testing', 'color' => '#8DD6F9', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Integration Testing', 'color' => '#8DD6F9', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Test-Driven Development', 'color' => '#8DD6F9', 'sort_order' => 3],
                        ]
                    ],
                ]
            ],

            // 5. Interview Preparation
            [
                'user_id' => $userId,
                'name' => 'Interview Preparation',
                'description' => 'Coding challenges and interview questions',
                'color' => '#9B59B6',
                'icon' => 'work',
                'sort_order' => 5,
                'children' => [
                    [
                        'user_id' => $userId,
                        'name' => 'Coding Challenges',
                        'description' => 'LeetCode, HackerRank, etc.',
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
                        'name' => 'System Design',
                        'description' => 'Scalability and architecture',
                        'color' => '#9B59B6',
                        'sort_order' => 2,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Scalability', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Load Balancing', 'color' => '#9B59B6', 'sort_order' => 2],
                            ['user_id' => $userId, 'name' => 'Database Design', 'color' => '#9B59B6', 'sort_order' => 3],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Behavioral Questions',
                        'description' => 'STAR method and common questions',
                        'color' => '#9B59B6',
                        'sort_order' => 3,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'STAR Method', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Common Questions', 'color' => '#9B59B6', 'sort_order' => 2],
                        ]
                    ],
                    [
                        'user_id' => $userId,
                        'name' => 'Complexity Analysis',
                        'description' => 'Big O notation',
                        'color' => '#9B59B6',
                        'sort_order' => 4,
                        'children' => [
                            ['user_id' => $userId, 'name' => 'Time Complexity', 'color' => '#9B59B6', 'sort_order' => 1],
                            ['user_id' => $userId, 'name' => 'Space Complexity', 'color' => '#9B59B6', 'sort_order' => 2],
                        ]
                    ],
                ]
            ],

            // 6. Projects & Ideas
            [
                'user_id' => $userId,
                'name' => 'Projects & Ideas',
                'description' => 'Personal project notes and code snippets',
                'color' => '#F39C12',
                'icon' => 'lightbulb',
                'sort_order' => 6,
                'children' => [
                    ['user_id' => $userId, 'name' => 'Project Ideas', 'color' => '#F39C12', 'sort_order' => 1],
                    ['user_id' => $userId, 'name' => 'Project Notes', 'color' => '#F39C12', 'sort_order' => 2],
                    ['user_id' => $userId, 'name' => 'Architecture Decisions', 'color' => '#F39C12', 'sort_order' => 3],
                    ['user_id' => $userId, 'name' => 'Code Snippets Library', 'color' => '#F39C12', 'sort_order' => 4],
                ]
            ],
        ];
    }
}
