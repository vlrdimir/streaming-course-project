<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserSettingsController extends Controller
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }
    
    public function index()
    {
        // Cek apakah user sudah login
        if (!$this->session->has('id')) {
            return redirect()->to('/login');
        }
        
        // Ambil data user dari database
        $userId = $this->session->get('id');
        $userData = $this->userModel->find($userId);
        
        if (!$userData) {
            return redirect()->to('/login');
        }
        
        return view('user/settings', [
            'user' => $userData
        ]);
    }
    
    public function update()
    {
        // Cek apakah user sudah login
        if (!$this->session->has('id')) {
            return redirect()->to('/login');
        }
        
        $userId = $this->session->get('id');
        
        // Validasi input untuk perubahan password
        $rules = [];
        
        // Jika ada input password baru
        if ($this->request->getPost('new_password')) {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[new_password]'
            ];
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Verifikasi password lama
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login');
        }
        
        // Jika ada input password
        if ($this->request->getPost('new_password')) {
            $currentPassword = $this->request->getPost('current_password');
            
            if (!password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->with('error', 'Password saat ini tidak valid');
            }
            
            // Update password
            $this->userModel->update($userId, [
                'password' => $this->request->getPost('new_password')
            ]);
            
            return redirect()->to('user/settings')->with('message', 'Password berhasil diperbarui');
        }
        
        // Jika tidak ada perubahan
        return redirect()->to('user/settings')->with('warning', 'Tidak ada perubahan yang disimpan');
    }
} 