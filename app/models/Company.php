<?php

namespace App\models;

use App\core\Model;
use PDO;

class Company extends Model
{
    protected $table = 'companys';

    // Fillable attributes
    protected $fillable = ['name', 'sector', 'address', 'phone', 'email'];

    // Attributes
    protected $id;
    protected $name;
    protected $sector;
    protected $address;
    protected $phone;
    protected $email;
    protected $created_at;
    protected $updated_at;
    protected $deleted_at;

    public function getCount()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getRecentCompanies($limit = 5)
    {
        $limit = (int)$limit;
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT {$limit}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveCount()
    {
        // Active companies are those created in the last 30 days
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function findByEmail($email)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL", [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySector($sector)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE sector = ? AND deleted_at IS NULL", [$sector]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}