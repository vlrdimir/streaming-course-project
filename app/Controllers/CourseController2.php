<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\LessonProgressModel;
use App\Models\EnrollmentModel;

class CourseController2 extends BaseController
{
    protected $courseModel;
    protected $moduleModel;
    protected $lessonModel;
    protected $lessonProgressModel;
    protected $enrollmentModel;
    protected $db;
    
    // Current user from session
    protected $currentUser;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->moduleModel = new ModuleModel();
        $this->lessonModel = new LessonModel();
        $this->lessonProgressModel = new LessonProgressModel();
        $this->enrollmentModel = new EnrollmentModel();
        
        // Get user from session using the helper method
        $this->currentUser = $this->getCurrentUser();
        
        $this->db = \Config\Database::connect();
    }
    
    public function enroll($courseId) 
    {
        // Cek apakah course ada
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return $this->show404("Course tidak ditemukan");
        }

        // Cek apakah user sudah enroll sebelumnya
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $existingEnrollment = $this->enrollmentModel->getEnrollment($userId, $courseId);
      
        if ($existingEnrollment) {
            return redirect()->to("/course/$courseId");
        }

        if ($this->isTruthy($course['is_premium'] ?? false)) {
            $blockedMessage = 'Kursus premium ini belum tersedia untuk dibeli.';

            if (($course['status'] ?? null) === 'published' && $this->isTruthy($course['is_purchasable'] ?? false) && (int) ($course['price_amount'] ?? 0) > 0) {
                return redirect()->to(site_url('user/view-course/' . $courseId . '/checkout'));
            }

            if (($course['status'] ?? null) === 'published' && !$this->isTruthy($course['is_purchasable'] ?? false)) {
                $blockedMessage = 'Pembelian kursus premium ini sedang tidak tersedia.';
            }

            return redirect()->to(site_url('user/view-course/' . $courseId))->with('error', $blockedMessage);
        }

        $result = $this->enrollmentModel->enrollUser($userId, $courseId);

        // Jika enrollment gagal
        if (!$result) {
            return $this->show404("Gagal melakukan enrollment, silakan coba lagi");
        }

        return redirect()->to("/course/$courseId");
    }


    private function redirectUnenrolledCourseAccess(array $course, int $courseId)
    {
        if (!$this->isTruthy($course['is_premium'] ?? false)) {
            return $this->show404("Anda belum melakukan enrollment untuk course ini");
        }

        if (($course['status'] ?? null) === 'published' && $this->isTruthy($course['is_purchasable'] ?? false) && (int) ($course['price_amount'] ?? 0) > 0) {
            return redirect()->to(site_url('user/view-course/' . $courseId . '/checkout'))
                ->with('error', 'Kursus premium ini memerlukan pembayaran sebelum kamu bisa mulai belajar.');
        }

        return redirect()->to(site_url('user/view-course/' . $courseId))
            ->with('error', $this->buildPremiumAccessBlockedMessage($course));
    }

    private function buildPremiumAccessBlockedMessage(array $course): string
    {
        if (($course['status'] ?? null) !== 'published') {
            return 'Kursus premium ini belum dipublikasikan untuk pembelajaran.';
        }

        if (!$this->isTruthy($course['is_purchasable'] ?? false)) {
            return 'Pembelian kursus premium ini sedang tidak tersedia.';
        }

        if ((int) ($course['price_amount'] ?? 0) <= 0) {
            return 'Harga kursus premium ini belum siap untuk checkout.';
        }

        return 'Kursus premium ini memerlukan pembayaran sebelum kamu bisa mulai belajar.';
    }

    private function getCourseIdForLesson(int $lessonId): ?int
    {
        $lesson = $this->lessonModel->find($lessonId);

        if (!$lesson) {
            return null;
        }

        $module = $this->moduleModel->find($lesson['module_id']);

        if (!$module) {
            return null;
        }

        return isset($module['course_id']) ? (int) $module['course_id'] : null;
    }

    private function hasLearningAccess(int $userId, int $courseId): bool
    {
        return !empty($this->enrollmentModel->getEnrollment($userId, $courseId));
    }


    // as api
    public function markCourseCompleted($courseId,$lessonId) 
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        $resolvedCourseId = $this->getCourseIdForLesson((int) $lessonId);

        if ($resolvedCourseId === null || $resolvedCourseId !== (int) $courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lesson tidak ditemukan untuk course ini'
            ])->setStatusCode(404);
        }

        if (!$this->hasLearningAccess((int) $userId, (int) $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda belum memiliki akses ke kursus ini'
            ])->setStatusCode(403);
        }
        
        $this->lessonProgressModel->markAsCompleted($userId, $lessonId);
        $next = $this->lessonModel->getNextLesson($lessonId);

        // return response json
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lesson marked as complete',
            'lessonId' => $lessonId,
            'nextLesson' => $next
        ]);
    }

    // API endpoint untuk mendapatkan previous dan next lesson
    public function getLessonNavigation($courseId, $lessonId)
    {
        $previousLesson = $this->lessonModel->getPreviousLesson($lessonId);
        $nextLesson = $this->lessonModel->getNextLesson($lessonId);

        return $this->response->setJSON([
            'success' => true,
            'previousLesson' => $previousLesson ? [
                'id' => $previousLesson['id'],
                'title' => $previousLesson['title'],
                'moduleId' => $previousLesson['module_id']
            ] : null,
            'nextLesson' => $nextLesson ? [
                'id' => $nextLesson['id'],
                'title' => $nextLesson['title'],
                'moduleId' => $nextLesson['module_id']
            ] : null
        ]);
    }

    public function redirectCourse($courseId) 
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $course = $this->courseModel->find($courseId);

        if (!$course) {
            return $this->show404("Course tidak ditemukan");
        }
        
        $userCurrentProgress = $this->enrollmentModel->getEnrollmentWithProgress($userId, $courseId);
        
        // Cek apakah user sudah enroll
        if (empty($userCurrentProgress)) {
            return $this->redirectUnenrolledCourseAccess($course, (int) $courseId);
        }
        
        // Jika ada lesson_progress, gunakan lesson terakhir
        if (!empty($userCurrentProgress['lesson_progress'])) {
            $lessonProgress = $userCurrentProgress['lesson_progress'];
            $lastLesson = end($lessonProgress);
            $lessonId = $lastLesson['lesson_id'];
            
            return redirect()->to("/course/$courseId/lesson/$lessonId");
        }
        
        // Jika tidak ada lesson_progress, cari lesson pertama dari modul pertama
        $courseData = $this->courseModel->getCourseWithContent($courseId, $userId);
        
        if (empty($courseData) || empty($courseData['modules'])) {
            return $this->show404("Course tidak tersedia atau belum memiliki modul");
        }
        
        // Ambil modul pertama
        $firstModule = $courseData['modules'][0];
        
        if (empty($firstModule['lessons'])) {
            return $this->show404("Modul belum memiliki lesson");
        }
        
        // Ambil lesson pertama
        $firstLesson = $firstModule['lessons'][0];
        $lessonId = $firstLesson['id'];
        
        return redirect()->to("/course/$courseId/lesson/$lessonId");
    }

    public function courseById($courseId,$lessonId)
    {
        $moduleLib = new \App\Libraries\ModuleLib();
        
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $course = $this->courseModel->find($courseId);

        if (!$course) {
            return $this->show404("Course tidak ditemukan");
        }

        $userCurrentProgress = $this->enrollmentModel->getEnrollmentWithProgress($userId, $courseId);

        // Cek apakah user sudah melakukan enrollment
        if (empty($userCurrentProgress)) {
            return $this->redirectUnenrolledCourseAccess($course, (int) $courseId);
        }
    
        // Get course data
        $courseData = $this->courseModel->getCourseWithContent($courseId, $userId);
        
        // Cek apakah course ditemukan dan memiliki modul
        if (empty($courseData) || empty($courseData['modules'])) {
            return $this->show404("Course tidak tersedia atau belum memiliki modul");
        }

        // Find current lesson and its module
        $currentLesson = null;
        $currentModuleId = null;
        
        $modules = is_array($courseData['modules'] ?? null) ? $courseData['modules'] : [];

        foreach ($modules as $module) {
            $lessons = is_array($module['lessons'] ?? null) ? $module['lessons'] : [];

            foreach ($lessons as $lesson) {
                if ($lesson['id'] == $lessonId) {
                    $currentLesson = $lesson;
                    $currentModuleId = $module['id'];
                    break 2;
                }
            }
        }

        if (!$currentLesson) {
            return $this->show404("Lesson tidak ditemukan");
        }

        $currentLessonData = [
            'courseId' => $courseData['id'],
            'moduleId' => $currentModuleId,
            'lessonId' => $lessonId,
            'progress_percentage_course' => $userCurrentProgress['progress_percentage'],
            'title' => $currentLesson['title'],
            'videoUrl' => $currentLesson['video_url'],
            'content' => $currentLesson['content'],
            'status' => $currentLesson['status']
        ];

        // Pass data to the view
        return view('course2/index', [
            'courseData' => $courseData,
            'currentLesson' => $currentLessonData,
            'moduleLib' => $moduleLib,
        ]);
    }

    public function index()
    {
        // Get course data
        $courseData = $this->getCourseData();
        
        // Get current lesson
        $currentLesson = [
            'moduleId' => 2,
            'lessonId' => 6, // CSS Box Model lesson
            'title' => 'CSS Box Model',
            'videoUrl' => 'https://www.youtube.com/watch?v=hP6pQ51yIcU'
        ];
        
        // Pass data to the view
        return view('course2/index', [
            'courseData' => $courseData,
            'currentLesson' => $currentLesson,
            'user' => $this->currentUser
        ]);
    }
    
    public function lesson($moduleId, $lessonId)
    {
        // Get course data
        $courseData = $this->getCourseData();
    
        // Find the specific lesson
        $currentModule = null;
        $currentLesson = null;
        
        foreach ($courseData['modules'] as $module) {
            if ($module['id'] == $moduleId) {
                $currentModule = $module;
                foreach ($module['lessons'] as $lesson) {
                    if ($lesson['id'] == $lessonId) {
                        $currentLesson = $lesson;
                        break;
                    }
                }
                break;
            }
        }
        
        if (!$currentLesson) {
            return redirect()->to('/course');
        }
        
        // Mark lesson as started (if user is logged in)
        if ($this->currentUser) {
            $this->lessonProgressModel->markAsStarted($this->currentUser['id'], $lessonId);
        }
        
        return view('course2/index', [
            'courseData' => $courseData,
            'currentLesson' => [
                'moduleId' => $moduleId,
                'lessonId' => $lessonId,
                'title' => $currentLesson['title'],
                'videoUrl' => $currentLesson['videoUrl']
            ],
            'user' => $this->currentUser
        ]);
    }
    
    public function markComplete()
    {
        $lessonId = $this->request->getPost('lesson_id');
        
        if (!$lessonId || !$this->currentUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request or user not logged in'
            ]);
        }

        $courseId = $this->getCourseIdForLesson((int) $lessonId);

        if ($courseId === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lesson not found'
            ])->setStatusCode(404);
        }

        if (!$this->hasLearningAccess((int) $this->currentUser['id'], $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda belum memiliki akses ke kursus ini'
            ])->setStatusCode(403);
        }
        
        // Mark lesson as completed
        $this->lessonProgressModel->markAsCompleted($this->currentUser['id'], $lessonId);
        
        // Get the lesson to find its module and course
        $lesson = $this->lessonModel->find($lessonId);
        if (!$lesson) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lesson not found'
            ]);
        }
        
        // Get the module to find its course
        $module = $this->moduleModel->find($lesson['module_id']);
        if (!$module) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Module not found'
            ]);
        }
        
        // Update enrollment progress
        $this->enrollmentModel->updateProgress($this->currentUser['id'], $module['course_id']);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lesson marked as complete',
            'lessonId' => $lessonId
        ]);
    }
    
    public function updateProgress()
    {
        $lessonId = $this->request->getPost('lesson_id');
        $position = $this->request->getPost('position');
        
        if (!$lessonId || !$this->currentUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request or user not logged in'
            ]);
        }

        $courseId = $this->getCourseIdForLesson((int) $lessonId);

        if ($courseId === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lesson not found'
            ])->setStatusCode(404);
        }

        if (!$this->hasLearningAccess((int) $this->currentUser['id'], $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda belum memiliki akses ke kursus ini'
            ])->setStatusCode(403);
        }
        
        // Update video position : Deleted
        // $this->lessonProgressModel->updatePosition($this->currentUser['id'], $lessonId, $position);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Progress updated',
            'lessonId' => $lessonId,
            'position' => $position
        ]);
    }
   
    
    private function getCourseData()
    {
        // In a real app, this would come from the database
        // For now, we'll use hardcoded data that matches our database schema
        return [
            'title' => 'Complete Web Development Bootcamp',
            'description' => 'Master modern web development with this comprehensive course',
            'progress' => 35,
            'modules' => [
                [
                    'id' => 1,
                    'title' => 'Getting Started with Web Development',
                    'completed' => true,
                    'lessons' => [
                        ['id' => 1, 'title' => 'Introduction to Web Development', 'duration' => '10:25', 'completed' => true, 'videoUrl' => 'https://www.youtube.com/watch?v=UB1O30fR-EE'],
                        ['id' => 2, 'title' => 'Setting Up Your Development Environment', 'duration' => '15:30', 'completed' => true, 'videoUrl' => 'https://www.youtube.com/watch?v=MpGLUVbqoYQ'],
                        ['id' => 3, 'title' => 'Understanding HTML Basics', 'duration' => '12:45', 'completed' => true, 'videoUrl' => 'https://www.youtube.com/watch?v=qz0aGYrrlhU'],
                    ]
                ],
                [
                    'id' => 2,
                    'title' => 'CSS Fundamentals',
                    'completed' => false,
                    'lessons' => [
                        ['id' => 4, 'title' => 'Introduction to CSS', 'duration' => '14:20', 'completed' => true, 'videoUrl' => 'https://www.youtube.com/watch?v=1PnVor36_40'],
                        ['id' => 5, 'title' => 'CSS Selectors and Properties', 'duration' => '18:15', 'completed' => true, 'videoUrl' => 'https://www.youtube.com/watch?v=FHZn6706e3Q'],
                        ['id' => 6, 'title' => 'CSS Box Model', 'duration' => '16:40', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=hP6pQ51yIcU'],
                        ['id' => 7, 'title' => 'Flexbox and Grid Layout', 'duration' => '22:10', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=JJSoEo8JSnc'],
                    ]
                ],
                [
                    'id' => 3,
                    'title' => 'JavaScript Essentials',
                    'completed' => false,
                    'lessons' => [
                        ['id' => 8, 'title' => 'Introduction to JavaScript', 'duration' => '15:30', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk'],
                        ['id' => 9, 'title' => 'Variables and Data Types', 'duration' => '17:45', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=heV-2QtMw0g'],
                        ['id' => 10, 'title' => 'Functions and Scope', 'duration' => '20:15', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=WZSUsfJjJ3I'],
                        ['id' => 11, 'title' => 'DOM Manipulation', 'duration' => '25:30', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=iPUm-9-X1MQ'],
                    ]
                ],
                [
                    'id' => 4,
                    'title' => 'Building Real Projects',
                    'completed' => false,
                    'lessons' => [
                        ['id' => 12, 'title' => 'Project Planning and Setup', 'duration' => '12:20', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=j9_xW9xwz3U'],
                        ['id' => 13, 'title' => 'Building a Responsive Website', 'duration' => '28:45', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=DuJyOxW4Hrc'],
                        ['id' => 14, 'title' => 'Creating Interactive Elements', 'duration' => '24:10', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=2wte_JYEgRQ'],
                        ['id' => 15, 'title' => 'Deployment and Publishing', 'duration' => '18:35', 'completed' => false, 'videoUrl' => 'https://www.youtube.com/watch?v=umtNfihhQyg'],
                    ]
                ]
            ]
        ];
    }

    // Menambahkan helper method untuk menampilkan halaman 404
    private function show404($message = "Halaman tidak ditemukan")
    {
        return view('errors/html/error_404', [
            'message' => $message
        ]);
    }

    private function isTruthy($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'TRUE', 't', 'T'], true);
    }
}

