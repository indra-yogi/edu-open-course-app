<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseCategory;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicate inserts if already seeded
        if (CourseCategory::count() > 0) {
            return;
        }

        CourseCategory::insert([
            ['name' => 'Programming', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Design', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info("Inserted course categories: Programming, Design, Marketing.");
    }
}
