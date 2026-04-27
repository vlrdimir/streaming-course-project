<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserProfileController extends Controller
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

        return view('user/profile', [
            'user' => $userData
        ]);
    }

    public function edit()
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

        log_message('error', 'User data: ' . json_encode($userData));

        return view('user/edit_profile', [
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

        // Ambil data user saat ini
        $currentUser = $this->userModel->find($userId);
        if (!$currentUser) {
            return redirect()->to('/login');
        }

        // Ambil data dari form
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $fullName = $this->request->getPost('full_name');
        $bio = $this->request->getPost('bio');

        // Validasi input
        $rules = [
            'full_name' => 'permit_empty|max_length[100]',
            'bio' => 'permit_empty|max_length[500]',
            'profile_picture' => 'permit_empty|uploaded[profile_picture]|max_size[profile_picture,1024]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]'
        ];

        // Jika validasi gagal
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyiapkan data untuk update
        $updateData = [
            'full_name' => $fullName,
            'bio' => $bio
        ];

        // Cek apakah username berubah dan unik
        if ($username && $username !== $currentUser['username']) {
            // Cek keunikan username secara manual
            if (!$this->userModel->isUsernameUnique($username, $userId)) {
                return redirect()->back()->withInput()->with('error', 'Username sudah digunakan');
            }
            $updateData['username'] = $username;
        }

        // Cek apakah email berubah dan unik
        if ($email && $email !== $currentUser['email']) {
            // Cek keunikan email secara manual
            if (!$this->userModel->isEmailUnique($email, $userId)) {
                return redirect()->back()->withInput()->with('error', 'Email sudah digunakan');
            }
            $updateData['email'] = $email;
        }

        // Handle profile picture upload
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            log_message('error', 'File: ' . FCPATH . 'avatar/' . $newName);
            $file->move(FCPATH . 'avatar', $newName);
            $updateData['profile_picture'] = $newName;
        }

        try {
            // Update data user dengan skip validasi
            $result = $this->userModel->skipValidation(true)->update($userId, $updateData);

            // Update session data jika username berubah
            if (isset($updateData['username'])) {
                $this->session->set('username', $updateData['username']);
            }

            return redirect()->to('user/profile')->with('message', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'Error updating user profile: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }
}
