<?php

namespace App\Libraries;

class ModuleList
{
    public function render()
    {
        // This would typically come from a database
        $modules = [
            [
                'title' => 'Persiapan Belajar',
                'completed' => true,
                'expanded' => true,
                'submodules' => [
                    ['title' => 'Persetujuan Hak Cipta', 'completed' => true, 'gratis' => true],
                    ['title' => 'Prasyarat Kemampuan', 'completed' => true, 'gratis' => true],
                    ['title' => 'Prasyarat Tools', 'completed' => true, 'gratis' => true],
                    ['title' => 'Mekanisme Belajar', 'completed' => true, 'gratis' => true, 'slug' => 'mekanisme-belajar'],
                    ['title' => 'Forum Diskusi', 'completed' => true, 'gratis' => true],
                    ['title' => 'Glosarium', 'completed' => true, 'gratis' => true],
                    ['title' => 'Daftar Referensi', 'completed' => true, 'gratis' => true],
                ]
            ],
            [
                'title' => 'Pengenalan React',
                'completed' => true,
                'expanded' => false,
                'slug' => 'pengenalan-react'
            ],
            [
                'title' => 'Konsep Dasar React',
                'completed' => true,
                'expanded' => false,
                'slug' => 'konsep-dasar-react'
            ],
            [
                'title' => 'React UI Component',
                'completed' => true,
                'expanded' => false
            ],
        ];

        return view('components/module_list', ['modules' => $modules]);
    }
}

