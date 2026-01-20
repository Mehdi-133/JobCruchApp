<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\Auth;

class JobController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $this->view('front/jobs/index', ['user' => $user]);
    }
}