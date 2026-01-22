<?php

namespace App\models;

use App\core\Model;
use PDO;

class Application extends Model
{
    protected $table = 'applications';

    // Constants for application status
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    // Fillable attributes
    protected $fillable = ['user_id', 'annonce_id', 'cover_letter', 'status', 'cv_path'];

    // Attributes
    protected $id;
    protected $user_id;
    protected $annonce_id;
    protected $cover_letter;
    protected $cv_path;
    protected $applied_at;
    protected $status;

    /**
     * Get all applications with detailed information
     */
    public function getAllApplicationsWithDetails()
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, 
                    u.name as student_name, 
                    u.email as student_email,
                    u.speciality,
                    u.promo as promo,
                    an.title as job_title,
                    an.contract as contract_type,
                    c.name as company_name,
                    a.applied_at,
                    a.status
             FROM {$this->table} a 
             LEFT JOIN users u ON a.user_id = u.id 
             LEFT JOIN annonces an ON a.annonce_id = an.id
             LEFT JOIN companys c ON an.company = c.id
             ORDER BY a.applied_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of applications
     */
    public function getCount()
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table}");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    /**
     * Get count of applications by status
     */
    public function getCountByStatus($status)
    {
        $stmt = $this->dbInstance->secureQuery("SELECT COUNT(*) as count FROM {$this->table} WHERE status = ?", [$status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    /**
     * Get pending applications count
     */
    public function getPendingCount()
    {
        return $this->getCountByStatus(self::STATUS_PENDING);
    }

    /**
     * Get reviewed applications count
     */
    public function getReviewedCount()
    {
        return $this->getCountByStatus(self::STATUS_REVIEWED);
    }

    /**
     * Get accepted applications count
     */
    public function getAcceptedCount()
    {
        return $this->getCountByStatus(self::STATUS_ACCEPTED);
    }

    /**
     * Get rejected applications count
     */
    public function getRejectedCount()
    {
        return $this->getCountByStatus(self::STATUS_REJECTED);
    }

    /**
     * Get all applications for a specific user
     */
    public function getByUserId($userId)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, an.title as job_title, an.company, an.location 
             FROM {$this->table} a 
             LEFT JOIN annonces an ON a.annonce_id = an.id 
             WHERE a.user_id = ? 
             ORDER BY a.applied_at DESC",
            [$userId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all applications for a specific job announcement
     */
    public function getByAnnonceId($annonceId)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, u.name, u.email 
             FROM {$this->table} a 
             LEFT JOIN users u ON a.user_id = u.id 
             WHERE a.annonce_id = ? 
             ORDER BY a.applied_at DESC",
            [$annonceId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent applications
     */
    public function getRecentApplications($limit = 10)
    {
        $limit = (int) $limit; // Ensure it's an integer
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, u.name, u.email, an.title as job_title 
             FROM {$this->table} a 
             LEFT JOIN users u ON a.user_id = u.id 
             LEFT JOIN annonces an ON a.annonce_id = an.id 
             ORDER BY a.applied_at DESC 
             LIMIT {$limit}"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get applications by status
     */
    public function getByStatus($status)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, u.name, u.email, an.title as job_title, an.company 
             FROM {$this->table} a 
             LEFT JOIN users u ON a.user_id = u.id 
             LEFT JOIN annonces an ON a.annonce_id = an.id 
             WHERE a.status = ? 
             ORDER BY a.applied_at DESC",
            [$status]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update application status
     */
    public function updateStatus($id, $status)
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_REVIEWED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED
        ];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update($id, ['status' => $status]);
    }

    /**
     * Check if user already applied for a job
     */
    public function hasUserApplied($userId, $annonceId)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT COUNT(*) as count FROM {$this->table} 
             WHERE user_id = ? AND annonce_id = ?",
            [$userId, $annonceId]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    /**
     * Get application with full details
     */
    public function getApplicationDetails($id)
    {
        $stmt = $this->dbInstance->secureQuery(
            "SELECT a.*, 
                    u.name, u.email,
                    an.title as job_title, an.company, an.location, an.contract, an.description as job_description
             FROM {$this->table} a 
             LEFT JOIN users u ON a.user_id = u.id 
             LEFT JOIN annonces an ON a.annonce_id = an.id 
             WHERE a.id = ?",
            [$id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete application
     */
    public function deleteApplication($id)
    {
        return $this->delete($id);
    }
}
