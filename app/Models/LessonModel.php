<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table = 'lessons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'module_id', 'title', 'description', 'content', 'video_url',
        'video_duration', 'order_index'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'module_id' => 'required|integer',
        'title' => 'required|max_length[255]',
        'order_index' => 'required|integer',
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Lesson title is required',
        ],
    ];
    
    protected $skipValidation = false;
    
    // Get lessons for a module
   public function getLessonsByModule($moduleId, $userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('lessons l');
        
        $builder->select('l.*, lp.id as progress_id, lp.status, lp.progress_percentage, lp.completed_at')
                ->where('l.module_id', $moduleId)
                ->orderBy('l.order_index', 'ASC');
        
        // Join with lesson_progress if user ID is provided
        if ($userId !== null) {
            $builder->join('lesson_progress lp', "l.id = lp.lesson_id AND lp.user_id = $userId", 'left');
        } else {
            // Add null values for progress fields when userId is not provided
            $builder->select("NULL as progress_id, NULL as status, NULL as progress_percentage, NULL as completed_at", false);
        }
        
        return $builder->get()->getResultArray();
    }
    // Get lesson with resources
    // public function getLessonWithResources($lessonId)
    // {
    //     $lesson = $this->find($lessonId);
        
    //     if (!$lesson) {
    //         return null;
    //     }
        
    //     $resourceModel = new \App\Models\LessonResourceModel();
    //     $lesson['resources'] = $resourceModel->where('lesson_id', $lessonId)->findAll();
        
    //     return $lesson;
    // }
    
    // Get next lesson
    public function getNextLesson($currentLessonId)
    {
        $currentLesson = $this->find($currentLessonId);
        
        if (!$currentLesson) {
            return null;
        }
        
        // Try to find next lesson in the same module
        $nextLesson = $this->where('module_id', $currentLesson['module_id'])
                           ->where('order_index >', $currentLesson['order_index'])
                           ->orderBy('order_index', 'ASC')
                           ->first();
        
        if ($nextLesson) {
            return $nextLesson;
        }
        
        // If no next lesson in the same module, find the first lesson of the next module
        $moduleModel = new \App\Models\ModuleModel();
        $currentModule = $moduleModel->find($currentLesson['module_id']);
        
        $nextModule = $moduleModel->where('course_id', $currentModule['course_id'])
                                  ->where('order_index >', $currentModule['order_index'])
                                  ->orderBy('order_index', 'ASC')
                                  ->first();
        
        if ($nextModule) {
            return $this->where('module_id', $nextModule['id'])
                        ->orderBy('order_index', 'ASC')
                        ->first();
        }
        
        return null;
    }
    
    // Get previous lesson
    public function getPreviousLesson($currentLessonId)
    {
        $currentLesson = $this->find($currentLessonId);
        
        if (!$currentLesson) {
            return null;
        }
        
        // Try to find previous lesson in the same module
        $prevLesson = $this->where('module_id', $currentLesson['module_id'])
                           ->where('order_index <', $currentLesson['order_index'])
                           ->orderBy('order_index', 'DESC')
                           ->first();
        
        if ($prevLesson) {
            return $prevLesson;
        }
        
        // If no previous lesson in the same module, find the last lesson of the previous module
        $moduleModel = new \App\Models\ModuleModel();
        $currentModule = $moduleModel->find($currentLesson['module_id']);
        
        $prevModule = $moduleModel->where('course_id', $currentModule['course_id'])
                                  ->where('order_index <', $currentModule['order_index'])
                                  ->orderBy('order_index', 'DESC')
                                  ->first();
        
        if ($prevModule) {
            return $this->where('module_id', $prevModule['id'])
                        ->orderBy('order_index', 'DESC')
                        ->first();
        }
        
        return null;
    }
}

