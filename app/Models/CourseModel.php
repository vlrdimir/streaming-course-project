<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'title', 'slug', 'description', 'short_description', 'thumbnail',
        'status', 'created_by', 'published_at', 'duration', 'level',
        'is_featured', 'is_premium', 'price_amount', 'price_currency', 'is_purchasable'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'slug' => 'required|max_length[255]|is_unique[courses.slug,id,{id}]',
        'status' => 'required|in_list[draft,published,private]',
        'level' => 'permit_empty|in_list[beginner,intermediate,advanced]',
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Course title is required',
        ],
        'slug' => [
            'required' => 'Course slug is required',
            'is_unique' => 'Course slug must be unique',
        ],
    ];
    
    protected $skipValidation = false;
    
    // Get published courses
    public function getPublishedCourses($limit = null, $offset = 0)
    {
        return $this->where('status', 'published')
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    // Get courses by category
    public function getCoursesByCategory($categoryId, $limit = null, $offset = 0)
    {
        return $this->select('courses.*')
                    ->join('course_categories', 'courses.id = course_categories.course_id')
                    ->where('course_categories.category_id', $categoryId)
                    ->where('courses.status', 'published')
                    ->orderBy('courses.created_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    // Get courses created by a specific user
    public function getCoursesByUser($userId, $limit = null, $offset = 0)
    {
        return $this->where('created_by', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    // Get featured courses
    public function getFeaturedCourses($limit = 6)
    {
        return $this->where('status', 'published')
                    ->where('is_featured', true)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }
    
    // Search courses
    public function searchCourses($keyword, $limit = null, $offset = 0)
    {
        return $this->like('title', $keyword)
                    ->orLike('description', $keyword)
                    ->where('status', 'published')
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    // Get courses with flexible filters
    public function getFilteredCourses($filters = [], $limit = null, $offset = 0)
    {
        $this->where('status', 'published');
        
        // Apply category filter if provided
        if (!empty($filters['category'])) {
            $this->select('courses.*')
                ->join('course_categories', 'courses.id = course_categories.course_id', 'left')
                ->where('course_categories.category_id', $filters['category']);
        }
        
        // Apply level filter if provided
        if (!empty($filters['level'])) {
            $this->where('level', $filters['level']);
        }
        
        // Apply keyword search if provided
        if (!empty($filters['keyword'])) {
            $this->groupStart()
                ->like('title', $filters['keyword'])
                ->orLike('description', $filters['keyword'])
                ->groupEnd();
        }
        
        return $this->orderBy('created_at', 'DESC')->findAll($limit, $offset);
    }
    
    // Get course with modules and lessons
    public function getCourseWithContent($courseId,$userId)
    {
        $course = $this->find($courseId);
        
        if (!$course) {
            return null;
        }
        
        $moduleModel = new \App\Models\ModuleModel();
        $course['modules'] = $moduleModel->getModulesWithLessons($courseId,$userId);
        
        return $course;
    }
    
    // Allow updates without full model validation on partial payloads.
    public function update($id = null, $data = null): bool
    {
        $this->skipValidation = true;
        
        $result = parent::update($id, $data);
        
        $this->skipValidation = false;
        
        return $result;
    }
}

