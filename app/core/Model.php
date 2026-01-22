<?php

namespace App\core;

use PDO;
use App\core\Database;


class Model
{

    protected $db;
    protected $dbInstance;
    protected $table;

    public function __construct()
    {

        $this->dbInstance = Database::getInstance();
        $this->db = $this->dbInstance->getConnection();
    }


    public  function All()
    {

        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllBy($field, $value)
    {

        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE {$field} = ?", [$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {

        $stmt = $this->dbInstance->secureQuery("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($data)
    {
        $keys  = implode(',', array_keys($data));
        $placeHolders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$keys}) VALUES ({$placeHolders})";
        $this->dbInstance->secureQuery($sql, $data);
        return $this->db->lastInsertId();
    }


    public function update($id, $data)
    {
        $fields = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";
        $data['id'] = $id;
        return $this->dbInstance->secureQuery($sql, $data);
    }


    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->dbInstance->secureQuery($sql, [$id]);
        return $stmt->rowCount() > 0;
    }
}
