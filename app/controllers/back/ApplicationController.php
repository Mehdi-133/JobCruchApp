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
        $applications = $applicationModel->getAllApplicationsWithDetails();

        $this->view('back/applications/index', [
            'current_page' => 'applications',
            'applications' => $applications,
            'csrf_token' => Security::getToken()
        ]);
    }

    public function accept($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $applicationModel = new Application();
        $applicationModel->updateStatus($id, Application::STATUS_ACCEPTED);
        
        $this->redirect('admin/applications');
    }

    public function reject($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $applicationModel = new Application();
        $applicationModel->updateStatus($id, Application::STATUS_REJECTED);
        
        $this->redirect('admin/applications');
    }
}