<?php

namespace App\controllers\back;
use App\core\Controller;

class JobController extends Controller{


public function index(){

     $this->view('main/index');
  }

public function create(){
    $this->view('main/create');
}

public function edit(){
    $this->view('main/edit');
}

public function delete(){
    $this->view('main/delete');
}

public function store(){
    $this->view('main/dashboard');

}

public function update(){
    $this->view('main/dashboard');
}













}




