<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\core\Validator;
use App\models\Announcement;
use App\models\Company;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcementModel = new Announcement();
        $announcements = $announcementModel->getRecentAnnouncements(100); // Get more announcements

        $companyModel = new Company();
        $companies = $companyModel->All();

        $this->view('back/announcements/index', [
            'current_page' => 'announcements',
            'announcements' => $announcements,
            'companies' => $companies,
            'csrf_token' => Security::getToken()
        ]);
    }


    public function create()
    {
        $companyModel = new Company();
        $companies = $companyModel->getAll();

        $this->view('back/announcements/create', [
            'current_page' => 'announcements',
            'companies' => $companies,
            'csrf_token' => Security::getToken()
        ]);
    }

    public function store()
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $validator = new Validator($_POST);
        $validator->required('title')
            ->max('title', 255)
            ->required('description')
            ->required('company')
            ->required('contract')
            ->in('contract', ['CDI', 'CDD', 'Internship', 'Freelance'])
            ->required('location')
            ->required('expires_at');

        if ($validator->fails()) {
            $companyModel = new Company();
            $companies = $companyModel->getAll();

            $this->view('back/announcements/create', [
                'current_page' => 'announcements',
                'companies' => $companies,
                'errors' => $validator->errors(),
                'old' => $_POST,
                'csrf_token' => Security::getToken()
            ]);
            return;
        }

        $announcementModel = new Announcement();
        $announcementModel->create([
            'title' => Security::sanitize($_POST['title']),
            'description' => Security::sanitize($_POST['description']),
            'company' => (int)$_POST['company'],
            'contract' => Security::sanitize($_POST['contract']),
            'location' => Security::sanitize($_POST['location']),
            'skills_required' => Security::sanitize($_POST['skills_required'] ?? ''),
            'expires_at' => $_POST['expires_at'],
            'posted_at' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ]);

        $this->redirect('admin/announcements');
    }

    public function update($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $validator = new Validator($_POST);
        $validator->required('title')
            ->max('title', 255)
            ->required('description')
            ->required('company')
            ->required('contract')
            ->in('contract', ['CDI', 'CDD', 'Internship', 'Freelance'])
            ->required('location')
            ->required('expires_at');

        if ($validator->fails()) {
            $this->redirect('admin/announcements');
            return;
        }

        $announcementModel = new Announcement();
        $announcementModel->update($id, [
            'title' => Security::sanitize($_POST['title']),
            'description' => Security::sanitize($_POST['description']),
            'company' => (int)$_POST['company'],
            'contract' => Security::sanitize($_POST['contract']),
            'location' => Security::sanitize($_POST['location']),
            'skills_required' => Security::sanitize($_POST['skills_required'] ?? ''),
            'expires_at' => $_POST['expires_at']
        ]);

        $this->redirect('admin/announcements');
    }

    public function delete($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $announcementModel = new Announcement();
        $announcementModel->delete($id);

        $this->redirect('admin/announcements');
    }

    public function toggleStatus($id)
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);

        if ($announcement) {
            $newStatus = $announcement['is_active'] ? 0 : 1;
            $announcementModel->update($id, ['is_active' => $newStatus]);
        }

        $this->redirect('admin/announcements');
    }

}
