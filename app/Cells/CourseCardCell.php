<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class CourseCardCell extends Cell
{
    public $course;
    public $userLayout = false;
    public $showProgress = false;
    public $enrolledAt = null;
    public $completedAt = null;
    
    public $progressPercentage = 0;

    public function render(): string
    {
        return $this->view('course_card', [
            'course' => $this->course,
            'userLayout' => $this->userLayout,
            'showProgress' => $this->showProgress,
            'enrolledAt' => $this->enrolledAt,
            'completedAt' => $this->completedAt,
            'progressPercentage' => $this->progressPercentage
        ]);
    }

    public function getStatusBadge(): string
    {
        if ($this->progressPercentage == 100) {
            return '<span class="absolute top-2 right-2 px-2 py-1 text-xs bg-green-500 text-white rounded-md">Selesai</span>';
        } elseif ($this->progressPercentage > 0) {
            return '<span class="absolute top-2 right-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded-md">Dipelajari</span>';
        } else {
            return '<span class="absolute top-2 right-2 px-2 py-1 text-xs bg-gray-500 text-white rounded-md">Belum Dimulai</span>';
        }
    }
} 