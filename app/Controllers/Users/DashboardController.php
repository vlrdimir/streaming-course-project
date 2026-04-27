<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\LessonProgressModel;

class DashboardController extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $lessonProgressModel;
    protected $currentUser;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->lessonProgressModel = new LessonProgressModel();
        
        // Get user from session using the helper method
        $this->currentUser = $this->getCurrentUser();
    }
    
    public function index()
    {
        $userId = $this->currentUser['id'];

       
        log_message('info', json_encode(session()->get('username')));
        
        // $userId = 1;
        
        // Get user's enrolled courses
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);
        
        // Filter courses that are not completed (progress < 100%) for continue learning section
        $continueLearningCourses = array_filter($enrolledCourses, function($course) {
            return $course['progress_percentage'] < 100;
        });
        
        // Get only 3 courses for continue learning section
        $recentCourses = array_slice($continueLearningCourses, 0, 3);
        
        // Count completed lessons
        $completedLessons = $this->lessonProgressModel
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->countAllResults();
        
        // Count in-progress courses
        $inProgressCount = 0;
        $completedCount = 0;
        
        foreach ($enrolledCourses as $course) {
            if ($course['progress_percentage'] == 100) {
                $completedCount++;
            } else if ($course['progress_percentage'] > 0) {
                $inProgressCount++;
            }
        }
        

        return view('user/dashboard', [
            'user' => $this->currentUser,
            'enrolledCourses' => $enrolledCourses,
            'recentCourses' => $recentCourses,
            'completedLessons' => $completedLessons,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'totalEnrolled' => count($enrolledCourses)
        ]);
    }
}
