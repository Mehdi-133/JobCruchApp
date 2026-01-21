<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Auth;
use App\models\Student;
use App\models\Company;
use App\models\Announcement;
use App\models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if admin is logged in
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }

        $studentModel = new Student();
        $companyModel = new Company();
        $announcementModel = new Announcement();
        $applicationModel = new Application();

        $stats = [
            'students' => [
                'total' => $studentModel->getCount(),
                'active' => $studentModel->getActiveCount(),
                'recent' => $studentModel->getRecentStudents(5)
            ],
            'companies' => [
                'total' => $companyModel->getCount(),
                'active' => $companyModel->getActiveCount(),
                'recent' => $companyModel->getRecentCompanies(5)
            ],
            'announcements' => [
                'total' => $announcementModel->getCount(),
                'active' => $announcementModel->getActiveCount(),
                'expired' => $announcementModel->getExpiredCount(),
                'recent' => $announcementModel->getRecentAnnouncements(5)
            ],
            'applications' => [
                'total' => $applicationModel->getCount(),
                'pending' => $applicationModel->getPendingCount(),
                'reviewed' => $applicationModel->getReviewedCount(),
                'accepted' => $applicationModel->getAcceptedCount(),
                'rejected' => $applicationModel->getRejectedCount(),
                'recent' => $applicationModel->getRecentApplications(5)
            ]
        ];

        $this->view('back/dashboard/index', ['stats' => $stats]);
    }
}