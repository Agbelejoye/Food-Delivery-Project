<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/Auth.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    public function register($params = []) {
        $data = $this->getRequestData();
        
        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'password', 'role'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        // Sanitize input
        $data = $this->sanitizeInput($data);

        // Validate email format
        if (!$this->validateEmail($data['email'])) {
            return $this->sendError('Invalid email format', 400);
        }

        // Validate password strength
        $passwordErrors = Auth::validatePasswordStrength($data['password']);
        if (!empty($passwordErrors)) {
            return $this->sendError('Password validation failed', 400, $passwordErrors);
        }

        // Validate role
        $allowedRoles = ['customer', 'restaurant', 'agent'];
        if (!in_array($data['role'], $allowedRoles)) {
            return $this->sendError('Invalid role specified', 400);
        }

        // Check if email already exists
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser) {
            return $this->sendError('Email already registered', 409);
        }

        try {
            // Create user
            $user = $this->userModel->createUser($data);
            
            if (!$user) {
                return $this->sendError('Failed to create user', 500);
            }

            // Generate JWT token
            $token = Auth::generateToken($user);

            // Send welcome notification
            $this->sendNotification(
                $user['id'], 
                'Welcome to FoodExpress!', 
                'Thank you for joining our platform. Start exploring restaurants and place your first order!'
            );

            return $this->sendResponse([
                'user' => $user,
                'token' => $token
            ], 201, 'User registered successfully');

        } catch (Exception $e) {
            return $this->sendError('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login($params = []) {
        $data = $this->getRequestData();
        
        // Validate required fields
        $required = ['email', 'password'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        // Sanitize input
        $data = $this->sanitizeInput($data);

        try {
            // Find user by email
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return $this->sendError('Invalid credentials', 401);
            }

            // Verify password
            if (!Auth::verifyPassword($data['password'], $user['password_hash'])) {
                return $this->sendError('Invalid credentials', 401);
            }

            // Remove password hash from response
            unset($user['password_hash']);

            // Generate JWT token
            $token = Auth::generateToken($user);

            // Update last login (optional)
            $stmt = $this->db->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$user['id']]);

            return $this->sendResponse([
                'user' => $user,
                'token' => $token
            ], 200, 'Login successful');

        } catch (Exception $e) {
            return $this->sendError('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout($params = []) {
        // In a stateless JWT system, logout is handled client-side
        // Here we could implement token blacklisting if needed
        
        return $this->sendResponse(null, 200, 'Logged out successfully');
    }

    public function me($params = []) {
        if (!$this->user) {
            return $this->sendError('User not authenticated', 401);
        }

        try {
            // Get fresh user data
            $user = $this->userModel->find($this->user['id']);
            
            if (!$user) {
                return $this->sendError('User not found', 404);
            }

            // Get user stats
            $stats = $this->userModel->getUserStats($user['id']);

            return $this->sendResponse([
                'user' => $user,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to get user data: ' . $e->getMessage(), 500);
        }
    }

    public function updateProfile($params = []) {
        if (!$this->user) {
            return $this->sendError('User not authenticated', 401);
        }

        $data = $this->getRequestData();
        
        // Remove sensitive fields that shouldn't be updated via this endpoint
        unset($data['password'], $data['password_hash'], $data['role'], $data['email_verified']);

        // Sanitize input
        $data = $this->sanitizeInput($data);

        try {
            $updatedUser = $this->userModel->update($this->user['id'], $data);
            
            if (!$updatedUser) {
                return $this->sendError('Failed to update profile', 500);
            }

            return $this->sendResponse($updatedUser, 200, 'Profile updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Profile update failed: ' . $e->getMessage(), 500);
        }
    }

    public function changePassword($params = []) {
        if (!$this->user) {
            return $this->sendError('User not authenticated', 401);
        }

        $data = $this->getRequestData();
        
        // Validate required fields
        $required = ['current_password', 'new_password'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        try {
            // Get current user with password hash
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$this->user['id']]);
            $userWithPassword = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify current password
            if (!Auth::verifyPassword($data['current_password'], $userWithPassword['password_hash'])) {
                return $this->sendError('Current password is incorrect', 400);
            }

            // Validate new password strength
            $passwordErrors = Auth::validatePasswordStrength($data['new_password']);
            if (!empty($passwordErrors)) {
                return $this->sendError('New password validation failed', 400, $passwordErrors);
            }

            // Update password
            $this->userModel->updatePassword($this->user['id'], $data['new_password']);

            return $this->sendResponse(null, 200, 'Password changed successfully');

        } catch (Exception $e) {
            return $this->sendError('Password change failed: ' . $e->getMessage(), 500);
        }
    }
}
?>
