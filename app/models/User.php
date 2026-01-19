<?php

namespace App\models;
use App\core\Model;
use PDO;
class User extends Model{

    protected $table = 'users';

public function findByEmail($email) {
    $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function findByRole($role) {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE role = ?", [$role]);
        return $stmt->fetchAll();
    }

    public function getActiveUsers() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = ?", ['active']);
        return $stmt->fetchAll();
    }

    
}
