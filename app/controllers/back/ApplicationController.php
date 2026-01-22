<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\models\Application;

class ApplicationController extends Controller
{
    public function index()
    {
        $applicationModel = new Application();
        $applications = $applicationModel->All();

        $this->view('back/applications/index', [
            'current_page' => 'applications',
            'applications' => $applications,
            'csrf_token' => Security::getToken()
        ]);
    }
}