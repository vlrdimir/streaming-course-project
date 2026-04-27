<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\ModuleModel;

class ModuleController extends BaseController
{
    protected $courseModel;
    protected $moduleModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->moduleModel = new ModuleModel();
    }
    
    public function index($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        $modules = $this->moduleModel->where('course_id', $courseId)
                                    ->orderBy('order_index', 'ASC')
                                    ->findAll();
        
        $data = [
            'course' => $course,
            'modules' => $modules
        ];
        
        return view('admin/modules/index', $data);
    }
    
    public function create($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        $data = [
            'course' => $course
        ];
        
        return view('admin/modules/create', $data);
    }
    
    public function store($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        // Validate form input
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get the highest order_index for this course
        $highestOrder = $this->moduleModel->selectMax('order_index')
                                         ->where('course_id', $courseId)
                                         ->first();
        
        $nextOrder = isset($highestOrder['order_index']) ? $highestOrder['order_index'] + 1 : 0;
        
        // Prepare module data
        $moduleData = [
            'course_id' => $courseId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'order_index' => $nextOrder
        ];
        
        // Insert module
        $this->moduleModel->insert($moduleData);
        
        return redirect()->to("/admin/course/{$courseId}/modules")->with('message', 'Module created successfully');
    }
    
    public function edit($courseId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($id);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        $data = [
            'course' => $course,
            'module' => $module
        ];
        
        return view('admin/modules/edit', $data);
    }
    
    public function update($courseId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($id);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        // Validate form input
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare module data
        $moduleData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description')
        ];
        
        // Update module
        $this->moduleModel->update($id, $moduleData);
        
        return redirect()->to("/admin/course/{$courseId}/modules")->with('message', 'Module updated successfully');
    }
    
    public function delete($courseId, $id)
    {
        $course = $this->courseModel->find($courseId);
        $module = $this->moduleModel->find($id);
        
        if (!$course || !$module || $module['course_id'] != $courseId) {
            return redirect()->to('/admin/course')->with('error', 'Course or module not found');
        }
        
        // Delete module
        $this->moduleModel->delete($id);
        
        return redirect()->to("/admin/course/{$courseId}/modules")->with('message', 'Module deleted successfully');
    }
    
    public function reorder($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }
        
        // Get JSON data from request body
        $json = $this->request->getJSON();
        $moduleOrder = $json->modules ?? null;
        
        if (!$moduleOrder) {
            return $this->response->setJSON(['success' => false, 'message' => 'No module order provided']);
        }
        
        // Update module order
        $success = $this->moduleModel->reorderModules($courseId, $moduleOrder);
        
        if ($success) {
            return $this->response->setJSON(['success' => true, 'message' => 'Modules reordered successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to reorder modules']);
        }
    }
}

