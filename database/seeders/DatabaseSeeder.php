<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->curriculum() as $topicData) {
            $topic = Topic::create([
                'title' => $topicData['title'],
                'description' => $topicData['description'],
            ]);

            foreach ($topicData['lessons'] as $lessonData) {
                $lesson = $topic->lessons()->create([
                    'title' => $lessonData['title'],
                    'content' => $lessonData['content'],
                ]);

                foreach ($lessonData['questions'] as $question) {
                    $lesson->questions()->create(['question' => $question]);
                }

                // one summary quiz per lesson, reusing that lesson's questions
                $quiz = Quiz::create([
                    'title' => $lessonData['title'].' - Summary',
                    'type' => 'lesson_summary',
                ]);
                $quiz->questions()->attach($lesson->questions->pluck('id'));
            }
        }

        // a mixed review quiz pulling questions from across all lessons,
        // which is why questions and quizzes use a pivot table
        $review = Quiz::create([
            'title' => 'Mixed Review',
            'type' => 'mixed_review',
        ]);
        $review->questions()->attach(Question::inRandomOrder()->limit(8)->pluck('id'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function curriculum(): array
    {
        return [
            [
                'title' => 'PHP Fundamentals',
                'description' => 'The core language features you need before touching a framework.',
                'lessons' => [
                    [
                        'title' => 'Variables and Data Types',
                        'content' => 'PHP variables start with a dollar sign and do not need a declared type. The main scalar types are string, int, float and bool. Arrays hold multiple values and come in two flavours: indexed arrays use numeric keys, while associative arrays use named keys. PHP converts between types automatically (type juggling), so "5" + 3 gives 8. Use strict comparison (===) to compare both value and type and avoid surprises, and declare(strict_types=1) at the top of a file to stop silent coercion in function calls.',
                        'questions' => [
                            'What character do all PHP variable names start with?',
                            'What is the difference between == and === in PHP?',
                            'What is an associative array?',
                        ],
                    ],
                    [
                        'title' => 'Functions and Scope',
                        'content' => 'Functions are declared with the function keyword and can declare parameter and return types, for example function add(int $a, int $b): int. Variables created inside a function are local to it and are not visible outside. To use an outer variable inside a function you must pass it in as an argument, or capture it in a closure with the use keyword. Arrow functions (fn) automatically capture outer variables by value, which makes them handy for short callbacks such as array_map.',
                        'questions' => [
                            'How do you declare a return type on a PHP function?',
                            'Why can a function not see a variable defined outside of it by default?',
                            'What is the main convenience of an arrow function compared to a normal closure?',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Git and Version Control',
                'description' => 'Tracking changes and collaborating without overwriting each other.',
                'lessons' => [
                    [
                        'title' => 'Commits and the Staging Area',
                        'content' => 'Git records history as a series of commits, each a snapshot of your project. Changes move through three areas: the working directory (files you edit), the staging area (changes selected for the next commit) and the repository (committed history). git add moves changes into the staging area and git commit saves them. Staging lets you split unrelated edits into separate, focused commits. git status shows what is staged and unstaged, and git diff shows the exact line changes.',
                        'questions' => [
                            'What does git add do?',
                            'Why does Git have a staging area between editing and committing?',
                            'Which command shows the exact line changes that are not yet staged?',
                        ],
                    ],
                    [
                        'title' => 'Branching and Merging',
                        'content' => 'A branch is a lightweight pointer to a commit, so creating one is instant and cheap. Branches let you work on a feature in isolation while main stays stable. git switch -c feature creates and moves to a new branch. When the work is finished, git merge brings those commits back into another branch. If both branches edited the same lines, Git cannot decide which to keep and reports a merge conflict, which you resolve by editing the file and committing the result.',
                        'questions' => [
                            'What is a Git branch?',
                            'What causes a merge conflict?',
                            'Which command creates a new branch and switches to it?',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'HTTP and REST APIs',
                'description' => 'How clients and servers talk to each other over the web.',
                'lessons' => [
                    [
                        'title' => 'HTTP Methods and Status Codes',
                        'content' => 'Every HTTP request has a method that signals intent. GET reads data, POST creates it, PUT and PATCH update it (PUT replaces the whole resource, PATCH changes part of it) and DELETE removes it. The server replies with a status code. 2xx means success (200 OK, 201 Created, 204 No Content), 4xx means the client made a mistake (404 Not Found, 422 Unprocessable Content for validation errors) and 5xx means the server failed. Choosing the correct code lets clients react properly without parsing the response body.',
                        'questions' => [
                            'Which HTTP method is normally used to create a new resource?',
                            'What is the difference between PUT and PATCH?',
                            'What does a 204 status code mean?',
                        ],
                    ],
                    [
                        'title' => 'RESTful Resource Design',
                        'content' => 'REST organises an API around resources, which are nouns identified by URLs such as /topics or /topics/3/lessons. The HTTP method describes the action, so you use DELETE /lessons/5 rather than a URL like /deleteLesson. Collection URLs are plural, and a specific item is addressed by its id. Responses are usually JSON. REST APIs are stateless: each request carries everything the server needs, such as an authentication token, and the server stores no session between requests.',
                        'questions' => [
                            'In REST, what does a URL like /lessons/5 identify?',
                            'Why is DELETE /lessons/5 preferred over /deleteLesson?',
                            'What does it mean that a REST API is stateless?',
                        ],
                    ],
                ],
            ],
        ];
    }
}
