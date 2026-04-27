<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'user_id', 'course_id', 'completed_at', 'progress_percentage',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'enrolled_at';
    protected $updatedField = '';
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer',
    ];
    
    protected $skipValidation = false;
    
    // Check if user is enrolled in a course
    public function isEnrolled($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('is_active', true)
                    ->countAllResults() > 0;
    }
    
    // Get user enrollments with course details
    public function getUserEnrollments($userId, $limit = null, $offset = 0)
    {
        return $this->select('enrollments.*, courses.title, courses.slug, courses.thumbnail')
                    ->join('courses', 'enrollments.course_id = courses.id')
                    ->where('enrollments.user_id', $userId)
                    ->where('enrollments.is_active', true)
                    ->orderBy('enrollments.enrolled_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    // Get enrollment with progress details
    public function getEnrollmentWithProgress($userId, $courseId)
    {
        $enrollment = $this->where('user_id', $userId)
                           ->where('course_id', $courseId)
                           ->first();
        
        if (!$enrollment) {
            return null;
        }
        
        $lessonProgressModel = new \App\Models\LessonProgressModel();
        $enrollment['lesson_progress'] = $lessonProgressModel->getLessonProgressForCourse($userId, $courseId);
        
        return $enrollment;
    }
    
    // Update enrollment progress
    public function updateProgress($userId, $courseId)
    {
        $lessonProgressModel = new \App\Models\LessonProgressModel();
        $courseModel = new \App\Models\CourseModel();
        
        // Get course with modules and lessons
        $course = $courseModel->getCourseWithContent($courseId,$userId);
        
        if (!$course) {
            return false;
        }
        
        // Count total lessons
        $totalLessons = 0;
        $completedLessons = 0;
        
        foreach ($course['modules'] as $module) {
            $totalLessons += count($module['lessons']);
            
            foreach ($module['lessons'] as $lesson) {
                $progress = $lessonProgressModel->where('user_id', $userId)
                                               ->where('lesson_id', $lesson['id'])
                                               ->first();
                
                if ($progress && $progress['status'] === 'completed') {
                    $completedLessons++;
                }
            }
        }
        
        // Calculate progress percentage
        $progressPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;
        
        // Update enrollment
        $this->where('user_id', $userId)
             ->where('course_id', $courseId)
             ->set([
                 'progress_percentage' => $progressPercentage,
                 'completed_at' => ($progressPercentage >= 100) ? date('Y-m-d H:i:s') : null
             ])
             ->update();
        
        return true;
    }

    public function enrollUser($userId, $courseId)
    {

        if ($this->isEnrolled($userId, $courseId)) {
            return true;
        }

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'is_active' => true,
            'progress_percentage' => 0,
            'enrolled_at' => date('Y-m-d H:i:s'),
        ];


        
        $created = $this->insert($data);

        log_message('info', 'Enrollment created: ' . $created . json_encode($data));

        return $created;
    }
    
    // Get single enrollment record
    public function getEnrollment($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                   ->where('course_id', $courseId)
                   ->where('is_active', true)
                   ->first();
    }
}

