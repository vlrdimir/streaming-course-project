<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CategoryModel;
use App\Models\CourseReviewModel;

class CourseController extends BaseController
{
    protected $courseModel;
    protected $categoryModel;
    protected $courseReviewModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->categoryModel = new CategoryModel();
        $this->courseReviewModel = new CourseReviewModel();
    }

    private function getCourseValidationRules(?int $id = null): array
    {
        $slugRule = $id === null
            ? 'required|alpha_dash|min_length[3]|max_length[255]|is_unique[courses.slug]'
            : "required|alpha_dash|min_length[3]|max_length[255]|is_unique[courses.slug,id,$id]";

        $isPremium = (bool) $this->request->getPost('is_premium');

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => $slugRule,
            'description' => 'required',
            'status' => 'required|in_list[draft,published,private]',
            'level' => 'required|in_list[beginner,intermediate,advanced]',
            'duration' => 'permit_empty|integer',
        ];

        $rules['price_amount'] = $isPremium
            ? 'required|integer|greater_than[0]'
            : 'permit_empty|integer';

        $rules['price_currency'] = $isPremium
            ? 'required|regex_match[/^[A-Za-z]{3,10}$/]'
            : 'permit_empty|regex_match[/^[A-Za-z]{3,10}$/]';

        return $rules;
    }

    private function getCourseValidationMessages(): array
    {
        return [
            'price_amount' => [
                'required' => 'Price is required when a course is marked as premium.',
                'integer' => 'Price must be a whole number.',
                'greater_than' => 'Price must be greater than 0.',
            ],
            'price_currency' => [
                'required' => 'Currency is required when a course is marked as premium.',
                'regex_match' => 'Price currency must use a 3-10 letter currency code.',
            ],
        ];
    }

    private function getMonetizationData(): array
    {
        $isPremium = (bool) $this->request->getPost('is_premium');
        $priceCurrency = strtoupper(trim((string) $this->request->getPost('price_currency')));

        return [
            'is_premium' => $isPremium ? 'true' : 'false',
            'price_amount' => $isPremium ? (int) $this->request->getPost('price_amount') : null,
            'price_currency' => $isPremium ? $priceCurrency : 'IDR',
            'is_purchasable' => $isPremium && (bool) $this->request->getPost('is_purchasable') ? 'true' : 'false',
        ];
    }

    private function insertCourse(array $courseData): int
    {
        $db = \Config\Database::connect();

        $query = $db->query(
            'INSERT INTO courses (
                title,
                slug,
                description,
                short_description,
                status,
                level,
                duration,
                thumbnail,
                created_by,
                is_featured,
                is_premium,
                price_amount,
                price_currency,
                is_purchasable,
                published_at,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CAST(? AS BOOLEAN), CAST(? AS BOOLEAN), ?, ?, CAST(? AS BOOLEAN), ?, NOW(), NOW()) RETURNING id',
            [
                $courseData['title'],
                $courseData['slug'],
                $courseData['description'],
                $courseData['short_description'],
                $courseData['status'],
                $courseData['level'],
                $courseData['duration'],
                $courseData['thumbnail'],
                $courseData['created_by'],
                $courseData['is_featured'],
                $courseData['is_premium'],
                $courseData['price_amount'],
                $courseData['price_currency'],
                $courseData['is_purchasable'],
                $courseData['published_at'],
            ]
        );

        return (int) ($query->getRowArray()['id'] ?? 0);
    }

    private function updateCourseRecord(int $id, array $courseData): bool
    {
        $db = \Config\Database::connect();

        $sql = 'UPDATE courses SET
            title = ?,
            slug = ?,
            description = ?,
            short_description = ?,
            status = ?,
            level = ?,
            duration = ?,
            is_featured = CAST(? AS BOOLEAN),
            is_premium = CAST(? AS BOOLEAN),
            price_amount = ?,
            price_currency = ?,
            is_purchasable = CAST(? AS BOOLEAN),
            published_at = ?,
            updated_at = NOW()';

        $params = [
            $courseData['title'],
            $courseData['slug'],
            $courseData['description'],
            $courseData['short_description'],
            $courseData['status'],
            $courseData['level'],
            $courseData['duration'],
            $courseData['is_featured'],
            $courseData['is_premium'],
            $courseData['price_amount'],
            $courseData['price_currency'],
            $courseData['is_purchasable'],
            $courseData['published_at'],
        ];

        if (array_key_exists('thumbnail', $courseData)) {
            $sql .= ', thumbnail = ?';
            $params[] = $courseData['thumbnail'];
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        return (bool) $db->query($sql, $params);
    }
    
    public function index()
    {
        $courses = $this->courseModel->orderBy('created_at', 'DESC')->findAll();
        
        // Ambil rating untuk setiap course
        foreach ($courses as &$course) {
            $course['rating'] = $this->courseReviewModel->getAverageRating($course['id']) ?? 0;
        }
        
        $data = [
            'courses' => $courses
        ];
        
        return view('admin/course/index', $data);
    }
    
    public function create()
    {
        $data = [
            'categories' => $this->categoryModel->findAll()
        ];
        
        return view('admin/course/create', $data);
    }
    
    public function store()
    {
        // Validate form input
        $rules = $this->getCourseValidationRules();
        $rules['thumbnail'] = 'uploaded[thumbnail]|max_size[thumbnail,2048]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]';
        
        if (!$this->validate($rules, $this->getCourseValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Handle thumbnail upload
        $thumbnail = $this->request->getFile('thumbnail');
        $thumbnailName = $thumbnail->getRandomName();
        $thumbnail->move(ROOTPATH . 'public/uploads/thumbnails', $thumbnailName);
        
        // Prepare course data
        $courseData = [
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'short_description' => $this->request->getPost('short_description'),
            'status' => $this->request->getPost('status'),
            'level' => $this->request->getPost('level'),
            'duration' => $this->request->getPost('duration') ?: 0,
            'thumbnail' => 'uploads/thumbnails/' . $thumbnailName,
            'created_by' => $this->getCurrentUserId(),
            'is_featured' => $this->request->getPost('is_featured') ? 'true' : 'false',
            'published_at' => $this->request->getPost('status') === 'published' ? date('Y-m-d H:i:s') : null
        ];

        $courseData = array_merge($courseData, $this->getMonetizationData());
        
        // Insert course
        $courseId = $this->insertCourse($courseData);
        
        // Handle categories
        $categories = $this->request->getPost('categories') ?: [];
        $db = \Config\Database::connect();
        foreach ($categories as $categoryId) {
            $db->table('course_categories')->insert([
                'course_id' => $courseId,
                'category_id' => $categoryId
            ]);
        }
        
        return redirect()->to('/admin/course')->with('message', 'Course created successfully');
    }
    
    public function edit($id)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        // Get course categories
        $db = \Config\Database::connect();
        $courseCategories = $db->table('course_categories')
                              ->where('course_id', $id)
                              ->get()
                              ->getResultArray();
        
        $selectedCategories = array_column($courseCategories, 'category_id');
        
        $data = [
            'course' => $course,
            'categories' => $this->categoryModel->findAll(),
            'selectedCategories' => $selectedCategories
        ];
        
        return view('admin/course/edit', $data);
    }
    
    public function update($id)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course not found'
                ])->setStatusCode(404);
            }
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        // Validate form input
        $rules = $this->getCourseValidationRules((int) $id);
        $rules['thumbnail'] = 'permit_empty|max_size[thumbnail,2048]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]';
        
        if (!$this->validate($rules, $this->getCourseValidationMessages())) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        try {
            // Prepare course data
            $courseData = [
                'title' => $this->request->getPost('title'),
                'slug' => $this->request->getPost('slug'),
                'description' => $this->request->getPost('description'),
                'short_description' => $this->request->getPost('short_description'),
                'status' => $this->request->getPost('status'),
                'level' => $this->request->getPost('level'),
                'duration' => $this->request->getPost('duration') ?: 0,
                'is_featured' => $this->request->getPost('is_featured') ? 'true' : 'false',
                'published_at' => $course['published_at'],
            ];

            $courseData = array_merge($courseData, $this->getMonetizationData());
            
            // Handle thumbnail upload if provided
            $thumbnail = $this->request->getFile('thumbnail');
            if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
                $thumbnailName = $thumbnail->getRandomName();
                $thumbnail->move(ROOTPATH . 'public/uploads/thumbnails', $thumbnailName);
                $courseData['thumbnail'] = 'uploads/thumbnails/' . $thumbnailName;
            }
            
            // Update published_at if status changed to published
            if ($this->request->getPost('status') === 'published' && $course['status'] !== 'published') {
                $courseData['published_at'] = date('Y-m-d H:i:s');
            }
            
            // Update course
            $this->updateCourseRecord((int) $id, $courseData);
            
            // Handle categories
            $categories = $this->request->getPost('categories') ?: [];
            $db = \Config\Database::connect();
            
            // Delete existing categories
            $db->table('course_categories')->where('course_id', $id)->delete();
            
            // Insert new categories
            foreach ($categories as $categoryId) {
                $db->table('course_categories')->insert([
                    'course_id' => $id,
                    'category_id' => $categoryId
                ]);
            }
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Course updated successfully',
                    'data' => $courseData // Tambahkan data yang diupdate ke response
                ]);
            }
            
            return redirect()->to('/admin/course')->with('message', 'Course updated successfully');
            
        } catch (\Exception $e) {
            // Debug: Log error
            log_message('error', 'Error updating course: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update course',
                    'error' => $e->getMessage()
                ])->setStatusCode(500);
            }
            return redirect()->back()->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }
    
    public function delete($id)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        // Delete course
        $this->courseModel->delete($id);
        
        return redirect()->to('/admin/course')->with('message', 'Course deleted successfully');
    }
}
