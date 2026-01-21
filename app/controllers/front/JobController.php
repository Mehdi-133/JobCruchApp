<?php

    namespace App\controllers\front;


    use App\core\Controller;

    class JobController extends  Controller {

        public function index()
        {
            $this->view('front/jobs/index');
        }

    }


?>