<?php

namespace App\models;
use App\core\Model;
use PDO;

class User extends Model
{
    protected $table = 'users';

    // Constants for user roles
    const ROLE_ADMIN = 'admin';
    const ROLE_STUDENT = 'student';

    // Fillable attributes
    protected $fillable = ['email', 'name', 'password', 'role'];

    // Attributes
    protected $id;
    protected $email;
    protected $name;
    protected $password;
    protected $role;
    protected $speciality;
    protected $promo;
    protected $created_at;
    protected $updated_at;
    protected $deleted_at;

    public function __construct() {
        parent::__construct();
    }
    public function findByEmail($email)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByRole($role)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE role = ? AND deleted_at IS NULL", [$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveUsers()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE deleted_at IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountByRole($role)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE role = ? AND deleted_at IS NULL", [$role]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getRecentByRole($role, $limit = 5)
    {
        $limit = (int)$limit;
        $stmt = $this->dbInstance->secureQuery(
            "SELECT * FROM {$this->table} WHERE role = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT {$limit}",
            [$role]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveCountByRole($role)
    {
        // Active users are those created in the last 30 days
        $stmt = $this->dbInstance->secureQuery(
            "SELECT COUNT(*) as count FROM {$this->table} 
             WHERE role = ? AND deleted_at IS NULL 
             AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$role]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function isDeleted()
    {
        return !is_null($this->deleted_at);
    }
}
