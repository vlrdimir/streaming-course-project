<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\EnrollmentModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $courseModel;
    protected $moduleModel;
    protected $lessonModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->moduleModel = new ModuleModel();
        $this->lessonModel = new LessonModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
    
    public function index()
    {
        // Get statistics for dashboard
        $data = [
            'totalUsers' => $this->userModel->countAll(),
            'totalCourses' => $this->courseModel->countAll(),
            'totalModules' => $this->moduleModel->countAll(),
            'totalLessons' => $this->lessonModel->countAll(),
            'totalEnrollments' => $this->enrollmentModel->countAll(),
            'recentCourses' => $this->courseModel->orderBy('created_at', 'DESC')->findAll(5),
            'recentUsers' => $this->userModel->orderBy('created_at', 'DESC')->findAll(5)
        ];
        
        // Calculate course status counts
        $data['publishedCourses'] = $this->courseModel->where('status', 'published')->countAllResults();
        $data['privateCourses'] = $this->courseModel->where('status', 'private')->countAllResults();
        $data['draftCourses'] = $this->courseModel->where('status', 'draft')->countAllResults();
        
        return view('admin/dashboard', $data);
    }
}

