<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Membaca file JSON
        $jsonFile = file_get_contents(ROOTPATH . 'course.json');
        $courseData = json_decode($jsonFile, true);

        // Mengumpulkan semua kategori unik
        $categories = [];
        $categoryMap = [];

        foreach ($courseData['courses'] as $course) {
            foreach ($course['categories'] as $categoryName) {
                if (!isset($categoryMap[$categoryName])) {
                    $slug = strtolower(str_replace(' ', '-', $categoryName));
                    $categories[] = [
                        'name' => $categoryName,
                        'slug' => $slug,
                        'description' => 'Kategori untuk ' . $categoryName
                    ];
                    $categoryMap[$categoryName] = true;
                }
            }
        }

        // Menyimpan data kategori ke dalam tabel
        $this->db->table('categories')->insertBatch($categories);
    }
} 