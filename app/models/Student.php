<?php

namespace App\models;

use App\models\User;

class Student extends User
{
    protected $role = 'student';

    // Student-specific attributes
    protected $promotion;
    protected $speciality;

    // Override fillable to include student-specific fields
    protected $fillable = ['email', 'name', 'password', 'role', 'promotion', 'speciality'];

    public function __construct()
    {
        parent::__construct();
        $this->role = self::ROLE_STUDENT;
    }

    public function getCount()
    {
        return $this->getCountByRole($this->role);
    }

    public function getRecentStudents($limit = 5)
    {
        return $this->getRecentByRole($this->role, $limit);
    }

    public function getActiveCount()
    {
        return $this->getActiveCountByRole($this->role);
    }

    public function getAllStudents()
    {
        return $this->findByRole($this->role);
    }
}