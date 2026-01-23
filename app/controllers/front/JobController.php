<?php

    namespace App\controllers\front;


    use App\core\Controller;
    use App\core\Auth;
    use App\core\Security;
    use App\models\Announcement;
    use App\models\Application;

    class JobController extends  Controller {

        public function index()
        {
            // Check if user is authenticated
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }
            
            $user = Auth::user();
            $announcementModel = new Announcement();
            $applicationModel = new Application();
            
            // Get all active jobs
            $jobs = $announcementModel->All();
            
            // Check if user already applied to any job
            $userApplication = $applicationModel->getByUserId($user['id']);
            $hasApplied = !empty($userApplication);
            $appliedJobId = $hasApplied ? $userApplication[0]['annonce_id'] : null;
            
            $this->view('front/jobs/index', [
                'jobs' => $jobs,
                'hasApplied' => $hasApplied,
                'appliedJobId' => $appliedJobId,
                'user' => $user,
                'csrf_token' => Security::getToken(),
                'page_type' => 'student'
            ]);
        }

        public function apply()
        {
            // Check authentication
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }
            
            // Validate CSRF token
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF token');
            }
            
            $user = Auth::user();
            $applicationModel = new Application();
            
            // Check if user already applied to a job
            $existingApplication = $applicationModel->getByUserId($user['id']);
            if (!empty($existingApplication)) {
                $_SESSION['error'] = 'You have already applied to a job. You can only apply to one job.';
                header('Location: /jobs');
                exit;
            }
            
            // Handle CV upload
            $cvPath = null;
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['application/pdf'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                $fileType = $_FILES['cv']['type'];
                $fileSize = $_FILES['cv']['size'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['error'] = 'Invalid file type. Only PDF files are allowed for CV.';
                    header('Location: /jobs');
                    exit;
                }
                
                if ($fileSize > $maxSize) {
                    $_SESSION['error'] = 'CV file size must not exceed 5MB.';
                    header('Location: /jobs');
                    exit;
                }
                
                // Create uploads directory if it doesn't exist
                $uploadDir = __DIR__ . '/../../public/uploads/cvs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate unique filename
                $filename = uniqid('cv_', true) . '.pdf';
                $destination = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
                    $cvPath = 'uploads/cvs/' . $filename;
                }
            }
            
            // Create application
            $applicationModel->create([
                'user_id' => $user['id'],
                'annonce_id' => Security::sanitize($_POST['job_id']),
                'cover_letter' => Security::sanitize($_POST['cover_letter'] ?? ''),
                'cv_path' => $cvPath,
                'status' => Application::STATUS_PENDING
            ]);
            
            $_SESSION['success'] = 'Your application has been submitted successfully!';
            header('Location: /jobs');
            exit;
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
            $this->view('front/jobs/show', ['job' => $job, 'page_type' => 'student']);
        }

    }


?>