<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $currentUser = null;

    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Authorization token required'
            ]);
            return false;
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
            self::$currentUser = (array) $decoded->data;
            return true;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired token'
            ]);
            return false;
        }
    }

    public static function requireRole($role) {
        if (!self::$currentUser) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Authentication required'
            ]);
            return false;
        }

        if (self::$currentUser['role'] !== $role && self::$currentUser['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
            return false;
        }

        return true;
    }

    public static function getCurrentUser() {
        return self::$currentUser;
    }

    public static function generateToken($user) {
        $payload = [
            'iss' => BACKEND_URL,
            'aud' => FRONTEND_URL,
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION,
            'data' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ]
        ];

        return JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function generateResetToken() {
        return bin2hex(random_bytes(32));
    }

    public static function validatePasswordStrength($password) {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return $errors;
    }
}
?>
