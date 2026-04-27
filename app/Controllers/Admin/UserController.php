<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;
    protected array $allRoles = ['user', 'admin', 'super_admin'];
    protected array $privilegedRoles = ['admin', 'super_admin'];
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        $data = [
            'users' => $this->userModel->findAll(),
            'canManagePrivilegedRoles' => $this->canManagePrivilegedRoles(),
        ];
        
        return view('admin/users/index', $data);
    }
    
    public function create()
    {
        return view('admin/users/create', [
            'assignableRoles' => $this->getAssignableRoles(),
            'canManagePrivilegedRoles' => $this->canManagePrivilegedRoles(),
        ]);
    }
    
    public function store()
    {
        $assignableRoles = $this->getAssignableRoles();

        // Validate form input
        $rules = [
            'username' => 'required|regex_match[/^[A-Za-z0-9][A-Za-z0-9\- ]*[A-Za-z0-9]$/]|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'matches[password]',
            'full_name' => 'permit_empty|max_length[100]',
            'role' => 'required|in_list[' . implode(',', $assignableRoles) . ']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $requestedRole = $this->request->getPost('role');
        if (!in_array($requestedRole, $assignableRoles, true)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki izin untuk membuat user dengan role tersebut.');
        }
        
        // Prepare user data
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $requestedRole,
            'bio' => $this->request->getPost('bio')
        ];
        
        // Insert user
        $this->userModel->insert($userData);
        
        return redirect()->to('/admin/users')->with('message', 'User created successfully');
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        if (!$this->canManageUser($user)) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak memiliki izin untuk mengubah user dengan role tersebut.');
        }
        
        $data = [
            'user' => $user,
            'assignableRoles' => $this->getAssignableRoles(),
            'canManagePrivilegedRoles' => $this->canManagePrivilegedRoles(),
        ];
        
        return view('admin/users/edit', $data);
    }
    
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        if (!$this->canManageUser($user)) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak memiliki izin untuk mengubah user dengan role tersebut.');
        }

        $assignableRoles = $this->getAssignableRoles();
        
        // Validate form input
        $rules = [
            'username' => "required|regex_match[/^[A-Za-z0-9][A-Za-z0-9\- ]*[A-Za-z0-9]$/]|min_length[3]|max_length[50]|is_unique[users.username,id,$id]",
            'email' => "required|valid_email|max_length[100]|is_unique[users.email,id,$id]",
            'full_name' => 'permit_empty|max_length[100]',
            'role' => 'required|in_list[' . implode(',', $assignableRoles) . ']',
        ];
        
        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $requestedRole = $this->request->getPost('role');
        if (!in_array($requestedRole, $assignableRoles, true)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki izin untuk mengubah user ke role tersebut.');
        }
        
        // Prepare user data
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $requestedRole,
            'bio' => $this->request->getPost('bio')
        ];
        
        // Add password to data if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = $this->request->getPost('password');
        }
        
        // Update user
        $this->userModel->update($id, $userData);
        
        return redirect()->to('/admin/users')->with('message', 'User updated successfully');
    }
    
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        if (!$this->canManageUser($user)) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak memiliki izin untuk menghapus user dengan role tersebut.');
        }
        
        // Delete user
        $this->userModel->delete($id);
        
        return redirect()->to('/admin/users')->with('message', 'User deleted successfully');
    }

    protected function getAssignableRoles(): array
    {
        if ($this->isSuperAdmin()) {
            return $this->allRoles;
        }

        return ['user'];
    }

    protected function canManagePrivilegedRoles(): bool
    {
        return $this->isSuperAdmin();
    }

    protected function canManageUser(array $user): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return !in_array($user['role'], $this->privilegedRoles, true);
    }
}

