<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Company;
use App\core\Security;
use App\core\Validator;

class CompanyController extends Controller
{
    public function index()
    {

        $company = new Company();
        $companys = $company->All();
        $this->view('back/companies/index', ['companies' => $companys]);
    }

    public function create()
    {
        $token = Security::getToken();
        $this->view('back/companies/create', ['csrf_token' => $token]);
    }


    public function store()
    {

        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }


        $validator = new Validator($_POST);
        $validator->required('name')
            ->min('name', 2)
            ->required('email')
            ->email('email')
            ->required('address')
            ->min('address', 10);

        if ($validator->fails()) {
            $token = Security::getToken();
            $this->view('back/companies/create', [
                'errors' => $validator->errors(),
                'csrf_token' => $token,
                'old' => $_POST
            ]);
            return;
        }

        // Create company
        $company = new Company();
        $companyData = [
            'name' => Security::sanitize($_POST['name']),
            'sector' => Security::sanitize($_POST['sector']),
            'email' => Security::sanitize($_POST['email']),
            'phone' => Security::sanitize($_POST['phone']),
            'address' => Security::sanitize($_POST['address'])
        ];


        $company->create($companyData);

        // Redirect with success
        header('Location: /admin/companies');
        exit;
    }

}