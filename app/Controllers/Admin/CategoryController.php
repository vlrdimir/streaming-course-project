<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'categories' => $this->categoryModel->findAll()
        ];

        return view('admin/categories/index', $data);
    }

    public function create()
    {
        return view('admin/categories/create');
    }

    public function store()
    {
        // Validate form input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'slug' => 'required|alpha_dash|min_length[3]|max_length[100]|is_unique[categories.slug]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare category data
        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description')
        ];

        // Insert category
        $this->categoryModel->insert($categoryData);

        return redirect()->to('/admin/categories')->with('message', 'Category created successfully');
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        $data = [
            'category' => $category
        ];

        log_message('info', json_encode($data));

        return view('admin/categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        // Validate form input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'slug' => "required|alpha_dash|min_length[3]|max_length[100]|is_unique[categories.slug,id,$id]",
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare category data
        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description')
        ];

        // Update category
        $this->categoryModel->update($id, $categoryData);

        return redirect()->to('/admin/categories')->with('message', 'Category updated successfully');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        // Delete category
        $this->categoryModel->delete($id);

        return redirect()->to('/admin/categories')->with('message', 'Category deleted successfully');
    }
}
