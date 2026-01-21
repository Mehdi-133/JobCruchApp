<?php

namespace App\models;

use App\core\Model;
Use PDO;

class Company extends  Model
{
    protected $table = 'companys';


    public function All()
    {
        return parent::All();
    }

    public function findById($id)
    {
        return parent::findById($id);
    }

    public function create($data)
    {
        return parent::create($data);
    }

    public function update($id, $data)
    {
        return parent::update($id, $data);
    }

    public function delete($id)
    {
        return parent::delete($id);
    }


}