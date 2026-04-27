<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\UserModel;
use App\Models\CourseModel;

class EnrollmentController extends BaseController
{
    protected $enrollmentModel;
    protected $userModel;
    protected $courseModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
    }

    public function index()
    {
        // Get enrollments with user and course details
        $enrollments = $this->enrollmentModel->select('enrollments.*, users.username, users.email, courses.title as course_title')
            ->join('users', 'enrollments.user_id = users.id')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->orderBy('enrollments.enrolled_at', 'DESC')
            ->findAll();

        $data = [
            'enrollments' => $enrollments
        ];

        log_message('debug', 'Enrollments: ' . json_encode($enrollments));

        return view('admin/enrollments/index', $data);
    }

    public function show($id)
    {
        $enrollment = $this->enrollmentModel->select('enrollments.*, users.username, users.email, users.full_name, courses.title as course_title, courses.slug as course_slug')
            ->join('users', 'enrollments.user_id = users.id')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->where('enrollments.id', $id)
            ->first();

        if (!$enrollment) {
            return redirect()->to('/admin/enrollments')->with('error', 'Enrollment not found');
        }

        // Get lesson progress for this enrollment
        $db = \Config\Database::connect();
        $lessonProgress = $db->query("
            SELECT lp.*, l.title as lesson_title, m.title as module_title
            FROM lesson_progress lp
            JOIN lessons l ON lp.lesson_id = l.id
            JOIN modules m ON l.module_id = m.id
            WHERE lp.user_id = ? AND m.course_id = ?
            ORDER BY m.order_index, l.order_index
        ", [$enrollment['user_id'], $enrollment['course_id']])->getResultArray();

        $data = [
            'enrollment' => $enrollment,
            'lessonProgress' => $lessonProgress
        ];

        return view('admin/enrollments/show', $data);
    }
}
