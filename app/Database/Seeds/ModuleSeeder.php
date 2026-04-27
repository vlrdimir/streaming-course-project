<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        // Membaca file JSON
        $jsonFile = file_get_contents(ROOTPATH . 'course.json');
        $courseData = json_decode($jsonFile, true);

        // Ambil data courses dari database
        $courses = $this->db->table('courses')->get()->getResultArray();

        // Buat mapping untuk course berdasarkan slug
        $courseMap = [];
        foreach ($courses as $course) {
            $courseMap[$course['slug']] = $course['id'];
        }

        // Buat data modules
        $moduleData = [];
        $now = date('Y-m-d H:i:s');

        foreach ($courseData['courses'] as $course) {
            $courseId = $courseMap[$course['slug']];
            
            foreach ($course['modules'] as $index => $module) {
                $moduleData[] = [
                    'course_id' => $courseId,
                    'title' => $module['title'],
                    'description' => $module['description'],
                    'order_index' => $index + 1, // Mulai dari 1
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        // Simpan data ke tabel modules
        if (!empty($moduleData)) {
            $this->db->table('modules')->insertBatch($moduleData);
        }
    }
} 