<?php
require_once __DIR__ . '/../core/Controller.php';

class ContactController extends Controller {
    public function submit($params = []) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                return $this->sendError('Invalid JSON payload', 400);
            }

            $required = ['firstName','lastName','email','subject','message'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    return $this->sendError("Missing field: $field", 422);
                }
            }
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->sendError('Invalid email format', 422);
            }

            $this->createTableIfNotExists();

            $stmt = $this->db->prepare("INSERT INTO contact_messages (first_name,last_name,email,phone,subject,message,created_at) VALUES (?,?,?,?,?,?,NOW())");
            $stmt->execute([
                $input['firstName'],
                $input['lastName'],
                $input['email'],
                isset($input['phone']) ? $input['phone'] : null,
                $input['subject'],
                $input['message']
            ]);

            return $this->sendResponse(['id' => $this->db->lastInsertId()], 201, 'Message received');
        } catch (Exception $e) {
            return $this->sendError('Failed to submit message: ' . $e->getMessage(), 500);
        }
    }

    private function createTableIfNotExists() {
        $sql = "
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NULL,
                subject VARCHAR(100) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->exec($sql);
    }
}
