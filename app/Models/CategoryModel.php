<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'name', 'slug', 'description'
    ];
    
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[100]|is_unique[categories.slug,id,{id}]',
        'description' => 'permit_empty'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Category name is required',
        ],
        'slug' => [
            'required' => 'Category slug is required',
            'is_unique' => 'Category slug must be unique',
        ],
    ];
    
    protected $skipValidation = false;
    
    // Get categories for a specific course
    public function getCategoriesForCourse($courseId)
    {
        return $this->select('categories.*')
                    ->join('course_categories', 'categories.id = course_categories.category_id')
                    ->where('course_categories.course_id', $courseId)
                    ->findAll();
    }
}

