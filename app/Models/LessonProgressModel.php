<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonProgressModel extends Model
{
    protected $table = 'lesson_progress';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'user_id', 'lesson_id', 'status', 'progress_percentage',
        'started_at', 'completed_at'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'lesson_id' => 'required|integer',
        'status' => 'required|in_list[not_started,in_progress,completed]',
    ];
    
    protected $skipValidation = false;
    
    // Get lesson progress for a user
    public function getLessonProgress($userId, $lessonId)
    {
        return $this->where('user_id', $userId)
                    ->where('lesson_id', $lessonId)
                    ->first();
    }
    
    // Get all lesson progress for a course
    public function getLessonProgressForCourse($userId, $courseId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT lp.*
            FROM lesson_progress lp
            JOIN lessons l ON lp.lesson_id = l.id
            JOIN modules m ON l.module_id = m.id
            WHERE lp.user_id = ? AND m.course_id = ?
        ", [$userId, $courseId]);
        
        return $query->getResultArray();
    }
    
    // Mark lesson as started
    public function markAsStarted($userId, $lessonId)
    {
        $existing = $this->getLessonProgress($userId, $lessonId);
        
        if ($existing) {
            if ($existing['status'] === 'not_started') {
                $this->update($existing['id'], [
                    'status' => 'in_progress',
                    'started_at' => date('Y-m-d H:i:s')
                ]);
            }
            return $existing['id'];
        } else {
            return $this->insert([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'status' => 'in_progress',
                'progress_percentage' => 0,
                'started_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // Mark lesson as completed
    public function markAsCompleted($userId, $lessonId)
    {
        $existing = $this->getLessonProgress($userId, $lessonId);
        
        if ($existing) {
            $this->update($existing['id'], [
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Update enrollment progress
            $lessonModel = new \App\Models\LessonModel();
            $lesson = $lessonModel->find($lessonId);
            
            if ($lesson) {
                $moduleModel = new \App\Models\ModuleModel();
                $module = $moduleModel->find($lesson['module_id']);
                
                if ($module) {
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $enrollmentModel->updateProgress($userId, $module['course_id']);
                }
            }
            
            return $existing['id'];
        } else {
            $id = $this->insert([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'status' => 'completed',
                'progress_percentage' => 100,
                'started_at' => date('Y-m-d H:i:s'),
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Update enrollment progress
            $lessonModel = new \App\Models\LessonModel();
            $lesson = $lessonModel->find($lessonId);
            
            if ($lesson) {
                $moduleModel = new \App\Models\ModuleModel();
                $module = $moduleModel->find($lesson['module_id']);
                
                if ($module) {
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $enrollmentModel->updateProgress($userId, $module['course_id']);
                }
            }
            
            return $id;
        }
    }
}

