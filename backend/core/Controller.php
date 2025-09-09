<?php
abstract class Controller {
    protected $db;
    protected $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = Auth::getCurrentUser();
    }

    protected function sendResponse($data, $statusCode = 200, $message = null) {
        http_response_code($statusCode);
        
        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $data
        ];

        if ($message) {
            $response['message'] = $message;
        }

        echo json_encode($response);
    }

    protected function sendError($message, $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
    }

    protected function getRequestData() {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?: [];
    }

    protected function validateRequired($data, $required) {
        $missing = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    protected function generateOrderNumber() {
        return 'FD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    protected function sendNotification($userId, $title, $message, $type = 'system') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $title, $message, $type]);
            
            // Here you could also send push notifications, emails, etc.
            
        } catch (Exception $e) {
            error_log("Failed to send notification: " . $e->getMessage());
        }
    }

    protected function logOrderStatus($orderId, $status, $notes = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO order_status_history (order_id, status, notes) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$orderId, $status, $notes]);
        } catch (Exception $e) {
            error_log("Failed to log order status: " . $e->getMessage());
        }
    }
}
?>
