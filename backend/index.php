<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';
require_once 'config/config.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';
require_once 'core/Model.php';
require_once 'middleware/Auth.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize router
$router = new Router();

// Authentication routes
$router->add('POST', '/api/auth/register', 'AuthController@register');
$router->add('POST', '/api/auth/login', 'AuthController@login');
$router->add('POST', '/api/auth/logout', 'AuthController@logout');
$router->add('GET', '/api/auth/me', 'AuthController@me');

// Customer routes
$router->add('GET', '/api/restaurants', 'RestaurantController@index');
$router->add('GET', '/api/restaurants/{id}', 'RestaurantController@show');
$router->add('GET', '/api/menu', 'MenuController@index');
$router->add('GET', '/api/menu/{id}', 'MenuController@show');

// Cart routes (protected)
$router->add('GET', '/api/cart', 'CartController@index', ['auth']);
$router->add('POST', '/api/cart', 'CartController@store', ['auth']);
$router->add('PUT', '/api/cart/{id}', 'CartController@update', ['auth']);
$router->add('DELETE', '/api/cart/{id}', 'CartController@destroy', ['auth']);

// Order routes (protected)
$router->add('GET', '/api/orders', 'OrderController@index', ['auth']);
$router->add('POST', '/api/orders', 'OrderController@store', ['auth']);
$router->add('GET', '/api/orders/{id}', 'OrderController@show', ['auth']);
$router->add('PUT', '/api/orders/{id}/status', 'OrderController@updateStatus', ['auth']);

// Agent routes (protected)
$router->add('GET', '/api/agent/orders', 'AgentController@getOrders', ['auth', 'agent']);
$router->add('PUT', '/api/agent/orders/{id}/pickup', 'AgentController@pickupOrder', ['auth', 'agent']);
$router->add('PUT', '/api/agent/orders/{id}/deliver', 'AgentController@deliverOrder', ['auth', 'agent']);
$router->add('GET', '/api/agent/earnings', 'AgentController@getEarnings', ['auth', 'agent']);

// Admin routes (protected)
$router->add('GET', '/api/admin/dashboard', 'AdminController@dashboard', ['auth', 'admin']);
$router->add('GET', '/api/admin/restaurants', 'AdminController@getRestaurants', ['auth', 'admin']);
$router->add('POST', '/api/admin/restaurants', 'AdminController@createRestaurant', ['auth', 'admin']);
$router->add('PUT', '/api/admin/restaurants/{id}', 'AdminController@updateRestaurant', ['auth', 'admin']);
$router->add('DELETE', '/api/admin/restaurants/{id}', 'AdminController@deleteRestaurant', ['auth', 'admin']);

// File upload routes
$router->add('POST', '/api/upload', 'UploadController@store', ['auth']);

// Contact routes
$router->add('POST', '/api/contact/submit', 'ContactController@submit');

// Partner routes
$router->add('POST', '/api/partners/apply', 'PartnerController@apply');
$router->add('POST', '/api/partners/assets', 'PartnerController@uploadAssets');
$router->add('GET', '/api/partners/testimonials', 'PartnerController@testimonials');
$router->add('POST', '/api/partners/testimonials', 'PartnerController@createTestimonial', ['auth', 'admin']);

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove base path if needed
$basePath = '/backend';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

try {
    $router->dispatch($requestMethod, $requestUri, $db);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>
