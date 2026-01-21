<?php

    namespace App\controllers\front;


    use App\core\Controller;
    use App\models\Announcement;

    class JobController extends  Controller {

        public function index()
        {
            $this->view('front/jobs/index');
        }

        public function show($id)
        {
            // Validate the ID
            if (!is_numeric($id)) {
                header('Location: /jobs');
                exit;
            }
            
            // Get the job/announcement from database
            $announcement = new Announcement();
            $job = $announcement->find($id);
            
            if (!$job) {
                // Job not found, redirect to jobs list
                header('Location: /jobs');
                exit;
            }
            
            // Render the job details view
            $this->view('front/jobs/show', ['job' => $job]);
        }

    }


?>