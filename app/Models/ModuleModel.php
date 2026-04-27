<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table = 'modules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'course_id', 'title', 'description', 'order_index'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'course_id' => 'required|integer',
        'title' => 'required|max_length[255]',
        'order_index' => 'required|integer',
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Module title is required',
        ],
    ];
    
    protected $skipValidation = false;
    
    // Get modules for a course
    public function getModulesByCourse($courseId)
    {
      
        return $this->where('course_id', $courseId)
                    ->orderBy('order_index', 'ASC')
                    ->findAll();
    }
    
    // Get modules with lessons
    public function getModulesWithLessons($courseId, $userId)
    {
        $modules = $this->getModulesByCourse($courseId);
        
        if (empty($modules)) {
            return [];
        }
        
        $lessonModel = new \App\Models\LessonModel();
        
        foreach ($modules as &$module) {
            $module['lessons'] = $lessonModel->getLessonsByModule($module['id'],$userId);
        }
        
        return $modules;
    }
    
    // Reorder modules
    public function reorderModules($courseId, $moduleOrder)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            foreach ($moduleOrder as $index => $moduleId) {
                $this->update($moduleId, ['order_index' => $index]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error reordering modules: ' . $e->getMessage());
            return false;
        }
    }
}

