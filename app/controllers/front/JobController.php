<?php

    namespace App\controllers\front;


    use App\core\Controller;
    use App\core\Auth;
    use App\core\Security;
    use App\models\Announcement;
    use App\models\Application;
    use App\models\User;

    class JobController extends  Controller {

        public function index()
        {
            // Check if user is authenticated
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }
            
            $user = Auth::user();
            
            // Get fresh user data from database to ensure profile_image is loaded
            $userModel = new User();
            $freshUserData = $userModel->findById($user['id']);
            if ($freshUserData) {
                $user = $freshUserData;
                // Update session with fresh data
                Auth::updateSession($user);
            }
            
            $announcementModel = new Announcement();
            $applicationModel = new Application();
            
            // Get all active jobs
            $jobs = $announcementModel->All();
            
            // Check if user has an ACCEPTED application (if accepted, can't apply anymore)
            $acceptedApplication = $applicationModel->getByUserIdAndStatus($user['id'], Application::STATUS_ACCEPTED);
            $hasAccepted = !empty($acceptedApplication);
            $acceptedJobId = $hasAccepted ? $acceptedApplication[0]['annonce_id'] : null;
            
            // Get all jobs user has already applied to
            $allApplications = $applicationModel->getByUserId($user['id']);
            $appliedJobIds = array_map(function($app) { return $app['annonce_id']; }, $allApplications);
            
            $this->view('front/jobs/index', [
                'jobs' => $jobs,
                'hasAccepted' => $hasAccepted,
                'acceptedJobId' => $acceptedJobId,
                'appliedJobIds' => $appliedJobIds,
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
            $jobId = Security::sanitize($_POST['job_id']);
            
            // Check if user has an ACCEPTED application (if accepted, can't apply anymore)
            $acceptedApplication = $applicationModel->getByUserIdAndStatus($user['id'], Application::STATUS_ACCEPTED);
            if (!empty($acceptedApplication)) {
                $_SESSION['error'] = 'You have already been accepted for a position. You cannot apply to other jobs.';
                header('Location: /jobs');
                exit;
            }
            
            // Check if user already applied to this specific job
            if ($applicationModel->hasUserApplied($user['id'], $jobId)) {
                $_SESSION['error'] = 'You have already applied to this job.';
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

        public function profile()
        {
            // Check if user is authenticated
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }

            $user = Auth::user();
            
            // Get fresh user data from database to ensure profile_image is loaded
            $userModel = new User();
            $freshUserData = $userModel->findById($user['id']);
            if ($freshUserData) {
                $user = $freshUserData;
                // Update session with fresh data
                Auth::updateSession($user);
            }
            
            $applicationModel = new Application();

            // Get application statistics
            $stats = [
                'total_applications' => $applicationModel->getCountByUserId($user['id']),
                'pending_applications' => $applicationModel->getCountByUserIdAndStatus($user['id'], Application::STATUS_PENDING),
                'accepted_applications' => $applicationModel->getCountByUserIdAndStatus($user['id'], Application::STATUS_ACCEPTED),
                'rejected_applications' => $applicationModel->getCountByUserIdAndStatus($user['id'], Application::STATUS_REJECTED),
            ];

            // Get recent applications (limit 5)
            $allApplications = $applicationModel->getByUserIdWithDetails($user['id']);
            $recent_applications = array_slice($allApplications, 0, 5);

            $this->view('front/profile/dashboard', [
                'user' => $user,
                'stats' => $stats,
                'recent_applications' => $recent_applications,
                'csrf_token' => Security::getToken(),
                'page_type' => 'student'
            ]);
        }

        public function updateProfile()
        {
            // Check if user is authenticated
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }

            // Verify CSRF token
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
                header('Location: /profile');
                exit;
            }

            $user = Auth::user();
            $userModel = new User();

            // Prepare update data
            $updateData = [
                'name' => $_POST['name'] ?? $user['name'],
                'email' => $_POST['email'] ?? $user['email'],
                'speciality' => $_POST['speciality'] ?? $user['speciality'],
                'promo' => $_POST['promo'] ?? $user['promo'],
            ];

            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = $_FILES['profile_image']['type'];
                $fileSize = $_FILES['profile_image']['size'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($fileType, $allowedTypes)) {
                    header('Location: /profile?error=invalid_image_type');
                    exit;
                }

                if ($fileSize > $maxSize) {
                    header('Location: /profile?error=image_too_large');
                    exit;
                }

                // Create uploads directory if it doesn't exist
                $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $user['id'] . '_' . time() . '.' . $extension;
                $destination = $uploadDir . $filename;

                // Move uploaded file
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                    // Delete old profile image if exists
                    if ($user['profile_image'] && file_exists(__DIR__ . '/../../public/' . $user['profile_image'])) {
                        unlink(__DIR__ . '/../../public/' . $user['profile_image']);
                    }

                    $updateData['profile_image'] = 'uploads/profiles/' . $filename;
                } else {
                    // File upload failed, but continue with other updates
                    error_log('File upload failed. Destination: ' . $destination . ', Tmp: ' . $_FILES['profile_image']['tmp_name']);
                }
            }

            // Update user in database
            $userModel->update($user['id'], $updateData);

            // Refresh session with updated data
            Auth::updateSession($updateData);

            // Redirect back to profile with success
            header('Location: /profile?success=Profile updated successfully');
            exit;
        }

        public function applications()
        {
            // Check authentication
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }
            
            $user = Auth::user();
            
            // Get fresh user data from database to ensure profile_image is loaded
            $userModel = new User();
            $freshUserData = $userModel->findById($user['id']);
            if ($freshUserData) {
                $user = $freshUserData;
                // Update session with fresh data
                Auth::updateSession($user);
            }
            
            $applicationModel = new Application();
            
            // Get all applications for the current user with job details
            $applications = $applicationModel->getByUserIdWithDetails($user['id']);
            
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'applications' => $applications
                ]);
                return;
            }
            
            $this->view('front/jobs/myapplication', [
                'applications' => $applications,
                'user' => $user,
                'csrf_token' => Security::getToken(),
                'page_type' => 'student'
            ]);
        }

        public function refreshApplications()
        {
            // Check authentication
            if (!Auth::check()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            $user = Auth::user();
            $applicationModel = new Application();
            
            // Get user's applications with details
            $applications = $applicationModel->getAllApplicationsWithDetails();
            
            // Filter applications for current user
            $userApplications = array_filter($applications, function($app) use ($user) {
                return $app['user_id'] == $user['id'];
            });
            
            // Re-index array
            $userApplications = array_values($userApplications);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'applications' => $userApplications
            ]);
        }

        private function isAjaxRequest()
        {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }

    }


?>