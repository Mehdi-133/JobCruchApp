<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\models\User;
use App\core\Security;

echo "=== Admin User Setup ===\n\n";

// Admin credentials
$adminData = [
    'email' => 'admin@jobdating.com',
    'name' => 'admin',
    'password' => Security::hashPassword('admin123'), // Change this password!
    'role' => 'admin',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

try {
    $userModel = new User();
    
    // Check if admin already exists
    $existing = $userModel->findByEmail($adminData['email']);
    if ($existing) {
        echo "❌ Admin user already exists!\n";
        echo "Email: {$adminData['email']}\n";
        exit;
    }
    
    // Create admin
    $userId = $userModel->create($adminData);
    
    echo "✅ Admin user created successfully!\n\n";
    echo "Login Credentials:\n";
    echo "Email: {$adminData['email']}\n";
    echo "Password: admin123\n";
    echo "\n⚠️  Please change the password after first login!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
