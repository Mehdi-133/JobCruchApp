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
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                return;
            }
            die('Invalid CSRF token');
        }

        $applicationModel = new Application();
        $result = $applicationModel->updateStatus($id, Application::STATUS_ACCEPTED);
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Application accepted successfully' : 'Failed to accept application',
                'status' => Application::STATUS_ACCEPTED
            ]);
            return;
        }
        
        $this->redirect('admin/applications');
    }

    public function reject($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                return;
            }
            die('Invalid CSRF token');
        }

        $applicationModel = new Application();
        $result = $applicationModel->updateStatus($id, Application::STATUS_REJECTED);
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Application rejected successfully' : 'Failed to reject application',
                'status' => Application::STATUS_REJECTED
            ]);
            return;
        }
        
        $this->redirect('admin/applications');
    }

    public function reset($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                return;
            }
            die('Invalid CSRF token');
        }

        $applicationModel = new Application();
        $result = $applicationModel->updateStatus($id, Application::STATUS_PENDING);
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Application reset to pending successfully' : 'Failed to reset application',
                'status' => Application::STATUS_PENDING
            ]);
            return;
        }
        
        $this->redirect('admin/applications');
    }

    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}