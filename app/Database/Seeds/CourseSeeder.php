<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Membaca file JSON
        $jsonFile = file_get_contents(ROOTPATH . 'course.json');
        $courseData = json_decode($jsonFile, true);

        $authorId = $this->resolveAuthorId();

        $data = [];
        $now = date('Y-m-d H:i:s');

        foreach ($courseData['courses'] as $course) {
            // Mengkonversi level sesuai format database (lowercase)
            $level = strtolower($course['level']);
            
            $data[] = [
                'title' => $course['title'],
                'slug' => $course['slug'],
                'description' => $course['description'],
                'short_description' => $course['short_description'],
                'thumbnail' => $course['slug'] . '.png',
                'status' => 'published',
                'created_by' => $authorId,
                'published_at' => $now,
                'duration' => $course['duration'],
                'level' => strtolower($level),
                'is_featured' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('courses')->insertBatch($data);
    }

    private function resolveAuthorId(): int
    {
        $existingAdmin = $this->db->table('users')
            ->select('id')
            ->groupStart()
                ->where('role', 'super_admin')
                ->orWhere('role', 'admin')
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        if ($existingAdmin !== null) {
            return (int) $existingAdmin['id'];
        }

        $this->db->table('users')->insert([
            'username' => 'seed-admin',
            'email' => 'seed-admin@example.com',
            'password' => password_hash('seed-admin-123', PASSWORD_DEFAULT),
            'full_name' => 'Seeder Admin',
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }
}
