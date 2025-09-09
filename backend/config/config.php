<?php
// Application configuration
define('APP_NAME', 'FoodExpress API');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// JWT Configuration
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600 * 24); // 24 hours

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@foodexpress.com');
define('SMTP_FROM_NAME', 'FoodExpress');

// Payment Configuration
define('STRIPE_SECRET_KEY', 'sk_test_your_stripe_secret_key');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_stripe_publishable_key');

// Application URLs
define('FRONTEND_URL', 'http://localhost:5173');
define('BACKEND_URL', 'http://localhost/food-delivery-project/backend');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// CORS Headers
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: ' . FRONTEND_URL);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
}
?>
