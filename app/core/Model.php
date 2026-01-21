<?php

namespace App\core;

use PDO;
use App\core\Database;


class Model
{

    protected $db;
    protected $table;

    public function __construct()
    {

        $this->db = Database::getInstance()->getConnection();
    }


    public  function All()
    {

        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllBy($field, $value)
    {

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($data)
    {
        $keys  = implode(',', array_keys($data));
        $placeHolders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$keys}) VALUES ({$placeHolders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }


    public function update($id, $data)
    {
        $fields = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";
        $data['id'] = $id;
        return $this->db->query($sql, $data);
    }


    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
