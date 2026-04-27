<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;

class LessonController extends BaseController
{
    protected $courseModel;
    protected $moduleModel;
    protected $lessonModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->moduleModel = new ModuleModel();
        $this->lessonModel = new LessonModel();
    }
    
    public function index($courseId, $moduleId)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        $lessons = $this->lessonModel->where('module_id', $moduleId)
                                    ->orderBy('order_index', 'ASC')
                                    ->findAll();
        
        $data = [
            'course' => $course,
            'module' => $module,
            'lessons' => $lessons
        ];
        
        return view('admin/modules/lessons/index', $data);
    }
    
    public function create($courseId, $moduleId)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        $data = [
            'course' => $course,
            'module' => $module
        ];
        
        return view('admin/modules/lessons/create', $data);
    }
    
    public function store($courseId, $moduleId)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        // Validate form input
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'content' => 'permit_empty',
            'video_url' => 'required|valid_url',
            'video_duration' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get the highest order_index for this module
        $highestOrder = $this->lessonModel->selectMax('order_index')
                                         ->where('module_id', $moduleId)
                                         ->first();
        
        $nextOrder = isset($highestOrder['order_index']) ? $highestOrder['order_index'] + 1 : 0;
        
        // Prepare lesson data
        $lessonData = [
            'module_id' => $moduleId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'content' => $this->request->getPost('content'),
            'video_url' => $this->request->getPost('video_url'),
            'video_duration' => $this->request->getPost('video_duration'),
            'order_index' => $nextOrder
        ];
        
        // Insert lesson
        $this->lessonModel->insert($lessonData);
        
        return redirect()->to("/admin/course/{$courseId}/modules/{$moduleId}/lessons")->with('message', 'Lesson created successfully');
    }
    
    public function edit($courseId, $moduleId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        $lesson = $this->lessonModel->find($id);
        
        if (!$course || !$module || !$lesson || $module['course_id'] != $courseId || $lesson['module_id'] != $moduleId) {
            return redirect()->to('/admin/course')->with('error', 'Course, module, or lesson not found');
        }
        
        $data = [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson
        ];
        
        return view('admin/modules/lessons/edit', $data);
    }
    
    public function update($courseId, $moduleId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        $lesson = $this->lessonModel->find($id);
        
        if (!$course || !$module || !$lesson || $module['course_id'] != $courseId || $lesson['module_id'] != $moduleId) {
            return redirect()->to('/admin/course')->with('error', 'Course, module, or lesson not found');
        }
        
        // Validate form input
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'content' => 'permit_empty',
            'video_url' => 'required|valid_url',
            'video_duration' => 'required|integer'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare lesson data
        $lessonData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'content' => $this->request->getPost('content'),
            'video_url' => $this->request->getPost('video_url'),
            'video_duration' => $this->request->getPost('video_duration')
        ];
        
        // Update lesson
        $this->lessonModel->update($id, $lessonData);
        
        return redirect()->to("/admin/course/{$courseId}/modules/{$moduleId}/lessons")->with('message', 'Lesson updated successfully');
    }
    
    public function delete($courseId, $moduleId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        $lesson = $this->lessonModel->find($id);
        
        if (!$course || !$module || !$lesson || $module['course_id'] != $courseId || $lesson['module_id'] != $moduleId) {
            return redirect()->to('/admin/course')->with('error', 'Course, module, or lesson not found');
        }
        
        // Delete lesson
        $this->lessonModel->delete($id);
        
        return redirect()->to("/admin/course/{$courseId}/modules/{$moduleId}/lessons")->with('message', 'Lesson deleted successfully');
    }
    
    public function reorder($courseId, $moduleId)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($moduleId);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course or module not found']);
        }
        
        // Get JSON data from request body
        $json = $this->request->getJSON();
        $lessonOrder = $json->lessons ?? null;
        
        if (!$lessonOrder) {
            return $this->response->setJSON(['success' => false, 'message' => 'No lesson order provided']);
        }
        
        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            foreach ($lessonOrder as $index => $lessonId) {
                $this->lessonModel->update($lessonId, ['order_index' => $index]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to reorder lessons']);
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Lessons reordered successfully']);
        } catch (\Exception $e) {
            log_message('error', 'Error reordering lessons: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An error occurred while reordering lessons']);
        }
    }
}

