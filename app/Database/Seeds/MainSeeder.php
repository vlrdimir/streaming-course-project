<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // php spark db:seed MainSeeder
        // Reset only imported course-related tables so reruns are safe after partial failures.
        $this->db->table('lessons')->emptyTable();
        $this->db->table('modules')->emptyTable();
        $this->db->table('course_categories')->emptyTable();
        $this->db->table('courses')->emptyTable();
        $this->db->table('categories')->emptyTable();

        // Urutan eksekusi seeder penting karena relasi antar tabel
        $this->call('CategorySeeder');     // Harus dijalankan pertama
        $this->call('CourseSeeder');       // Harus dijalankan setelah CategorySeeder
        $this->call('CourseCategorySeeder'); // Harus dijalankan setelah CourseSeeder dan CategorySeeder
        $this->call('ModuleSeeder');       // Harus dijalankan setelah CourseSeeder
        $this->call('LessonSeeder');       // Harus dijalankan setelah ModuleSeeder
    }
}
