<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    public function run()
    {
        // Membaca file JSON
        $jsonFile = file_get_contents(ROOTPATH . 'course.json');
        $courseData = json_decode($jsonFile, true);

        // Ambil data courses dan categories dari database
        $courses = $this->db->table('courses')->get()->getResultArray();
        $categories = $this->db->table('categories')->get()->getResultArray();

        // Buat mapping untuk course dan category berdasarkan slug/nama
        $courseMap = [];
        foreach ($courses as $course) {
            $courseMap[$course['slug']] = $course['id'];
        }

        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category['name']] = $category['id'];
        }

        // Buat data relasi course_categories
        $courseCategoryData = [];

        foreach ($courseData['courses'] as $course) {
            $courseId = $courseMap[$course['slug']];
            
            foreach ($course['categories'] as $categoryName) {
                if (isset($categoryMap[$categoryName])) {
                    $courseCategoryData[] = [
                        'course_id' => $courseId,
                        'category_id' => $categoryMap[$categoryName]
                    ];
                }
            }
        }

        // Simpan data ke tabel course_categories
        if (!empty($courseCategoryData)) {
            $this->db->table('course_categories')->insertBatch($courseCategoryData);
        }
    }
} 