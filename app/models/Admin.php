<?php

namespace App\models;

use App\models\User;

class Admin extends User
{
    protected $role = 'admin';

    public function __construct()
    {
        parent::__construct();
        $this->role = self::ROLE_ADMIN;
    }

    public function getCount()
    {
        return $this->getCountByRole($this->role);
    }

    public function getRecentAdmins($limit = 5)
    {
        return $this->getRecentByRole($this->role, $limit);
    }

    public function getActiveCount()
    {
        return $this->getActiveCountByRole($this->role);
    }

    public function getAllAdmins()
    {
        return $this->findByRole($this->role);
    }

    public function isAdmin($userId)
    {
        $admin = $this->findById($userId);
        return $admin && $admin['role'] === 'admin';
    }
}
