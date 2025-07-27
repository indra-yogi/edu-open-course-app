<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseCategory;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get uploader (assumes "John Doe" exists)
        $uploader = User::where('name', 'John Doe')->first();

        if (!$uploader) {
            $this->command->warn("User 'John Doe' not found. Please seed users first.");
            return;
        }

        // Get category IDs by name
        $categories = CourseCategory::pluck('id', 'name'); // ['Programming' => 1, ...]

        // Define course data
        $topCourses = [
            [
                'title' => 'Intro to Web Development',
                'category' => 'Programming',
                'image_url' => 'web-dev.jpg',
            ],
            [
                'title' => 'Graphic Design Basics',
                'category' => 'Design',
                'image_url' => 'design.jpg',
            ],
            [
                'title' => 'Digital Marketing 101',
                'category' => 'Marketing',
                'image_url' => 'marketing.jpg',
            ],
        ];

        $now = now();
        $insertData = [];

        foreach ($topCourses as $courseData) {
            $insertData[] = [
                'user_id'     => $uploader->id,
                'title'       => $courseData['title'],
                'description' => 'Auto-generated course about ' . strtolower($courseData['category']),
                'thumbnail'   => $courseData['image_url'],
                'is_approved' => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Insert all courses at once
        Course::insert($insertData);

        // Get the inserted courses
        $courses = Course::orderByDesc('id')->take(count($insertData))->get()->reverse()->values();

        foreach ($courses as $index => $course) {
            $categoryName = $topCourses[$index]['category'];
            $categoryId = $categories[$categoryName] ?? null;

            if ($categoryId) {
                $course->categories()->attach($categoryId);
            }

            // Add a material for each course
            CourseMaterial::create([
                'course_id' => $course->id,
                'title' => 'Introduction',
                'file_path' => Str::slug($course->title) . '.pdf',
                'file_type' => 'pdf',
            ]);
        }

        $this->command->info("Inserted " . count($topCourses) . " top courses with materials.");
    }
}

