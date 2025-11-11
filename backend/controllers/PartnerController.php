<?php
require_once __DIR__ . '/../core/Controller.php';

class PartnerController extends Controller {

    public function apply($params = []) {
        try {
            $inputRaw = file_get_contents('php://input');
            $input = json_decode($inputRaw, true);
            if (!$input) {
                return $this->sendError('Invalid JSON payload', 400);
            }

            $required = ['businessName','contactName','email','phone','city','cuisine','restaurantCount','partnershipType'];
            foreach ($required as $field) {
                if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
                    return $this->sendError("Missing field: $field", 422);
                }
            }
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->sendError('Invalid email format', 422);
            }

            $this->createApplicationsTable();

            $stmt = $this->db->prepare("INSERT INTO partner_applications (
                business_name, contact_name, email, phone, city, cuisine, restaurant_count, partnership_type, agree, logo_url, menu_pdf_url, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->execute([
                $input['businessName'],
                $input['contactName'],
                $input['email'],
                $input['phone'],
                $input['city'],
                $input['cuisine'],
                (int)$input['restaurantCount'],
                $input['partnershipType'],
                isset($input['agree']) && $input['agree'] ? 1 : 0,
                isset($input['logoUrl']) ? $input['logoUrl'] : null,
                isset($input['menuPdfUrl']) ? $input['menuPdfUrl'] : null
            ]);

            return $this->sendResponse(['id' => $this->db->lastInsertId()], 201, 'Application submitted');
        } catch (Exception $e) {
            return $this->sendError('Failed to submit application: ' . $e->getMessage(), 500);
        }
    }

    public function uploadAssets($params = []) {
        try {
            // Accept files: logo (image) and menu_pdf (pdf)
            if (!isset($_FILES['logo']) && !isset($_FILES['menu_pdf'])) {
                return $this->sendError('No files uploaded', 400);
            }

            $result = [];
            // Ensure upload directory
            $partnerDir = rtrim(UPLOAD_PATH, '/\\') . DIRECTORY_SEPARATOR . 'partners' . DIRECTORY_SEPARATOR;
            if (!is_dir($partnerDir)) {
                mkdir($partnerDir, 0755, true);
            }

            // Handle logo
            if (isset($_FILES['logo'])) {
                $logo = $_FILES['logo'];
                $allowedImageMimes = ['image/jpeg','image/png','image/gif','image/webp'];
                if ($logo['error'] === UPLOAD_ERR_OK) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $logo['tmp_name']);
                    finfo_close($finfo);
                    if (!in_array($mime, $allowedImageMimes)) {
                        return $this->sendError('Invalid logo type', 422);
                    }
                    if ($logo['size'] > (5 * 1024 * 1024)) {
                        return $this->sendError('Logo exceeds 5MB', 422);
                    }
                    $ext = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
                    $fileName = 'logo_' . uniqid() . '_' . time() . '.' . $ext;
                    $dest = $partnerDir . $fileName;
                    if (!move_uploaded_file($logo['tmp_name'], $dest)) {
                        return $this->sendError('Failed to save logo', 500);
                    }
                    $result['logoUrl'] = BACKEND_URL . '/uploads/partners/' . $fileName;
                } else {
                    return $this->sendError('Logo upload error', 400);
                }
            }

            // Handle menu PDF
            if (isset($_FILES['menu_pdf'])) {
                $pdf = $_FILES['menu_pdf'];
                if ($pdf['error'] === UPLOAD_ERR_OK) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $pdf['tmp_name']);
                    finfo_close($finfo);
                    if ($mime !== 'application/pdf') {
                        return $this->sendError('Menu must be a PDF', 422);
                    }
                    if ($pdf['size'] > (10 * 1024 * 1024)) {
                        return $this->sendError('Menu PDF exceeds 10MB', 422);
                    }
                    $ext = 'pdf';
                    $fileName = 'menu_' . uniqid() . '_' . time() . '.' . $ext;
                    $dest = $partnerDir . $fileName;
                    if (!move_uploaded_file($pdf['tmp_name'], $dest)) {
                        return $this->sendError('Failed to save menu PDF', 500);
                    }
                    $result['menuPdfUrl'] = BACKEND_URL . '/uploads/partners/' . $fileName;
                } else {
                    return $this->sendError('Menu PDF upload error', 400);
                }
            }

            return $this->sendResponse($result, 201, 'Assets uploaded');
        } catch (Exception $e) {
            return $this->sendError('Upload failed: ' . $e->getMessage(), 500);
        }
    }

    public function testimonials($params = []) {
        try {
            $this->createTestimonialsTable();
            $stmt = $this->db->query("SELECT id,business_name,quote,author,rating,avatar_url FROM partner_testimonials ORDER BY id DESC LIMIT 12");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sendResponse(['items' => $rows], 200, 'OK');
        } catch (Exception $e) {
            return $this->sendError('Failed to load testimonials: ' . $e->getMessage(), 500);
        }
    }

    public function createTestimonial($params = []) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) return $this->sendError('Invalid JSON', 400);
            $required = ['businessName','quote','author','rating'];
            foreach ($required as $f) { if (empty($input[$f])) return $this->sendError("Missing field: $f", 422); }
            $rating = (int)$input['rating'];
            if ($rating < 1 || $rating > 5) return $this->sendError('Rating must be 1-5', 422);

            $this->createTestimonialsTable();
            $stmt = $this->db->prepare("INSERT INTO partner_testimonials (business_name,quote,author,rating,avatar_url,created_at) VALUES (?,?,?,?,?,NOW())");
            $stmt->execute([
                $input['businessName'],
                $input['quote'],
                $input['author'],
                $rating,
                isset($input['avatarUrl']) ? $input['avatarUrl'] : null
            ]);
            return $this->sendResponse(['id' => $this->db->lastInsertId()], 201, 'Created');
        } catch (Exception $e) {
            return $this->sendError('Failed to save testimonial: ' . $e->getMessage(), 500);
        }
    }

    private function createApplicationsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS partner_applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_name VARCHAR(200) NOT NULL,
            contact_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(60) NOT NULL,
            city VARCHAR(120) NOT NULL,
            cuisine VARCHAR(120) NOT NULL,
            restaurant_count INT NOT NULL,
            partnership_type VARCHAR(40) NOT NULL,
            agree TINYINT(1) DEFAULT 0,
            logo_url VARCHAR(500) NULL,
            menu_pdf_url VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sql);
    }

    private function createTestimonialsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS partner_testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_name VARCHAR(200) NOT NULL,
            quote TEXT NOT NULL,
            author VARCHAR(150) NOT NULL,
            rating INT NOT NULL,
            avatar_url VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sql);
    }
}
