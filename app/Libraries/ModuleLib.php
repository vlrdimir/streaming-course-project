<?php namespace App\Libraries;

class ModuleLib {
    public function isModuleCompleted($module) {
        // If there are no lessons, return false
        if (!isset($module['lessons']) || empty($module['lessons'])) {
            return false;
        }
        
        $totalLessons = count($module['lessons']);
        $completedLessons = 0;
        
        // Count completed lessons
        foreach ($module['lessons'] as $lesson) {
            if (isset($lesson['status']) && $lesson['status'] === 'completed') {
                $completedLessons++;
            }
        }
        
        // Check if all lessons are completed
        return $completedLessons === $totalLessons;
    }
}