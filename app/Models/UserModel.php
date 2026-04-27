<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'profile_picture',
        'bio',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'username' => 'required|regex_match[/^[A-Za-z0-9][A-Za-z0-9\- ]*[A-Za-z0-9]$/]|min_length[3]|max_length[50]',
        'email' => 'required|valid_email|max_length[100]',
        'password' => 'required|min_length[8]',
        'full_name' => 'permit_empty|max_length[100]',
        'role' => 'required|in_list[super_admin,admin,user]',
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'is_unique' => 'Username sudah digunakan',
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan',
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 8 karakter',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function findUserByEmailOrUsername($login)
    {
        return $this->where('email', $login)
            ->orWhere('username', $login)
            ->first();
    }

    // Helper method untuk memeriksa keunikan username
    public function isUsernameUnique($username, $exceptId = null)
    {
        $query = $this->where('username', $username);
        if ($exceptId !== null) {
            $query = $query->where('id !=', $exceptId);
        }
        $result = $query->countAllResults();
        return $result === 0;
    }

    // Helper method untuk memeriksa keunikan email
    public function isEmailUnique($email, $exceptId = null)
    {
        $query = $this->where('email', $email);
        if ($exceptId !== null) {
            $query = $query->where('id !=', $exceptId);
        }
        $result = $query->countAllResults();
        return $result === 0;
    }

    public function isAdmin($userId)
    {
        $user = $this->find($userId);

        return $user !== null && in_array($user['role'], ['admin', 'super_admin'], true);
    }
}
