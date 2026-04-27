<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        // Membaca file JSON
        $jsonFile = file_get_contents(ROOTPATH . 'course.json');
        $courseData = json_decode($jsonFile, true);

        // Ambil data courses dan modules dari database
        $courses = $this->db->table('courses')->get()->getResultArray();
        $modules = $this->db->table('modules')->get()->getResultArray();

        // Buat mapping untuk course berdasarkan slug
        $courseMap = [];
        foreach ($courses as $course) {
            $courseMap[$course['slug']] = $course['id'];
        }

        // Buat mapping untuk module berdasarkan course_id dan title
        $moduleMap = [];
        foreach ($modules as $module) {
            $key = $module['course_id'] . '_' . $module['title'];
            $moduleMap[$key] = $module['id'];
        }

        // Buat data lessons
        $lessonData = [];
        $now = date('Y-m-d H:i:s');

        foreach ($courseData['courses'] as $course) {
            $courseId = $courseMap[$course['slug']];
            
            foreach ($course['modules'] as $moduleIndex => $module) {
                $moduleKey = $courseId . '_' . $module['title'];
                $moduleId = $moduleMap[$moduleKey];
                
                foreach ($module['lessons'] as $lessonIndex => $lesson) {
                    $lessonData[] = [
                        'module_id' => $moduleId,
                        'title' => $lesson['title'],
                        'description' => substr($lesson['description_content'], 0, 100) . '...',
                        'content' => $lesson['description_content'],
                        'video_url' => $lesson['video_url'],
                        'video_duration' => $lesson['video_duration'],
                        'order_index' => $lessonIndex + 1, // Mulai dari 1
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
        }

        // Simpan data ke tabel lessons
        if (!empty($lessonData)) {
            $this->db->table('lessons')->insertBatch($lessonData);
        }
    }
} 