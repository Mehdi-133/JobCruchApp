<?php

namespace App\models;

use App\core\Model;
use App\core\Database;
use PDO;

class Announcement extends Model
{
    protected $table = 'annonces';

    // Constants for contract types
    const CONTRACT_CDI = 'CDI';
    const CONTRACT_CDD = 'CDD';
    const CONTRACT_INTERNSHIP = 'Internship';
    const CONTRACT_FREELANCE = 'Freelance';

    // Fillable attributes
    protected $fillable = ['title', 'description', 'company', 'contract', 'location', 'skills_required'];

    // Attributes
    protected $id;
    protected $title;
    protected $description;
    protected $company;
    protected $contract;
    protected $location;
    protected $skills_required;
    protected $posted_at;
    protected $is_active;

    public function getCount()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table}");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getActiveCount()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getExpiredCount()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 0");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function find($id)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, c.name as company_name, c.email as company_email, c.phone as company_phone 
             FROM {$this->table} a 
             LEFT JOIN companys c ON a.company = c.id 
             WHERE a.id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentAnnouncements($limit = 10)
    {
        $limit = (int)$limit;
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, c.name as company_name 
             FROM {$this->table} a 
             LEFT JOIN companys c ON a.company = c.id 
             ORDER BY a.posted_at DESC LIMIT {$limit}"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCompany($companyId)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE company = ?", [$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByContract($contractType)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE contract = ? AND is_active = 1", [$contractType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isExpired()
    {
        return !$this->is_active;
    }

    public static function getContractTypes()
    {
        return [
            self::CONTRACT_CDI,
            self::CONTRACT_CDD,
            self::CONTRACT_INTERNSHIP,
            self::CONTRACT_FREELANCE
        ];
    }
}